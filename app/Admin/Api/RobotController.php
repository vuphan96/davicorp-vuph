<?php


namespace App\Admin\Api;


use App\Admin\Controllers\AdminReportController;
use App\Admin\Models\AdminDavicookOrder;
use App\Admin\Models\AdminEInvoice;
use App\Front\Models\ShopCustomer;
use App\Front\Models\ShopDavicookOrder;
use App\Front\Models\ShopDavicookOrderDetail;
use App\Front\Models\ShopEInvoice;
use App\Front\Models\ShopEInvoiceDetail;
use App\Front\Models\ShopEInvoiceHistory;
use App\Front\Models\ShopOrder;
use App\Front\Models\ShopOrderDetail;
use Carbon\Carbon;
use DateInterval;
use DatePeriod;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RobotController extends ApiController
{
    public function getListEInv(Request $request)
    {
        $data = $request->all();
        if (!isset($data['from_date']) || !isset($data['to_date'])) {
            return $this->responseError([], Response::HTTP_BAD_REQUEST,
                "Invalid parameters!");
        }

        $fromDateSt = $data['from_date'];
        $fromDate = Carbon::createFromFormat('Y-m-d H:i:s', $fromDateSt);
        $toDateSt = $data['to_date'];
        $toDate = Carbon::createFromFormat('Y-m-d H:i:s', $toDateSt);
        $listInv = ShopEInvoice::where("process_status", "1")->whereBetween("plan_start_date", [$fromDate, $toDate])->get();
        if (!is_null($listInv)) {
            foreach ($listInv as $inv) {
                $department_id = ShopCustomer::where('customer_code', $inv->customer_code)->first()->department_id ?? '';
                $inv['department_id'] = $department_id;
                $inv['detail'] = ShopEInvoiceDetail::where('einv_id', $inv->id)->groupBy('product_code', 'price')
                    ->select(
                        'id',
                        DB::raw('sum(qty) as qty'),
                        DB::raw('sum(tax_amount) as tax_amount'),
                        'einv_id',
                        'product_code',
                        'product_name',
                        'unit',
                        'price',
                        'pretax_price',
                        'tax_no',
                        'created_at',
                        'updated_at',
                    )
                    ->get();
            }
        }

        return $this->responseSuccess($listInv);
    }

    public function getEInvDetail(Request $request)
    {
        $data = $request->all();
        if (!isset($data['id_name'])) {
            return $this->responseError([], Response::HTTP_BAD_REQUEST,
                "Invalid parameters!");
        }

        $idName = $data['id_name'];
        $einv = ShopEInvoice::firstWhere('id_name', $idName);

        return $this->responseSuccess($einv);
    }

    public function updateStatusEInv(Request $request)
    {
        $data = $request->all();
        if (!isset($data['id_name'])) {
            return $this->responseError([], Response::HTTP_BAD_REQUEST,
                "Invalid parameters!");
        }

        $idName = $data['id_name'];

        try {
            DB::beginTransaction();
            $einv = ShopEInvoice::firstWhere('id_name', $idName);
            if (is_null($einv)) {
                return $this->responseError([], Response::HTTP_BAD_REQUEST,
                    "Invoice not exist!");
            }

            $status = $data['status'];
            $einv->process_status = $status;

            if (isset($data['einv_id'])) {
                $einv->einv_id = $data['einv_id'];
                $einv->sign_status = 4;
            }
            if (isset($data['end_date'])) {
                $endDateSt = $data['end_date'];
                $einv->invoice_date = Carbon::createFromFormat('Y-m-d H:i:s', $endDateSt);
            }
            $einv->update();

            if ($status == 3 || $status == 4) {
                $history = new ShopEInvoiceHistory();
                $history->einv_id = $einv->id;
                if (isset($data['start_date'])) {
                    $startDateSt = $data['start_date'];
                    $history->start_date = Carbon::createFromFormat('Y-m-d H:i:s', $startDateSt);
                }

                if (isset($data['end_date'])) {
                    $endDateSt = $data['end_date'];
                    $history->end_date = Carbon::createFromFormat('Y-m-d H:i:s', $endDateSt);
                }

                $errMsg = 'Tạo hóa đơn thành công!';
                if (isset($data['error']) && $status == 3) {
                    $errMsg = isset($data['error']) ? $data['error'] : 'Tạo hóa đơn thất bại!';
                }
                $history->error = $errMsg;
                $history->status = ($status == 3) ? 0 : 1;
                $history->save();
            }

            DB::commit();
            return $this->responseSuccess('ok');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->responseError([], Response::HTTP_BAD_REQUEST, $e->getMessage());
        }
    }

    public function updateSignStatusEInv(Request $request)
    {
        $data = $request->all();
        if (!isset($data['id_name'])) {
            return $this->responseError([], Response::HTTP_BAD_REQUEST,
                "Invalid parameters!");
        }

        $idName = $data['id_name'];

        try {
            DB::beginTransaction();
            $einv = ShopEInvoice::firstWhere('id_name', $idName);
            if (is_null($einv)) {
                return $this->responseError([], Response::HTTP_BAD_REQUEST,
                    "Invoice not exist!");
            }

            $einv->sign_status = 4;

            if (isset($data['einv_id'])) {
                $einv->einv_id = $data['einv_id'];
            }
            $einv->update();

            $history = new ShopEInvoiceHistory();
            $history->einv_id = $einv->id;
            $history->start_date = now();
            $history->end_date = Carbon::createFromFormat('Y-m-d H:i:s', now());
            $history->error = isset($data['error']) ? $data['error'] : 'Phát hành hóa đơn thành công!';
            $history->status = 1;
            $history->save();

            DB::commit();
            return $this->responseSuccess('ok');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->responseError([], Response::HTTP_BAD_REQUEST, $e->getMessage());
        }
    }

    public function getListSaleOrder(Request $request)
    {

        $data = $request->all();
        if (!isset($data['from_date']) || !isset($data['to_date'])) {
            return $this->responseError([], Response::HTTP_BAD_REQUEST,
                "Invalid parameters!");
        }

        $fromDateSt = $data['from_date'];
        $fromDate = Carbon::createFromFormat('Y-m-d', $fromDateSt);

        $toDateSt = $data['to_date'];
        $toDate = Carbon::createFromFormat('Y-m-d', $toDateSt);

        // CORP
        $objectOrderDetail = new ShopOrderDetail();
        $tableOrderDetail = SC_DB_PREFIX . 'shop_order_detail';
        $tableOrder = SC_DB_PREFIX . 'shop_order';
        $tableCustomer = SC_DB_PREFIX . 'shop_customer';
        $orderDetailList = $objectOrderDetail
            ->join($tableOrder . ' as so', $tableOrderDetail . '.order_id', '=', 'so.id')
            ->join($tableCustomer . ' as c', 'so.customer_id', '=', 'c.id')
            ->where("so.delivery_time", ">=", $fromDateSt)
            ->where("so.delivery_time", "<=", $toDateSt)
            ->whereIn("so.status", [1,2])
            ->where($tableOrderDetail . ".qty_reality", '>', 0)
            ->select(
                'so.delivery_time as delivery_date',
                'so.bill_date as bill_date',
                'so.id_name as order_id',
                'so.customer_code',
                'so.name as customer_name',
                'so.explain',
                'so.total',
                'so.status',
                'so.created_at',
                'so.updated_at',
                'so.object_id',
                'c.student_code',
                'c.teacher_code',
                $tableOrderDetail . '.product_code',
                $tableOrderDetail . '.product_name',
                $tableOrderDetail . '.product_unit',
                $tableOrderDetail . '.qty_reality as qty',
                $tableOrderDetail . '.price'
            )->get();

        $listOrderCorp = [];
        foreach ($orderDetailList->groupBy('order_id') as $keyOrder => $itemOrder) {
            $customer_code = ($itemOrder[0]['object_id'] == 1) ? $itemOrder[0]['teacher_code'] : $itemOrder[0]['student_code'];
            if ($customer_code == null || $customer_code == '') {
                $customer_code = $itemOrder[0]['customer_code'];
            }

            $orderExplain = $itemOrder[0]['explain'] . ' ' . Carbon::createFromFormat('Y-m-d', $itemOrder[0]['bill_date'])->format('d/m/Y');

            $order = [
                'order_id' => $keyOrder,
                'customer_code' => $customer_code,
                'customer_name' => $itemOrder[0]['customer_name'],
                'explain' => $orderExplain,
                'object_id' => $itemOrder[0]['object_id'],
                'total' => $itemOrder[0]['total'],
                'status' => $itemOrder[0]['status'],
                'delivery_date' => $itemOrder[0]['delivery_date'],
                'bill_date' => $itemOrder[0]['bill_date'],
                'created_at' => $itemOrder[0]['created_at']->format('Y-m-d H:i:s'),
                'updated_at' => isset($itemOrder[0]['updated_at']) ? $itemOrder[0]['updated_at']->format('Y-m-d H:i:s') : $itemOrder[0]['created_at']->format('Y-m-d H:i:s'),
                'detail' => $itemOrder
            ];
            $listOrderCorp[] = $order;
        }



        // COOK Suat an
        $listOrderCookSa = [];
        $tableOrderCookDetail = SC_DB_PREFIX . 'shop_davicook_order_detail';
        $tableOrderCook = SC_DB_PREFIX . 'shop_davicook_order';
        $tableCustomerCook = SC_DB_PREFIX . 'shop_davicook_customer';
        $orderCook = new ShopDavicookOrder();
        $orderCookData = $orderCook->leftJoin($tableCustomerCook . ' as c', $tableOrderCook . '.customer_id', '=', 'c.id')
            ->where($tableOrderCook . ".delivery_date", ">=", $fromDateSt)
            ->where($tableOrderCook . ".delivery_date", "<=", $toDateSt)
            ->where($tableOrderCook . ".type", 0)
            ->whereIn($tableOrderCook . '.status', [0, 1, 2])
            ->select(
                $tableOrderCook . '.delivery_date',
                $tableOrderCook . '.bill_date',
                $tableOrderCook . '.id_name as order_id',
                $tableOrderCook . '.customer_code',
                $tableOrderCook . '.customer_name',
                $tableOrderCook . '.explain',
                $tableOrderCook . '.number_of_reality_servings',
                $tableOrderCook . '.number_of_extra_servings',
                $tableOrderCook . '.price_of_servings',
                $tableOrderCook . '.status',
                $tableOrderCook . '.created_at',
                $tableOrderCook . '.updated_at',
                'c.serving_price'
            )->get();

        if ($orderCookData) {
            foreach ($orderCookData as $orderCookDatum) {
                $orderDetaiQty = ($orderCookDatum['number_of_reality_servings'] ?? 0);
                $orderDetaiPrice = $orderCookDatum['serving_price'] ?? $orderCookDatum['price_of_servings'];
                $orderTotal = $orderDetaiQty * $orderDetaiPrice;
                $listOrderCookSa[] = [
                    'order_id' => $orderCookDatum['order_id'],
                    'customer_code' => $orderCookDatum['customer_code'],
                    'customer_name' => $orderCookDatum['customer_name'],
                    'explain' => '',
                    'object_id' => null,
                    'total' => $orderTotal,
                    'status' => $orderCookDatum['status'],
                    'delivery_date' => $orderCookDatum['delivery_date'],
                    'bill_date' => $orderCookDatum['bill_date'],
                    'created_at' => $orderCookDatum['created_at']->format('Y-m-d H:i:s'),
                    'updated_at' => isset($orderCookDatum['updated_at']) ? $orderCookDatum['updated_at']->format('Y-m-d H:i:s') : $orderCookDatum['created_at']->format('Y-m-d H:i:s'),
                    'detail' => array(
                        [
                            'product_code' => 'S001',
                            'product_name' => 'Suất ăn',
                            'product_unit' => '',
                            'qty' => $orderDetaiQty,
                            'price' => $orderDetaiPrice
                        ]
                    )
                ];
            }
        }



        // Cook hang tuoi
        $listOrderCookHt = [];
        $objectOrderCookDetail = new ShopDavicookOrderDetail();
        $orderCookDetailList = $objectOrderCookDetail
            ->join($tableOrderCook . ' as so', $tableOrderCookDetail . '.order_id', '=', 'so.id')
            ->where("so.delivery_date", ">=", $fromDateSt)
            ->where("so.delivery_date", "<=", $toDateSt)
            ->whereIn("so.status", [0, 1, 2])
            ->where($tableOrderCookDetail . ".product_type", 1)
            ->where($tableOrderCookDetail . ".total_bom", '>', 0)
            ->select(
                'so.delivery_date',
                'so.bill_date',
                'so.id_name as order_id',
                'so.total',
                'so.customer_code',
                'so.customer_name',
                'so.explain',
                'so.status',
                'so.created_at',
                'so.updated_at',
                $tableOrderCookDetail . '.product_code',
                $tableOrderCookDetail . '.product_name',
                $tableOrderCookDetail . '.product_unit',
                $tableOrderCookDetail . '.real_total_bom as qty',
                DB::raw('0 as price')
            )->get();

        if ($orderCookDetailList) {
            foreach ($orderCookDetailList->groupBy('order_id') as $keyOrder => $itemOrder) {

                // gộp số lượng theo sản phẩm
                $orderDetails = [];
                foreach ($itemOrder->groupBy('product_code') as $keyProduct => $itemProduct){
                    $orderDetailItem = $itemProduct->first();
                    $orderDetailItem['qty'] = round($itemProduct->sum('qty'), 2);
                    $orderDetails[] = $orderDetailItem;
                }

                $order = [
                    'order_id' => str_replace("SA", "HT", $keyOrder),
                    'customer_code' => $itemOrder[0]['customer_code'],
                    'customer_name' => $itemOrder[0]['customer_name'],
                    'explain' => '',
                    'object_id' => $itemOrder[0]['object_id'],
                    'total' => $itemOrder[0]['total'],
                    'status' => $itemOrder[0]['status'],
                    'delivery_date' => $itemOrder[0]['delivery_date'],
                    'bill_date' => $itemOrder[0]['bill_date'],
                    'created_at' => $itemOrder[0]['created_at']->format('Y-m-d H:i:s'),
                    'updated_at' => isset($itemOrder[0]['updated_at']) ? $itemOrder[0]['updated_at']->format('Y-m-d H:i:s') : $itemOrder[0]['created_at']->format('Y-m-d H:i:s'),
                    'detail' => $orderDetails
                ];
                $listOrderCookHt[] = $order;
            }

        }

        // COOK hang kho
        $listOrderCookHk = [];
        $objectOrderCookDetail = new ShopDavicookOrderDetail();
        $orderCookDetailList = $objectOrderCookDetail
            ->join($tableOrderCook . ' as so', $tableOrderCookDetail . '.order_id', '=', 'so.id')
            ->where("so.export_date", ">=", $fromDateSt)
            ->where("so.export_date", "<=", $toDateSt)
            ->whereIn("so.status", [2])
            ->where($tableOrderCookDetail . ".product_type", 0)
            ->where($tableOrderCookDetail . ".total_bom", '>', 0)
            ->select(
                'so.delivery_date as delivery_date',
                DB::raw('DATE_FORMAT(so.delivery_date, "%d/%m") as delivery_date_format'),
                'so.export_date as export_date',
                DB::raw('DATE_FORMAT(so.export_date, "%d%m%y") as export_date_format'),
                'so.bill_date',
                'so.id_name as order_id',
                'so.total',
                'so.customer_code',
                'so.customer_name',
                'so.explain',
                'so.status',
                'so.created_at',
                'so.updated_at',
                $tableOrderCookDetail . '.product_code',
                $tableOrderCookDetail . '.product_name',
                $tableOrderCookDetail . '.product_unit',
                $tableOrderCookDetail . '.real_total_bom as qty',
                DB::raw('0 as price')
            )
            ->orderBy('so.updated_at', 'DESC')
            ->get();

        if ($orderCookDetailList) {
            foreach ($orderCookDetailList->groupBy(['customer_code', 'export_date']) as $export_date) {
                foreach ($export_date as $keyOrder => $itemOrder) {
                    $minDeliveryDate = $itemOrder->first()->delivery_date_format;
                    $maxDeliveryDate = $itemOrder->last()->delivery_date_format;

                    $fistItemOrder = $itemOrder->first();
                    // gộp số lượng theo sản phẩm
                    $orderDetails = [];
                    foreach ($itemOrder->groupBy('product_code') as $keyProduct => $itemProduct){
                        $orderDetailItem = $itemProduct->first();
                        $orderDetailItem['qty'] = round($itemProduct->sum('qty'), 2);
                        $orderDetails[] = $orderDetailItem;
                    }

                    $orderId = $fistItemOrder['customer_code'] . $fistItemOrder['export_date_format'];

                    $order = [
                        'order_id' => $orderId,
                        'customer_code' => $fistItemOrder['customer_code'],
                        'customer_name' => $fistItemOrder['customer_name'],
                        'explain' => 'Xuất hàng khô cho bếp ngày ' . $minDeliveryDate . '-' . $maxDeliveryDate,
                        'object_id' => $fistItemOrder['object_id'],
                        'total' => $fistItemOrder['total'],
                        'status' => $fistItemOrder['status'],
                        'delivery_date' => $fistItemOrder['export_date'],
                        'bill_date' => $fistItemOrder['bill_date'],
                        'created_at' => $fistItemOrder['created_at']->format('Y-m-d H:i:s'),
                        'updated_at' => isset($fistItemOrder['updated_at']) ? $fistItemOrder['updated_at']->format('Y-m-d H:i:s') : $fistItemOrder['created_at']->format('Y-m-d H:i:s'),
                        'detail' => $orderDetails
                    ];
                    $listOrderCookHk[] = $order;
                }
            }

        }

        $result = [
            "davicorp" => $listOrderCorp,
            "davicook_sa" => $listOrderCookSa,
            "davicook_ht" => $listOrderCookHt,
            "davicook_hk" => $listOrderCookHk
        ];

        return $this->responseSuccess($result);
    }

    public function updateStatusSaleOrder(Request $request)
    {
        $data = $request->all();
        if (!isset($data['orders'])) {
            return $this->responseError([], Response::HTTP_BAD_REQUEST,
                "Invalid parameters!");
        }

        $orderUpdateList = $data['orders'];

        foreach ($orderUpdateList as $orderUpdate) {
            $idName = $orderUpdate['id_name'];

            $prefixIdName = substr($idName, 0, 2);

            if ($prefixIdName == 'DH') {
                try {
                    $order = ShopOrder::firstWhere('id_name', $idName);
                    if (is_null($order)) {
                        continue;
                    }

                    $status = $orderUpdate['status'];
                    $order->fast_sync_status = $status;
                    $order->timestamps = false;
                    $order->save();

                } catch (\Exception $e) {
                }
            } else if ($prefixIdName == 'SA') {
                try {
                    $order = ShopDavicookOrder::firstWhere('id_name', $idName);
                    if (is_null($order)) {
                        continue;
                    }

                    $status = $orderUpdate['status'];
                    $order->fast_sync_status = $status;
                    $order->timestamps = false;
                    $order->save();
                } catch (\Exception $e) {
                }
            }
        }
        return $this->responseSuccess('ok');

    }

    public function getListPurchaseOrder(Request $request)
    {
        $data = $request->all();
        if (!isset($data['from_date']) || !isset($data['to_date'])) {
            return $this->responseError([], Response::HTTP_BAD_REQUEST,
                "Invalid parameters!");
        }

        $fromDateSt = $data['from_date'];
        $fromDate = Carbon::createFromFormat('Y-m-d', $fromDateSt)->startOfDay();

        $toDateSt = $data['to_date'];
        $toDate = Carbon::createFromFormat('Y-m-d', $toDateSt)->endOfDay();

        // Lay du lieu tung ngay
        $interval = DateInterval::createFromDateString('1 day');
        $period = new DatePeriod($fromDate, $interval, $toDate);

        $listOrder = [];
        foreach ($period as $dt) {
            $keyDeliveryDate = $dt->format('d/m/Y');
            $dataSearch = [
                "from_to" => $keyDeliveryDate,
                "end_to" => $keyDeliveryDate
            ];

            $dataImport = app('App\Admin\Controllers\AdminReportController')->getDataPriceImport([], $dataSearch);
            foreach ($dataImport->groupBy('supplier_code') as $keySupplier => $itemSupplier) {
                $order = [
                    "delivery_date" => $keyDeliveryDate,
                    "supplier_code" => $keySupplier
                ];

                $orderDetails = [];
                foreach ($itemSupplier as $item) {
                    if (!$item["qtyProduct"] && $item["qtyProduct"] == 0) {
                        continue;
                    }
                    $itemExist = null;
                    $idxRemove = -1;

                    foreach ($orderDetails as $key => $orderDetail) {
                        if ($orderDetail["product_code"] == $item["product_code"]
                            && $orderDetail["price"] == $item["price"]) {
                            $idxRemove = $key;
                            $itemExist = $orderDetail;

                            $itemExist["qtyProduct"] = round($itemExist["qtyProduct"] + $item["qtyProduct"], 7);
                            break;
                        }
                    }

                    if (is_null($itemExist)) {
                        $orderDetails[] = $item;
                    } else {
                        $orderDetails[$idxRemove] = $itemExist;
                    }
                }
                $orderDetails = new Collection($orderDetails);
                $orderDetails = $orderDetails->sortBy([
                    'sort',
                    'qtyProduct',
                ])->values();
                if (count($orderDetails) > 0) {
                    $order["supplier_name"] = $orderDetails[0]["supplier_name"];
                    $order["details"] = $orderDetails;
                    $listOrder[] = $order;
                }
            }
        }

        return $this->responseSuccess($listOrder);
    }
}