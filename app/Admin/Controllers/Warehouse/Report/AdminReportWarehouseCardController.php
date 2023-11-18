<?php

namespace App\Admin\Controllers\Warehouse\Report;


use App\Admin\Models\AdminExport;
use App\Admin\Models\AdminExportDetail;
use App\Admin\Models\AdminWarehouse;
use App\Admin\Models\ReportWarehouseCard;
use App\Exports\Warehouse\Report\ReportWarehouseCard\ExcelWarehouseCardExport;
use App\Front\Models\ShopCategory;
use App\Front\Models\ShopDepartment;
use App\Front\Models\ShopOrder;
use App\Front\Models\ShopProduct;
use App\Front\Models\ShopSupplier;
use App\Front\Models\ShopZone;
use Maatwebsite\Excel\Facades\Excel;
use SCart\Core\Admin\Controllers\RootAdminController;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class AdminReportWarehouseCardController extends RootAdminController
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
            'title' => "Báo cáo thẻ kho",
            'icon' => 'fa fa-indent',
            'removeList' => 1,
        ];

        $listTh = [
            'STT' => 'STT',
            'bill_date' => 'Ngày chứng từ',
            'product_code' => 'Mã SP',
            'product_name' => 'Tên SP',
            'order_id_name' => 'Mã phiếu',
            'explain' => 'Diễn giải',
            'qty_import' => 'Số lượng nhập',
            'qty_export' => 'Số lượng xuất',
            'qty_stock' => 'Sô lượng tồn kho',
            'object_name' => 'Đối tượng'
        ];

        $dataSearch = [
            'keyword' => sc_clean(request('keyword') ?? ''),
            'explain' => sc_clean(request('explain') ?? ''),
            'type_order' => sc_clean(request('type_order') ?? ''),
            'from_to' => sc_clean(request('from_to') ?? ''),
            'end_to' => sc_clean(request('end_to') ?? ''),
            'product_id' => sc_clean(request('product_id') ?? ''),
            'supplier_id' => sc_clean(request('supplier_id') ?? ''),
            'warehouse' => sc_clean(request('warehouse') ?? []),
            'key_search' => sc_clean(request('key_search') ?? [])
        ];
        $dataTr = [];
        $cssTh = [
            'STT' => 'max-width:50px;',
            'bill_date' => 'min-width: 60px; max-width:90px;',
            'product_code' => 'text-align: center; min-width: 60px; max-width:60px;',
            'product_name' => 'min-width: 150px',
            'order_id_name' => 'text-align: center; min-width: 60px; max-width:100px; ',
            'explain' => 'text-align: center; min-width: 60px; max-width:100px;',
            'qty_import' => 'text-align: right; min-width: 60px; max-width:120px; ',
            'qty_export' => 'text-align: right; min-width: 60px; max-width:120px;',
            'qty_stock' => 'text-align: right; min-width: 60px; max-width:120px;',
            'object_name' => 'min-width: 150px',
        ];
        $cssTd = [
            'STT' => 'text-align: center;',
            'bill_date' => '',
            'product_code' => 'text-align: center',
            'product_name' => ' ',
            'order_id_name' => 'text-align: center',
            'explain' => 'text-align: center;',
            'qty_import' => 'text-align: right;',
            'qty_export' => 'text-align: right; ',
            'qty_stock' => 'text-align: right; ',
            'object_name' => 'text-align: left;',
        ];

        $data['cssTh'] = $cssTh;
        $data['cssTd'] = $cssTd;
        $stt = 0;
        if (!empty($dataSearch['key_search'])) {
            $dataTmp = ReportWarehouseCard::getListWarehouseCard($dataSearch)->paginate(config('pagination.admin.medium'));
            $stt = $dataTmp->firstItem();
        } else {

            $dataTmp = $this->paginate(new Collection());
        }
        $productStockBegin = ReportWarehouseCard::getListWarehouseCard($dataSearch, 1)->paginate(config('pagination.admin.medium'))->first();


        $qtyImportAmount = $dataTmp->sum('qty_import');
        $qtyExportAmount = $dataTmp->sum('qty_export');
        $qtyStockAmount = $dataTmp->sum('qty_stock');
        foreach ($dataTmp as $key => $row) {
            $dataTr[$row->id ?? ''] = [
                'STT' => $stt ?? '',
                'bill_date' => $row->bill_date ?? '',
                'product_code' => $row->product_code ?? '',
                'product_name' => $row->product_name ?? '',
                'order_id_name' => $row->order_id_name ?? '',
                'explain' => $row->explain ?? '',
                'qty_import' => $row->qty_import ?? '',
                'qty_export' => $row->qty_export ?? '',
                'qty_stock' => $row->qty_stock ?? '',
                'object_name' => $row->object_name ?? '',
            ];
            $stt++;
        }
        $data['dataTr'] = $dataTr;
        $data['listTh'] = $listTh;
        $data['qtyImportAmount'] = number_format($qtyImportAmount, 2) ?? '' ;
        $data['qtyExportAmount'] = number_format($qtyExportAmount, 2) ?? '' ;
        $data['qtyStockAmount'] = number_format($qtyStockAmount,2) ?? '' ;
        $data['qtyProductStockBegin'] = $dataSearch['product_id'] ? ($productStockBegin->qty_stock ?? 0) : 0;

        $optionWarehouse = '';
        $dataWarehouse = AdminWarehouse::get();
        foreach ($dataWarehouse as $key => $item) {
            $optionWarehouse .= '<option  ' . ( in_array($item->id, $dataSearch['warehouse']) ? "selected" : "" ) . ' value="' . $item->id . '">' . $item->name . '</option>';
        }

        $typeOrder = ReportWarehouseCard::$TYPE_ORDER;
        $optionTypeOrder = '';
        foreach ($typeOrder as $key => $value) {
            $optionTypeOrder .= '<option  ' .(($dataSearch['type_order'] == $key) ? "selected" : "") . ' value="' . $key . '">' . $value . '</option>';
        }
        $explain = ShopOrder::$NOTE;
        $optionExplain = '';
        foreach ($explain as $key => $value) {
            $optionExplain .= '<option  ' . (($dataSearch['explain'] == $value) ? "selected" : "") . ' value="' . $value . '">' . $value . '</option>';
        }
        $product = ShopProduct::where('status', 1)->get();
        $optionProduct = '';
        foreach ($product as $key => $value) {
            $optionProduct .= '<option  ' . (($dataSearch['product_id'] == $value->id) ? "selected" : "") . ' value="' . $value->id . '">' . $value->name . '</option>';
        }
        $supplier = ShopSupplier::where('status', 1)->get();
        $optionSupplier = '';
        foreach ($supplier as $key => $value) {
            $optionSupplier .= '<option  ' . (($dataSearch['supplier_id'] == $value->id) ? "selected" : "") . ' value="' . $value->id . '">' . $value->name . '</option>';
        }
        $currentDay = nowDateString();
        $from_day = $dataSearch['from_to'] ? $dataSearch['from_to'] : $currentDay ;
        $end_day = $dataSearch['end_to'] ? $dataSearch['end_to'] : $currentDay ;

        $data['pagination'] = $dataTmp->appends(request()->except(['_token', '_pjax']))->links($this->templatePathAdmin . 'component.pagination');
        $data['resultItems'] = sc_language_render('admin.result_item', ['item_from' => $dataTmp->firstItem(), 'item_to' => $dataTmp->lastItem(), 'total' => $dataTmp->total()]);
        //menuRight
        $data['menuRight'][] = '<a data-perm="warehouse_card:export" class="btn btn-success btn-flat" title="" id="button_export_filter">
                                <i class="fa fa-file-export" title="Xuất Excel"></i> Xuất Excel</a> &nbsp;
                                <a data-perm="warehouse_card:print" href="javascript:void(0)" class="btn btn-flat btn-info" onclick="savePdf()"><i class="fas fa-file-pdf"></i>&nbsp;Xuất PDF</a>&nbsp;&nbsp;
                                 ';
        //=menuRight
        $data['topMenuRight'][] = '
                <form action="' . sc_route_admin('warehouse_card_report.index') . '" id="button_search">
                <input type="hidden" name="key_search" value="search" id="key_search">
                <br>
                    <div class="">
                        <div class="row">
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
                            <div class="col-lg-2 col-md-2 col-sm-6">
                                <div class="form-group" >
                                    <label>Từ ngày</label>
                                    <div class="input-group " >
                                        <input type="text" name="from_to" id="from_to" class="form-control input-sm date_time rounded-0" style="text-align: center" placeholder="Chọn ngày" value="' . $from_day . '" />
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-2 col-md-2 col-sm-6">
                                <div class="form-group">
                                    <label>Đến ngày</label>
                                    <div class="input-group" >
                                        <input type="text" name="end_to" id="end_to" class="form-control input-sm date_time rounded-0" style="text-align: center" placeholder="Chọn ngày" value="' . $end_day . '"  />
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-2 col-md-2 col-sm-12">
                                <div class="form-group">
                                    <label>Loại phiếu</label>
                                    <div class="input-group">
                                        <select class="form-control select2" name="type_order" id="type_order" style="width: 100%">
                                            <option value="">Chọn phiếu</option>
                                            '.$optionTypeOrder.'
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-2 col-md-2 col-sm-12">
                                <div class="form-group">
                                    <label>Diễn giải</label>
                                    <div class="input-group">
                                        <select class="form-control select2 " name="explain" id="explain" style="width: 100%">
                                            <option value="">Tất cả</option>
                                            '.$optionExplain.'
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-2 col-md-2 col-sm-12">
                                <div class="form-group">
                                    <label>Sản phẩm</label>
                                    <div class="input-group">
                                        <select class="form-control select2" name="product_id" id="product_id" style="width: 100%">
                                            <option value="">Tất cả</option>
                                            '.$optionProduct.'
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-2 col-md-2 col-sm-12">
                                <div class="form-group">
                                    <label>Nhà cung cấp</label>
                                    <div class="input-group">
                                        <select class="form-control select2" name="supplier_id" id="supplier_id" style="width: 100%" >
                                            <option value="">Tất cả</option>
                                            '.$optionSupplier.'
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-2 col-md-2 col-sm-12" style="padding-left: 0px;">
                                <div class="form-group">
                                    <label>Tìm kiếm</label>
                                    <div class="input-group">
                                        <input type="text" name="keyword" id="keyword" class="form-control rounded-0 float-right" placeholder="khách hàng, mã khách hàng" value="' . $dataSearch['keyword'] . '">
                                        <div class="input-group-append">
                                            <button type="submit" class="btn btn-primary  btn-flat" id="submit_report"><i class="fas fa-search"></i></button>
                                        </div>
                                    </div>
                                </div>
                            </div>                     
                            
                        </div>
                    </div>
                </form>
                ';

        //=menuSearch
        return view($this->templatePathAdmin . 'screen.warehouse.report.warehouse_card.index')
            ->with($data);
    }

    /**
     * Xuất file excel đơn hàng nhập.
     * @return \Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function exportExcel()
    {
        $dataSearch = [
            'keyword' => sc_clean(request('keyword') ?? ''),
            'explain' => sc_clean(request('explain') ?? ''),
            'type_order' => sc_clean(request('type_order') ?? ''),
            'from_to' => sc_clean(request('from_to') ?? ''),
            'end_to' => sc_clean(request('end_to') ?? ''),
            'product_id' => sc_clean(request('product_id') ?? ''),
            'supplier_id' => sc_clean(request('supplier_id') ?? ''),
            'warehouse' => sc_clean(request('warehouse') ?? []),
            'key_search' => sc_clean(request('key_search') ?? [])
        ];
        $dataTmp = ReportWarehouseCard::getListWarehouseCard($dataSearch)->get();
        $dataStockBegin = ReportWarehouseCard::getListWarehouseCard($dataSearch, 1)->first();
        $qtyStockBegin = $dataStockBegin ? $dataStockBegin->qty_stock : 0;
        if (!count($dataTmp) > 0) {
            return redirect()->route('warehouse_card_report.index')->with('error' , 'Không có dữ liệu!');
        }

        if (count($dataTmp) > 20000) {
            return redirect()->route('warehouse_card_report.index')->with('error' , 'Dữ liệu quá lớn!');
        }
        $from_to = str_replace("/","_",$dataSearch['from_to']);
        $end_to = str_replace("/","_",$dataSearch['end_to']);
        $fileName = 'BCTHEKHO_'.$from_to.'-'.$end_to. '.xlsx';

        return Excel::download(new ExcelWarehouseCardExport($dataSearch, $dataTmp, $qtyStockBegin), $fileName);
    }

    public function paginate($items, $perPage = 20, $page = null, $options = ['path' => ''])
    {
        $perPage = $this->numPaginate;
        $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);
        $items = $items instanceof Collection ? $items : Collection::make($items);
        return new LengthAwarePaginator($items->forPage($page, $perPage), $items->count(), $perPage, $page, $options);

    }

    /**
     * @return \Illuminate\Http\RedirectResponse|void
     */
    public function printPdf()
    {
        $dataSearch = [
            'keyword' => sc_clean(request('keyword') ?? ''),
            'explain' => sc_clean(request('explain') ?? ''),
            'type_order' => sc_clean(request('type_order') ?? ''),
            'from_to' => sc_clean(request('from_to') ?? ''),
            'end_to' => sc_clean(request('end_to') ?? ''),
            'product_id' => sc_clean(request('product_id') ?? ''),
            'supplier_id' => sc_clean(request('supplier_id') ?? ''),
            'warehouse' => sc_clean(request('warehouse') ?? []),
            'key_search' => sc_clean(request('key_search') ?? [])
        ];
        $dataTmp = ReportWarehouseCard::getListWarehouseCard($dataSearch)->get();
        $dataStockBegin = ReportWarehouseCard::getListWarehouseCard($dataSearch, 1)->first();
        $qtyStockBegin = $dataStockBegin ? $dataStockBegin->qty_stock : 0;
        if (!count($dataTmp) > 0) {
            return redirect()->route('warehouse_card_report.index')->with('error' , 'Không có dữ liệu!');
        }

        if (count($dataTmp) > 20000) {
            return redirect()->route('warehouse_card_report.index')->with('error' , 'Dữ liệu quá lớn!');
        }

        $html = view($this->templatePathAdmin . 'screen.warehouse.report.warehouse_card.print_pdf')
            ->with(['data' => $dataTmp, 'dataSearch' => $dataSearch, 'qty_stock' => $qtyStockBegin ])->render();

        $html = mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8');
        return $html;
    }

    private function getDataExportOrder($dataSearch)
    {
        $tableImportOrder = (new AdminExport())->getTable();
        $tableImportOrderDetail = (new AdminExportDetail())->getTable();
        $data = AdminExport::join($tableImportOrderDetail . ' as iod', function ($join) use ($tableImportOrder) {
            $join->on('iod.export_id', $tableImportOrder.'.id');
        });

        if ($dataSearch['date_start']) {
            $dateStartCustom = convertVnDateObject($dataSearch['date_start'])->toDateString();
            $data= $data->whereDate($tableImportOrder.'.date_export', '>=', $dateStartCustom);
        }

        if ($dataSearch['date_end']) {
            $dateEndCustom = convertVnDateObject($dataSearch['date_end'])->toDateString();
            $data= $data->where($tableImportOrder.'.date_export', '<=', $dateEndCustom);
        }

        if ($dataSearch['warehouse']) {
            $data= $data->whereIn($tableImportOrder.'.warehouse_id', $dataSearch['warehouse']);
        }

        if ($dataSearch['department']) {
            $data= $data->whereIn('iod.department_id', $dataSearch['department']);
        }

        if ($dataSearch['category']) {
            $data= $data->where('iod.category_id', $dataSearch['category']);
        }

        if ($dataSearch['zone']) {
            $data= $data->whereIn('iod.zone_id', $dataSearch['zone']);
        }

        if ($dataSearch['product_kind'] !== '') {
            $data= $data->where('iod.product_kind', $dataSearch['product_kind']);
        }

        if ($dataSearch['keyword']) {
            $keyword = $dataSearch['keyword'];
            $data = $data->where(function ($sql) use ($keyword, $tableImportOrder) {
                $sql->where('iod.product_name', 'like', '%' .$keyword . '%');
                $sql->orWhere('iod.product_sku', 'like', '%' .$keyword . '%');
                ;
            });
        }

        $data = $data->orderBy('product_name')->orderBy('qty_reality')->select(
            'iod.product_id',
            'iod.id as detail_id',
            'iod.product_name',
            'iod.product_sku as product_code',
            'iod.customer_name',
            'iod.customer_code',
            'iod.comment',
            'iod.qty_reality',
            $tableImportOrder . '.warehouse_name'
        )->get();

        return $data;
    }
}
