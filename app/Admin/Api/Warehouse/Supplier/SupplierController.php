<?php

namespace App\Admin\Api\Warehouse\Supplier;

use App\Admin\Api\ApiController;
use App\Admin\Models\AdminImport;
use App\Admin\Models\AdminImportDetail;
use App\Admin\Models\AdminImportHistory;
use App\Front\Models\ShopSupplier;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class SupplierController extends ApiController
{
    const DEFAULT_PAGE_NUM = 1;
    const DEFAULT_PAGE_SIZE = 20;
    public function getOrderImport(Request $request)
    {   
        $pageNum = $request->get("page_num");
        if (!isset($pageNum)){
            $pageNum = self::DEFAULT_PAGE_NUM;
        }
        $pageSize = $request->get("page_size");
        if (!isset($pageSize)){
            $pageSize = self::DEFAULT_PAGE_SIZE;
        }
        $user = $request->user();
        $supplier_id = $user->id;
        try {
            $date_start = $request->date_start ? date($request->date_start) : '';
            $date_end = $request->date_end ? date($request->date_end) : '';
            $keyword = $request->keyword;
            $order_import = AdminImport::where('supplier_id', $supplier_id);
            if ($request->date_from){
                $order_import = $order_import->whereDate('delivery_date', '>=', $date_start);
            }
            if ($request->date_to){
                $order_import = $order_import->whereDate('delivery_date', '<=', $date_end);
            }
            if ($keyword) {
                $order_import = $order_import->where(function ($q) use ($keyword){
                    $q->where('id_name', 'like', '%' . $keyword . '%');
                });
            }
            $order_import = $order_import->orderBy('delivery_date', 'DESC')->orderBy('created_at', 'DESC');
            $data = $order_import->paginate($perPage = $pageSize, $columns = ['*'], $pageName = 'page_num', $page = $pageNum);
            return $this->responseSuccess($data);
        } catch (\Throwable $e) {
            Log::error($e);
            return $this->responseError([], Response::HTTP_BAD_REQUEST, 'Lỗi mạng. Vui lòng thử lại!');
        }
    }

    public function getOrderImportDetail($id)
    {
        $orderImport = AdminImport::with('details', 'history')->find($id);
        if ($orderImport) {
            return $this->responseSuccess($orderImport);
        }
        return $this->responseError([], Response::HTTP_BAD_REQUEST, 'Không tìm thấy đơn hàng này!');
    }
    public function confirmedOrder(Request $request, $id)
    {
        try {
            $import = AdminImport::find($id);
            if (!$import) {
                return $this->responseError([], Response::HTTP_BAD_REQUEST, 'Không tìm thấy đơn nhập này!');
            }
            if ($import->status != 3) {
                $import->status = 2;
                $import->save();
                return $this->responseError([], Response::HTTP_BAD_REQUEST, 'Đơn nhập hàng đã được xác nhận');
            }else{
                return $this->responseError([], Response::HTTP_BAD_REQUEST, 'Lỗi mạng. Vui lòng thử lại!');
            }
        } catch (\Throwable $e) {
            Log::error($e);
            return $this->responseError([], Response::HTTP_BAD_REQUEST, $e->getMessage());
        }
    }

    public function saveSignature(Request $request, $id)
    {
        try {
            $orderImport = AdminImport::find($id);
            if ($orderImport && $orderImport->status == 2) {
                $file = $request->file('signature_data');
                if ($file && $file->isValid() && starts_with($file->getMimeType(), 'image/')) {
                    $filename = 'signature_' . uniqid() . '.' . $file->getClientOriginalExtension();
                    Storage::put('storage/' . $filename, $file);
                    $orderImport->update([
                        'status' => 3
                    ]);
                    return $this->responseSuccess([], Response::HTTP_OK, 'Lưu chữ kí thành công');
                } else {
                    return $this->responseError([], Response::HTTP_BAD_REQUEST, 'Bạn chưa xác nhận bằng chữ kí');
                }
            } else {
                return $this->responseError([], Response::HTTP_BAD_REQUEST, 'Đơn nhập chưa xác nhận hoặc đã xử lý trước đó');
            }
        } catch (\Exception $e) {
            return $this->responseError([], Response::HTTP_BAD_REQUEST, 'Có lỗi xảy ra khi lưu chữ kí: ' . $e->getMessage());
        }
    }
}
