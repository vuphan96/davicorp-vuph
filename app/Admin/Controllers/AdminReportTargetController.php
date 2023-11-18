<?php

namespace App\Admin\Controllers;

use App\Admin\Models\AdminExport;
use App\Admin\Models\AdminExportDetail;
use App\Admin\Models\AdminReportWarehouseProductDept;
use App\Admin\Models\AdminSystemChangeHistory;
use App\Admin\Models\AdminWarehouse;
use App\Admin\Models\AdminWarehouseDeptExport;
use App\Admin\Models\AdminWarehouseProduct;
use App\Admin\Models\ReportWarehouseCard;
use App\Exports\AdminReportTargetExport;
use App\Admin\Models\AdminCategory;
use App\Admin\Models\AdminDavicookOrder;
use App\Admin\Models\AdminOrder;
use App\Front\Models\ShopCategory;
use App\Front\Models\ShopDepartment;
use App\Front\Models\ShopGenId;
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

class AdminReportTargetController extends RootAdminController{
    private $orderDavicook;
    private $orderDavicorp;
    public function __construct(AdminDavicookOrder $orderDavicook, AdminOrder $orderDavicorp)
    {
        $this->orderDavicook = $orderDavicook;
        $this->orderDavicorp = $orderDavicorp;
        parent::__construct();
    }

