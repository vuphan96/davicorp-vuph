<?php

namespace App\Admin\Api\Warehouse\Admin;

use App\Admin\Api\ApiController;
use App\Admin\Models\ReportWarehouseProductDept;
use App\Exports\DavicorpOrder\AdminExportMultipleSheet;
use App\Exports\Warehouse\Report\ProductDept\ReportProductDeptExcel;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class ReportProductDept extends ApiController
{
    const DEFAULT_PAGE_NUM = 1;
    const DEFAULT_PAGE_SIZE = 30;

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getReportProductDept(Request $request)
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
            $dataSearch = [
                'date_start' => $request->date_start,
                'date_end' => $request->date_end,
                'category_id' => $request->category_id,
                'product_kind' => $request->product_kind,
                'customer_id' => $request->customer_id ?? [],
                'keyword' => $request->keyword,
            ];
            $data = $this->getDataBySearch($dataSearch)->paginate($perPage = $pageSize, $columns = ['*'], $pageName = 'page_num', $page = $pageNum);
            return $this->responseSuccess($data);
        } catch (\Throwable $e) {
            Log::error($e);
            return $this->responseError([], Response::HTTP_BAD_REQUEST, 'Lỗi mạng. Vui lòng thử lại!');
        }
    }

    public function exportExcelReportProductDept(Request $request)
    {
        $dataSearch = [
            'date_start' => $request->date_start,
            'date_end' => $request->date_end,
            'category_id' => $request->category_id,
            'product_kind' => $request->product_kind,
            'customer_id' => $request->customer_id ?? [],
            'keyword' => $request->keyword,
        ];
        $dataOrderMerge = $this->getDataBySearch($dataSearch)->get();
        if (!count($dataOrderMerge) > 0) {
            return $this->responseError([], Response::HTTP_BAD_REQUEST,'Lỗi dữ liệu!');
        }
        if (count($dataOrderMerge) > 20000) {
            return $this->responseError([], Response::HTTP_BAD_REQUEST, 'Dữ liệu quá tải!');
        }
        $from_to = str_replace("/","_",$dataSearch['date_start'] ?? now());
        $end_to = str_replace("/","_",$dataSearch['date_end'] ?? now());
        $fileName = 'BCNOHANG_'.$from_to.'-'.$end_to. '.xlsx';
        try{
            return Excel::download(new ReportProductDeptExcel($dataSearch, $dataOrderMerge), $fileName);
        } catch (\Throwable $e){
            Log::warning($e->getMessage());
            return $this->responseError([], Response::HTTP_BAD_REQUEST, $e->getMessage());
        }

    }

    private function getDataBySearch($dataSearch)
    {
        $customer_id = $dataSearch['customer_id'];
        $date_start = $dataSearch['date_start'];
        $date_end = $dataSearch['date_end'];
        $category_id = $dataSearch['category_id'];
        $product_kind = $dataSearch['product_kind'];
        $keyword = $dataSearch['keyword'];
        $query = new ReportWarehouseProductDept();
        if(!empty($customer_id)){
            $query = $query->whereIn('customer_code', $customer_id);
        }
        if(!empty($category_id)){
            $query = $query->where('cateogry_id', $category_id);
        }
        if(!empty($date_start)){
            $query = $query->whereDate('export_date', '>=', $date_start);
        }
        if(!empty($date_end)){
            $query = $query->whereDate('export_date', '<=', $date_end);
        }
        if(!empty($product_kind)){
            $query = $query->where('product_kind', $product_kind);
        }
        if(!empty($keyword)){
            $query = $query->where(function ($sql) use ($keyword) {
                $sql->where('product_name', 'like', '%' . $keyword . '%');
                $sql->orWhere('product_code', 'like', '%' . $keyword . '%');
            });
        }
        return $query;
    }
}