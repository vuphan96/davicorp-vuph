<?php

namespace App\Admin\Api;

use App\Admin\Controllers\AdminCustomerController;
use App\Admin\Models\AdminDriver;
use App\Admin\Models\AdminDriverCustomer;
use App\Admin\Models\AdminHoliday;
use App\Admin\Models\AdminNotification;
use App\Admin\Models\AdminNotificationCustomer;
use App\Admin\Models\AdminOrder;
use App\Admin\Models\AdminProduct;
use App\Admin\Models\AdminProductPriceDetail;
use App\Admin\Models\AdminShopOrderChangeExtra;
use App\Admin\Models\AdminUnit;
use App\Admin\Models\AdminUser;
use App\Exports\DavicorpOrder\AdminExportMultipleSheet;
use App\Exports\DavicorpOrder\AdminMultipleSheetSalesInvoiceListRealOrder;
use App\Front\Models\ShopCustomer;
use App\Front\Models\ShopDeviceToken;
use App\Front\Models\ShopImportPriceboard;
use App\Front\Models\ShopOrder;
use App\Front\Models\ShopOrderDetail;
use App\Front\Models\ShopOrderHistory;
use App\Front\Models\ShopPoint;
use App\Front\Models\ShopPointHistory;
use App\Front\Models\ShopProduct;
use App\Front\Models\ShopProductSupplier;
use App\Front\Models\ShopRewardPrinciple;
use App\Front\Models\ShopRewardTier;
use App\Front\Models\ShopSupplier;
use App\Front\Models\ShopUserPriceboard;
use App\Http\Models\ShopProductDescription;
use Carbon\Carbon;
use Dompdf\Dompdf;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Nette\Utils\Random;

class OrderController extends ApiController
{
    /**
     * Get authenticated user
     */
    public function getOrderDetail($id)
    {
        $data = ShopOrder::findOrFail($id);
        $delivery_time = $data->delivery_time;
        $now = Carbon::now();
        $tomorrow = $now->format('Y-m-d');

        if ($delivery_time <= $now){
            $is_edit = 0;
        } else if ($delivery_time == $tomorrow) {
            if ($now->format('H:i') < config('admin.time_not_edit_order')) {
                $is_edit = 1;
            } else {
                $is_edit = 0;
            }
        } else {
            $is_edit = 1;
        }
        $order_details_array = [];
        $orderDetails = ShopOrderDetail::where('order_id', $id)->get();
        foreach ($orderDetails as $orderDetail) {
            $product = ShopProduct::find($orderDetail->product_id);
            $product_unit = AdminUnit::find($product->unit_id ?? '');
            $product_status = $orderDetail->product->status ?? '';
            $data_order_detail = [
                'order_id' => $orderDetail->id,
                'minimum_qty' => $product->minimum_qty_norm ?? 0,
                'product_id' => $orderDetail->product_id,
                'status' => $product_status,
                'disabled' => ($product_status != 1),
                'product_name' => $product->name ?? $orderDetail->product_name,
                'unit' => $product_unit->name ?? $orderDetail->product_unit,
                'unit_type' => $product_unit->type ?? "0",
                'price' => $orderDetail->price,
                'qty' => $orderDetail->qty,
                'total_price' => $orderDetail->total_price,
                'supplier_id' => $orderDetail->supplier_id,
                'comment' => $orderDetail->comment,
                'created_at' => $orderDetail->created_at,
                'updated_at' => $orderDetail->updated_at,
                'removed' => is_null($product)
            ];
            array_push($order_details_array, $data_order_detail);
        }

        $order_histories_array = [];
        $orderHistories = ShopOrderHistory::where('order_id', $id)->orderBy('add_date', 'DESC')->get();
        foreach ($orderHistories as $orderHistory) {
            if ($orderHistory->customer_id == 0) {
                $edit_user = AdminUser::find($orderHistory->admin_id);
            } else {
                $edit_user = ShopCustomer::find($orderHistory->customer_id);
            }
            $data_order_history = [
                'add_date' => Carbon::make($orderHistory->add_date ?? "0000-00-00 00:00")->format('d/m/Y H:i:s'),
                'admin_id' => $orderHistory->admin_id ?? "",
                'content' => $orderHistory->content,
                'customer_id' => $orderHistory->customer_id ?? "",
                'id' => $orderHistory->id,
                'order_id' => $orderHistory->order_id,
                'order_status_id' => $orderHistory->order_status_id,
                'title' => $orderHistory->title,
                'edit_name' => $edit_user->name,
                'detail_before' => $orderHistory->json_detail_before ? json_decode($orderHistory->json_detail_before) : [],
                'detail_after' => $orderHistory->json_detail_after ? json_decode($orderHistory->json_detail_after) : []
            ];
            array_push($order_histories_array, $data_order_history);
        }

        $data['orderDetails'] = $order_details_array;
        $data['orderHistories'] = $order_histories_array;
        $data['is_edit'] = $is_edit;
        return $this->responseSuccess($data);
    }

