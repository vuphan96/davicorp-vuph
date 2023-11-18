<?php
namespace App\Admin\Api\Warehouse\Admin;

use App\Admin\Api\ApiController;
use App\Admin\Models\AdminImport;
use App\Admin\Models\AdminImportDetail;
use App\Admin\Models\AdminWarehouse;
use App\Front\Models\ShopDavicookOrderReturnHistory;
use App\Front\Models\ShopGenId;
use App\Front\Models\ShopOrderReturnHistory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class ReportProductReturn extends ApiController
{
    const DEFAULT_PAGE_NUM = 1;
    const DEFAULT_PAGE_SIZE = 30;
    public function getOrderDavicorp(Request $request){
        $date_start = $request->date_start;
        $date_end = $request->date_end;
        $keyword = $request->keyword;
        $product_kind = $request->product_kind;
        $category = $request->category;
        $customer = $request->customer;
        $data_return = new ShopOrderReturnHistory();
        if ($date_start){
            $data_return = $data_return->whereDate("created_at", ">=", convertVnDateObject($date_start)->toDateString());
        }
        if ($date_end){
            $data_return = $data_return->whereDate("created_at", "<=", convertVnDateObject($date_end)->toDateString());
        }
        if ($category){
            $data_return = $data_return->where('category_id',"=", $category);
        }
        if($product_kind =='0'){
            $data_return = $data_return->where('product_kind', "=", '0');
        }
        if($product_kind =='1'){
            $data_return = $data_return->where('product_kind', "=", '1');
        }
        if ($customer){
            $data_return = $data_return->where('customer_code', "=", $customer);
        }
        if ($keyword) {
            $data_return = $data_return->where(function ($sql) use ($keyword){
                $sql->where("product_code", "like", "%" . $keyword . "%")
                ->orWhere("product_name", "like", "%" . $keyword . "%")
                ->orWhere("order_id_name", "like", "%" . $keyword . "%")
                ->orWhere("customer_name", "like", "%" . $keyword . "%")
            ;
            });
        }
        $dataReturn = $data_return->orderBy("created_at", 'desc')->orderBy('customer_name')->get();
        return $dataReturn;
    }
    public function getOrderDavicook(Request $request){
        $date_start = $request->date_start;
        $date_end = $request->date_end;
        $keyword = $request->keyword;
        $product_kind = $request->product_kind;
        $category = $request->category;
        $customer = $request->customer;
        $data_return = new ShopDavicookOrderReturnHistory();
        if ($date_start){
            $data_return = $data_return->whereDate("created_at", ">=", convertVnDateObject($date_start)->toDateString());
        }
        if ($date_end){
            $data_return = $data_return->whereDate("created_at", "<=", convertVnDateObject($date_end)->toDateString());
        }
        if ($category){
            $data_return = $data_return->where('category_id',"=", $category);
        }
        if($product_kind =='0'){
            $data_return = $data_return->where('product_kind', "=", '0');
        }
        if($product_kind =='1'){
            $data_return = $data_return->where('product_kind', "=", '1');
        }
        if ($request->customer){
            $data_return = $data_return->where('customer_code', "=", $customer);
        }
        if ($keyword) {
            $data_return = $data_return->where(function ($sql) use ($keyword){
                $sql->where("product_code", "like", "%" . $keyword . "%")
                ->orWhere("product_name", "like", "%" . $keyword . "%")
                ->orWhere("order_id_name", "like", "%" . $keyword . "%")
                ->orWhere("customer_name", "like", "%" . $keyword . "%")
            ;
            });
        }
        $dataReturn = $data_return->orderBy("created_at", 'desc')->orderBy('customer_name')->get();
        return $dataReturn;
    }
    public function getListReport(Request $request){
        $pageNum = $request->get("page_num");
        $pageSize = $request->get("page_size");
        try {
            $dataReturnOrderDavicook = $this->getOrderDavicook($request);
            $dataReturnOrderDavicorp = $this->getOrderDavicorp($request);
            $dataOrderMerge = $dataReturnOrderDavicorp->mergeRecursive($dataReturnOrderDavicook);
            $sorted = $dataOrderMerge->sortBy(['created_at']);
            if($pageNum && $pageSize){
                $data = $this->paginate($sorted,$pageSize,$pageNum);
            }else{
                $data = $this->paginate($sorted);
            }
            return $this->responseSuccess($data);
        } catch (\Throwable $e) {
            Log::error($e);
            return $this->responseError([], Response::HTTP_BAD_REQUEST, 'Lỗi mạng. Vui lòng thử lại!');
        }
    }
    public function paginate($items, $perPage =null, $page = null, $options = ['path' => ''])
    {
        $perPage = $perPage ?? self::DEFAULT_PAGE_SIZE;
        $page = $page ?? self::DEFAULT_PAGE_NUM;
        $items = $items instanceof Collection ? $items : Collection::make($items);
        return new LengthAwarePaginator($items->forPage($page, $perPage), $items->count(), $perPage, $page, $options);
    }
    public function createOrderImport(Request $request){
        $warehouse = $request['warehouse_id'];
        $ids = new Collection($request['ids'] ?? []);
        $dataReturnDavicook = ShopDavicookOrderReturnHistory::whereIn('id', $ids)->get();
        $dataReturnDavicorp = ShopOrderReturnHistory::whereIn('id', $ids)->get();
        $dataReturns = $dataReturnDavicook->merge($dataReturnDavicorp);
        if($warehouse){
            try{
                $wareHouse = AdminWarehouse::where('id', $warehouse)->first();
                $dataInsert = [
                    'id' => sc_uuid(),
                    'id_name' => ShopGenId::genNextId('order_import'),
                    'supplier_name' => 'Nhập hàng từ đơn trả',
                    'warehouse_id' => $wareHouse->id,
                    'warehouse_code' => $wareHouse->warehouse_code,
                    'warehouse_name' => $wareHouse->name,
                    'delivery_date' => now(),
                    'reality_delivery_date' => null,
                    'total' => 0,
                    'total_reality' => 0,
                    'status' => 1,
                    'edit' => 0,
                    'note' => null,
                    'type_import' => 4,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
                $newImport = AdminImport::create($dataInsert);
                $dataInsertDetail = [];
                foreach ($dataReturns as $dataReturn){
                    $dataInsertDetail[] = array(
                        'id' => sc_uuid(),
                        'import_id' => $newImport->id,
                        'import_id_name' => $newImport->id_name,
                        'product_id' => $dataReturn->product_id,
                        'product_code' => $dataReturn->product_code,
                        'product_name' => $dataReturn->product_name,
                        'category_id' => $dataReturn->category_id,
                        'product_kind' => $dataReturn->product_kind,
                        'qty_order' => $dataReturn->qty_not_import,
                        'qty_reality' => $dataReturn->qty_not_import,
                        'customer_id' => $dataReturn->customer_id,
                        'customer_code' => $dataReturn->customer_code,
                        'customer_name' => $dataReturn->customer_name,
                        'unit_id' => '',
                        'unit_name' => $dataReturn->product_unit,
                        'product_price' => 0,
                        'amount' => 0,
                        'amount_reality' => 0,
                        'comment' => '',
                        'created_at' => now(),
                        'updated_at' => now(),
                    );
                }
                AdminImportDetail::insert($dataInsertDetail);
                return $this->responseSuccess([], Response::HTTP_OK, 'Tạo phiếu nhập thành công!');
            }catch (\Throwable $e) {
                return $this->responseError([], Response::HTTP_BAD_REQUEST, 'Lỗi mạng. Vui lòng thử lại!');
            }
        }else{
            return $this->responseError([], Response::HTTP_BAD_REQUEST, 'Bạn chưa chọn kho nhập hàng!');
        }
    }


}