    /**
     * Report two targets.
     */
    public function target(){
        $data = [
            // ,
            'title' => sc_language_render('admin.target.report.title'),
            'subTitle' => '',
            'icon' => 'fa fa-indent',
            'urlDeleteItem' => sc_route_admin('admin_price.delete'),
            'removeList' => '', // 1 - Enable function delete list item
            'buttonRefresh' => '', // 1 - Enable button refresh
            'buttonSort' => '', // 1 - Enable button sort
            'css' => '',
            'js' => '',
            'url_action' => sc_route_admin('admin_price.create'),
        ];

        $listTh = [
            'STT' => 'STT',
            'customer_sku'      => 'Mã khách hàng',
            'customer_name' => 'Tên khách hàng',
            'qty'   => 'Số lượng',
            'note'    => 'Ghi chú'

        ];
        $keyword = sc_clean(request('keyword') ?? '');
        $dataSearch = [
            'keyword' => $keyword,
            'from_to' => sc_clean(request('from_to') ?? ''),
            'end_to' => sc_clean(request('end_to') ?? ''),
            'category' => sc_clean(request('category') ?? ''),
            'key_export' => sc_clean(request('key_export') ?? []),
            'key_search' => sc_clean(request('key_search') ?? ''),
            'key_department' => sc_clean(request('key_department') ?? ''),
            'key_zone' => sc_clean(request('key_zone') ?? ''),

        ];
//        dd($dataSearch);
        $data['menuLeft'] = sc_config_group('menuLeft', Request::route()->getName());
        $dataTr = [];
        $cssTh = [
            'STT' => 'text-align: center; width: 7%',
            'customer_sku' => 'text-align: center; width: 13%',
            'customer_name' => 'text-align: center; width: 30%',
            'qty' => 'text-align: center; width: 10%',
            'note' => 'text-align: center; width: 35%'
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
            $dataOrderMerge = $this->getDataSearch($dataSearch['key_export'], $dataSearch);
        } else {
            $dataOrderMerge = new Collection();
        }
        $dataTmp = null;
        $dataGroupProductById = [];

        $countData = $dataOrderMerge->count();
        setlocale(LC_COLLATE, 'vi_VN.UTF-8', 'vi.UTF-8', 'vi_VN', 'vi');
        $sorted = $dataOrderMerge->sortBy('qty')->sortBy(
            'product_name'
        , SORT_LOCALE_STRING);
        $arr = [];
        foreach ($sorted->groupBy('product_id') as $key => $value) {
            $dataGroupProductById[$key] = $value->sum('qty');
            foreach( $value as $k => $item) {
                $arr[$item['detail_id']] = $k +1;
            }
        }
        $dataOrderMergePaginate = $this->paginate($sorted);
        foreach ($dataOrderMergePaginate->groupBy('product_id') as $key => $row) {
            $number = $arr[$row->first()['detail_id']];
            if($number == 1) {
                $dataTr[] = [
                    'STT' => '',
                    'customer_sku'      => '',
                    'product_name' => '<span style="font-weight: bold; text-transform: uppercase">' . ($row->first()->product_name ?? $row->first()['product_name'] ).'</span>' ?? '',
                    'qty'   => '<span style="font-weight: bold">' . number_format($dataGroupProductById[$key], 2)  .'</span>' ?? '',
                    'note'    => ''
                ];
            }

            foreach ($row as  $keyTmp => $item) {
                $dataTr[] = [
                    'STT' => $arr[$item['detail_id']],
                    'customer_sku'      => $item['customer_code'] ?? sc_language_render('customer.delete'),
                    'customer_name' => $item['customer_name'] ,
                    'qty'   => number_format($item['qty'], 2),
                    'note'    => $item['note'],
                ];
            }
        }

        $page = request('page') ?? 1;
        $data['dataTr'] = $dataTr;

        $ofsetStart = ($page - 1) * config('pagination.search.default');
        $ofsetEnd = ($page - 1) * config('pagination.search.default') + count($dataOrderMergePaginate);
        $data['ofsetEnd'] = $ofsetEnd;
        $data['ofsetStart'] = $ofsetStart;
        $data['listTh'] = $listTh;

        $categories = ShopCategory::all();
        $optionCategories = '';
        foreach ($categories as $key => $category) {
            $optionCategories .= '<option  ' . (($dataSearch['category'] == $category->id) ? "selected" : "") . ' value="' . $category->id . '">' . $category->name . '</option>';
        }
        $typeSearch = [
            1 => 'Mặt hàng Davicorp',
            2 => 'Hàng tươi Davicook',
            3 => 'Hàng khô Davicook',
        ];
        $optionTypeSearch = '';
        foreach ($typeSearch as $key => $value) {
            $optionTypeSearch .= '<option  ' . ( is_array($dataSearch['key_export']) ? ( in_array($key, $dataSearch['key_export']) ? "selected" : "") : "" ) . ' value="' . $key . '">' . $value . '</option>';
        }
        $optionDepartment = '';
        $datarDepartment = ShopDepartment::get();
        foreach ($datarDepartment as $key => $item) {
            $optionDepartment .= '<option  ' . ( $dataSearch['key_department'] ? ( $item->id == $dataSearch['key_department']) ? "selected" : "" : "") . ' value="' . $item->id . '">' . $item->name . '</option>';
        }
        $optionZone = '';
        $datarZone = ShopZone::get();
        foreach ($datarZone as $key => $item) {
            $optionZone .= '<option  ' . ( $dataSearch['key_zone'] ? ( $item->id == $dataSearch['key_zone']) ? "selected" : "" : "") . ' value="' . $item->id . '">' . $item->name . '</option>';
        }

        $data['pagination'] = $dataOrderMergePaginate->appends(request()->except(['_token', '_pjax']))->links($this->templatePathAdmin . 'component.pagination');
        $data['resultItems'] = sc_language_render('admin.result_item', ['item_from' => $ofsetStart ? $ofsetStart : 0, 'item_to' =>(empty($check)) ? $ofsetEnd : count($dataOrderMerge), 'total' => count($dataOrderMerge)]);
        $currentDay = nowDateString();
        $from_day = $dataSearch['from_to'] ? $dataSearch['from_to'] : $currentDay ;
        $end_day = $dataSearch['end_to'] ? $dataSearch['end_to'] : $currentDay ;
        //menuRight
        $data['menuRight'][] = '<a class="btn btn-success btn-flat" title="" id="button_export_filter">
                                <i class="fa fa-file-export" title="' . sc_language_render('category-export') . '"></i> ' . sc_language_render('category-export') . '</a> &nbsp;
                                <a href="javascript:void(0)" class="btn btn-flat btn-info" onclick="savePdf()"><i class="fas fa-file-pdf"></i>&nbsp;' . sc_language_render("admin.report.print_pdf") . '</a>&nbsp;&nbsp;
                                <a class="btn btn-primary btn-flat" onclick="getListExportModal()"  title="" data-toggle="modal"  ><i class="fa fa-file-export" ></i> Xuất kho</a> &nbsp;
                                
                                 ';

        //=menuRight
        $data['topMenuRight'][] = '
                <form action="' . sc_route_admin('admin_report_2target.index') . '" id="button_search">
                <input type="hidden" name="key_search" value="search" id="key_search">
                <input type="hidden" name="data_count" value="' . $countData . '" id="data_count">
                <br>
                    <div class="">
                        <div class="row">
                            <div class="col-lg-2 col-md-2 col-sm-12" style="padding-left: 0px">
                                <div class="form-group">
                                    <label>Mặt hàng</label>
                                    <div class="input-group">
                                        <select id="key_export" class="form-control d-none" name="key_export[]" multiple="multiple">
                                            ' . $optionTypeSearch . '
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-2 col-md-2 col-sm-12">
                                <div class="form-group">
                                    <label>Danh mục</label>
                                    <div class="input-group">
                                        <select class="form-control select2" name="category" id="category" style="width: 100%">
                                            <option value="">' . sc_language_render('front.categories') . '</option>
                                            ' . $optionCategories . '
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
                                    <label>Loại khách hàng</label>
                                    <div class="input-group">
                                        <select class="form-control select2" name="key_department" id="key_department" style="width: 100%">
                                            <option value="">Loại khách hàng</option>
                                            ' . $optionDepartment . '
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-2 col-md-2 col-sm-12">
                                <div class="form-group">
                                    <label>Khu vực</label>
                                    <div class="input-group">
                                        <select class="form-control select2" name="key_zone" id="key_zone" style="width: 100%">
                                            <option value="">Chọn khu vực</option>
                                            ' . $optionZone . '
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-2 col-md-2 col-sm-12" style="padding-left: 0px;">
                                <div class="form-group">
                                    <label>Tìm kiếm</label>
                                    <div class="input-group">
                                        <input type="text" name="keyword" id="keyword" class="form-control rounded-0 float-right" placeholder="' . sc_language_render('admin.name_target.search_placeholder') . '" value="' . $keyword . '">
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
        return view($this->templatePathAdmin . 'screen.report.target.index')
            ->with($data);

    }
    public function getListDataExportModal(){
        $dataSearch = [
            'keyword' => sc_clean(request('keyword') ?? ''),
            'from_to' => sc_clean(request('from_to') ?? ''),
            'end_to' => sc_clean(request('end_to') ?? ''),
            'category' => sc_clean(request('category') ?? ''),
            'key_export' => sc_clean(request('key_export') ?? []),
            'key_search' => sc_clean(request('key_search') ?? ''),
            'key_department' => sc_clean(request('key_department') ?? ''),
            'key_zone' => sc_clean(request('key_zone') ?? ''),
        ];

        if (!empty($dataSearch['key_search'])) {
            $dataSearch['key_search'] = 'searched';
            $dataExportModal = $this->getDataSearchExport($dataSearch['key_export'], $dataSearch);
        } else {
            $dataExportModal = new Collection();
        }
        setlocale(LC_COLLATE, 'vi_VN.UTF-8', 'vi.UTF-8', 'vi_VN', 'vi');
        $dataExportSort = $dataExportModal->sortBy('qty')->sortBy(
            'product_name'
            , SORT_LOCALE_STRING);
        $dataExportTmp = [];
        $i = 0;
        $arrCustomer = [];
        foreach ($dataExportSort->groupBy('customer_code') as $code => $item) {
            $arrCustomer[] = [
                'customer_code' => $code,
                'customer_name' => $item->first()['customer_name']
            ];
        }
        foreach ($dataExportSort->groupBy('product_id') as $key => $row) {
            $qtySum = $row->sum('qty');
            $dataExportTmp[] = [
                'product_id'    => $key,
                'product_code'  => $row->first()['product_code'],
                'product_name'  => $row->first()['product_name'] ?? '',
                'product_unit'  => $row->first()['product_unit'],
                'qty'           => number_format($qtySum, 2),
                'qty_reality'   => number_format($qtySum, 2),
            ];
            foreach ($row as $index => $item) {
                $dataExportTmp[$i]['customer'][$index] =  [
                    'product_id'    => $item['product_id'],
                    'product_code'  => $item['product_code'],
                    'product_name'  => $item['product_name'] ?? '',
                    'product_unit'  => $item['product_unit'],
                    'order_detail_id'  => $item['detail_id'],
                    'order_id'  => $item['order_id'],
                    'id_barcode'  => $item['id_barcode'],
                    'customer_short_name'  => $item['customer_short_name'],
                    'customer_num'  => $item['customer_num'],
                    'order_code'  => $item['order_code'],
                    'customer_name'  => $item['customer_name'],
                    'customer_code'  => $item['customer_code'],
                    'qty'  => number_format($item['qty'], 2),
                    'qty_reality'  => number_format($item['qty'], 2),
                    'note'  => $item['note'] ?? '',
                    'product_kind'  => $item['product_kind'] ?? '',
                    'department_id'  => $item['department_id'] ?? '',
                    'zone_id'  => $item['zone_id'] ?? '',
                    'category_id'  => $item['category_id'] ?? '',
                    'explain'  => $item['explain'] ?? '',
                    'delivery_date'  => $item['delivery_date'],
                    'object_id'  => $item['object_id'] ?? '',
                ];
            }
            $i++;
        }
        $dataWarehouse = AdminWarehouse::get();

        $data = [
            'dataExportTmp' => $dataExportTmp,
            'dataCustomer' => $arrCustomer,
            'dataWarehouse' => $dataWarehouse
        ];
        return response()->json(['data'=>$data, 'error'=>0]);

    }