    /**
     * Check chặn đặt hàng vào cuối tuần và ngày nghĩ lễ
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function validateOrder(Request $request) {
        $data = $request->all();
        $deliveryTime = Carbon::createFromFormat('d-m-Y', $data['delivery_time']);
        $billDate = Carbon::createFromFormat('d-m-Y', $data['bill_date']);

        $flagDeliveryTime = $this->checkBlockOrderOnDateRange($deliveryTime);
        if ($flagDeliveryTime) {
            return $this->responseError([], Response::HTTP_BAD_REQUEST,
                "Ngày giao hàng không hợp lệ, vui lòng chọn lại ngày giao!");
        }

        $flagBillDate = $this->checkBlockOrderOnDateRange($billDate);
        if ($flagBillDate) {
            return $this->responseError([], Response::HTTP_BAD_REQUEST,
                "Ngày trên hóa đơn không hợp lệ, vui lòng chọn lại ngày hóa đơn!");
        }
        // Check T7, CN
        $checkBlockOrderSatAndSun = $this->checkBlockOrderOnWeekend($deliveryTime);
        if ($checkBlockOrderSatAndSun) {
            return $this->responseError([], Response::HTTP_BAD_REQUEST,
                "Từ 11h trưa thứ 7 đến hết ngày CN, không thể tạo mới đơn hàng có ngày giao vào Chủ nhật và Thứ 2. Vui lòng chọn lại ngày giao hàng!");
        }

        // Check kỳ nghĩ lễ
        $nearestHoliday = $this->getNearestHolidayByDeliveryTime($deliveryTime->toDateString());
        if (!empty($nearestHoliday)){
            return $this->responseError([], Response::HTTP_BAD_REQUEST,
                "Ngày giao hàng trùng kỳ nghỉ " . $nearestHoliday->name . ". Vui lòng chọn ngày khác!");
        }

        // Check không được đặt hàng vào ngày đầu tiên kỳ nghỉ lễ
        $holidayWithFirstDay = $this->getHolidayWithFirstDayOfDeliveryTime($deliveryTime->toDateString());
        if (!empty($holidayWithFirstDay)) {
            return $this->responseError([], Response::HTTP_BAD_REQUEST,
                "Không thể chọn ngày giao hàng trùng với ngày làm việc đầu tiên của kỳ nghỉ " . $holidayWithFirstDay->name . ". Vui lòng chọn ngày khác!");
        }

        return $this->responseSuccess(true);
    }

    /**
     * Get authenticated user
     */
    public function createOrder(Request $request)
    {
        try {
            $data = $request->all();
            // Validate
            $deliveryTime = Carbon::createFromFormat('d-m-Y', $data['delivery_time']);
            $billDateUpdate = Carbon::createFromFormat('d-m-Y', $data['bill_date']);
            $now = Carbon::now();

            # check khóa ngày giao hang và ngày trên hóa đơn theo khoản ngày đã cho
            $flagDeliveryTime = $this->checkBlockOrderOnDateRange($deliveryTime);
            if ($flagDeliveryTime) {
                return $this->responseError([], Response::HTTP_BAD_REQUEST,
                    "Ngày giao hàng không hợp lệ, vui lòng chọn lại ngày giao!");
            }

            $flagBillDate = $this->checkBlockOrderOnDateRange($billDateUpdate);
            if ($flagBillDate) {
                return $this->responseError([], Response::HTTP_BAD_REQUEST,
                    "Ngày trên hóa đơn không hợp lệ, vui lòng chọn lại ngày hóa đơn!");
            }

            // Block t7, CN
            $checkBlockOrderSatAndSun = $this->checkBlockOrderOnWeekend($deliveryTime);
            if ($checkBlockOrderSatAndSun) {
                return $this->responseError([], Response::HTTP_BAD_REQUEST,
                    "Từ 11h trưa thứ 7 đến hết ngày CN, không thể tạo mới đơn hàng có ngày giao vào Chủ nhật và Thứ 2. Vui lòng chọn lại ngày giao hàng!");
            }
            // Check kỳ nghĩ lễ
            $nearestHoliday = $this->getNearestHolidayByDeliveryTime($deliveryTime->toDateString());
            if (!empty($nearestHoliday)) {
                return $this->responseError([], Response::HTTP_BAD_REQUEST,
                    "Ngày giao hàng trùng kỳ nghỉ " . $nearestHoliday->name . ". Vui lòng chọn ngày khác!");
            }

            // Check ngày đầu tiên kỳ nghĩ lễ
            $holidayWithFirstDay = $this->getHolidayWithFirstDayOfDeliveryTime($deliveryTime->toDateString());
            if (!empty($holidayWithFirstDay)) {
                return $this->responseError([], Response::HTTP_BAD_REQUEST,
                    "Không thể chọn ngày giao hàng trùng với ngày làm việc đầu tiên của kỳ nghỉ " . $holidayWithFirstDay->name . ". Vui lòng chọn ngày khác!");
            }

            if ($deliveryTime <= $now){
                return $this->responseError([], Response::HTTP_BAD_REQUEST,
                    "Ngày giao hàng phải lớn hơn ngày hiện tại!");
            }

            if ($deliveryTime->isTomorrow()) {
                $timeNotEditOrder = config('admin.time_not_edit_order');
                if ($now->format('H:i') > $timeNotEditOrder) {
                    return $this->responseError([], Response::HTTP_BAD_REQUEST,
                        "Ngày giao hàng không hợp lệ, vui lòng chọn ngày giao hàng khác!");
                }
            }
            
            DB::beginTransaction();
            $bill_date_format = Carbon::createFromFormat('d-m-Y', $data['bill_date']);
            $user = $request->user();
            $customer = ShopCustomer::find($user->id);
            if ($data['explain'] == 'Hàng đợt 2') {
                $customerDriver = AdminDriverCustomer::where('customer_id', $user->id)->where('type_order', 2)->first();
            } else {
                $customerDriver = AdminDriverCustomer::where('customer_id', $user->id)->where('type_order', 1)->first();
            }
            $driver = AdminDriver::where('id', $customerDriver->staff_id ?? '')->first();
            $order = new ShopOrder();
            $order->name = $customer->name;
            $order->customer_code = $customer->customer_code;
            $order->customer_num = $customer->order_num;
            $order->department_id = $customer->department_id;
            $order->customer_short_name = $customer->short_name;
            $order->drive_id = $driver->id ?? '';
            $order->drive_code = $driver->id_name ?? '';
            $order->drive_address = $driver->address ?? '';
            $order->drive_name = $driver->full_name ?? '';
            $order->drive_phone = $driver->phone ?? '';
            $order->phone = isset($user['phone'])?$user['phone']:'';
            $order->customer_id = $user->id;
            $order->email = isset($user['email'])?$user['email']:'';
            $order->address = isset($user['address'])?$user['address']:'';
            $order->comment = isset($data['comment'])?$data['comment']:'';
            $order->object_id = isset($data['object_id'])?$data['object_id']:'';
            $order->explain = isset($data['explain'])?$data['explain']:'';
            $order->id_name =  $order->getNextId();
            $order->status = 1; // Đang khả dụng
            $order->delivery_time = $deliveryTime;
            $order->is_order_admin = 0;
            $order->bill_date = $bill_date_format;
            $total_bill = 0;
            $order->save();

            $is_draff_order = false;
            foreach ($data['data_orders'] as $key => $data_order) {
                // Kiểm tra sản phẩm bị off
                $product = AdminProduct::getProductAdmin($data_order['product_id']);
                $supplier_id = ShopProductSupplier::getSupplierOfProductAndCustomer($data_order['product_id'], $user->id)->supplier_id ?? '';
                $supplier = ShopSupplier::find($supplier_id);
//                $price = ShopImportPriceboard::getImportPriceDetail($supplier_id, $data['bill_date'], $data_order['product_id']);
                if(empty($product)){
                    DB::rollBack();
                    return $this->responseError([], Response::HTTP_BAD_REQUEST,
                        "Sản phẩm không tồn tại, vui lòng kiểm tra lại!");
                }
                if ($product->status != 1){
                    DB::rollBack();
                    return $this->responseError([], Response::HTTP_BAD_REQUEST,
                        "Đặt đơn thất bại vì sản phẩm " . ($product->name ?? "") . " đã hết hàng, vui lòng loại bỏ ra khỏi giỏ hàng và chọn sản phẩm thay thế!");
                }
                $price = $this->getPriceDetailProduct($data_order['product_id'], $user->id, $bill_date_format, $data['object_id']);
                $order_detail = new ShopOrderDetail();
                $order_detail->order_id = $order->id;
                $order_detail->product_id = $data_order['product_id'];
                $order_detail->product_code = $product->sku;
                $order_detail->product_name = $product->name;
                $order_detail->product_short_name = $product->short_name;
                $order_detail->product_num = $product->order_num;
                $order_detail->category_id = $product->category_id;
                $order_detail->product_priority_level = $product->purchase_priority_level;
                $order_detail->product_unit = $product->unit->name;
                $order_detail->supplier_id = $supplier_id;
                $order_detail->supplier_code = $supplier->supplier_code ?? '';
                $order_detail->supplier_name = $supplier->name ?? '';
                $order_detail->price = $price;
//                $order_detail->import_price = $price;
                // Đơn hàng tồn tại sản phẩm không có báo giá, hiển thị trạng thái đơn hàng là ""Đơn nháp""
                if (!$order_detail->price || $order_detail->price <= 0){
                    $is_draff_order = true;
                }

                $order_detail->qty = $data_order['qty'];
                $order_detail->qty_reality = $data_order['qty'];
                $order_detail->total_price = $price * $data_order['qty'];
                $order_detail->reality_total_price = $price * $data_order['qty'];
                $total_bill += $order_detail->total_price;
                $order_detail->comment = isset($data_order['comment'])?$data_order['comment']:'';
                $order_detail->created_at = now()->addSecond($key + 3);
                $order_detail->save();
            }
            $order->subtotal = $total_bill;
            $order->total = $total_bill;
            $order->actual_total_price = $total_bill;
            if ($is_draff_order) {
                $order->status = 2; // Đơn nháp
            }
            $order->update();

            DB::commit();

            $notification = new AdminNotification();
            $notification->title = "Đơn hàng mới";
            $notification->content = "Khách hàng đã đặt đơn hàng số #" . $order->id_name . " với tổng số tiền " . number_format($total_bill, 0, ',', " ");
            $notification->id_order = $order->id;
            $notification->order_code = $order->id_name;
            $notification->customer_code = $customer->customer_code;
            $notification->customer_name = $customer->name;
            $notification->desc = $customer->name . " tạo đơn hàng mới.";
            $notification->order_type = 1;
            $notification->edit_type = 1;
            $notification->display = 0;
            $notification->save();

            $notification_customer = new AdminNotificationCustomer();
            $notification_customer->notification_id = $notification->id;
            $notification_customer->customer_id = $user->id;
            $notification_customer->seen = 0;
            $notification_customer->save();

            $notification_amdin = new AdminNotification();
            $notification_amdin->title = "Đơn hàng mới";
            $notification_amdin->content = 'Bạn có đơn hàng mới từ ' . $customer->name;
            $notification_amdin->id_order = $order->id;
            $notification_amdin->link = '/sc_admin/order';
            $notification_amdin->display = 0;
            $notification_amdin->is_admin = 1;
            $notification_amdin->save();

            $devices = ShopDeviceToken::where('customer_id', $user->id)->get();
            foreach ($devices as $device) {
                AdminNotificationCustomer::sendCloudMessageToAndroid($device->device_token, $notification->content, $notification->title);
            }
            AdminNotificationCustomer::sendNotifyToWeb($user->id, $notification->content, $notification->title);
            AdminNotificationCustomer::sendNotifyToAdmin($notification_amdin->content, $notification_amdin->title);

            return $this->responseSuccess($order);
        } catch (\Throwable $e) {
            Log::error($e);
            DB::rollBack();
            return $this->responseError([], Response::HTTP_BAD_REQUEST, 'Lỗi mạng. Vui lòng thử lại');
        }
    }

