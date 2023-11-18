<?php

namespace App\Admin\Controllers\Warehouse\Report;

use App\Admin\Models\AdminExport;
use App\Admin\Models\AdminExportDetail;
use App\Admin\Models\AdminImport;
use App\Admin\Models\AdminImportDetail;
use App\Admin\Models\AdminSystemChangeHistory;
use App\Admin\Models\AdminWarehouse;
use App\Admin\Models\AdminWarehouseProduct;
use App\Exports\AdminReportTargetExport;
use App\Admin\Models\AdminCategory;
use App\Admin\Models\AdminDavicookOrder;
use App\Admin\Models\AdminOrder;
use App\Exports\Warehouse\Report\OrderImport\ExportOrderImport;
use App\Front\Models\ShopCategory;
use App\Front\Models\ShopDepartment;
use App\Front\Models\ShopGenId;
use App\Front\Models\ShopSupplier;
use App\Front\Models\ShopZone;
use Dompdf\Dompdf;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use SCart\Core\Admin\Admin;
use SCart\Core\Admin\Controllers\RootAdminController;
use Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use PDF;
use ZipArchive;
use function PHPUnit\Framework\never;

class AdminReportImportOrderController extends RootAdminController
{
    private $orderImport;
    private $numPaginate;
    public function __construct(AdminImport $orderImport)
    {
        $this->orderImport = $orderImport;
        $this->numPaginate = 30;
        parent::__construct();
    }