    /**
     * Export Excel target
     * @param  int  $keyExport
     * @return \Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function storeToStorage()
    {
        $dataSearch = [
            'from_to' => sc_clean(request('from_to') ?? ''),
            'end_to' => sc_clean(request('end_to') ?? ''),
            'keyword' => sc_clean(request('keyword') ?? ''),
            'category' => sc_clean(request('category') ?? ''),
            'keyExport' => sc_clean(request('key_export')) ?? [],
            'key_search' => sc_clean(request('key_search') ?? ''),
            'key_department' => sc_clean(request('key_department') ?? ''),
            'key_zone' => sc_clean(request('key_zone') ?? ''),
        ];

        $keyExport = sc_clean(request('key_export')) ?  explode(',', sc_clean(request('key_export'))) : [];
        $dataOrderMerge = $this->getDataSearch($keyExport, $dataSearch);

        if (!count($dataOrderMerge) > 0) {
            return redirect()->route('admin_report_2target.index')->with('error' , 'Không có dữ liệu');
        }

        if (count($dataOrderMerge) > 50000) {
            return redirect()->route('admin_report_2target.index')->with('error' , 'Dữ liệu quá lớn!');
        }
        ini_set('max_execution_time', 180);
        setlocale(LC_COLLATE, 'vi_VN.UTF-8', 'vi.UTF-8', 'vi_VN', 'vi');
        $sorted = $dataOrderMerge->sortBy('qty')->sortBy(
            'product_name'
            , SORT_LOCALE_STRING);
        $from_to = str_replace("/","_",$dataSearch['from_to']);
        $end_to = str_replace("/","_",$dataSearch['end_to']);

        $path = '/excel/target/file/';
        if (count($sorted) > 7000) {
            $sorted = $sorted->chunk(7000);
            foreach ($sorted as $key => $data) {
                $fileName = 'BC2CHITIEU_File_'.($key+1).'_'.$from_to.'-'.$end_to. '.xlsx';
                Excel::store(new AdminReportTargetExport($dataSearch, $data), $path.$fileName);
            }
        }

        $zip_file = storage_path('app/public/excel/target/BC2ChiTieu.zip');
        Storage::delete('excel/target/BC2ChiTieu.zip');
        $zip = new ZipArchive();

        $arrFile = Storage::allFiles($path);
        if ($zip->open($zip_file, \ZipArchive::CREATE || \ZipArchive::OVERWRITE) === TRUE)
        {
            foreach ($arrFile as $key => $value) {
                $relativeNameInZipFile = basename($value);
                $url = storage_path('app/public/'.$value);
                $zip->addFile($url, $relativeNameInZipFile);
            }

            $zip->close();
        }

        foreach ($arrFile as $value) {
            Storage::delete($value);
        }

        ini_set('max_execution_time', 30);

        return response()->download($zip_file);
    }


    /**
     * Export Excel target
     * @param  int  $keyExport
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAndChunkData()
    {
        $dataSearch = [
            'from_to' => sc_clean(request('from_to') ?? ''),
            'end_to' => sc_clean(request('end_to') ?? ''),
            'keyword' => sc_clean(request('keyword') ?? ''),
            'category' => sc_clean(request('category') ?? ''),
            'keyExport' => sc_clean(request('key_export')) ?? [],
            'key_search' => sc_clean(request('key_search') ?? ''),
            'key_department' => sc_clean(request('key_department') ?? ''),
            'key_zone' => sc_clean(request('key_zone') ?? ''),
        ];

        $keyExport = sc_clean(request('key_export')) ?  explode(',', sc_clean(request('key_export'))) : [];
        $dataOrderMerge = $this->getDataSearch($keyExport, $dataSearch);

        if (!count($dataOrderMerge) > 0) {
            return response()->json(['error' => 1, 'msg' => 'Không có dữ liệu!']);
        }

        if (count($dataOrderMerge) > 50000) {
            return response()->json(['error' => 1, 'msg' => 'Dữ liệu của bạn quá lớn để xuất excel!']);
        }
        ini_set('max_execution_time', 180);
        setlocale(LC_COLLATE, 'vi_VN.UTF-8', 'vi.UTF-8', 'vi_VN', 'vi');
        $sorted = $dataOrderMerge->sortBy('qty')->sortBy(
            'product_name'
            , SORT_LOCALE_STRING);
        $sorted = $sorted->chunk(20000);
        $dataSessionReport = [
            'item' => $sorted,
            'dataSearch' => [
                'from_to' => $dataSearch['from_to'],
                'end_to' => $dataSearch['end_to'],
                'strDate' => date_format(now(), 'dmYHis')
            ]
        ];
        session()->put('dataReportTarget', $dataSessionReport);

        return response()->json(count($sorted));
    }

    /**
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function storeStorageFileExcel()
    {
        $key = request('key');
        $data = session('dataReportTarget') ?? [];
        if ($data['item'][$key]) {
            $from_to = str_replace("/","_",$data['dataSearch']['from_to']);
            $end_to = str_replace("/","_",$data['dataSearch']['end_to']);
            $strTime = $data['dataSearch']['strDate'];
            $fileName = 'BC2CHITIEU_File_'.($key+1).'_'.$from_to.'-'.$end_to.'_'.$strTime.'.xlsx';
            return Excel::download(new AdminReportTargetExport($data['dataSearch'], $data['item'][$key]), $fileName);
        }
    }
//        FIXME: Check lại
//    public function downloadFileZip()
//    {
//        $zip_file_storage ='/excel/target/BC2ChiTieu.zip';
//        $path = '/excel/target/file/';
//        $zip_file = storage_path('app/public/excel/target/BC2ChiTieu.zip');
//        Storage::delete('excel/target/BC2ChiTieu.zip');
//        $zip = new ZipArchive();
//
//        $arrFile = Storage::allFiles($path);
//        if ($zip->open($zip_file, \ZipArchive::CREATE || \ZipArchive::OVERWRITE) === TRUE)
//        {
//            foreach ($arrFile as $key => $value) {
//                $relativeNameInZipFile = basename($value);
//                $url = storage_path('app/public/'.$value);
//                $zip->addFile($url, $relativeNameInZipFile);
//            }
//
//            $zip->close();
//        }
//
//        foreach ($arrFile as $value) {
//            Storage::delete($value);
//        }
//
//        return Storage::download($zip_file_storage);
//    }


    public function exportExcelTarget()
    {
        $dataSearch = [
            'from_to' => sc_clean(request('from_to') ?? ''),
            'end_to' => sc_clean(request('end_to') ?? ''),
            'keyword' => sc_clean(request('keyword') ?? ''),
            'category' => sc_clean(request('category') ?? ''),
            'keyExport' => sc_clean(request('key_export')) ?? [],
            'key_search' => sc_clean(request('key_search') ?? ''),
            'key_department' => sc_clean(request('key_department') ?? ''),
            'key_zone' => sc_clean(request('key_zone') ?? ''),
        ];

        $keyExport = sc_clean(request('key_export')) ?  explode(',', sc_clean(request('key_export'))) : [];
        $dataOrderMerge = $this->getDataSearch($keyExport, $dataSearch);

        if (!count($dataOrderMerge) > 0) {
            return redirect()->route('admin_report_2target.index')->with('error' , 'Không có dữ liệu');
        }

        if (count($dataOrderMerge) > 30000) {
            return redirect()->route('admin_report_2target.index')->with('error' , 'Dữ liệu quá lớn!');
        }
        ini_set('max_execution_time', 180);
        setlocale(LC_COLLATE, 'vi_VN.UTF-8', 'vi.UTF-8', 'vi_VN', 'vi');
        $sorted = $dataOrderMerge->sortBy('qty')->sortBy(
            'product_name'
            , SORT_LOCALE_STRING);
        $from_to = str_replace("/","_",$dataSearch['from_to']);
        $end_to = str_replace("/","_",$dataSearch['end_to']);
        $fileName = 'BC2CHITIEU_'.$from_to.'-'.$end_to. '.xlsx';
        $attributes = [
            'name_admin' =>  Admin::user()->name,
            'time' =>  now(),
            'desc' =>  $fileName,
            'type' =>  'BC Ban Hang 2 Chi Tieu',
            'kind' =>  'Xuất Pdf',
        ];
        AdminSystemChangeHistory::storeData($attributes);
        return Excel::download(new AdminReportTargetExport($dataSearch, $sorted), $fileName);
    }

    public function paginate($items, $perPage = 20, $page = null, $options = ['path' => ''])
    {
        $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);
        $items = $items instanceof Collection ? $items : Collection::make($items);
        return new LengthAwarePaginator($items->forPage($page, $perPage), $items->count(), $perPage, $page, $options);

    }

    /**
     * @return \Illuminate\Http\RedirectResponse|void
     */
    public function saveTargetPdf()
    {
        $dataSearch = [
            'from_to' => sc_clean(request('from_to') ?? ''),
            'end_to' => sc_clean(request('end_to') ?? ''),
            'keyword' => sc_clean(request('keyword') ?? ''),
            'category' => sc_clean(request('category') ?? ''),
            'key_search' => sc_clean(request('key_search') ?? ''),
            'key_department' => sc_clean(request('key_department') ?? ''),
            'key_zone' => sc_clean(request('key_zone') ?? ''),
        ];

        $keyExport = sc_clean(request('key_export')) ?  explode(',', sc_clean(request('key_export'))) : [];
        $dataOrderMerge = $this->getDataSearch($keyExport, $dataSearch);

        if (!count($dataOrderMerge) > 0) {
            return redirect()->route('admin_report_2target.index')->with('error' , 'Không có dữ liệu');
        }

        setlocale(LC_COLLATE, 'vi_VN.UTF-8', 'vi.UTF-8', 'vi_VN', 'vi');
        $sorted = $dataOrderMerge->sortBy('qty')->sortBy(
            'product_name'
            , SORT_LOCALE_STRING);

        $html = view($this->templatePathAdmin . 'screen.report.target.print_pdf_template')
            ->with(['data' => $sorted, 'dataSearch' => $dataSearch])->render();


        $fileName = 'BC Ban Hang 2 Chi Tieu-'. $dataSearch['from_to'] .'-'.$dataSearch['end_to']. '.pdf';
        $fileName = str_replace("/","_",$fileName);
        $attributes = [
            'name_admin' =>  Admin::user()->name,
            'time' =>  now(),
            'desc' =>  $fileName,
            'type' =>  'BC Ban Hang 2 Chi Tieu',
            'kind' =>  'Xuất Pdf',
        ];
        AdminSystemChangeHistory::storeData($attributes);

        $html = mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8');
        return $html;
    }