    /**
     * Get authenticated user
     */
    public function updateOrder(Request $request, $id)
    {
        try {
            $order = ShopOrder::find($id);
            $deliveryTimeOrder = $order->delivery_time;
            $now = Carbon::now();
            $changes = [];
            $startOfWeek = Carbon::now()->startOfWeek()->addDay(5)->addHour(11);
            $endOfWeek = Carbon::now()->endOfWeek();
            $checkBlockOrderSatAndSun = AdminHoliday::where('type', 'everyday')->first();
            if ($checkBlockOrderSatAndSun->status == 1) {
                if ($now >= $startOfWeek && $now <= $endOfWeek) {
                    if ($deliveryTimeOrder >= $startOfWeek && $deliveryTimeOrder <= $endOfWeek->addDay(1)) {
                        return $this->responseError([], Response::HTTP_BAD_REQUEST,
                            "Từ 11h trưa thứ 7 đến hết ngày CN, không thể chỉnh sửa đơn hàng có ngày giao vào Chủ nhật và Thứ 2!");
                    }
                }
            }

            $checkBlockOrderWithHolidayToday = AdminHoliday::where('start_date', '<=', $now)
                ->where('end_date', '>=', $now)
                ->where('edit_date', $deliveryTimeOrder)
                ->where('status', 1)->first();
            if ($checkBlockOrderWithHolidayToday) {
                return $this->responseError([], Response::HTTP_BAD_REQUEST,
                    "Không thể sửa đơn hàng với ngày giao hàng là ngày đầu tiên kỳ nghỉ " . $checkBlockOrderWithHolidayToday->name);
            }

            // Case đối với ngày lễ 30/4 - 1/5; Ngày 29 đặt đơn hàng cho ngày đầu tiên khi hết kỳ lễ là 2/5. Thì sau 14h30 ngày 29/4 ko dc sửa dơn hàng
            $holidayWithFirstDayF = $this->checkBlockUpdateOrderByFirstDayHoliday($deliveryTimeOrder);
            if (!empty($holidayWithFirstDayF)) {
                return $this->responseError([], Response::HTTP_BAD_REQUEST,
                    "Không thể sửa đơn hàng với ngày giao hàng là ngày đầu tiên kỳ nghỉ  " . $holidayWithFirstDayF->name);
            }

            $data = $request->all();

            // Validate
            $deliveryTime = Carbon::createFromFormat('d-m-Y', $data['delivery_time']);
            $billDateUpdate = Carbon::createFromFormat('d-m-Y', $data['bill_date']);

            # check khóa ngày giao hang và ngày trên hóa đơn theo khoản ngày đã cho
            $flagDeliveryTime = $this->checkBlockOrderOnDateRange($deliveryTime);
            if ($flagDeliveryTime) {
                return $this->responseError([], Response::HTTP_BAD_REQUEST,
                    "Ngày giao hàng không hợp lệ, vui lòng chọn lại ngày giao!");
            }
            $flagBillDate = $this->checkBlockOrderOnDateRange($billDateUpdate);
            if ($flagBillDate) {
                return $this->responseError([], Response::HTTP_BAD_REQUEST,
                    "Ngày trên hóa đơn không hợp lệ, vui lòng chọn lại ngày hóa đơn!");
            }

            $now = Carbon::now();
            $checkBlockOrderWithHoliday = AdminHoliday::where('start_date', '<=', $deliveryTime->toDateString())
                ->where('end_date', '>=', $deliveryTime->toDateString())
                ->where('status', 1)->first();
            if ($checkBlockOrderWithHoliday) {
                return $this->responseError([], Response::HTTP_BAD_REQUEST,
                    "Chọn ngày giao hàng trùng kỳ nghỉ " . $checkBlockOrderWithHoliday->name . ". Vui lòng chọn ngày khác!");
            }


            $checkBlockOrderWithHolidayTodayEdit = AdminHoliday::where('start_date', '<=', $now)
                ->where('end_date', '>=', $now)
                ->where('edit_date', $deliveryTime->toDateString())
                ->where('status', 1)->first();
            if ($checkBlockOrderWithHolidayTodayEdit) {
                return $this->responseError([], Response::HTTP_BAD_REQUEST,
                    "Không thể chọn ngày giao hàng trùng với ngày làm việc đầu tiên của kỳ nghỉ " . $checkBlockOrderWithHolidayToday->name. ". Vui lòng chọn ngày khác!");
            }
            $errNotEdit = "Đơn hàng đã khóa, không thể sửa!";
            if ($deliveryTime <= $now){
                return $this->responseError([], Response::HTTP_BAD_REQUEST,
                    "Ngày giao hàng phải lớn hơn ngày hiện tại!");
            }

            if ($deliveryTime->isTomorrow()) {
                $timeNotEditOrder = config('admin.time_not_edit_order');
                if ($now->format('H:i') > $timeNotEditOrder) {
                    return $this->responseError([], Response::HTTP_BAD_REQUEST,
                        $errNotEdit);
                }
            }

            DB::beginTransaction();
            $user = $request->user();
            $customer_id = $user->id;
            $orderHistoryContent = [];

            // Create histories update order
            if ($data['comment'] != $order->comment) {
                array_push($orderHistoryContent, 'Chỉnh sửa ghi chú');
            }

            // Create histories update explain
            if ($data['explain'] != $order->explain) {
                array_push($orderHistoryContent, 'Sửa diễn giải: ' . $order->explain . ' -> ' . $data['explain']);
            }

            $deliveryTimeBefore = Carbon::make($order->delivery_time ?? '')->format('d-m-Y');
            if ($data['delivery_time'] != $deliveryTimeBefore) {
                array_push($orderHistoryContent, 'Sửa ngày giao hàng: ' . $deliveryTimeBefore . ' -> ' . $data['delivery_time']);
            }

            $billDate = Carbon::createFromFormat('d-m-Y', $data['bill_date']);
            $billDateBefore = Carbon::make($order->bill_date ?? '')->format('d-m-Y');
            if ($data['bill_date'] != $billDateBefore) {
                $orderHistoryContent[] = 'Sửa ngày in hóa đơn: ' . $billDateBefore . ' -> ' . $data['bill_date'];
            }

            if ($data['object_id'] != $order->object_id) {
                $orderHistoryContent[] = 'Sửa đối tượng: ' . $order->object_id . ' -> ' . $data['object_id'];
            }

            $order->comment = isset($data['comment'])?$data['comment']:'';
            $order->explain = $data['explain'];
            $order->object_id = $data['object_id'];
            $order->delivery_time = $deliveryTime;
            $order->bill_date = $billDate;
            $order->update();
            $order_old_details = ShopOrderDetail::where('order_id', $id)->get();
            $arrIdProductDetail = $order_old_details->pluck('product_id')->toArray();

            //Chuẩn bị data add vào lịch sử đơn
            $orderDetailBeforeList = $order_old_details->toArray();
            $orderDetailAfterList = [];


            //INSERT NOW
            $newDetailIds = data_get($data['data_orders'], "*.id"); //Get list id thêm mới
            $newDetailProductIds = data_get($data['data_orders'], "*.product_id"); //Get list product_id thêm mới
            $oldDetailIds = data_get($order_old_details->toArray(), "*.id"); //Get list id thêm mới
            $deleteItems = array_filter($order_old_details->toArray(), function ($item) use ($newDetailIds, $oldDetailIds){
                return !in_array($item['id'], $newDetailIds) && in_array($item['id'], $oldDetailIds);
            });
            $newItems = array_filter($data['data_orders'], function ($item){
                return !isset($item["id"]);
            });
            $editItems = array_filter($data['data_orders'], function ($item) use ($order_old_details){
                if(!isset($item["id"])){
                    return false;
                }
                $originalDetail = $order_old_details->find($item["id"]);
                return $originalDetail && ($originalDetail->qty != $item['qty']);
            });

            //Xoá item cũ
            $delete = ShopOrderDetail::whereIn('id', data_get($deleteItems, '*.id'))->delete();
            foreach (data_get($deleteItems, '*.id') as $deleteItem) {
                $detailItem = $order_old_details->find($deleteItem)->toArray();
                $product = AdminProduct::getProductAdmin( $deleteItem ? $detailItem['product_id'] : '' );
                if(!in_array($product->id, $newDetailProductIds)) {
                    $content = "Xóa sản phẩm";
                    $dataChange = [
                        'order_id' => $id,
                        'order_name' => $order->id_name,
                        'delivery_date_origin' => $order->delivery_time,
                        'product_id' => $detailItem['product_id'],
                        'product_name' => $product->name,
                        'product_short_name' => $product->short_name,
                        'product_num' => $product->order_num,
                        'product_unit' => $product->unit->name,
                        'qty' => $detailItem['qty_reality'],
                        'qty_change' => $detailItem['qty_reality'],
                        'content' => $content,
                        'type_content' => 11,
                        'customer_code' => $order->customer_code,
                        'customer_name' => $order->customer_name,
                        'create_order' => date('d-m-Y H:i:s', strtotime($order->created_at)),
                        'order_detail_id' => $detailItem['id'],
                        'category_id' => $product->category_id,
                        'product_code' => $product->sku,
                        'note' => $detailItem['comment'],
                        'status' => $order->status,
                        'type_order' => 1,
                    ];
                    (new AdminShopOrderChangeExtra())->create($dataChange);
                }
                $changeText = "Xoá sản phẩm $product->name";
                $orderDetailAfterList[] = [
                    "user" => $order->name,
                    "time" => formatDateVn(now(), 1),
                    "comment" => $changeText
                ];
                $changes[] = $changeText;
            }

            //Chuẩn bị data update

            //Khoi tao flag check don nhap
            $is_draff_order = false;
            foreach ($newItems as $key => $item){
                //Chuẩn bị data
                $product = AdminProduct::getProductAdmin($item['product_id']);
                $supplier_id = ShopProductSupplier::getSupplierOfProductAndCustomer($item['product_id'], $customer_id)->supplier_id ?? '';
                $supplier = ShopSupplier::find($supplier_id);
                //Tạo data item
                $order_detail = new ShopOrderDetail();
                $order_detail->order_id = $order->id;
                $order_detail->product_id = $item['product_id'];
                $order_detail->product_code = $product->sku;
                $order_detail->product_name = $product->name;
                $order_detail->product_short_name = $product->short_name;
                $order_detail->product_num = $product->order_num;
                $order_detail->category_id = $product->category_id;
                $order_detail->product_priority_level = $product->purchase_priority_level;
                $order_detail->product_unit = $product->unit->name;
                $order_detail->supplier_id = $supplier_id;
                $order_detail->supplier_code = $supplier->supplier_code ?? '';
                $order_detail->supplier_name = $supplier->name ?? '';
                $order_detail->price = $item['price'];
                $order_detail->qty = $item['qty'];
                $order_detail->qty_reality = $item['qty'];
                $order_detail->total_price = $item['price'] * $item['qty'];
                $order_detail->reality_total_price = $item['price'] * $item['qty'];
                $order_detail->comment = $data_order['comment'] ?? "";
                $order_detail->created_at = now()->addSecond($key + 3);
                $order_detail->save();
                $preparedInsert[] = $order_detail;

                // Đơn hàng tồn tại sản phẩm không có báo giá, hiển thị trạng thái đơn hàng là ""Đơn nháp""
                if (!$order_detail->price || $order_detail->price <= 0){
                    $is_draff_order = true;
                }
                //Change
                if (!in_array($item['product_id'], $arrIdProductDetail)) {
                    $qty_change = $item['qty'];
                    $content = "Thêm mới sản phẩm";
                    $dataChange = [
                        'order_id' => $id,
                        'delivery_date_origin' => $order->delivery_time,
                        'order_name' => $order->id_name,
                        'product_id' => $item['product_id'],
                        'product_name' => $product->name,
                        'product_short_name' => $product->short_name,
                        'product_num' => $product->order_num,
                        'product_unit' => $product->unit->name,
                        'qty' => $qty_change,
                        'qty_change' => $qty_change,
                        'content' => $content,
                        'type_content' => 8,
                        'customer_code' => $order->customer_code,
                        'customer_name' => $order->customer_name,
                        'create_order' => date('d-m-Y H:i:s', strtotime($order->created_at)),
                        'order_detail_id' => $order_detail->id,
                        'category_id' => $product->category_id,
                        'product_code' => $product->sku,
                        'note' => $item['comment'],
                        'status' => $order->status,
                        'type_order' => 1,
                    ];
                    (new AdminShopOrderChangeExtra())->create($dataChange);
                }

                //History
                $qty = $item['qty'];
                $changeText = "Thêm sản phẩm $product->name: số lượng $qty";
                $orderDetailAfterList[] = [
                    "user" => $order->name,
                    "time" => formatDateVn(now(), 1),
                    "comment" => $changeText
                ];
                $changes[] = $changeText;
            }

            foreach ($editItems as $key => $item) {
                $order_detail = $order_old_details->find($item['id'] ?? '');
                $originalQty = $order_detail ? $order_detail->qty : 0;
                //Chuẩn bị data
                $product = AdminProduct::getProductAdmin($item['product_id']);
                $supplier_id = ShopProductSupplier::getSupplierOfProductAndCustomer($item['product_id'], $customer_id)->supplier_id ?? '';
                $supplier = ShopSupplier::find($supplier_id);
                //Tạo data edit item
                $order_detail->price = $item['price'];
                $order_detail->product_id = $product->id;
                $order_detail->product_code = $product->sku;
                $order_detail->product_name = $product->name;
                $order_detail->product_short_name = $product->short_name;
                $order_detail->product_num = $product->order_num;
                $order_detail->category_id = $product->category_id;
                $order_detail->product_priority_level = $product->purchase_priority_level;
                $order_detail->product_unit = $product->unit->name;
                $order_detail->supplier_id = $supplier_id;
                $order_detail->supplier_code = $supplier->supplier_code ?? '';
                $order_detail->supplier_name = $supplier->name ?? '';
                $order_detail->qty = $item['qty'];
                $order_detail->total_price = $item['price'] * $item['qty'];
                $order_detail->comment = $data_order['comment'] ?? "";
                $order_detail->updated_at = now()->addSecond($key + 3);
                if ($item['qty'] != $originalQty) {
                    $order_detail->qty_reality = $item['qty'];
                    $order_detail->reality_total_price = $item['price'] * $item['qty'];
                }
                $order_detail->save();

                // Đơn hàng tồn tại sản phẩm không có báo giá, hiển thị trạng thái đơn hàng là ""Đơn nháp""
                if (!$order_detail->price || $order_detail->price <= 0){
                    $is_draff_order = true;
                }

                $qty_change = $originalQty -  $item['qty'];
                $content = "Chỉnh sửa số lượng";
                $dataChange = [
                    'order_id' => $id,
                    'delivery_date_origin' => $order->delivery_time,
                    'order_name' => $order->id_name,
                    'product_id' => $item['product_id'],
                    'product_name' => $product->name,
                    'product_short_name' => $product->short_name,
                    'product_num' => $product->order_num,
                    'product_unit' => $product->unit->name,
                    'qty' => $item['qty'],
                    'qty_change' => $qty_change,
                    'content' => $content,
                    'type_content' => 2,
                    'customer_code' => $order->customer_code,
                    'customer_name' => $order->customer_name,
                    'create_order' => date('d-m-Y H:i:s', strtotime($order->created_at)),
                    'order_detail_id' => $order_detail->id,
                    'category_id' => $product->category_id,
                    'product_code' => $product->sku,
                    'note' => $item['comment'],
                    'status' => $order->status,
                    'type_order' => 1,
                ];
                (new AdminShopOrderChangeExtra())->create($dataChange);
                //Change info
                $qty = $item["qty"];
                $changeText = "Chỉnh sửa sản phẩm $product->name: Số lượng thay đổi $originalQty -> $qty";
                $orderDetailAfterList[] = [
                    "user" => $order->name,
                    "time" => formatDateVn(now(), 1),
                    "comment" => $changeText
                ];
                $changes[] = $changeText;
            }
            $itemDetailFinal = ShopOrderDetail::where('order_id', $id)->get();
            $totalPriceReality = 0;
            $total_bill = 0;
            foreach ($itemDetailFinal as $item){
                $price = $this->getPriceDetailProduct($item->product_id, $customer_id, $billDate, $data['object_id']);
                $item->price = $price;
                $item->total_price = $price*$item->qty;
                $item->reality_total_price = $price * $item->qty_reality;
                $totalPriceReality += $item->price * $item->qty_reality;
                $total_bill += $price * $item->qty;
                foreach ($data['data_orders'] as $value) {
                    if ($value['product_id'] == $item->product_id) {
                        $item->comment = $value['comment'];
                        break;
                    }
                }
                $item->save();
            }

            //Prepare
            $jsonDetailAfter = json_encode($orderDetailAfterList);
            $jsonDetailBefore = json_encode($orderDetailBeforeList);
            if((count($editItems) > 0) || (count($newItems) > 0) || (count($deleteItems) > 0)) $orderHistoryContent[] = 'Sửa chi tiết đơn hàng';

            if (count($orderHistoryContent) > 0) {
                $orderHistoryContent = implode("<br/>", $changes);
            } else {
                $orderHistoryContent = '';
            }
            // Lưu lịch sử sửa đơn
            $orderHistory = new ShopOrderHistory();
            $orderHistory->order_id = $id;
            $orderHistory->order_code = $order->id_name;
            $orderHistory->user_name = $order->name;
            $orderHistory->user_code = $order->customer_code;
            $orderHistory->is_admin = 2;
            $orderHistory->title = 'Khách hàng sửa đơn';
            $orderHistory->content = $orderHistoryContent;
            $orderHistory->customer_id = $user->id;
            $orderHistory->order_status_id = $order->status;
            $orderHistory->admin_id = 1;
            $orderHistory->json_detail_before = $jsonDetailBefore;
            $orderHistory->json_detail_after = $jsonDetailAfter;
            $orderHistory->save();
            //Tính giá đơn hàng
            $order->subtotal = $total_bill;
            $order->total = $total_bill;
            $order->actual_total_price = $totalPriceReality;
            if ($is_draff_order){
                $order->status = 2; // Đơn nháp
            } else{
                $order->status = 1; // Đang khả dụng
            }
            $order->edited = 1;
            $order->update();

            $customer = ShopCustomer::find($user->id);

            $notification = new AdminNotification();
            $notification->title = "Sửa đơn hàng";
            $notification->content = 'khách hàng chỉnh sửa đơn hàng #' . $order->id_name;
            $notification->id_order = $order->id;
            $notification->order_code = $order->id_name;
            $notification->customer_code = $customer->customer_code;
            $notification->customer_name = $customer->name;
            $notification->desc = $customer->name. " sửa đơn hàng.";
            $notification->order_type = 1;
            $notification->edit_type = 2;
            $notification->display = 0;
            $notification->save();

            $notification_amdin = new AdminNotification();
            $notification_amdin->title = "Sửa đơn hàng";
            $notification_amdin->content = 'Đơn hàng ' . $order->id_name . ' của ' . $customer->name .  ' đã được chỉnh sửa';
            $notification_amdin->id_order = $order->id;
            $notification_amdin->display = 0;
            $notification_amdin->link = '/sc_admin/order';
            $notification_amdin->is_admin = 1;
            $notification_amdin->save();
            AdminNotificationCustomer::sendNotifyToAdmin($notification_amdin->content, $notification_amdin->title);

            DB::commit();
            return $this->responseSuccess($order);
        } catch (\Throwable $e) {
            Log::error($e);
            DB::rollBack();
            return $this->responseError([], Response::HTTP_BAD_REQUEST, 'Lỗi mạng. Vui lòng thử lại');
        }
    }

