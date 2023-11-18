<?php

namespace App\Admin\Controllers\Warehouse\Report;


use App\Admin\Models\AdminExport;
use App\Admin\Models\AdminExportDetail;
use App\Admin\Models\AdminReportInventory;
use App\Admin\Models\AdminReportWarehouseProductStock;
use App\Admin\Models\AdminWarehouse;
use App\Exports\Warehouse\Report\OrderExport\ExcelOrderExport;
use App\Exports\Warehouse\Report\ProductStock\ReportProductDeptExcel;
use Maatwebsite\Excel\Facades\Excel;
use SCart\Core\Admin\Controllers\RootAdminController;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class AdminReportWarehouseProductStockController extends RootAdminController
{
    private $numPaginate;
    public function __construct()
    {
        $this->numPaginate = 30;
        parent::__construct();
    }

    /**
     * Report two targets.
     */
    public function index(){
        $data = [
            'title' => "Báo cáo nhập xuất tồn",
            'icon' => 'fa fa-indent',
            'removeList' => 1,
        ];

        $listTh = [
            'STT' => 'STT',
            'product_code' => 'Mã sản phẩm',
            'product_name' => 'Tên sản phẩm',
            'qty_stock_first' => 'Tồn đầu kỳ',
            'value_stock_first' => 'Giá trị đầu kỳ',
            'qty_import' => 'SL nhập',
            'value_import' => 'Giá trị nhập',
            'qty_export' => 'SL xuất',
            'value_export' => 'Giá trị xuất',
            'qty_stock_final' => 'Tồn cuối kỳ',
            'value_stock_final' => 'Giá trị cuối kỳ',
        ];

        $dataSearch = [
            'keyword' => sc_clean(request('keyword') ?? ''),
            'date_start' => sc_clean(request('date_start') ?? nowDateString()),
            'date_end' => sc_clean(request('date_end') ?? nowDateString()),
            'warehouse' => sc_clean(request('warehouse') ?? []),
            'product_kind' => sc_clean(request('product_kind') ?? ''),
            'key_search' => sc_clean(request('key_search') ?? ''),
        ];
        $cssTh = [
            'STT' => 'text-align: center; min-width: 60px; max-width:60px; padding-left: 0px; padding-right: 0px',
            'product_code' => 'text-align: center; width: 7%',
            'product_name' => 'text-align: center; width: 15%',
            'qty_stock_first' => 'text-align: center; width: 7%',
            'value_stock_first' => 'text-align: center; width: 13%',
            'qty_import' => 'text-align: center; width: 7%',
            'value_import' => 'text-align: center; width: 13%',
            'qty_export' => 'text-align: center; width: 7%',
            'value_export' => 'text-align: center; width: 13%',
            'qty_stock_final' => 'text-align: center; width: 7%',
            'value_stock_final' => 'text-align: center; width: 13%',
        ];
        $cssTd = [
            'STT' => 'text-align: center',
            'product_code' => 'text-align: center',
            'product_name' => '',
            'qty_stock_first' => 'text-align: center',
            'value_stock_first' => 'text-align: center',
            'qty_import' => 'text-align: center',
            'value_import' => 'text-align: center',
            'qty_export' => 'text-align: center',
            'value_export' => 'text-align: center',
            'qty_stock_final' => 'text-align: center',
            'value_stock_final' => 'text-align: center',
        ];

        $data['cssTh'] = $cssTh;
        $data['cssTd'] = $cssTd;
        if (!empty($dataSearch['key_search'])) {
            $dataOrderMerge = $this->getDataSearch($dataSearch);
        } else {
            $dataOrderMerge = new Collection();
        }
        $dataOrderMerge = $dataOrderMerge->groupBy('product_id');
        $countData = 0;
        $dataTr = [];
        $i = 1;
        $dataOrderMergePaginate = $this->paginate($dataOrderMerge);
        foreach ($dataOrderMergePaginate as $keyProduct => $row) {
            $dataTr[$keyProduct] = [
                'STT' => $i++,
                'product_code' => $row->first()->product_code,
                'product_name' => $row->first()->product_name,
                'qty_stock_first' => $row->first()->qty_stock,
                'value_stock_first' => 'Check BA',
                'qty_import' => $row->sum('qty_import'),
                'value_import' => 'Check BA',
                'qty_export' => $row->sum('qty_export'),
                'value_export' => 'Check BA',
                'qty_stock_final' => $row->last()->qty_stock,
                'value_stock_final' => 'Check BA',
            ];
        }

        $dataWarehouse = AdminWarehouse::get();
        $page = request('page') ?? 1;
        $data['dataTr'] = $dataTr;
        $ofsetStart = ($page - 1) * ($this->numPaginate);
        $ofsetEnd = ($page - 1) * ($this->numPaginate) + count($dataOrderMergePaginate);
        $data['ofsetEnd'] = $ofsetEnd;
        $data['ofsetStart'] = $ofsetStart;
        $data['listTh'] = $listTh;
        $data['dataWarehouse'] = $dataWarehouse;

        $optionWarehouse = '';
        foreach ($dataWarehouse as $key => $item) {
            $optionWarehouse .= '<option  ' . ( in_array($item->id, $dataSearch['warehouse']) ? "selected" : "" ) . ' value="' . $item->id . '">' . $item->name . '</option>';
        }

        $data['pagination'] = $dataOrderMergePaginate->appends(request()->except(['_token', '_pjax']))->links($this->templatePathAdmin . 'component.pagination');
        $data['resultItems'] = sc_language_render('admin.result_item', ['item_from' => $ofsetStart ? $ofsetStart : 0, 'item_to' =>(empty($check)) ? $ofsetEnd : count($dataOrderMerge), 'total' => count($dataOrderMerge)]);
        //menuRight
        $data['menuRight'][] = '<a class="btn btn-success btn-flat" title="" id="button_export">
                                <i class="fa fa-file-export" title="Xuất Excel"></i> Xuất Excel</a> &nbsp;
                                <a href="javascript:void(0)" class="btn btn-flat btn-info" onclick="savePdf()"><i class="fas fa-file-pdf"></i>&nbsp;Xuất PDF</a>&nbsp;&nbsp;
                                 ';
        //=menuRight
        $data['topMenuRight'][] = '
                <form action="' . sc_route_admin('warehouse_product_stock.index') . '" id="button_search">
                <input type="hidden" name="key_search" value="search" id="key_search">
                <input type="hidden" name="data_count" value="' . $countData . '" id="data_count">
                <br>
                    <div class="">
                        <div class="row">
                            <div class="col-lg-2 col-md-2 col-sm-6">
                                <div class="form-group" >
                                    <label>Từ ngày</label>
                                    <div class="input-group " >
                                        <input type="text" name="date_start" id="date_start" class="form-control input-sm date_time rounded-0" style="text-align: center" placeholder="Chọn ngày" value="' . $dataSearch['date_start'] . '" />
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-2 col-md-2 col-sm-6">
                                <div class="form-group">
                                    <label>Đến ngày</label>
                                    <div class="input-group" >
                                        <input type="text" name="date_end" id="date_end" class="form-control input-sm date_time rounded-0" style="text-align: center" placeholder="Chọn ngày" value="' . $dataSearch['date_end'] . '"  />
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-2 col-md-2 col-sm-12">
                                <div class="form-group">
                                    <label>Chọn kho</label>
                                    <div class="input-group">
                                        <select class="form-control select-custom" name="warehouse[]" id="warehouse" style="width: 100%" multiple="multiple">
                                            ' . $optionWarehouse . '
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-2 col-md-2 col-sm-12">
                                <div class="form-group">
                                    <label>Loại mặt hàng</label>
                                    <div class="input-group">
                                        <select class="form-control" name="product_kind" id="product_kind" style="width: 100%">
                                            <option value="">Tất cả</option>
                                            <option value="0" '.($dataSearch['product_kind'] == 0 ? 'selected' : "").'>Hàng khô</option>
                                            <option value="1" '.($dataSearch['product_kind'] == 1 ? 'selected' : "").'>Hàng tươi</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-2 col-md-2 col-sm-12" style="padding-left: 0px;">
                                <div class="form-group">
                                    <label>Tìm kiếm</label>
                                    <div class="input-group">
                                        <input type="text" name="keyword" id="keyword" class="form-control rounded-0 float-right" placeholder="' . sc_language_render('admin.name_target.search_placeholder') . '" value="' . $dataSearch['keyword'] . '">
                                        <div class="input-group-append">
                                            <button type="submit" class="btn btn-primary  btn-flat" id="submit_report_target"><i class="fas fa-search"></i></button>
                                        </div>
                                    </div>
                                </div>
                            </div>                     
                            
                        </div>
                    </div>
                </form>
                ';

        //=menuSearch
        $data['menuLeft'][] = '
        <div class="form-group">
            <label for="product_kind" style="display: inline-block; width: 70px;">Sắp xếp</label>
            <select class="form-control" name="product_kind" id="product_kind" style="width: 300px; display: inline-block;">
                <option value="">Mặc định</option>
                <option value="0" >Tồn kho tăng dần</option>
                <option value="1" >Tồn kho giảm dần</option>
            </select>
        </div>
        ';
        return view($this->templatePathAdmin . 'screen.warehouse.report.product_stock.index')
            ->with($data);
    }

    public function paginate($items, $perPage = 20, $page = null, $options = ['path' => ''])
    {
        $perPage = $this->numPaginate;
        $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);
        $items = $items instanceof Collection ? $items : Collection::make($items);
        return new LengthAwarePaginator($items->forPage($page, $perPage), $items->count(), $perPage, $page, $options);

    }

    /**
     * Xuất file excel.
     * @return \Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function exportExcel()
    {
        $dataSearch = [
            'keyword' => sc_clean(request('keyword') ?? ''),
            'date_start' => sc_clean(request('date_start') ?? nowDateString()),
            'date_end' => sc_clean(request('date_end') ?? nowDateString()),
            'warehouse' => request('warehouse') != '' ? explode(',', request('warehouse')) : [],
            'product_kind' => sc_clean(request('product_kind') ?? ''),
            'key_search' => sc_clean(request('key_search') ?? ''),
        ];
        $dataOrderMerge = $this->getDataSearch($dataSearch);
        $dataOrderMerge = $dataOrderMerge->groupBy('product_id');
        if (!count($dataOrderMerge) > 0) {
            return redirect()->route('warehouse_product_stock.index')->with('error' , 'Không có dữ liệu!');
        }

        if (count($dataOrderMerge) > 20000) {
            return redirect()->route('warehouse_product_stock.index')->with('error' , 'Dữ liệu quá lớn!');
        }

        $from_to = str_replace("/","_",$dataSearch['date_start']);
        $end_to = str_replace("/","_",$dataSearch['date_end']);
        $fileName = 'BCNHAPXUATTON_'.$from_to.'-'.$end_to. '.xlsx';

        return Excel::download(new ReportProductDeptExcel($dataSearch, $dataOrderMerge), $fileName);
    }

    /**
     * Xuất pdf
     * @return \Illuminate\Http\RedirectResponse|void
     */
    public function printPdf()
    {
        $dataSearch = [
            'keyword' => sc_clean(request('keyword') ?? ''),
            'date_start' => sc_clean(request('date_start') ?? nowDateString()),
            'date_end' => sc_clean(request('date_end') ?? nowDateString()),
            'warehouse' => request('warehouse') != '' ? explode(',', request('warehouse')) : [],
            'product_kind' => sc_clean(request('product_kind') ?? ''),
            'key_search' => sc_clean(request('key_search') ?? ''),
        ];
        $dataOrderMerge = $this->getDataSearch($dataSearch);
        $dataOrderMerge = $dataOrderMerge->groupBy('product_id');
        if (!count($dataOrderMerge) > 0) {
            return redirect()->route('warehouse_product_stock.index')->with('error' , 'Không có dữ liệu!');
        }

        if (count($dataOrderMerge) > 20000) {
            return redirect()->route('warehouse_product_stock.index')->with('error' , 'Dữ liệu quá lớn!');
        }

        $html = view($this->templatePathAdmin . 'screen.warehouse.report.product_stock.print_pdf_template')
            ->with(['data' => $dataOrderMerge, 'dataSearch' => $dataSearch])->render();

        $html = mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8');
        return $html;
    }

    /**
     * Lấy data
     * @param $dataSearch
     * @return mixed
     */
    private function getDataSearch($dataSearch) {
        $dataProductStock = new AdminReportWarehouseProductStock();
        if ($dataSearch['date_start']) {
            $dateStartCustom = convertVnDateObject($dataSearch['date_start'])->toDateString();
            $dataProductStock = $dataProductStock->whereDate('date_action', '>=', $dateStartCustom);
        }
        if ($dataSearch['date_end']) {
            $dateEndCustom = convertVnDateObject($dataSearch['date_end'])->toDateString();
            $dataProductStock = $dataProductStock->whereDate('date_action', '<=', $dateEndCustom);
        }
    
        if ($dataSearch['warehouse']) {
            $dataProductStock = $dataProductStock->whereIn('warehouse_id', $dataSearch['warehouse']);
        }
    
        if ($dataSearch['product_kind'] !== '') {
            $dataProductStock = $dataProductStock->where('product_kind', $dataSearch['product_kind']);
        }
    
        if ($dataSearch['keyword']) {
            $keyword = $dataSearch['keyword'];
            $dataProductStock =$dataProductStock->where(function ($sql) use ($keyword) {
                $sql->where('product_name', 'like', '%' . $keyword . '%');
                $sql->orWhere('product_code', 'like', '%' . $keyword . '%');
            });
        }
    
        $data = $dataProductStock->orderBy('date_action', 'asc')->get();

        return $data;
    }
    
}