    public function exportAllItem(){
        $warehouse_id = request('warehouse');
        $data_export = request('data_export');
        $dataExportDetail = json_decode($data_export, true);
        $dataWarehouse = AdminWarehouse::find($warehouse_id);
        DB::beginTransaction();
        try{
            $dataInsertOrderExport = [
                'id_name' => ShopGenId::genNextId('order_export'),
                'customer_id' =>  '',
                'customer_name' => 'Xuất kho từ báo cáo hàng ngày',
                'customer_code' =>  '',
                'customer_addr' => '',
                'phone' => '',
                'email' =>  '',
                'warehouse_id' => $warehouse_id,
                'warehouse_name' => $dataWarehouse->name,
                'warehouse_code' => $dataWarehouse->warehouse_code,
                'date_export' => Carbon::now()->format('Y-m-d'),
                'type_order' => 2,
                'status' => 1,
                'edit' => 0,
                'note' => '',
            ];
            $dataExport = AdminExport::create($dataInsertOrderExport);
            $dataDetailInsert = [];
            $dataDeptInsert = [];
            foreach ($dataExportDetail as $item) {
                $qty_reality = (float)str_replace(',', '',$item['qty_reality']);
                $dataDetailInsert[] = [
                    'id' => sc_uuid(),
                    'export_id' => $dataExport->id,
                    'export_code' => $dataExport->id_name,
                    'product_id' => $item['product_id']?? '',
                    'product_name' => $item['product_name']?? '',
                    'product_sku' => $item['product_code']?? '',
                    'unit' => $item['product_unit']?? '',
                    'product_kind' => $item['product_kind']?? '',
                    'qty' => (float)str_replace(',', '',$item['qty']) ?? '',
                    'qty_reality' => $qty_reality?? '',
                    'order_detail_id' => $item['order_detail_id']?? '',
                    'order_id' => $item['order_id']?? '',
                    'order_id_name' => $item['order_id_name']?? '',
                    'order_explain' => $item['order_explain']?? '',
                    'order_object_id' => $item['order_object_id']?? '',
                    'order_delivery_date' => $item['order_delivery_date']?? '',
                    'category_id' => $item['category_id']?? '',
                    'department_id' => $item['department_id'] ?? '',
                    'customer_name' => $item['customer_name']?? '',
                    'customer_code' => $item['customer_code']?? '',
                    'zone_id' => $item['zone_id'] ?? '',
                    'comment' => $item['note'] ?? '',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()

                ];
                $diff =  ((float)$item['qty'] ?? 0) - ((float)$qty_reality ?? 0);
                if($diff > 0){
                    $dataDeptInsert[] = [
                        'id' => sc_uuid(),
                        'order_id' => $item['order_id'] ?? '',
                        'order_id_name' => $item['order_id_name'] ?? '',
                        'order_detail_id' => $item['order_detail_id']?? '',
                        'order_id_barcode' => $item['order_id_barcode']?? '',
                        'product_id' => $item['product_id'] ?? '',
                        'product_name' => $item['product_name'] ?? '',
                        'product_code' => $item['product_code'] ?? '',
                        'product_kind' => $item['product_kind'] ?? '',
                        'customer_short_name' => $item['order_customer_short_name'] ?? '',
                        'customer_num' => $item['customer_num'] ?? '',
                        'department_id' => $item['department_id'] ?? '',
                        'qty_export_origin' => $item['qty'],
                        'qty_dept' => $diff,
                        'qty_export' => 0,
                        'export_date' => now() ?? 0,
                        'customer_name' => $item['customer_name']?? '',
                        'customer_code' => $item['customer_code']?? '',
                        'customer_id' => $item['customer_id']?? '',
                        'export_code' => $dataExport->id_name,
                        'export_id' => $dataExport->id,
                        'category_id' => $item['category_id'],
                        'created_at' => now(),
                        'updated_at' => now()
                    ];
                }
            }
            AdminExportDetail::insert($dataDetailInsert);
            AdminReportWarehouseProductDept::insert($dataDeptInsert); //Dept

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Dữ liệu xuất kho thành công']);
        } catch (\Throwable $e) {
        DB::rollback();
        return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Handel search data davicorp + davicook.
     * @param $keyExport
     * @param $dataSearch
     * @return \Illuminate\Database\Eloquent\Collection|mixed
     */
    public function getDataSearch($keyExport, $dataSearch)
    {

        if (in_array(1, $keyExport) && count($keyExport) == 1) {
            # Mặt hàng davicorp
            return $this->orderDavicorp->getAllDetailOrderProduct($dataSearch);// ok
        }

        if (in_array(2, $keyExport) && count($keyExport) == 1 ) {
            # Mặt hàng tươi Davicook.
            return $this->orderDavicook->getFreshProductToReportTwoTarget($dataSearch, [1], [0,1,2], 'delivery_date');// ok
        }

        if (in_array(3, $keyExport) && count($keyExport) == 1) {
            # Mặt hàng khô Davicook.
            return $this->orderDavicook->getDryProductToReportTwoTarget($dataSearch, [0], [2], 'export_date');// có gộp
        }

        if (empty(array_diff([1, 2], $keyExport)) && count($keyExport) == 2) {
            # Mặt hàng Corp + Tươi Cook.
            $dataDavicorp = $this->orderDavicorp->getAllDetailOrderProduct($dataSearch);
            $dataProductFreshDavicook = $this->orderDavicook->getFreshProductToReportTwoTarget($dataSearch, [1], [0, 1, 2], 'delivery_date');

            return $dataDavicorp->mergeRecursive($dataProductFreshDavicook);
        }

        if (empty(array_diff([1, 3], $keyExport)) && count($keyExport) == 2) {
            # Mặt hàng Corp + Khô Cook.
            $dataDavicorp = $this->orderDavicorp->getAllDetailOrderProduct($dataSearch);
            $dataProductDryDavicook = $this->orderDavicook->getDryProductToReportTwoTarget($dataSearch, [0], [2], 'export_date');

            return $dataDavicorp->mergeRecursive($dataProductDryDavicook);
        }

        if (empty(array_diff([2, 3], $keyExport)) && count($keyExport) == 2) {
            $dataProductDryDavicook = $this->orderDavicook->getDryProductToReportTwoTarget($dataSearch, [0], [2], 'export_date');
            $dataProductFreshDavicook = $this->orderDavicook->getFreshProductToReportTwoTarget($dataSearch, [1], [0, 1, 2], 'delivery_date');

            return $dataProductDryDavicook->mergeRecursive($dataProductFreshDavicook);
        }

        $dataDavicorp = $this->orderDavicorp->getAllDetailOrderProduct($dataSearch);
        $dataProductDryDavicook = $this->orderDavicook->getDryProductToReportTwoTarget($dataSearch, [0], [2], 'export_date');
        $dataProductFreshDavicook = $this->orderDavicook->getFreshProductToReportTwoTarget($dataSearch, [1], [0, 1, 2], 'delivery_date');
        $dataCook = $dataProductDryDavicook->mergeRecursive($dataProductFreshDavicook);

        return $dataDavicorp->mergeRecursive($dataCook);
    }

    public function getDataSearchExport($keyExport, $dataSearch)
    {
        if (in_array(1, $keyExport) && count($keyExport) == 1) {
            # Mặt hàng davicorp
            return $this->orderDavicorp->getAllDetailOrderProduct($dataSearch);// ok
        }

        if (in_array(2, $keyExport) && count($keyExport) == 1 ) {
            # Mặt hàng tươi Davicook.
            return $this->orderDavicook->getFreshProductToReportTwoTargetForExport($dataSearch, [1], [0,1,2], 'delivery_date');// ok
        }

        if (in_array(3, $keyExport) && count($keyExport) == 1) {
            # Mặt hàng khô Davicook.
            return $this->orderDavicook->getDryProductToReportTwoTargetForExport($dataSearch, [0], [2], 'export_date');// có gộp

        }

        if (empty(array_diff([1, 2], $keyExport)) && count($keyExport) == 2) {
            # Mặt hàng Corp + Tươi Cook.
            $dataDavicorp = $this->orderDavicorp->getAllDetailOrderProduct($dataSearch);
            $dataProductFreshDavicook = $this->orderDavicook->getFreshProductToReportTwoTargetForExport($dataSearch, [1], [0, 1, 2], 'delivery_date');

            return $dataDavicorp->mergeRecursive($dataProductFreshDavicook);
        }

        if (empty(array_diff([1, 3], $keyExport)) && count($keyExport) == 2) {
            # Mặt hàng Corp + Khô Cook.
            $dataDavicorp = $this->orderDavicorp->getAllDetailOrderProduct($dataSearch);
            $dataProductDryDavicook = $this->orderDavicook->getDryProductToReportTwoTargetForExport($dataSearch, [0], [2], 'export_date');

            return $dataDavicorp->mergeRecursive($dataProductDryDavicook);
        }

        if (empty(array_diff([2, 3], $keyExport)) && count($keyExport) == 2) {
            $dataProductDryDavicook = $this->orderDavicook->getDryProductToReportTwoTargetForExport($dataSearch, [0], [2], 'export_date');
            $dataProductFreshDavicook = $this->orderDavicook->getFreshProductToReportTwoTargetForExport($dataSearch, [1], [0, 1, 2], 'delivery_date');

            return $dataProductDryDavicook->mergeRecursive($dataProductFreshDavicook);
        }

        $dataDavicorp = $this->orderDavicorp->getAllDetailOrderProduct($dataSearch);
        $dataProductDryDavicook = $this->orderDavicook->getDryProductToReportTwoTargetForExport($dataSearch, [0], [2], 'export_date');
        $dataProductFreshDavicook = $this->orderDavicook->getFreshProductToReportTwoTargetForExport($dataSearch, [1], [0, 1, 2], 'delivery_date');
        $dataCook = $dataProductDryDavicook->mergeRecursive($dataProductFreshDavicook);

        return $dataDavicorp->mergeRecursive($dataCook);
    }
}