    /**
     * @param $order_id
     */
    public function getTotalRealityPrice($order_id) {
        $totalPrice = 0;
        $details = ShopOrderDetail::where('order_id', $order_id)->get();
        foreach ($details as $item) {
            $totalPrice += $item->price * $item->qty_reality;
        }

        return $totalPrice;
    }

    public function getOrderHistories(Request $request)
    {
        try {
            $from = date($request->date_from);
            $to = date($request->date_to);
            $user = $request->user();
            $orders = ShopOrder::where('customer_id', $user->id)->where('status', '!=', 7);

            if ($request->date_from){
                $orders = $orders->where('delivery_time', '>=', $from);
            }

            if ($request->date_to){
                $orders = $orders->where('delivery_time', '<=', $to);
            }

            if ($request->explain && $request->explain != ''){
                $orders = $orders->where('explain', '=', $request->explain);
            }
            if ($request->object_id && $request->object_id != ''){
                $orders = $orders->where('object_id', '=', $request->object_id);
            }
            $orders = $orders->whereDate('delivery_time', '>=', '2023-3-1');
            $orders = $orders->orderBy('delivery_time', 'DESC')->orderBy('created_at', 'DESC')->get();
            $data = [];
            if ($orders) {
                foreach ($orders->groupBy('delivery_time') as $key => $listOrder) {
                    $isEditable = $this->checkOrderIsEditable($key);
                    foreach ($listOrder as $order) {
                        $order['editable'] = $isEditable;
                    }

                    $data[] = (object)[
                        'day' => $key,
                        'total' => $listOrder->sum('total'),
                        'actual_total_price' => $listOrder->sum('actual_total_price'),
                        'details' => $listOrder
                    ];
                }
            }
            return $this->responseSuccess($data);
        } catch (\Throwable $e) {
            Log::error($e);
            return $this->responseError([], Response::HTTP_BAD_REQUEST, 'Lỗi mạng. Vui lòng thử lại!');
        }
    }