    /**
     * Report two targets.
     */
    public function index(){
        $data = [
            'title' => "Báo cáo nhập hàng",
            'icon' => 'fa fa-indent',
            'removeList' => 1,
        ];

        $listTh = [
            'STT' => 'STT',
            'code' => 'Mã tương ứng',
            'name' => 'Tên',
            'warehouse' => 'Thuộc kho',
            'qty' => 'Số lượng',
            'price' => 'Giá nhập',
            'amount_reality' => 'Thành tiền',
            'comment' => 'Ghi chú'
        ];

        $dataSearch = [
            'keyword' => sc_clean(request('keyword') ?? ''),
            'date_start' => sc_clean(request('date_start') ?? nowDateString()),
            'date_end' => sc_clean(request('date_end') ?? nowDateString()),
            'category' => sc_clean(request('category') ?? ''),
            'warehouse' => sc_clean(request('warehouse') ?? []),
            'supplier' => sc_clean(request('supplier') ?? ''),
            'zone' => sc_clean(request('zone') ?? []),
            'department' => sc_clean(request('department') ?? []),
            'product_kind' => sc_clean(request('product_kind') ?? ''),
            'key_search' => sc_clean(request('key_search') ?? ''),
        ];
        $dataTr = [];
        $cssTh = [
            'STT' => 'text-align: center; min-width: 60px; max-width:60px; padding-left: 0px; padding-right: 0px',
            'code' => 'text-align: center; width: 9%',
            'name' => 'text-align: center; width: 36%',
            'warehouse' => 'text-align: center; width: 15%',
            'qty' => 'text-align: center; width: 10%',
            'price' => 'text-align: center; width: 12%',
            'amount_reality' => 'text-align: center; width: 15%',
            'comment' => 'text-align: center; width: 25%; min-width: 120px;'
        ];
        $cssTd = [
            'STT' => 'text-align: center',
            'customer_sku' => 'text-align: center',
            'customer_name' => '',
            'qty' => 'text-align: center',
            'note' => ''
        ];

        $data['cssTh'] = $cssTh;
        $data['cssTd'] = $cssTd;
        if (!empty($dataSearch['key_search'])) {
            $dataOrderMerge = $this->getDataImportOrder($dataSearch);
        } else {
            $dataOrderMerge = new Collection();
        }
        $dataTmp = null;
        $dataGroupProductById = [];

        $countData = $dataOrderMerge->count();
        $arr = [];
        foreach ($dataOrderMerge->groupBy(['supplier_id','product_id']) as $keySupplier => $itemBySupplier) {
            foreach ($itemBySupplier as $keyProduct => $value) {
                $dataGroupProductById[$keySupplier][$keyProduct] = $value->sum('qty_reality');
                foreach($value as $k => $item) {
                    $arr[$item->detail_id] = $k + 1;
                }
            }

        }
        $dataOrderMergePaginate = $this->paginate($dataOrderMerge);
        foreach ($dataOrderMergePaginate->groupBy(['supplier_id','product_id']) as $keySupplier => $arrBySupplier) {
            $dataTr[] = [
                'STT' => '',
                'code' => '',
                'name   ' => '<span style="text-transform: uppercase;font-weight: bold">'.($arrBySupplier->first()->first()->supplier_name ?? '' ).'</span>',
                'warehouse' => '',
                'qty' => '',
                'price' => '',
                'amount_reality' => '',
                'comment' => ''
            ];
            foreach ($arrBySupplier as $keyProduct => $arrByProduct) {
                $dataTr[] = [
                    'STT' => '',
                    'code' => '<span style="text-transform: uppercase;font-weight: bold">'.($arrByProduct->first()->product_code ?? '' ).'</span>',
                    'name   ' => '<span style="text-transform: uppercase;font-weight: bold">'.($arrByProduct->first()->product_name ?? '' ).'</span>',
                    'warehouse' => '',
                    'qty' => '<span style="text-transform: uppercase;font-weight: bold">'.($dataGroupProductById[$keySupplier][$keyProduct] ?? 0 ).'</span>',
                    'price' => '',
                    'amount_reality' => '',
                    'comment' => ''
                ];
                foreach ($arrByProduct as $product) {
                    $dataTr[$product->detail_id] = [
                        'STT' => $arr[$product->detail_id],
                        'code' => $product->customer_code ?? '',
                        'name   ' => $product->customer_name == '' ? "Hàng xuất từ kho" : $product->customer_name,
                        'warehouse' => $product->warehouse_name,
                        'qty' => number_format($product->qty_reality, 2),
                        'price' => number_format($product->product_price),
                        'amount_reality' => number_format($product->amount_reality),
                        'comment' => $product->comment,
                    ];
                }
            }
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

        $categories = ShopCategory::all();
        $optionCategories = '';
        foreach ($categories as $key => $category) {
            $optionCategories .= '<option  ' . (($dataSearch['category'] == $category->id) ? "selected" : "") . ' value="' . $category->id . '">' . $category->name . '</option>';
        }
        $optionDepartment = '';
        $datarDepartment = ShopDepartment::get();
        foreach ($datarDepartment as $key => $item) {
            $optionDepartment .= '<option  ' . ( in_array($item->id, $dataSearch['department']) ? "selected" : "" ) . ' value="' . $item->id . '">' . $item->name . '</option>';
        }
        $optionZone = '';
        $datarZone = ShopZone::get();
        foreach ($datarZone as $key => $item) {
            $optionZone .= '<option  ' . ( in_array($item->id, $dataSearch['zone']) ? "selected" : "" ) . ' value="' . $item->id . '">' . $item->name . '</option>';
        }

        $optionSupplier = '';
        $datarSupplier = ShopSupplier::get();
        foreach ($datarSupplier as $key => $item) {
            $optionSupplier .= '<option  ' . ( $dataSearch['supplier'] ? ( $item->id == $dataSearch['supplier']) ? "selected" : "" : "") . ' value="' . $item->id . '">' . $item->name . '</option>';
        }

        $optionWarehouse = '';
        $dataWarehouse = AdminWarehouse::get();
        foreach ($dataWarehouse as $key => $item) {
            $optionWarehouse .= '<option  ' . ( in_array($item->id, $dataSearch['warehouse']) ? "selected" : "" ) . ' value="' . $item->id . '">' . $item->name . '</option>';
        }

        $data['pagination'] = $dataOrderMergePaginate->appends(request()->except(['_token', '_pjax']))->links($this->templatePathAdmin . 'component.pagination');
        $data['resultItems'] = sc_language_render('admin.result_item', ['item_from' => $ofsetStart ? $ofsetStart : 0, 'item_to' =>(empty($check)) ? $ofsetEnd : count($dataOrderMerge), 'total' => count($dataOrderMerge)]);
        //menuRight
        $data['menuRight'][] = '<a class="btn btn-success btn-flat" title="" id="button_export_filter">
                                <i class="fa fa-file-export" title="Xuất Excel"></i> Xuất Excel</a> &nbsp;
                                <a href="javascript:void(0)" class="btn btn-flat btn-info" onclick="savePdf()"><i class="fas fa-file-pdf"></i>&nbsp;Xuất PDF</a>&nbsp;&nbsp;
                                 ';
        //=menuRight
        $data['topMenuRight'][] = '
                <form action="' . sc_route_admin('warehouse_report_import.index') . '" id="button_search">
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
                                    <label>Danh mục</label>
                                    <div class="input-group">
                                        <select class="form-control" name="category" id="category" style="width: 100%">
                                            <option value="">' . sc_language_render('front.categories') . '</option>
                                            ' . $optionCategories . '
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
                            <div class="col-lg-2 col-md-2 col-sm-12">
                                <div class="form-group">
                                    <label>Loại khách hàng</label>
                                    <div class="input-group">
                                        <select class="form-control select-custom" name="department[]" id="department" style="width: 100%" multiple="multiple">
                                            ' . $optionDepartment . '
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-2 col-md-2 col-sm-12">
                                <div class="form-group">
                                    <label>Khu vực</label>
                                    <div class="input-group">
                                        <select class="form-control select-custom" name="zone[]" id="zone" style="width: 100%" multiple="multiple">
                                            ' . $optionZone . '
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-2 col-md-2 col-sm-12">
                                <div class="form-group">
                                    <label>Chọn NCC</label>
                                    <div class="input-group">
                                        <select class="form-control select2" name="supplier" id="supplier" style="width: 100%">
                                            <option value="">Tất cả</option>
                                            ' . $optionSupplier . '
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
        return view($this->templatePathAdmin . 'screen.warehouse.report.order_import.index')
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
            'date_start' => sc_clean(request('date_start') ?? nowDateString()),
            'date_end' => sc_clean(request('date_end') ?? nowDateString()),
            'category' => sc_clean(request('category') ?? ''),
            'warehouse' => request('warehouse') != '' ? explode(',', request('warehouse')) : [],
            'supplier' => sc_clean(request('supplier') ?? ''),
            'zone' => request('zone') != '' ? explode(',', request('zone')) : [],
            'department' => request('department') != '' ? explode(',', request('department')) : [],
            'product_kind' => sc_clean(request('product_kind') ?? ''),
            'key_search' => sc_clean(request('key_search') ?? ''),
        ];
        $dataOrderMerge = $this->getDataImportOrder($dataSearch);
        if (!count($dataOrderMerge) > 0) {
            return redirect()->route('warehouse_report_import.index')->with('error' , 'Không có dữ liệu!');
        }

        if (count($dataOrderMerge) > 20000) {
            return redirect()->route('warehouse_report_import.index')->with('error' , 'Dữ liệu quá lớn!');
        }
        $count = $dataOrderMerge->sum('qty_reality');
        $from_to = str_replace("/","_",$dataSearch['date_start']);
        $end_to = str_replace("/","_",$dataSearch['date_end']);
        $fileName = 'BCNHAPHANG_'.$from_to.'-'.$end_to. '.xlsx';

        return Excel::download(new ExportOrderImport($dataSearch, $dataOrderMerge, $count), $fileName);
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
            'date_start' => sc_clean(request('date_start') ?? nowDateString()),
            'date_end' => sc_clean(request('date_end') ?? nowDateString()),
            'category' => sc_clean(request('category') ?? ''),
            'warehouse' => request('warehouse') != '' ? explode(',', request('warehouse')) : [],
            'supplier' => sc_clean(request('supplier') ?? ''),
            'zone' => request('zone') != '' ? explode(',', request('zone')) : [],
            'department' => request('department') != '' ? explode(',', request('department')) : [],
            'product_kind' => sc_clean(request('product_kind') ?? ''),
            'key_search' => sc_clean(request('key_search') ?? ''),
        ];
        $dataOrderMerge = $this->getDataImportOrder($dataSearch);
        if (!count($dataOrderMerge) > 0) {
            return redirect()->route('warehouse_report_import.index')->with('error' , 'Không có dữ liệu!');
        }

