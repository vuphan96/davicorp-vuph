<?php

namespace App\Admin\Api\Warehouse\Admin;

use App\Admin\Api\ApiController;
use App\Admin\Models\AdminHoliday;
use App\Admin\Models\AdminImport;
use App\Admin\Models\AdminImportDetail;
use App\Admin\Models\AdminImportHistory;
use App\Admin\Models\AdminNotification;
use App\Admin\Models\AdminNotificationCustomer;
use App\Admin\Models\AdminOrder;
use App\Admin\Models\AdminProduct;
use App\Admin\Models\AdminProductPriceDetail;
use App\Admin\Models\AdminShopOrderChangeExtra;
use App\Admin\Models\AdminWarehouse;
use App\Exports\DavicorpOrder\AdminExportMultipleSheet;
use App\Exports\DavicorpOrder\AdminMultipleSheetSalesInvoiceListRealOrder;
use App\Front\Models\ShopCustomer;
use App\Front\Models\ShopDeviceToken;
use App\Front\Models\ShopGenId;
use App\Front\Models\ShopImport;
use App\Front\Models\ShopOrder;
use App\Front\Models\ShopOrderDetail;
use App\Front\Models\ShopOrderHistory;
use App\Front\Models\ShopProduct;
use App\Front\Models\ShopProductSupplier;
use App\Front\Models\ShopSupplier;
use App\Front\Models\ShopUserPriceboard;
use Carbon\Carbon;
use Dompdf\Dompdf;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class OrderImportController extends ApiController
{
    const DEFAULT_PAGE_NUM = 1;
    const DEFAULT_PAGE_SIZE = 20;
    /**
     * Get authenticated user
     */
    public function getListOrderImport(Request $request)
    {
        $pageNum = $request->get("page_num");
        if (!isset($pageNum)){
            $pageNum = self::DEFAULT_PAGE_NUM;
        }

        $pageSize = $request->get("page_size");
        if (!isset($pageSize)){
            $pageSize = self::DEFAULT_PAGE_SIZE;
        }
        try {
            $date_start = $request->date_start ? date($request->date_start) : '';
            $date_end = $request->date_end ? date($request->date_end) : '';
            $keyword = $request->keyword;
            $id_name = $request->id_name;
            $supplier = $request->supplier;
            $orders = new ShopImport();

            if ($request->date_from){
                $orders = $orders->whereDate('delivery_date', '>=', $date_start);
            }

            if ($request->date_to){
                $orders = $orders->whereDate('delivery_date', '<=', $date_end);
            }

            if ($id_name) {
                $orders = $orders->where('id_name', $id_name);
            }

            if ($supplier) {
                $orders = $orders->where(function ($q) use ($supplier){
                    $q->orWhere('supplier_code', 'like', '%' . $supplier . '%');
                    $q->orWhere('supplier_name', 'like', '%' . $supplier . '%');
                });
            }

            if ($keyword) {
                $orders = $orders->where(function ($q) use ($keyword) {
                    $q->where('id_name', 'like', '%' . $keyword . '%');
                    $q->orWhere('supplier_code', 'like', '%' . $keyword . '%');
                    $q->orWhere('supplier_name', 'like', '%' . $keyword . '%');
                });
            }
            $data = $orders->paginate($perPage = $pageSize, $columns = ['*'], $pageName = 'page_num', $page = $pageNum);
            return $this->responseSuccess($data);
        } catch (\Throwable $e) {
            Log::error($e);
            return $this->responseError([], Response::HTTP_BAD_REQUEST, 'Lỗi mạng. Vui lòng thử lại!');
        }
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function detailOrderImport($id)
    {
        $orders = AdminImport::with('details', 'history')->find($id);
        if ($orders) {
            return $this->responseSuccess($orders);
        }
        return $this->responseError([], Response::HTTP_BAD_REQUEST, 'Không tìm thấy đơn hàng này!');
    }

    /**
     * Hủy đơn nhập hàng
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function cancelOrderImport(Request $request, $id)
    {
        try {
            $user = $request->user('admin-api');
            $import = AdminImport::find($id);
            if (!$import) {
                return $this->responseError([], Response::HTTP_BAD_REQUEST, 'Không tìm thấy đơn nhập này!');
            }
            if ($import->status == 2 || $import->status == 3) {
                return $this->responseError([], Response::HTTP_BAD_REQUEST, 'Đơn NHhập hàng trạng thái "Đã xác nhận và Đã nhập kho" Không thể hủy!');
            }
            $import->status = 1;
            $import->type_import = 5;
            $import->edit = 1;
            $import->save();
            $title = "Hủy đơn nhập hàng";
            $content = "Thủ kho Hủy đơn nhập hàng";
            $this->storeHistoryOrderImport($user, $import, $title, $content);
            DB::commit();
            return $this->responseSuccess([], Response::HTTP_OK, 'Hủy đơn hàng nhập thành công');
        } catch (\Throwable $e) {
            DB::rollBack();
            return $this->responseError([], Response::HTTP_BAD_REQUEST, $e->getMessage());
        }
    }

    /**
     * Clone đơn nhập hàng!
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function cloneImportOrder($id)
    {
        $id = request('id') ?? '';
        $import = AdminImport::find($id);
        if (!$import) {
            return $this->responseError([], Response::HTTP_BAD_REQUEST, 'Không tìm thấy đơn nhập này!');
        }
        DB::beginTransaction();
        try {
            $dataImport = Arr::except($import->toArray(), ['id', 'edit', 'created_at', 'updated_at', 'id_name']);
            $dataImport['id_name'] = ShopGenId::genNextId('order_import');
            $dataImport['edit'] = 0;
            $dataImport['status'] = 1;
            $dataDetails = Arr::except($import->details->toArray(), ['id', 'import_id', 'created_at', 'updated_at', 'comment']);
            $newImport = AdminImport::create($dataImport);
            foreach ($dataDetails as $key => $orderDetail) {
                $orderDetail['id'] = sc_uuid();
                $orderDetail['import_id'] = $newImport->id;
                $orderDetail['comment'] = '';
                AdminImportDetail::create($orderDetail);
            }
            DB::commit();
            return $this->responseSuccess([], Response::HTTP_OK, 'Nhân bản thành công');
        } catch (\Throwable $e) {
            DB::rollBack();
            return $this->responseError([], Response::HTTP_BAD_REQUEST, $e->getMessage());
        }
    }

    /**
     * Lấy sản phẩm và giá tiền nhập theo nhà cung cấp
     * @param Request $request
     * @return \Illuminate\Support\Collection
     */
    public function getProducts(Request $request)
    {
        $warehouse = $request->warehouse_id;
        $deliveryDate = $request->delivery_date;
        $productTableName = SC_DB_PREFIX . 'shop_product as p';
        $productWarehouseTableName = SC_DB_PREFIX . 'shop_product_warehouse as pw';
        $importPriceboardTableName = SC_DB_PREFIX . 'shop_import_priceboard as ip';
        $importPriceboardDetailTableName = SC_DB_PREFIX . 'shop_import_priceboard_detail as ipd';
        $supplierTable = SC_DB_PREFIX . 'shop_supplier as sl';
        $unitTable = SC_DB_PREFIX.'shop_unit as un';
        $productList = DB::table($productWarehouseTableName)
            ->join($productTableName, 'pw.product_id', '=', 'p.id')
            ->join($unitTable, 'p.unit_id', '=', 'un.id')
            ->where('pw.warehouse_id', '=', $warehouse)
            ->select('p.id', 'p.name', 'p.sku', 'pw.latest_import_qty', 'pw.qty', 'un.name as unit_name')
            ->get();
        foreach ($productList as $product) {
            if(isset($deliveryDate)){
                $priceBoard = DB::table($importPriceboardDetailTableName)
                    ->join($importPriceboardTableName, 'ipd.priceboard_id', '=', 'ip.id')
                    ->join($supplierTable, 'ip.supplier_id', '=', 'sl.id')
                    ->where('ipd.product_id', '=', $product->id)
                    ->whereDate( 'ip.start_date', '<=', $deliveryDate )
                    ->whereDate( 'ip.end_date', '>=', $deliveryDate)
                    ->select('ip.supplier_id', 'ipd.price', 'sl.name','sl.address', 'sl.email', 'sl.phone')
                    ->get();
                $product->priceboard = $priceBoard;
            }
        }
        return $this->responseSuccess($productList);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function getSuppliers()
    {
        $supplier = ShopSupplier::all();
        return $this->responseSuccess($supplier);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function storeNewImportOrder(Request $request)
    {
        $data = $request->all();
        $warehouse = $request->warehouse_id;
        $deliveryDate = $request->delivery_date;
        DB::beginTransaction();
        try {
            $wareHouse = AdminWarehouse::where('id', $warehouse)->first();
            $newData = new Collection($data['data_import'] ?? []);
            $newData = $newData->groupBy('supplier_id');
            $now = now();
            foreach ($newData as $keySupplierId => $item) {
                $supplier = ShopSupplier::where('id', $keySupplierId)->first();
                $dataInsert = [
                    'id' => sc_uuid(),
                    'id_name' => ShopGenId::genNextId('order_import'),
                    'supplier_id' => $supplier->supplier_id,
                    'supplier_name' => $supplier->name,
                    'warehouse_id' => $wareHouse->id,
                    'warehouse_code' => $wareHouse->warehouse_code,
                    'warehouse_name' => $wareHouse->name,
                    'address' => $supplier->address,
                    'email' => $supplier->email,
                    'phone' => $supplier->phone,
                    'delivery_date' => Carbon::createFromFormat('d/m/Y', $deliveryDate),
                    'reality_delivery_date' => null,
                    'total' => 0,
                    'total_reality' => 0,
                    'status' => 1,
                    'edit' => 0,
                    'note' => null,
                    'type_import' => 5,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
                $newImport = AdminImport::create($dataInsert);
                $dataInsertDetail = [];
                $total = 0;
                foreach ($item as $detail) {
                    $total += (float)$detail['price'] * (float)$detail['qty'];
                    $product = ShopProduct::with('unit')->where('id', $detail['product_id'])->first();
                    $dataInsertDetail[] = array(
                        'id' => sc_uuid(),
                        'import_id' => $newImport->id,
                        'import_id_name' => $newImport->id_name,
                        'product_id' => $product->id,
                        'product_code' => $product->sku,
                        'product_name' => $product->name,
                        'category_id' => $product->category_id,
                        'product_kind' => $product->kind,
                        'qty_order' => $detail['qty'],
                        'qty_reality' => $detail['qty'],
                        'customer_id' => null,
                        'customer_code' => null,
                        'customer_name' => null,
                        'unit_id' => $product->unit->id ?? '',
                        'unit_name' => $product->unit->name ?? '',
                        'product_price' => $detail['price'],
                        'amount' => (float)$detail['price'] * (float)$detail['qty'],
                        'amount_reality' => (float)$detail['price'] * (float)$detail['qty'],
                        'comment' => $detail['comment'],
                        'created_at' => $now,
                        'updated_at' => $now,
                    );
                }
                $newImport->total = $total;
                $newImport->total_reality = $total;
                $newImport->save();

                AdminImportDetail::insert($dataInsertDetail);
            }
            DB::commit();
            return $this->responseSuccess([], Response::HTTP_OK, 'Tạo đơn nhập hàng thành công');
        } catch (\Throwable $e) {
            DB::rollBack();
            return $this->responseError([], Response::HTTP_BAD_REQUEST, $e->getMessage());
        }
    }

    public function updateInfoImport(Request $request, $id)
    {
        $user = $request->user('admin-api');
        $data = $request->all();
        $importOrder = AdminImport::find($id);
        $arrStatus = AdminImport::$IMPORT_STATUS;
        DB::beginTransaction();
        $edit = false;
        try {
            $supplier_id = $data['supplier_id'];
            $delivery_date = Carbon::createFromFormat('d/m/Y', $data['delivery_date']);
            $note = $data['note'];
            $status = $data['status'];
            $contentHistory = [];
            if ($importOrder->status != $status) {
                $edit = true;
                $importOrder->status = $status;
                $contentHistory[] = "- Thủ kho sửa trạng thái : ".$arrStatus[$importOrder->status].' -> '.$arrStatus[$status];
            }
            if ($importOrder->delivery_date != $delivery_date) {
                $edit = true;
                $importOrder->delivery_date = $delivery_date;
                $contentHistory[] = "- Thủ kho sửa ngày giao hàng : ".$importOrder->delivery_date.' -> '.$delivery_date;
            }
            if ($note != '') {
                $edit = true;
                $importOrder->note = $note;
                $contentHistory[] = "- Thủ kho sửa ghi chú";
            }
            if ($importOrder->supplier_id != $supplier_id) {
                if ($importOrder->supplier_id == '') {
                    return $this->responseError([], Response::HTTP_BAD_REQUEST, 'Đơn nhập từ BC trả hàng không thể chỉnh s!');
                }
                $edit = true;
                $supplier = ShopSupplier::where('id', $supplier_id)->first();
                $importOrder->supplier_id = $supplier_id;
                $importOrder->supplier_name = $supplier->name;
                $importOrder->address = $supplier->address;
                $importOrder->email = $supplier->email;
                $importOrder->phone = $supplier->phone;
                $contentHistory[] = "- Thủ kho sửa nhà cung cấp : ".$importOrder->supplier_name.' -> '.$supplier->name;;
            }

            if ($edit == true) {
                $importOrder->save();
                $title = 'Thủ kho sửa thông tin đơn nhập';
                $content = implode("<br>", $contentHistory);
                $this->storeHistoryOrderImport($user, $importOrder, $title, $content);
            }

            DB::commit();
            return $this->responseSuccess([], Response::HTTP_OK, 'Chỉnh sửa thành công');
        } catch (\PDOException $error) {
            DB::rollBack();
            return $this->responseError([], Response::HTTP_BAD_REQUEST, $error->getMessage());
        }
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getProductForDetail($id)
    {
        $import = AdminImport::find($id);
        $productTableName = SC_DB_PREFIX . 'shop_product as p';
        $importPriceboardTableName = SC_DB_PREFIX . 'shop_import_priceboard as ip';
        $importPriceboardDetailTableName = SC_DB_PREFIX . 'shop_import_priceboard_detail as ipd';
        $supplierTable = SC_DB_PREFIX . 'shop_supplier as sl';
        $unitTable = SC_DB_PREFIX.'shop_unit as un';
        $productList = DB::table($productTableName)
            ->join($unitTable, 'p.unit_id', '=', 'un.id')
            ->select('p.id', 'p.name', 'p.sku', 'un.name as unit_name')
            ->get();
        foreach ($productList as $product) {
            $priceBoard = DB::table($importPriceboardDetailTableName)
                ->join($importPriceboardTableName, 'ipd.priceboard_id', '=', 'ip.id')
                ->join($supplierTable, 'ip.supplier_id', '=', 'sl.id')
                ->where('ipd.product_id', '=', $product->id)
                ->where('sl.id', '=', $import->supplier_id)
                ->whereDate( 'ip.start_date', '<=', $import->delivery_date)
                ->whereDate( 'ip.end_date', '>=', $import->delivery_date)
                ->select('ipd.price')
                ->first();
            $product->priceboard = $priceBoard->price ?? 0;
        }
        return $this->responseSuccess($productList);
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateImportDetail(Request $request, $id)
    {
        $data = $request->all();
        $importOrder = AdminImport::with('details')->find($id);
        if ($importOrder->status == 2 || $importOrder->status == 3) {
            return $this->responseError([], Response::HTTP_BAD_REQUEST, 'Đơn "Đã xác nhận" và "Đã nhập kho" không thể chỉnh sửa!');
        }
        $user = $request->user('admin-api');
        $deleteItems = isset($data['delete_items']) ? $data['delete_items'] : [];
        $editItems = isset($data['edit_items']) ? $data['edit_items'] : [];
        $addItems = isset($data['add_items']) ? $data['add_items'] : [];
        $contentHistory = [];
        DB::beginTransaction();
        try {
            if (count($deleteItems) > 0) {
                foreach ($deleteItems as $deleteItem) {
                    $contentHistory[] = "- Xoá sản phẩm : " . $deleteItem['product_name'];
                    AdminImportDetail::where('id', $deleteItem['id'])->delete();
                }
            }

            if (count($editItems) > 0) {
                foreach ($editItems as $editItem) {
                    $detailEdit = AdminImportDetail::where('id', $editItem['detail_id'])->first();
                    if ($detailEdit) {
                        $contentHistory[] = "- Chỉnh sửa số lượng thực tế : " . $editItem["product_name"] . ". Thay đổi " . $detailEdit->qty_reality . " -> " . $editItem["qty_reality"];
                        $detailEdit->qty_reality = $editItem['qty_reality'];
                        $detailEdit->amount_reality = $detailEdit->qty_reality * $detailEdit->product_price;
                        $detailEdit->save();

                    }
                }
            }

            if (count($addItems) > 0) {
                foreach ($addItems as $addItem) {
                    $product = ShopProduct::with('unit')->where('id', $addItem['product_id'])->first();
                    $dataInsertDetail[] = array(
                        'id' => sc_uuid(),
                        'import_id' => $importOrder->id,
                        'import_id_name' => $importOrder->id_name,
                        'product_id' => $product->id,
                        'product_code' => $product->sku,
                        'product_name' => $product->name,
                        'category_id' => $product->category_id,
                        'product_kind' => $product->kind,
                        'qty_order' => $addItem['qty'],
                        'qty_reality' => $addItem['qty'],
                        'customer_id' => null,
                        'customer_code' => null,
                        'customer_name' => null,
                        'unit_id' => $product->unit->id ?? '',
                        'unit_name' => $product->unit->name ?? '',
                        'product_price' => $addItem['price'],
                        'amount' => (float)$addItem['price'] * (float)$addItem['qty'],
                        'amount_reality' => (float)$addItem['price'] * (float)$addItem['qty'],
                        'comment' => $addItem['comment'],
                        'created_at' => now(),
                        'updated_at' => now(),
                    );
                    $contentHistory[] = "- Thêm sản phẩm : " . $product->name;
                }
            }

            AdminImportDetail::insert($dataInsertDetail);
            $this->updateTotalOrderImport($id);
            if (count($contentHistory) > 0) {
                $content = implode("<br/>", $contentHistory);
                $title = 'Thủ kho sửa chi tiết';
                $this->storeHistoryOrderImport($user, $importOrder, $title, $content);
            }
            DB::commit();
            return $this->responseSuccess([], Response::HTTP_OK, 'Chỉnh sửa thành công');
        } catch (\Throwable $e) {
            Log::error($e);
            DB::rollBack();
            return $this->responseError([], Response::HTTP_BAD_REQUEST, 'Lỗi mạng. Vui lòng thử lại');
        }
    }

    /**
     * @param $user
     * @param $import
     * @param $title
     * @param $content
     */
    public function storeHistoryOrderImport($user, $import, $title, $content)
    {
        $dataHistory = [
            'import_id' => $import->id,
            'import_id_name' => $import->id_name,
            'title' => $title,
            'content' => $content,
            'admin_id' => $user->id,
            'user_name' => $user->name,
        ];
        AdminImportHistory::create($dataHistory);
    }

    private function updateTotalOrderImport($id)
    {
        $import = AdminImport::with('details')->find($id);
        $import->total = $import->details->sum('amount');
        $import->total_reality = $import->details->sum('amount_reality');
        $import->edit = 1;
        $import->save();
    }
}