    /**
     * Lấy tổng giá tiền theo tháng hiện tại.
     * @param Request $request
     */
    public function getTotalAmountByCurrentMonth(Request $request)
    {
        try {
            $data = [];
            $from = date($request->date_from);
            $to = date($request->date_to);
            $user = $request->user();
            $now = Carbon::now();

            $orders = ShopOrder::where('customer_id', $user->id)->where('status', '!=', 7);

            if ($request->date_from){
                $orders = $orders->where('delivery_time', '>=', $from);
            }

            if ($request->date_to){
                $orders = $orders->where('delivery_time', '<=', $to);
            }

            if ($request->explain && $request->explain != ''){
                $orders = $orders->where('explain', '=', $request->explain);
            }

            if ($request->object_id && $request->object_id != ''){
                $orders = $orders->where('object_id', '=', $request->object_id);
            }

            if ($request->date_from == '' && $request->date_to == '') {
                $orders = $orders->whereMonth('delivery_time', '=', $now->month);
            }

            $orders = $orders->whereDate('delivery_time', '>=', '2023-3-1');
            $orders = $orders->orderBy('delivery_time', 'desc');
            $orders = $orders->get();
            array_push($data, (object)[
                'start_date_of_month' => $orders->last()->delivery_time ?? '',
                'end_date_of_month' => $orders->first()->delivery_time ?? '',
                'total' => $orders->sum('actual_total_price') ?? 0,
                'month' => $now->month,
            ]);

            return $this->responseSuccess($data);
        } catch (\Throwable $e) {
            Log::error($e);
            return $this->responseError([], Response::HTTP_BAD_REQUEST, 'Lỗi mạng. Vui lòng thử lại!');
        }
    }