        if (count($dataOrderMerge) > 20000) {
            return redirect()->route('warehouse_report_import.index')->with('error' , 'Dữ liệu quá lớn!');
        }

        $html = view($this->templatePathAdmin . 'screen.warehouse.report.order_import.print_pdf_template')
            ->with(['data' => $dataOrderMerge, 'dataSearch' => $dataSearch, 'count' => count($dataOrderMerge)])->render();

        $html = mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8');
        return $html;
    }

    private function getDataImportOrder($dataSearch)
    {
        $tableImportOrder = (new AdminImport())->table;
        $tableImportOrderDetail = (new AdminImportDetail())->table;
        $data = AdminImport::join($tableImportOrderDetail . ' as iod', function ($join) use ($tableImportOrder) {
            $join->on('iod.import_id', $tableImportOrder.'.id');
        });

        if ($dataSearch['date_start']) {
            $dateStartCustom = convertVnDateObject($dataSearch['date_start'])->toDateString();
            $data= $data->whereDate($tableImportOrder.'.delivery_date', '>=', $dateStartCustom);
        }

        if ($dataSearch['date_end']) {
            $dateEndCustom = convertVnDateObject($dataSearch['date_end'])->toDateString();
            $data= $data->where($tableImportOrder.'.delivery_date', '<=', $dateEndCustom);
        }

        if ($dataSearch['warehouse']) {
            $data= $data->whereIn($tableImportOrder.'.warehouse_id', $dataSearch['warehouse']);
        }

        if ($dataSearch['supplier']) {
            $data= $data->where($tableImportOrder.'.supplier_id', $dataSearch['supplier']);
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
                $sql->orWhere('iod.product_code', 'like', '%' .$keyword . '%');
                ;
            });
        }

        $data = $data->orderBy('supplier_name')->orderBy('product_name')->orderBy('qty_reality')->select(
            'iod.product_id',
            'iod.id as detail_id',
            'iod.product_name',
            'iod.product_code',
            'iod.customer_name',
            'iod.customer_code',
            'iod.product_price',
            'iod.comment',
            'iod.amount_reality',
            'iod.qty_reality',
            $tableImportOrder . '.warehouse_name',
            $tableImportOrder . '.supplier_id',
            $tableImportOrder . '.supplier_name'
        )->get();

        return $data;
    }
}
