<?php

namespace App\Admin\Controllers;

use App\Admin\Models\AdminShopOrderChangeExtra;
use App\Admin\Models\AdminSystemChangeHistory;
use App\Front\Models\ShopCategory;
use App\Exports\AdminPrintStampReportExport;
use App\Exports\AdminReportDavicookExport;
use App\Front\Models\ShopDepartment;
use App\Front\Models\ShopZone;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\AdminReportExport;
use SCart\Core\Admin\Admin;
use SCart\Core\Admin\Controllers\RootAdminController;
use Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use PDF;

class AdminStampAddReportController extends RootAdminController
{

    public function __construct()
    {
        parent::__construct();
    }

    public function paginate($items, $perPage = 20, $page = null, $options = ['path' => ''])
    {
        $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);
        $items = $items instanceof Collection ? $items : Collection::make($items);
        return new LengthAwarePaginator($items->forPage($page, $perPage), $items->count(), $perPage, $page, $options);

    }



    // báo cáo in tem bổ sung
    public function printStampExtraReport()
    {
        $data = [
            'title' => 'Báo cáo in tem mặt hàng bổ sung',
            'subTitle' => '',
            'icon' => 'fa fa-indent',
            'urlDeleteItem' => sc_route_admin('admin_price.delete'),
            'selectList' => '1', // 1 - Enable function delete list item
            'buttonRefresh' => '', // 1 - Enable button refresh
            'buttonSort' => '', // 1 - Enable button sort
            'css' => '',
            'js' => '',
            'url_action' => sc_route_admin('admin_price.create'),
        ];
        $listTh = [
            'send_time' => sc_language_render('send_time'),
            'content' => sc_language_render('admin.order.product.content'),
            'product_sku' => sc_language_render('product.sku'),
            'product_name' => sc_language_render('admin.order.product.name'),
            'order_name' =>'Mã đơn hàng',
            'customer_name' => sc_language_render('admin.order.customer_name'),
            'amount' => sc_language_render('product.quantity'),
            'unit' => sc_language_render('admin.money.unit'),
            'create_date' => 'NSX'

        ];
        $keyword = sc_clean(request('keyword') ?? '');
        $dataSearch = [
            'keyword' => $keyword,
            'from_to' => sc_clean(request('from_to') ?? ''),
            'end_to' => sc_clean(request('end_to') ?? ''),
            'category' => sc_clean(request('category') ?? ''),
            'check_filter' => sc_clean(request('check_filter') ?? ''),
            'order_send_time_from' => sc_clean(request('order_send_time_from') ?? ''),
            'order_send_time_to' => sc_clean(request('order_send_time_to') ?? ''),
            'content' => sc_clean(request('content') ?? ''),
            'zone' => sc_clean(request('zone') ?? []),
            'department' => sc_clean(request('department') ?? [])
        ];

        $keyExport = sc_clean(request('key_export') ?? []) ?? [];
        $content = sc_clean(request('content') ?? '');
        $dataTr = [];
        $cssTh = [
            'send_time' => 'text-align: center; width: 10%',
            'content' => 'text-align: center; width: 15%',
            'product_sku' => 'text-align: center; width: 10%',
            'product_name' => 'text-align: center; width: 15%',
            'order_name' => 'text-align: center; width: 15%',
            'customer_name' => 'text-align: center; width: 15%',
            'amount' => 'text-align: center; width: 10%',
            'unit' => 'text-align: center; width: 10%',
            'create_date' => 'text-align: center; width: 10%; min-width: 100px'
        ];
        $cssTd = [
            'send_time' => '',
            'content' => '',
            'product_sku' => 'text-align: center',
            'product_name' => '',
            'order_name' => '',
            'customer_name' => '',
            'amount' => 'text-align: center',
            'unit' => 'text-align: center',
            'create_date' => 'text-align: left',
        ];
        $data['cssTh'] = $cssTh;
        $data['cssTd'] = $cssTd;
        if (empty($dataSearch['check_filter'])) {
            $dataTmp = new \stdClass();
        } else {
            $dataTmp = $this->getDataStampsByFilter($keyExport, $dataSearch, $ids = null);
        }
        $dataItemSession = new Collection($dataTmp);
        $countData = $dataItemSession->count();
        $dataItemSession = $dataItemSession->sortBy([
            'order_num',
            'qty'
        ]);
        $dataItemSession = $this->paginate($dataItemSession);
        foreach ($dataItemSession as $key => $row) {
            $dataTr[$row['id']] = [
                'send_time' => $row['send_time'] ?? '',
                'content' => $row['content'] ?? '',
                'product_sku' => $row['product_sku'] ?? '',
                'product_name' => $row['product_name'] ?? '',
                'order_name' => $row['order_name'] ?? '',
                'customer_name' => $row['customer_name'] . (($row['object_id'] == 1) ? "(GV)" : "") ?? '',
                'amount' => number_format($row['qty'], 2) ?? '',
                'unit' => $row['name_unit'] ?? '',
                'create_date' => Carbon::make($row['delivery_time'])->format('d/m/Y') ?? '',
            ];
        }

        $data['listTh'] = $listTh;
        $data['dataTr'] = $dataTr;
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
            $optionTypeSearch .= '<option  ' . ( is_array($keyExport) ? ( in_array($key, $keyExport) ? "selected" : "") : "" ) . ' value="' . $key . '">' . $value . '</option>';
        }
        $contentSearch = [
            1 => "Chỉnh sửa định lượng món ăn",
            2 => "Chỉnh sửa số lượng",
            3 => "Chỉnh sửa số lượng thực tế",
            4 => "Chỉnh sửa số lượng sản phẩm",
            5 => "Chỉnh sửa số lượng suất ăn chính",
            6 => "Đơn hàng hủy",
            7 => "Thêm mới món ăn",
            8 => "Thêm mới sản phẩm",
            9 => 'Thêm mới nguyên liệu',
            10 => "Xóa nguyên liệu",
            11 => "Xóa sản phẩm",
        ];
        $optionContentSearch = '';
        foreach ($contentSearch as $key => $value) {
            $selected = ($value == $content) ? 'selected' : '';
            $optionContentSearch .= '<option ' . $selected . ' value="' . $value . '">' . $value . '</option>';
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
        $data['pagination'] = $dataItemSession->appends(request()->except(['_token', '_pjax']))->links($this->templatePathAdmin . 'component.pagination');
        $data['resultItems'] = sc_language_render('admin.result_item', ['item_from' => $dataItemSession->firstItem(), 'item_to' => $dataItemSession->lastItem(), 'total' => $dataItemSession->total()]);
        $currentDay = nowDateString();
        $from_day = $dataSearch['from_to'] ? $dataSearch['from_to'] : $currentDay;
        $end_day = $dataSearch['end_to'] ? $dataSearch['end_to'] : $currentDay;

        if ($keyword) {
            $from_day = $dataSearch['from_to'] ? $dataSearch['from_to'] : '';
            $end_day = $dataSearch['end_to'] ? $dataSearch['end_to'] : '';
        }

        $order_send_time_from = $dataSearch['order_send_time_from'] ? $dataSearch['order_send_time_from'] : '';
        $order_send_time_to = $dataSearch['order_send_time_to'] ? $dataSearch['order_send_time_to'] : '';

        //menuRight
        $data['menuRight'][] = '
                <div class="form-group">
                    <div class="input-group print-stamp">
                    <a class="btn  btn-success  btn-flat" title="" id="button_export_filter">
                        <i class="fa fa-file-export" title="' . sc_language_render('category-export') . '"></i> ' . sc_language_render('category-export') .
                        '</a> &nbsp;
                    <a href="javascript:void(0)" class="btn btn-flat btn btn-info" onclick="saveFilePdf()"><i class="fas fa-file-pdf"> </i> ' . sc_language_render("admin.report.print_pdf") . '</a> &nbsp;
                    <a href="javascript:void(0)" class="btn btn-outline-warning text-white" onclick="printStampExtraPdf()">' . sc_language_render("admin.stamp.print_pdf") . '</a>
                    </div>
                </div>';
        //=menuRight

        // topMenuRight
        $data['topMenuRight'][] = '
                <form action="' . sc_route_admin('admin_report_print_stamp_extra.index') . '" id="button_search">
                <input type="hidden" name="check_filter" id="check_filter" value=""/>
                <input type="hidden" name="data_count" value="' . $countData . '" id="data_count">
                <br>
                    <div class="input-group float-left" style="margin-left: 50px">
                        <div class="row">
                            <div style="width: 135px; margin: 0px 5px;">
                                <label>Mặt hàng:</label>
                                <div class="form-group">
                                    <div class="input-group">
                                        <select id="key_export" class="form-control d-none" name="key_export[]" multiple="multiple">
                                            ' . $optionTypeSearch . '
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div style="width: 150px; margin: 0px 5px;">
                                <label>Nội dung:</label>
                                <div class="form-group">
                                    <div class="input-group">
                                        <select id="content" class="form-control select2" name="content">
                                            <option value="">Lọc nội dung</option>
                                            ' . $optionContentSearch . '
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div style="width: 355px; margin: 0px 5px">
                                <div class="row">
                                    <div style="width: 160px; margin: 0px 5px">
                                        <label>Thời gian gửi:</label>
                                        <div class="form-group">
                                            <div class="input-group">
                                                <input type="text" name="order_send_time_from" id="order_send_time_from" class="form-control input-sm datepicker rounded-0" style="text-align: center" autocomplete="off" placeholder="" value="'.  $order_send_time_from .'" /> 
                                            </div>
                                        </div>
                                    </div>
                                    <div class="input-group-addon" style="color: #93A7C1; margin-top: 32px">Đến</div>
                                    <div style="width: 160px; margin: 0px 5px; margin-top: 32px;">
                                        <div class="form-group">
                                            <div class="input-group">
                                                <input type="text" name="order_send_time_to" id="order_send_time_to" class="form-control input-sm datepicker rounded-0" style="text-align: center"  placeholder="" autocomplete="off" value="'.  $order_send_time_to .'"  /> 
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div style="width: 140px; margin: 0px 5px;">
                                <label>Danh mục:</label>
                                <div class="form-group">
                                    <div class="input-group">
                                        <select class="form-control select2" name="category" id="category">
                                            <option value="">Tất cả</option>
                                            ' . $optionCategories . '
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div style="width: 302px; margin: 0px 5px;">
                                <div class="row">
                                    <div style="width: 130px; margin: 0px 5px">
                                        <label>Ngày giao hàng:</label>
                                        <div class="form-group" >
                                            <div class="input-group ">
                                                <input type="text" name="from_to" id="from_to" class="form-control input-sm date_time rounded-0" style="text-align: center"  placeholder="Chọn ngày" value="' . $from_day . '" /> 
                                            </div>
                                        </div>
                                    </div>
                                    <div class="input-group-addon" style="color: #93A7C1; margin-top: 32px">Đến</div>
                                    <div style="width: 130px; margin: 0px 5px; margin-top: 32px;">
                                        <div class="form-group">
                                            <div class="input-group">
                                                <input type="text" name="end_to" id="end_to" class="form-control input-sm date_time rounded-0" style="text-align: center"  placeholder="Chọn ngày" value="' . $end_day . '"  /> 
                                            </div>
                                        </div>
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
                            <div style="width: 280px; margin-top: 32px; padding-right: 42px;">
                                <div class="form-group">
                                    <div class="input-group">
                                    <input type="text" name="keyword" id="keyword" class="form-control rounded-0 float-right" placeholder="Tên mã SP và KH, Mã đơn" value="' . $keyword . '">
                                        <div class="input-group-append">
                                            <button type="submit" class="btn btn-primary  btn-flat btn-search" id="submit_search"><i class="fas fa-search"></i></button>
                                        </div>
                                    </div> 
                                </div>
                            </div>
                        </div>
                    </div>
                </form>';
        //=topMenuRight
        return view($this->templatePathAdmin . 'screen.list_stamp_extra_report')
            ->with($data);
    }

    /**
     * Preview stamp trên trình duyệt.
     * @return false|string|string[]|void|null
     */
    public function previewStampExtraPdf()
    {
        $ids = [];

        if (!empty(request('ids'))) {
            $ids = explode(',', request('ids'));
        }

        $dataSearch = [
            'from_to' => sc_clean(request('from_to') ?? ''),
            'end_to' => sc_clean(request('end_to') ?? ''),
            'order_send_time_from' => sc_clean(request('order_send_time_from') ?? ''),
            'order_send_time_to' => sc_clean(request('order_send_time_to') ?? ''),
            'order_date_from' => sc_clean(request('order_date_from') ?? ''),
            'order_date_to' => sc_clean(request('order_date_to') ?? ''),
            'keyword' => sc_clean(request('keyword') ?? ''),
            'category' => sc_clean(request('category') ?? ''),
            'content' => sc_clean(request('content') ?? ''),
            'zone' => sc_clean(request('zone') ?? []),
            'department' => sc_clean(request('department') ?? [])
        ];
        $keyExport = explode(',',sc_clean(request('key_export') ?? '')) ?? [];


        if (!empty($ids)) {
            $dataSearch['keyword'] = '';
            $dataSearch['category'] = '';
            $dataSearch['order_send_time_from'] = '';
            $dataSearch['order_send_time_to'] = '';
            $dataSearch['order_date_from'] = '';
            $dataSearch['order_date_to'] = '';
            $dataSearch['zone'] = [];
            $dataSearch['department'] = [];
        }
        $fileName = 'BaoCaoInTem-'. $dataSearch['from_to'] .'-'. $dataSearch['end_to']. '.pdf';
        $fileName = str_replace("/","_",$fileName);
        $attributes = [
            'name_admin' =>  Admin::user()->name,
            'time' =>  now(),
            'desc' =>  $fileName,
            'type' =>  'BC In Tem',
            'kind' =>  'In tem',
        ];
        AdminSystemChangeHistory::storeData($attributes);
        $data = $this->getDataDetailAndQrCodeToPreviewStamps($keyExport, $dataSearch, $ids);
        Log::info($data);
        $qrList = new Collection($data['listQr']);
        $dataItem = new Collection($data['listItem']);
        $dataItem = $dataItem->sortBy([
            'order_num',
            'qty'
        ]);
        $dataItem = $dataItem->values();
        $html = view($this->templatePathAdmin . 'print.stamp_report_print_template')
            ->with(['data' => $dataItem, 'qr' => $qrList, 'dataSearch' => $dataSearch])->render();
        $html = mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8');
        return $html;
    }

    /**
     * Down stamp định dạng excel.
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function exportExcelStampExtra()
    {
        $ids = [];
        if (!empty(request('ids'))) {
            $ids = explode(',', request('ids'));
        }
        $dataSearch = [
            'from_to' => sc_clean(request('from_to') ?? ''),
            'end_to' => sc_clean(request('end_to') ?? ''),
            'order_send_time_from' => sc_clean(request('order_send_time_from') ?? ''),
            'order_send_time_to' => sc_clean(request('order_send_time_to') ?? ''),
            'order_date_from' => sc_clean(request('order_date_from') ?? ''),
            'order_date_to' => sc_clean(request('order_date_to') ?? ''),
            'keyword' => sc_clean(request('keyword') ?? ''),
            'category' => sc_clean(request('category') ?? ''),
            'content' => sc_clean(request('content') ?? ''),
            'zone' => sc_clean(request('zone') ?? []),
            'department' => sc_clean(request('department') ?? [])
        ];
//        $keyExport = sc_clean(request('key_export') ?? '') ?? [];
        $keyExport = explode(',',sc_clean(request('key_export') ?? '')) ?? [];
        if (!empty($ids)) {
            $dataSearch['keyword'] = '';
            $dataSearch['category'] = '';
            $dataSearch['select_warehouse'] = '';
        }

        $from_to = $dataSearch['from_to'] ?? \Carbon\Carbon::make($dataSearch['from_to'])->format("d/m/Y");
        $end_to = $dataSearch['end_to'] ?? \Carbon\Carbon::make($dataSearch['end_to'])->format("d/m/Y");
        $fileName = 'BaoCaoInTem-'. $from_to .'-'.$end_to. '.xlsx';
        $fileName = str_replace("/","_",$fileName);

        $data = $this->getDataStampsByFilter($keyExport, $dataSearch, $ids);
        $data = new Collection($data);
        $data = $data->sortBy([
            'order_num',
            'qty'
        ]);
        $attributes = [
            'name_admin' =>  Admin::user()->name,
            'time' =>  now(),
            'desc' =>  $fileName,
            'type' =>  'BC In Tem',
            'kind' =>  'Xuất Excel',
        ];
        AdminSystemChangeHistory::storeData($attributes);
        return Excel::download(new AdminPrintStampReportExport($data, $dataSearch), $fileName);
    }

    /**
     * Down stamp định dạng PDF.
     * @return false|string|string[]|void|null
     */
    public function downloadFileStampExtraPdf()
    {
        $ids = [];

        if (!empty(request('ids'))) {
            $ids = explode(',', request('ids'));
        }

        $dataSearch = [
            'from_to' => sc_clean(request('from_to') ?? ''),
            'end_to' => sc_clean(request('end_to') ?? ''),
            'order_send_time_from' => sc_clean(request('order_send_time_from') ?? ''),
            'order_send_time_to' => sc_clean(request('order_send_time_to') ?? ''),
            'order_date_from' => sc_clean(request('order_date_from') ?? ''),
            'order_date_to' => sc_clean(request('order_date_to') ?? ''),
            'keyword' => sc_clean(request('keyword') ?? ''),
            'category' => sc_clean(request('category') ?? ''),
            'content' => sc_clean(request('content') ?? ''),
            'zone' => sc_clean(request('zone') ?? []),
            'department' => sc_clean(request('department') ?? [])
        ];
//        $keyExport = sc_clean(request('key_export') ?? '') ?? [];
        $keyExport = explode(',',sc_clean(request('key_export') ?? '')) ?? [];
        if (!empty($ids)) {
            $dataSearch['keyword'] = '';
            $dataSearch['category'] = '';
            $dataSearch['select_warehouse'] = '';
        }

        $data = $this->getDataStampsByFilter($keyExport, $dataSearch, $ids);
        $data = new Collection($data);
        $data = $data->sortBy([
            'order_num',
            'qty'
        ]);
        $fileName = 'BaoCaoInTem-'. $dataSearch['from_to'] .'-'. $dataSearch['end_to']. '.pdf';
        $fileName = str_replace("/","_",$fileName);
        $attributes = [
            'name_admin' =>  Admin::user()->name,
            'time' =>  now(),
            'desc' =>  $fileName,
            'type' =>  'BC In Tem',
            'kind' =>  'Xuất Pdf',
        ];
        AdminSystemChangeHistory::storeData($attributes);
        $html = view($this->templatePathAdmin . 'print.file_pdf_stamp_report_template')
            ->with(['data' => $data, 'dataSearch' => $dataSearch])->render();
        $html = mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8');
        return $html;
    }

    /**
     * Handel lấy dữ liệu đổ về in tem.
     * @param $dataSearch
     * @param null $ids
     * @return array|mixed
     */


    public function getDataStampsByFilter($keyExport, $dataSearch, $ids = null)
    {
        if ((in_array(1,$keyExport)) && (count($keyExport) == 1)) {
            # Mặt hàng Davicorp.
            return (new AdminShopOrderChangeExtra())->getStampList($dataSearch)['item'] ?? [];
        }

        if ((in_array(2,$keyExport)) && (count($keyExport) == 1)) {
            # Mặt hàng tươi Davicook.
            return (new AdminShopOrderChangeExtra())->getProductFreshDavicookToReportStamp($dataSearch, $ids, 1)['item'] ?? [];
        }

        if ((in_array(3,$keyExport)) && (count($keyExport) == 1)) {
            # Mặt hàng khô Davicook.
            return (new AdminShopOrderChangeExtra())->getProductDryDavicookToReportStamp($dataSearch, $ids)['item'] ?? [];

        }

        if (empty(array_diff([1, 2], $keyExport)) && (count($keyExport) == 2)) {
            # Mặt hàng Daivcorp + Tươi Davicook.
            $getDataDavicorp = (new AdminShopOrderChangeExtra())->getStampList($dataSearch)['item'] ?? [];
            $getDetailDavicookByStatusProductFresh = (new AdminShopOrderChangeExtra())->getProductFreshDavicookToReportStamp($dataSearch, $ids, 1)['item'] ?? [];
            return array_merge($getDataDavicorp, $getDetailDavicookByStatusProductFresh);
        }

        if (empty(array_diff([1, 3], $keyExport)) && (count($keyExport) == 2)) {
            # Mặt hàng Daivcorp + Khô Davicook.
            $getDataDavicorp = (new AdminShopOrderChangeExtra())->getStampList($dataSearch)['item'];
            $getDetailDavicookByStatusProductDry = (new AdminShopOrderChangeExtra())->getProductDryDavicookToReportStamp($dataSearch, $ids)["item"] ?? [];
            return array_merge($getDataDavicorp, $getDetailDavicookByStatusProductDry);
        }

        if (empty(array_diff([2, 3], $keyExport)) && (count($keyExport) == 2)) {
            # Mặt hàng Tươi Daivcook + Khô Davicook.
            $getDetailDavicookByStatusProductDry = (new AdminShopOrderChangeExtra())->getProductDryDavicookToReportStamp($dataSearch, $ids)["item"] ?? [];
            $getDetailDavicookByStatusProductFresh = (new AdminShopOrderChangeExtra())->getProductFreshDavicookToReportStamp($dataSearch, $ids, 1)['item'] ?? [];
            return array_merge($getDetailDavicookByStatusProductDry, $getDetailDavicookByStatusProductFresh);
        }

        # Get data Corp + Tươi + Khô Cook
        $getDataDavicorp = (new AdminShopOrderChangeExtra())->getStampList($dataSearch)['item'] ?? [];
        $objDavicookOrderDetailFresh = (new AdminShopOrderChangeExtra())->getProductFreshDavicookToReportStamp($dataSearch, $ids, 1)["item"] ?? [];
        $objDavicookOrderDetailDry = (new AdminShopOrderChangeExtra())->getProductDryDavicookToReportStamp($dataSearch, $ids)['item'] ?? [];
        $objDavicookOrderDetailMerge = array_merge($objDavicookOrderDetailFresh, $objDavicookOrderDetailDry);

        return array_merge($getDataDavicorp, $objDavicookOrderDetailMerge);
    }

    /**
     * @param $keyExport
     * @param $dataSearch
     * @param $ids
     * @return array|mixed
     */
    public function getDataDetailAndQrCodeToPreviewStamps($keyExport, $dataSearch, $ids)
    {
        $data['listItem'] = [];
        $data['listQr'] = [];
        if ((in_array(1,$keyExport)) && (count($keyExport) == 1)) {
            # Mặt hàng Davicorp.
            $getDataDavicorp = (new AdminShopOrderChangeExtra())->getStampList($dataSearch, $ids);
            $data['listQr'] = array_merge($getDataDavicorp["qr"]->toArray() ?? [], $data['listQr']);
            $data['listItem'] = $getDataDavicorp['item'] ?? [];
            return $data;
        }

        if ((in_array(2,$keyExport)) && (count($keyExport) == 1)) {
            $getDetailDavicookByStatusProductFresh = (new AdminShopOrderChangeExtra())->getProductFreshDavicookToReportStamp($dataSearch, $ids, 1);
            $data['listQr'] = array_merge($getDetailDavicookByStatusProductFresh["qr"]->toArray() ?? [], $data['listQr']);
            $data['listItem'] = $getDetailDavicookByStatusProductFresh['item'] ?? [];
            return $data;
        }

        if ((in_array(3,$keyExport)) && (count($keyExport) == 1)) {
            # Mặt hàng tươi Davicook.
            $getDetailDavicookByStatusProductDry = (new AdminShopOrderChangeExtra())->getProductDryDavicookToReportStamp($dataSearch, $ids);
            $data['listQr'] = array_merge($getDetailDavicookByStatusProductDry["qr"]->toArray() ?? [], $data['listQr']);
            $data['listItem'] = $getDetailDavicookByStatusProductDry['item'] ?? [];
            return $data;
        }

        if (empty(array_diff([1, 2], $keyExport)) && (count($keyExport) == 2)) {
            # Mặt hàng Daivcorp + Tươi Davicook.
            $getDataDavicorp = (new AdminShopOrderChangeExtra())->getStampList($dataSearch, $ids);
            $getDetailDavicookByStatusProductFresh = (new AdminShopOrderChangeExtra())->getProductFreshDavicookToReportStamp($dataSearch, $ids, 1);
            $data['listQr'] = array_merge($getDataDavicorp["qr"]->toArray() ?? [], $data['listQr']);
            $data['listQr'] = array_merge($getDetailDavicookByStatusProductFresh["qr"]->toArray() ?? [], $data['listQr']);
            $data['listItem'] = array_merge($getDataDavicorp['item'] ?? [], $getDetailDavicookByStatusProductFresh['item'] ?? []);
            return $data;
        }

        if (empty(array_diff([1, 3], $keyExport)) && (count($keyExport) == 2)) {
            # Mặt hàng Daivcorp + Khô Davicook.
            $getDataDavicorp = (new AdminShopOrderChangeExtra())->getStampList($dataSearch, $ids);
            $getDetailDavicookByStatusProductDry = (new AdminShopOrderChangeExtra())->getProductDryDavicookToReportStamp($dataSearch, $ids);
            $data['listQr'] = array_merge($getDataDavicorp["qr"]->toArray() ?? [], $data['listQr']);
            $data['listQr'] = array_merge($getDetailDavicookByStatusProductDry["qr"]->toArray() ?? [], $data['listQr']);
            $data['listItem'] = array_merge($getDataDavicorp['item'] ?? [], $getDetailDavicookByStatusProductDry['item'] ?? []);
            return $data;
        }

        if (empty(array_diff([2, 3], $keyExport)) && (count($keyExport) == 2)) {
            # Mặt hàng Tươi Daivcook + Khô Davicook.
            $getDetailDavicookByStatusProductDry = (new AdminShopOrderChangeExtra())->getProductDryDavicookToReportStamp($dataSearch, $ids);
            $getDetailDavicookByStatusProductFresh = (new AdminShopOrderChangeExtra())->getProductFreshDavicookToReportStamp($dataSearch, $ids, 1);
            $data['listQr'] = array_merge($getDetailDavicookByStatusProductDry["qr"]->toArray() ?? [], $data['listQr']);
            $data['listQr'] = array_merge($getDetailDavicookByStatusProductFresh["qr"]->toArray() ?? [], $data['listQr']);
            $data['listItem'] = array_merge($getDetailDavicookByStatusProductDry['item'] ?? [], $getDetailDavicookByStatusProductFresh['item'] ?? []);
            return $data;
        }

        # Get data Corp + Tươi + Khô Cook
        $getDataDavicorp = (new AdminShopOrderChangeExtra())->getStampList($dataSearch, $ids);
        $objDavicookOrderDetailFresh = (new AdminShopOrderChangeExtra())->getProductFreshDavicookToReportStamp($dataSearch, $ids, 1);
        $objDavicookOrderDetailDry = (new AdminShopOrderChangeExtra())->getProductDryDavicookToReportStamp($dataSearch, $ids);
        $objDavicookOrderDetailMerge = array_merge($objDavicookOrderDetailFresh["item"] ?? [], $objDavicookOrderDetailDry['item'] ?? []);
        $data['listQr'] = array_merge($getDataDavicorp["qr"]->toArray() ?? [], $data['listQr']);
        $data['listQr'] = array_merge($objDavicookOrderDetailFresh["qr"]->toArray() ?? [], $data['listQr']);
        $data['listQr'] = array_merge($objDavicookOrderDetailDry["qr"]->toArray() ?? [], $data['listQr']);
        $data['listItem'] = array_merge($getDataDavicorp['item'] ?? [], $objDavicookOrderDetailMerge);

        return $data;
    }
}