    public function cancelOrder(Request $request, $id)
    {

        try {
            $user = $request->user();
            $customer = ShopCustomer::find($user->id);
            $now = Carbon::now();
            $order = ShopOrder::findOrFail($id);
            $dataOrderDetails = ShopOrderDetail::where('order_id', $id)->get()->toArray();
            $deliveryTime = $order->delivery_time;

            // Check ngày đầu tiên kỳ nghĩ lễ
            $holidayWithFirstDay = $this->getHolidayWithFirstDayOfDeliveryTime($deliveryTime);
            if (!empty($holidayWithFirstDay)) {
                return $this->responseError([], Response::HTTP_BAD_REQUEST,
                    "Không thể hủy đơn hàng vào ngày đầu tiên kỳ nghỉ " . $holidayWithFirstDay->name);
            }

            $checkBlockOrderSatAndSun = $this->checkBlockOrderOnWeekend($deliveryTime);
            if ($checkBlockOrderSatAndSun){
                return $this->responseError([], Response::HTTP_BAD_REQUEST,
                    "Từ 11h trưa thứ 7 đến hết ngày CN, không thể hủy đơn hàng có ngày giao vào Chủ nhật và Thứ 2!");
            }

            if ($deliveryTime){
                $deliveryTime = Carbon::parse($deliveryTime);
                $errNotCancel = "Đơn hàng đã khóa, không thể hủy!";
                if ($deliveryTime <= $now){
                    return $this->responseError([], Response::HTTP_BAD_REQUEST,
                        $errNotCancel);
                }

                if ($deliveryTime->isTomorrow()) {
                    $timeNotEditOrder = config('admin.time_not_edit_order');
                    if ($now->format('H:i') > $timeNotEditOrder) {
                        return $this->responseError([], Response::HTTP_BAD_REQUEST,
                            $errNotCancel);
                    }
                }
            }

            DB::beginTransaction();
            $order->status = 7;
            $order->update();
            foreach ($dataOrderDetails as $detail ) {
                $dataChange = [
                    'order_id' => $id,
                    'delivery_date_origin' => $order->delivery_time,
                    'order_name' => $order->id_name,
                    'product_id' => $detail['product_id'],
                    'product_name' => $detail['product_name'],
                    'product_short_name' => $detail['product_short_name'],
                    'product_num' => $detail['product_num'],
                    'product_unit' => $detail['product_unit'],
                    'qty' => $detail['qty_reality'],
                    'qty_change' => $detail['qty_reality'],
                    'content' => "Đơn hàng hủy",
                    'type_content' => 6,
                    'customer_code' => $order->customer_code,
                    'customer_name' => $order->customer_name,
                    'create_order' => date('d-m-Y H:i:s', strtotime($order->created_at)),
                    'order_detail_id' => $detail['id'],
                    'category_id' => $detail['category_id'],
                    'product_code' => $detail['product_code'],
                    'note' => $detail['comment'],
                    'status' => $order->status,
                    'type_order' => 1,
                ];
                (new AdminShopOrderChangeExtra())->create($dataChange);
            }

            $notification = new AdminNotification();
            $notification->title = "Hủy đơn hàng";
            $notification->content = "khách hàng đã hủy đơn hàng số #" .$order->id_name;
            $notification->id_order = $order->id;
            $notification->order_code = $order->id_name;
            $notification->customer_code = $customer->customer_code;
            $notification->customer_name = $customer->name;
            $notification->desc = $customer->name. " hủy đơn hàng.";
            $notification->order_type = 1;
            $notification->edit_type = 4;
            $notification->display = 0;
            $notification->save();

            $notification_customer = new AdminNotificationCustomer();
            $notification_customer->notification_id = $notification->id;
            $notification_customer->customer_id = $user->id;
            $notification_customer->seen = 0;
            $notification_customer->save();

            $notification_amdin = new AdminNotification();
            $notification_amdin->title = "Hủy đơn hàng";
            $notification_amdin->content = 'Đơn hàng ' . $order->id_name . ' đã bị hủy từ ' . $customer->name . '';
            $notification_amdin->id_order = $order->id;
            $notification_amdin->seen = 0;
            $notification_amdin->link = '/sc_admin/order';
            $notification_amdin->is_admin = 1;
            $notification_amdin->save();

            $orderHistory = new ShopOrderHistory();
            $orderHistory->order_id = $order->id;
            $orderHistory->order_code = $order->id_name;
            $orderHistory->user_name = $order->name;
            $orderHistory->user_code = $order->customer_code;
            $orderHistory->is_admin = 2;
            $orderHistory->title = 'Hủy đơn hàng';
            $orderHistory->content = "Khách hàng hủy đơn";
            $orderHistory->customer_id = $order->customer_id;
            $orderHistory->order_status_id = $order->status;
            $orderHistory->admin_id = 1;
            $orderHistory->save();

            $devices = ShopDeviceToken::where('customer_id', $user->id)->get();
            foreach ($devices as $device) {
                AdminNotificationCustomer::sendCloudMessageToAndroid($device->device_token, $notification->content, $notification->title);
            }
            AdminNotificationCustomer::sendNotifyToWeb($user->id, $notification->content, $notification->title);

            DB::commit();
            return $this->responseSuccess([], Response::HTTP_OK, 'Hủy đơn hàng thành công');
        } catch (\Throwable $e) {
            DB::rollBack();
            return $this->responseError([], Response::HTTP_BAD_REQUEST, $e->getMessage());
        }
    }

    public function downloadPdf($id)
    {
        $orderData = AdminOrder::with('details', 'customer', 'customer.department', 'details.product', 'details.product.unit')->find($id);

        if (!$orderData) {
            return $this->responseError([], Response::HTTP_BAD_REQUEST, 'Hóa đơn không tồn tại hoặc đã bị xóa');
        }

        try{
            $pdfData = array($orderData);
            $html = view($this->templatePathAdmin . 'screen.davicorp_order.print_pdf.order_detail_template')
                ->with(['data' => $pdfData])->render();
            $html = mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8');
            $domPdf = new Dompdf();
            $domPdf->getOptions()->setChroot(public_path());
            $domPdf->loadHtml($html, 'UTF-8');
            $domPdf->setPaper('A5', 'portrait');
            $domPdf->render();
            return $domPdf->output();
        } catch (\Throwable $e){
            Log::warning($e->getMessage());
            return $this->responseError([], Response::HTTP_BAD_REQUEST, $e->getMessage());
        }

    }

    public function downloadExcel($id)
    {
        $orderData = AdminOrder::with('details','departments', 'customer', 'customer.department', 'details.product', 'details.product.unit')
            ->orderBy('name')->orderBy('bill_date', 'DESC')->findMany((array)$id);

        if (!$orderData) {
            return $this->responseError([], Response::HTTP_BAD_REQUEST, 'Hóa đơn không tồn tại hoặc đã bị xóa');
        }
        try{
            return Excel::download(new AdminExportMultipleSheet($orderData, 'print'), 'Đơn hàng - ' . Carbon::now() . '.xlsx');
        } catch (\Throwable $e){
            Log::warning($e->getMessage());
            return $this->responseError([], Response::HTTP_BAD_REQUEST, $e->getMessage());
        }

    }

    /**
     * Xuất hóa đơn điện tử cho web
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function downloadExcelEInvoice(Request $request)
    {
        $dataSearch = [
            'from_to' => sc_clean(request('from_to_time') ?? ''),
            'end_to' => sc_clean(request('end_to_time') ?? ''),
        ];
        $id = $request->id ?? '';
        $user = $request->user();
        $objOrder = new AdminOrder();
        $data = $objOrder->leftjoin(SC_DB_PREFIX . "shop_order_detail as sod", function($join){
            $join->on(SC_DB_PREFIX . "shop_order.id", "sod.order_id");
        });
        $data = $data->where('customer_id', $user->id)->where('status', '!=', 7);
        if ($id) {
            $objOrder = new AdminOrder();
            $data = $objOrder->leftjoin(SC_DB_PREFIX . "shop_order_detail as sod", function($join){
                $join->on(SC_DB_PREFIX . "shop_order.id", "sod.order_id");
            });
            $data = $data->where(SC_DB_PREFIX . "shop_order.id", $id);
        } else {
            $from = date($request->date_from);
            $to = date($request->date_to);

            if ($request->date_from){
                $data = $data->where('delivery_time', '>=', $from);
            }

            if ($request->date_to){
                $data = $data->where('delivery_time', '<=', $to);
            }

            if ($request->explain && $request->explain != ''){
                $data = $data->where('explain', '=', $request->explain);
            }

            if ($request->object_id && $request->object_id != ''){
                $data = $data->where('object_id', '=', $request->object_id);
            }

            $data = $data->whereDate('delivery_time', '>=', '2023-3-1');
        }
        $data = $data->select(SC_DB_PREFIX . "shop_order.delivery_time",
                SC_DB_PREFIX . "shop_order.id_name",
                SC_DB_PREFIX . "shop_order.id",
                SC_DB_PREFIX . "shop_order.explain" ,
                SC_DB_PREFIX . "shop_order.customer_id",
                SC_DB_PREFIX . "shop_order.name",
                SC_DB_PREFIX . "shop_order.address",
                "sod.qty_reality as qty",
                "sod.product_unit as unit_name", "sod.product_name", "sod.price", "sod.total_price", "sod.product_id", "sod.product_code as sku" )
            ->orderBy(SC_DB_PREFIX . "shop_order.delivery_time", 'ASC')
            ->orderBy(SC_DB_PREFIX . "shop_order.id_name", 'ASC')
            ->orderBy("sod.created_at", 'ASC')
            ->get();
        if (count($data->groupBy('id_name')) > 100) {
            Log::error('Số lượng đơn hàng quá tải. Vui lòng chọn lại bộ lọc!');
            return $this->responseError([], Response::HTTP_BAD_REQUEST, 'Số lượng đơn hàng quá tải. Vui lòng chọn lại bộ lọc!');
        }
        if (count($data) <= 0) {
            return $this->responseError([], Response::HTTP_BAD_REQUEST, 'Hóa đơn không tồn tại hoặc đã bị xóa');
        }
        try{


//            return Excel::raw(new AdminMultipleSheetSalesInvoiceListRealOrder($dataSearch, $data->groupBy('customer_id')), 'BẢNG KÊ HÓA ĐƠN BÁN HÀNG - BÊN THỰC ' . \Illuminate\Support\Carbon::now() . '.xlsx');
            $file = Excel::raw(new AdminMultipleSheetSalesInvoiceListRealOrder($dataSearch, $data->groupBy('customer_id')), \Maatwebsite\Excel\Excel::XLSX);
            $response =  array(
                'name' => 'BẢNG KÊ HÓA ĐƠN BÁN HÀNG - BÊN THỰC ' . \Illuminate\Support\Carbon::now() . '.xlsx',
                'file' => "data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64,".base64_encode($file) //mime type of used format
            );
            return $this->responseSuccess(['file' => $response]);

        } catch (\Throwable $e){
            Log::warning($e->getMessage());
            return $this->responseError([], Response::HTTP_BAD_REQUEST, $e->getMessage());
        }
    }


    /**
     * Khóa đơn hàng cuối tuần.
     * @param $deliveryTime
     * @return bool
     */
    public function checkBlockOrderOnWeekend($deliveryTime)
    {
        $now = Carbon::now();
        $startOfWeek = Carbon::now()->startOfWeek()->addDay(5)->addHour(11);
        $endOfWeek = Carbon::now()->endOfWeek();
        $checkBlockOrderSatAndSun = AdminHoliday::where('type', 'everyday')->first();
        if ($checkBlockOrderSatAndSun->status == 1) {
            if ($now >= $startOfWeek && $now <= $endOfWeek) {
                if ($deliveryTime >= $startOfWeek && $deliveryTime <= $endOfWeek->addDay(1)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Check khóa đặt hàng theo ngày giao hàng hoặc ngày trên hóa đơn
     * @param $date
     * @return bool
     */
    public function checkBlockOrderOnDateRange($date)
    {
        $holiday = AdminHoliday::where('type', 'date_range')
            ->whereDate('start_date', '<=', $date)
            ->whereDate('end_date', '>=', $date)
            ->where('status', 1)->first();

        if ($holiday) {
            return true;
        }

        return false;
    }

    /**
     * Kỳ nghĩ lễ 30/4 - 1/5 -> Ngày 29 đặt hàng cho ngày đầu tiên kết thúc kỳ nghỉ 2/5. Và sau 16h30 ngày 29 phải khóa sửa đơn hàng.
     * @param $date
     * @return bool
     */
    public function checkBlockUpdateOrderByFirstDayHoliday($deliveryTime)
    {
        $now = Carbon::now();
        $tomorrow = $now->tomorrow();
        $holiday = AdminHoliday::where('type', 'holiday')
            ->whereDate('start_date', $tomorrow)
            ->whereDate('edit_date', $deliveryTime)
            ->where('status', 1)->first();
        if ($holiday) {
            $currentTime = $now->format('H:i');
            if ($currentTime > config('admin.time_not_edit_order')) {
                return $holiday;
            }
        }
    }

    /**
     * Khóa đơn hàng vào ngày nghỉ lễ.
     * @param $deliveryTime
     * @return bool
     */
    public function getNearestHolidayByDeliveryTime($deliveryTime)
    {
        $holiday = AdminHoliday::where('start_date', '<=', $deliveryTime)
            ->where('type', 'holiday')
            ->where('end_date', '>=', $deliveryTime)
            ->where('status', 1)->first();

        return $holiday;
    }

    /**
     * Khóa đơn hàng vào ngày giao hàng đầu tiên kết thúc kỳ nghỉ lễ.
     * @param $deliveryTime
     * @return bool
     */
    public function getHolidayWithFirstDayOfDeliveryTime($deliveryTime)
    {
        $now = Carbon::now()->toDateString();
        $holiday = AdminHoliday::where('start_date', '<=', $now)
            ->where('type', 'holiday')
            ->where('end_date', '>=', $now)
            ->where('edit_date', $deliveryTime)
            ->where('status', 1)->first();

        return $holiday;
    }

    /**
     * Check edit button
     * @param $deliveryTime
     * @return bool
     */
    public function checkOrderIsEditable($deliveryTime) {
        // Check theo cuối tuần
        $theWeekend = $this->checkBlockOrderOnWeekend($deliveryTime);
        if ($theWeekend) {
            return false;
        }

        // Check kỳ nghĩ lễ
        $nearestHoliday = $this->getNearestHolidayByDeliveryTime($deliveryTime);
        if (!empty($nearestHoliday)){
            return false;
        }

        // Check theo ngày đầu tiên kỳ nghĩ lễ
        $holidayWithFirstDay = $this->getHolidayWithFirstDayOfDeliveryTime($deliveryTime);
        if (!empty($holidayWithFirstDay)) {
            return false;
        }

        // Check theo ngày đầu tiên kỳ nghĩ lễ
        $holidayWithFirstDayK = $this->checkBlockUpdateOrderByFirstDayHoliday($deliveryTime);
        if (!empty($holidayWithFirstDayK)) {
            return false;
        }

        $now = Carbon::now();

        if ($deliveryTime <= $now->toDateString()) {
            return false;
        }

        $currentTime = $now->format('H:i');
        if ($deliveryTime == $now->tomorrow()->toDateString() && $currentTime > config('admin.time_not_edit_order')){
            return false;
        }

        return true;
    }


    /**
     * Lấy giá ở backend
     * @param $product_id
     * @param $customer_id
     * @param $bill_date
     * @param $object_id
     * @return int
     */
    public function getPriceDetailProduct($product_id, $customer_id, $bill_date, $object_id) {
        $shopUserPriceBoard = ShopUserPriceboard::with('customers')
            ->whereHas('customers', function (Builder $query) use ($customer_id) {
                $query->where('customer_id', $customer_id);
            })
            ->whereDate('start_date', '<=', $bill_date)
            ->whereDate('due_date', '>=', $bill_date)
            ->orderBy('due_date', 'DESC')
            ->first();
        $products = AdminProductPriceDetail::where('product_id', $product_id)->where('product_price_id', $shopUserPriceBoard->product_price_id ?? '')->first();

        return $object_id == 1 ? ($products->price_1 ?? 0) : ($products->price_2 ?? 0);
    }
}