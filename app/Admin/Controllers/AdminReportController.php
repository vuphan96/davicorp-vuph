<?php

namespace App\Admin\Controllers;

use App\Admin\Models\AdminSystemChangeHistory;
use App\Admin\Models\AdminWarehouse;
use App\Exports\AdminReportMealDifferenceExport;
use App\Front\Models\ShopCategory;
use App\Front\Models\ShopDavicookOrder;
use App\Admin\Models\AdminCategory;
use App\Admin\Models\AdminCustomer;
use App\Admin\Models\AdminDavicookOrder;
use App\Front\Models\ShopImportPriceboard;
use App\Front\Models\ShopImportPriceboardDetail;
use App\Admin\Models\AdminOrder;
use App\Admin\Models\AdminProduct;
use App\Exports\AdminPrintStampReportExport;
use App\Exports\AdminReportDavicookExport;
use App\Exports\AdminRevenueReportExport;
use App\Exports\AdminReportNoteExport;
use App\Exports\AdminImportReportExportTemplate;
use App\Exports\AdminImportDetailReportExportTemplate;
use App\Front\Models\ShopDepartment;
use App\Front\Models\ShopOrder;
use App\Front\Models\ShopOrderDetail;
use App\Front\Models\ShopOrderObject;
use App\Front\Models\ShopProduct;
use App\Front\Models\ShopZone;
use Dompdf\Dompdf;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\AdminReportExport;
use SCart\Core\Admin\Admin;
use SCart\Core\Admin\Controllers\RootAdminController;
use Request;
use Exception;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use PDF;
use function Symfony\Component\Console\Style\table;
use function Symfony\Component\HttpKernel\Debug\format;
use function Termwind\ValueObjects\strong;

class AdminReportController extends RootAdminController
{

    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $data = [
            // ,
            'title' => sc_language_render('admin.revenue.report.title'),
            'subTitle' => '',
            'icon' => 'fa fa-indent',
            'urlDeleteItem' => sc_route_admin('admin_price.delete'),
            'removeList' => '', // 1 - Enable function delete list item
            'buttonRefresh' => 1, // 1 - Enable button refresh
            'buttonSort' => '', // 1 - Enable button sort
            'css' => '',
            'js' => '',
            'url_action' => sc_route_admin('admin_price.create'),
        ];

        $listTh = [
            'STT' => sc_language_render('admin.report.stt'),
            'delivery_time' => 'Ngày giao hàng',
            'id_name' => 'Mã đơn hàng',
            'customer_code' => sc_language_render('admin.report.customer_code'),
            'name' => sc_language_render('admin.report.name'),
            'explain' => 'Diễn giải',
            'order_sum_price' => sc_language_render('admin.report.order_sum_price')

        ];
        $keyword = sc_clean(request('keyword') ?? '');

        $dataSearch = [
            'keyword' => $keyword,
            'from_to' => sc_clean(request('from_to') ?? ''),
            'end_to' => sc_clean(request('end_to') ?? ''),
            'department' => sc_clean(request('department') ?? ''),
            'object' => sc_clean(request('object') ?? ''),
            'note' => sc_clean(request('note') ?? ''),
            'check_filter' => sc_clean(request('check_filter') ?? ''),
            'key_export' => sc_clean(request('key_export') ?? 0),
        ];
        $cssTh = [
            'STT' => 'text-align: center; width: 7%',
            'delivery_time' => 'text-align: center; width: 10%',
            'id_name' => 'text-align: center; width: 10%',
            'customer_code' => 'text-align: center; width: 10%',
            'name' => ' width: 30%',
            'explain' => 'text-align: center; width: 10%',
            'order_sum_price' => 'text-align: center; width: 20%'
        ];
        $cssTd = [
            'STT' => 'text-align: center',
            'delivery_time' => 'text-align: left',
            'id_name' => 'text-align: left',
            'customer_code' => 'text-align: center',
            'name' => 'text-align: left',
            'explain' => 'text-align: left',
            'order_sum' => 'text-align: right'
        ];
        $data['cssTh'] = $cssTh;
        $data['cssTd'] = $cssTd;
        $data['menuLeft'] = sc_config_group('menuLeft', Request::route()->getName());
        $obj = new AdminOrder();


        if ($dataSearch['key_export'] == 2) {
            if (!empty($dataSearch['department'])) {
                $dataOrder = new Collection();
            } else {
                $objOrderDavicook = $obj->getListOrderDavicook($dataSearch);
                $dataOrder = $objOrderDavicook;
            }
        } elseif ($dataSearch['key_export'] == 1) {
            $objOrderDavicorp = $obj->getRevenueReportOrder($dataSearch);
            $dataOrder = $objOrderDavicorp;
        } else {
            if (!empty($dataSearch['department'])) {
                $objOrderDavicorp = $obj->getRevenueReportOrder($dataSearch);
                $dataOrder = $objOrderDavicorp;
            } else {
                $objOrderDavicorp = $obj->getRevenueReportOrder($dataSearch);
                $objOrderDavicook = $obj->getListOrderDavicook($dataSearch);
                $dataOrder = $objOrderDavicorp->mergeRecursive($objOrderDavicook);
            }
        }

        $dataTmp = $this->paginate($dataOrder);
        if (empty($dataSearch['check_filter'])) {
            $dataTmp = $this->paginate(new \stdClass());
        } else {
            $total_revenue = $dataOrder->sum('amount') ?? '';
            $data['total_revenue'] = number_format($total_revenue);
        }


        $dataTr = [];
        $stt = $dataTmp->firstItem();
        $revenueByPage = 0;
        foreach ($dataTmp as $key => $row) {
            $dataTr[] = [
                'STT' => $stt ?? '',
                'delivery_time' => Carbon::make($row['delivery_date'] ?? '')->format('d/m/Y'),
                'id_name' => $row['id_name'],
                'customer_code' => $row['customer_code'],
                'name' => $row['customer_name'],
                'explain' => $row['explain'] ?? '',
                'order_sum' => number_format($row['amount'] ?? 0),
            ];
            $revenueByPage += $row['amount'] ?? $row->amount;
            $stt++;
        }
        $data['revenueByPage'] = number_format($revenueByPage) ?? '' ;
        $data['listTh'] = $listTh;
        $data['dataTr'] = $dataTr;
        $data['pagination'] = $dataTmp->appends(request()->except(['_token', '_pjax']))->links($this->templatePathAdmin . 'component.pagination');
        $data['resultItems'] = sc_language_render('admin.result_item', ['item_from' => $dataTmp->firstItem(), 'item_to' => $dataTmp->lastItem(), 'total' => $dataTmp->total()]);
        $departments = ShopDepartment::get();
        $optionDepartment = '';
        foreach ($departments as $key => $department) {
            $optionDepartment .= '<option  ' . (($dataSearch['department'] == $department->id) ? "selected" : "") . ' value="' . $department->id . '">' . $department->name . '</option>';
        }
        $objects = ShopOrderObject::get();
        $optionObject = '';
        foreach ($objects as $key => $object) {
            $optionObject .= '<option  ' . (($dataSearch['object'] == $object->id) ? "selected" : "") . ' value="' . $object->id . '">' . $object->name . '</option>';
        }
        $notes = ShopOrder::$NOTE;
        $optionNote = '';
        foreach ($notes as $key => $note) {
            $optionNote .= '<option  ' . (($dataSearch['note'] == $note) ? "selected" : "") . ' value="' . $note . '">' . $note . '</option>';
        }
        $optionTypeSearch = '';
        $typeSearch = [
            0 => 'Tổng hợp',
            1 => 'Mầm non',
            2 => 'Suất ăn',
        ];
        foreach ($typeSearch as $key => $value) {
            $optionTypeSearch .= '<option  ' . (($dataSearch['key_export'] == $key) ? "selected" : "") . ' value="' . $key . '">' . $value . '</option>';
        }
        $currentDay = nowDateString();
        $from_day = $dataSearch['from_to'] ? $dataSearch['from_to'] : $currentDay;
        $end_day = $dataSearch['end_to'] ? $dataSearch['end_to'] : $currentDay;
        $data['topMenuRight'][] = '
                <form action="' . sc_route_admin('admin_report_revenue.index') . '" id="button_search">
                <input type="hidden" name="check_filter" id="check_filter" value=""/>
                <br>
                    <div class="input-group float-left" style="width: 100%!important; display: flex;">
                        <div class="col-md-2">
                            <div class="form-group">
                                <div class="input-group ">
                                    <input type="text" name="from_to" id="from_to" class="form-control input-sm date_time rounded-0" style="text-align: center"  placeholder="Chọn ngày" value="' . $from_day . '"/> 
                                </div>
                            </div>
                        </div>
                        <div class="input-group-addon" style="color: #93A7C1">Đến</div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <div class="input-group">
                                    <input type="text" name="end_to" id="end_to" class="form-control input-sm date_time rounded-0" style="text-align: center"  placeholder="Chọn ngày" value="' . $end_day . '"/> 
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <div class="input-group">
                                    <select class="form-control" name="key_export" id="key_export" style=" overflow: hidden">
                                         ' . $optionTypeSearch . '
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <div class="input-group">
                                    <select class="form-control" name="note" id="note" style=" overflow: hidden">
                                        <option value="">Diễn giải</option>
                                        ' . $optionNote . '
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <div class="input-group">
                                    <select class="form-control" name="object" id="object" style=" overflow: hidden">
                                        <option value="">Đối tượng</option>
                                        ' . $optionObject . '
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group" >
                                <div class="input-group ">
                                    <select class="form-control" name="department" id="department" style="overflow: hidden">
                                        <option value="">' . sc_language_render('admin.customer.type_customer') . '</option>
                                        ' . $optionDepartment . '
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <div class="input-group">
                                <input type="text" title="' . sc_language_render('admin.report.search_placeholder') . '" name="keyword" id="keyword" class="form-control rounded-0 float-right" placeholder="' . sc_language_render('admin.report.search_placeholder') . '" value="' . $keyword . '">
                                    <div class="input-group-append">
                                        <button type="submit" class="btn btn-primary  btn-flat btn-search"><i class="fas fa-search"></i></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <div class="input-group">
                                <a class="btn  btn-success  btn-flat" title="" id="button_export_filter">
                                        <i class="fa fa-file-export" title="' . sc_language_render('category-export') . '"></i> ' . sc_language_render('category-export') .
            '</a> &nbsp;&nbsp;
                                <a href="javascript:void(0)" class="btn btn-flat btn btn-info text-white" onclick="savePdf()"><i class="fas fa-file-pdf"></i>&nbsp;' . sc_language_render("admin.report.print_pdf") . '</a>
         
                                </div>
                            </div>
                        </div>
                        
                    </div>
                    
                </form>
                ';

        //=menuSearch
        return view($this->templatePathAdmin . 'screen.report.revenue.index')
            ->with($data);
    }

    public function paginate($items, $perPage = 20, $page = null, $options = ['path' => ''])
    {
        $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);
        $items = $items instanceof Collection ? $items : Collection::make($items);
        return new LengthAwarePaginator($items->forPage($page, $perPage), $items->count(), $perPage, $page, $options);

    }
    public function exportExcel()
    {
        $dataSearch = [
            'from_to' => sc_clean(request('from_to') ?? ''),
            'end_to' => sc_clean(request('end_to') ?? ''),
            'keyword' => sc_clean(request('keyword') ?? ''),
            'department' => sc_clean(request('department') ?? ''),
            'object' => sc_clean(request('object') ?? ''),
            'note' => sc_clean(request('note') ?? ''),
        ];

        $from_to = $dataSearch['from_to'] ?? \Carbon\Carbon::make($dataSearch['from_to'])->format("d/m/Y");
        $end_to = $dataSearch['end_to'] ?? \Carbon\Carbon::make($dataSearch['end_to'])->format("d/m/Y");
        $fileName = 'BaoCaoDanhThu-'. $from_to .'-'.$end_to. '.xlsx';
        $fileName = str_replace("/","_",$fileName);
        $attributes = [
            'name_admin' =>  Admin::user()->name,
            'time' =>  now(),
            'desc' =>  $fileName,
            'type' =>  'BC Doanh Thu',
            'kind' =>  'Xuất Excel',
        ];
        AdminSystemChangeHistory::storeData($attributes);

        return Excel::download(new AdminRevenueReportExport($dataSearch), $fileName);
    }

    public function saveRevenuePdf()
    {
        $dataSearch = [
            'from_to' => sc_clean(request('from_to') ?? ''),
            'end_to' => sc_clean(request('end_to') ?? ''),
            'keyword' => sc_clean(request('keyword') ?? ''),
            'department' => sc_clean(request('department') ?? ''),
            'object' => sc_clean(request('object') ?? ''),
            'note' => sc_clean(request('note') ?? ''),
        ];
        $obj = new AdminOrder();

        if (!empty($dataSearch['department'])) {
            if ($dataSearch['department'] == 999) {
                $dataTmp = $obj->getListOrderDavicook($dataSearch);
            } else {
                $dataTmp = $obj->getRevenueReportOrder($dataSearch);
            }
        } else {
            $objOrderDavicorp = $obj->getRevenueReportOrder($dataSearch);
            $objOrderDavicook = $obj->getListOrderDavicook($dataSearch);
            $dataTmp = $objOrderDavicorp->mergeRecursive($objOrderDavicook);
        }
        if (!count($dataTmp) > 0) {
            return redirect()->route('admin_report_revenue.index')->with('error', 'Không có dữ liệu');
        }
        $fileName = 'BaoCaoDanhThu-'. $dataSearch['from_to'] .'-'. $dataSearch['end_to']. '.pdf';
        $fileName = str_replace("/","_",$fileName);
        $attributes = [
            'name_admin' =>  Admin::user()->name,
            'time' =>  now(),
            'desc' =>  $fileName,
            'type' =>  'BC Doanh Thu',
            'kind' =>  'Xuất Pdf',
        ];
        AdminSystemChangeHistory::storeData($attributes);
        $html = view($this->templatePathAdmin . 'screen.report.revenue.print_pdf_template')
            ->with(['data' => $dataTmp, 'dataSearch' => $dataSearch])->render();
        return $html;
    }

    // báo cáo in tem
    public function printStampReport()
    {
        $data = [
            'title' => sc_language_render('admin.print_tem.report.title'),
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
            'product_sku' => sc_language_render('product.sku'),
            'product_name' => sc_language_render('admin.order.product.name'),
            'customer_name' => sc_language_render('admin.order.customer_name'),
            'customer_num' => 'STT',
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
            'key_export' => sc_clean(request('key_export') ?? []),
            'check_filter' => sc_clean(request('check_filter') ?? ''),
            'order_date_from' => sc_clean(request('order_date_from') ?? ''),
            'order_date_to' => sc_clean(request('order_date_to') ?? ''),
            'zone' => sc_clean(request('zone') ?? []),
            'department' => sc_clean(request('department') ?? [])

        ];
        $dataTr = [];
        $cssTh = [
            'product_sku' => 'text-align: center; width: 15%',
            'product_name' => 'text-align: center; width: 25%',
            'customer_name' => 'text-align: center; width: 25%',
            'customer_num' => 'text-align: center; width: 8%',
            'amount' => 'text-align: center; width: 15%',
            'unit' => 'text-align: center; width: 15%',
            'create_date' => 'text-align: center; width: 15%; min-width: 100px'
        ];
        $cssTd = [
            'product_sku' => 'text-align: center',
            'product_name' => '',
            'customer_name' => '',
            'customer_num' => 'text-align: center',
            'amount' => 'text-align: center',
            'unit' => 'text-align: center',
            'create_date' => 'text-align: left',
        ];
        $data['cssTh'] = $cssTh;
        $data['cssTd'] = $cssTd;
        if (empty($dataSearch['check_filter'])) {
            $dataTmp = new \stdClass();
        } else {
            $dataTmp = $this->getDataStampsByFilter($dataSearch['key_export'], $dataSearch, $ids = null);
        }
        $dataItemSession = new Collection($dataTmp);
        $countData = $dataItemSession->count();
        $dataItemSession = $dataItemSession->sortBy([
            'order_num',
//            'product_sku',
            'qty'
        ]);
        $dataItemSession = $this->paginate($dataItemSession);
        foreach ($dataItemSession as $key => $row) {
            $dataTr[$row['id']] = [
                'product_sku' => $row['product_sku'] ?? '',
                'product_name' => $row['product_name'] ?? '',
                'customer_name' => $row['customer_name'] . (($row['object_id'] == 1) ? "(GV)" : "") ?? '',
                'customer_num' => $row['customer_num'],
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
            $optionTypeSearch .= '<option  ' . ( is_array($dataSearch['key_export']) ? ( in_array($key, $dataSearch['key_export']) ? "selected" : "") : "" ) . ' value="' . $key . '">' . $value . '</option>';
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

        $order_date_from = $dataSearch['order_date_from'] ? $dataSearch['order_date_from'] : '';
        $order_date_to = $dataSearch['order_date_to'] ? $dataSearch['order_date_to'] : '';

        //menuRight
        $data['menuRight'][] = '
                <div class="form-group">
                    <div class="input-group print-stamp">
                    <a class="btn  btn-success  btn-flat" title="" id="button_export_filter">
                        <i class="fa fa-file-export" title="' . sc_language_render('category-export') . '"></i> ' . sc_language_render('category-export') .
                        '</a> &nbsp;
                    <a href="javascript:void(0)" class="btn btn-flat btn btn-info" onclick="saveFilePdf()"><i class="fas fa-file-pdf"> </i> ' . sc_language_render("admin.report.print_pdf") . '</a> &nbsp;
                    <a href="javascript:void(0)" class="btn btn-outline-warning text-white" onclick="printStampPdf()">' . sc_language_render("admin.stamp.print_pdf") . '</a>
                    </div>
                </div>';
        //=menuRight

        // topMenuRight
        $data['topMenuRight'][] = '
                <form action="' . sc_route_admin('admin_report_print_stamp.index') . '" id="button_search">
                <input type="hidden" name="check_filter" id="check_filter" value=""/>
                <input type="hidden" name="data_count" value="' . $countData . '" id="data_count">
                <br>
                    <div class="input-group float-left" style="margin-left: 50px">
                        <div class="row">
                            <div style="width: 140px; margin: 0px 5px;">
                                <label>Mặt hàng:</label>
                                <div class="form-group">
                                    <div class="input-group">
                                        <select id="key_export" class="form-control d-none" name="key_export[]" multiple="multiple">
                                            ' . $optionTypeSearch . '
                                        </select>
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
                            <div style="width: 355px; margin: 0px 5px">
                                <div class="row">
                                    <div style="width: 160px; margin: 0px 5px">
                                        <label>Ngày đặt hàng:</label>
                                        <div class="form-group">
                                            <div class="input-group">
                                                <input type="text" name="order_date_from" id="order_date_from" class="form-control input-sm datepicker rounded-0" style="text-align: center" autocomplete="off" placeholder="" value="'.  $order_date_from .'" /> 
                                            </div>
                                        </div>
                                    </div>
                                    <div class="input-group-addon" style="color: #93A7C1; margin-top: 32px">Đến</div>
                                    <div style="width: 160px; margin: 0px 5px; margin-top: 32px;">
                                        <div class="form-group">
                                            <div class="input-group">
                                                <input type="text" name="order_date_to" id="order_date_to" class="form-control input-sm datepicker rounded-0" style="text-align: center"  placeholder="" autocomplete="off" value="'.  $order_date_to .'"  /> 
                                            </div>
                                        </div>
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
        return view($this->templatePathAdmin . 'screen.list_stamp_report')
            ->with($data);
    }

    /**
     * Preview stamp trên trình duyệt.
     * @return false|string|string[]|void|null
     */
    public function previewStampPdf()
    {
        $ids = [];

        if (!empty(request('ids'))) {
            $ids = explode(',', request('ids'));
        }

        $dataSearch = [
            'from_to' => sc_clean(request('from_to') ?? ''),
            'end_to' => sc_clean(request('end_to') ?? ''),
            'order_date_from' => sc_clean(request('order_date_from') ?? ''),
            'order_date_to' => sc_clean(request('order_date_to') ?? ''),
            'keyword' => sc_clean(request('keyword') ?? ''),
            'category' => sc_clean(request('category') ?? ''),
            'key_export' => sc_clean(request('key_export') ?? ''),
            'zone' => sc_clean(request('zone') ?? []),
            'department' => sc_clean(request('department') ?? [])
        ];

        $keyExport = explode(',', request('key_export')) ?? [];

        if (!empty($ids)) {
            $dataSearch['keyword'] = '';
            $dataSearch['category'] = '';
            $dataSearch['key_export'] = '';
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
    public function exportExcelStamp()
    {
        $ids = [];
        if (!empty(request('ids'))) {
            $ids = explode(',', request('ids'));
        }
        $dataSearch = [
            'from_to' => sc_clean(request('from_to') ?? ''),
            'end_to' => sc_clean(request('end_to') ?? ''),
            'order_date_from' => sc_clean(request('order_date_from') ?? ''),
            'order_date_to' => sc_clean(request('order_date_to') ?? ''),
            'keyword' => sc_clean(request('keyword') ?? ''),
            'category' => sc_clean(request('category') ?? ''),
            'key_export' => sc_clean(request('key_export') ?? ''),
        ];
        $keyExport = explode(',', request('key_export')) ?? [];
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
//            'product_sku',
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
    public function downloadFileStampPdf()
    {
        $ids = [];

        if (!empty(request('ids'))) {
            $ids = explode(',', request('ids'));
        }

        $dataSearch = [
            'from_to' => sc_clean(request('from_to') ?? ''),
            'end_to' => sc_clean(request('end_to') ?? ''),
            'order_date_from' => sc_clean(request('order_date_from') ?? ''),
            'order_date_to' => sc_clean(request('order_date_to') ?? ''),
            'keyword' => sc_clean(request('keyword') ?? ''),
            'category' => sc_clean(request('category') ?? ''),
            'key_export' => sc_clean(request('key_export') ?? []),
        ];
        $keyExport = explode(',', request('key_export')) ?? [];
        if (!empty($ids)) {
            $dataSearch['keyword'] = '';
            $dataSearch['category'] = '';
            $dataSearch['select_warehouse'] = '';
        }

        $data = $this->getDataStampsByFilter($keyExport, $dataSearch, $ids);
        $data = new Collection($data);
        $data = $data->sortBy([
            'order_num',
//            'product_sku',
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
     * báo cáo hàng nhập nhóm 2 chỉ tiêu - Mẫu 1
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function importGoodsReport()
    {
        $data = [
            // ,
            'title' => sc_language_render('admin.import.report.title'),
            'subTitle' => '',
            'icon' => 'fa fa-indent',
            'urlDeleteItem' => '',
            'removeList' => '', // 1 - Enable function delete list item
            'buttonRefresh' => '', // 1 - Enable button refresh
            'buttonSort' => '', // 1 - Enable button sort
            'css' => '',
            'js' => '',
            'url_action' => '',
            'url_export_excel' => sc_route_admin('admin_report_import_2target.template_1.export_excel'),
            'url_export_pdf' => sc_route_admin('admin_report_import_2target.template_1.export_pdf')
        ];

        $listTh = [
            'stt' => sc_language_render('admin.report.stt'),
            'code' => sc_language_render('admin.import.report.code'),
            'name' => sc_language_render('admin.import.report.name'),
            'qty_order' => sc_language_render('admin.import.report.qty_order'),
            'price_import' => sc_language_render('admin.import.report.price_import'),
            'into_money' => sc_language_render('admin.import.report.into_money'),
            'note' => sc_language_render('admin.import.report.note'),

        ];
        $keyword = sc_clean(request('keyword') ?? '');

        $dataSearch = [
            'keyword' => $keyword,
            'from_to' => sc_clean(request('from_to') ?? ''),
            'end_to' => sc_clean(request('end_to') ?? ''),
            'key_export' => sc_clean(request('key_export') ?? []),
            'check_filter' => sc_clean(request('check_filter') ?? ''),
            'department' => sc_clean(request('department') ?? ''),
        ];
        $cssTh = [
            'stt' => 'text-align: center; width: 7%',
            'code' => 'text-align: center; width: 10%',
            'name' => ' width: 30%',
            'qty_order' => 'text-align: center; width: 10%',
            'price_import' => 'text-align: center; width: 10%',
            'into_money' => 'text-align: center; width: 10%',
            'note' => 'text-align: center;  width: 22%',
        ];
        $cssTd = [
            'stt' => 'text-align: center',
            'code' => 'text-align: center',
            'name' => '',
            'qty_order' => 'text-align: right;',
            'price_import' => 'text-align: right;',
            'into_money' => 'text-align: right;',
            'note' => '',
        ];
        $data['cssTh'] = $cssTh;
        $data['cssTd'] = $cssTd;
        $data['listTh'] = $listTh;
        $dataTr = [];

        if (empty($dataSearch['check_filter'])) {
            $dataPriceImport = collect([]);
        } else {
            $dataPriceImport = $this->getDataPriceImport($dataSearch['key_export'],$dataSearch);
        }
        $dataProductById = [];
        $numberByDetailId = [];
        $dataPriceImport = $dataPriceImport->sortBy([
            'supplier_name',
            'sort',
            'product_name',
            'qtyProduct',
        ]);
        foreach ($dataPriceImport->groupBy('supplier_code') as $keySupplier => $item) {
            foreach ($item->groupBy('product_id') as $keyProduct => $value) {
                $dataProductById[$keySupplier][$keyProduct] = $value->sum('qtyProduct');
                foreach ($value as $k => $product) {
                    $numberByDetailId[$keySupplier][$product['detail_id']] = $k + 1;
                }
            }
        }
        $countData = (count($dataPriceImport));
        $dataSupplier = $this->paginate($dataPriceImport);
        foreach ($dataSupplier->groupBy('supplier_code') as $keySupplier => $itemSupplier) {
            $dataTr[] = [
                'stt' => '',
                'code' => '',
                'name' => $keySupplier ? '<span style="font-weight: bold; background-color: yellow; text-transform: uppercase;">' . $itemSupplier->first()['supplier_name'] . '</span>' : '<span style="font-weight: bold; color: red">Nhà cung cấp đã bị xóa</span>',
                'qty_order' => '',
                'price_import' => '',
                'into_money' => '',
                'note' => '',
            ];
            foreach ($itemSupplier->groupBy('product_id') as $keyProduct => $itemProduct) {
                $j = 1;
                $number = $numberByDetailId[$keySupplier][$itemProduct->first()['detail_id']];
                if ($number == 1) {
                    $dataTr[] = [
                        'stt' => '',
                        'code' => '<span style="font-weight: bold">' . $itemProduct->first()['product_code'] . '</span>' ?? '',
                        'name' => '<span style="font-weight: bold; text-transform: uppercase;">' . $itemProduct->first()['product_name'] . '</span>' ?? '',
                        'qty_order' => '<span style="font-weight: bold">' . number_format($dataProductById[$keySupplier][$keyProduct], 2). '</span>' ?? '',
                        'price_import' => '',
                        'into_money' => '',
                        'note' => '',
                    ];
                }
                foreach ($itemProduct as $orderDetail) {
                    $dataTr[] = [
                        'stt' => $numberByDetailId[$keySupplier][$orderDetail['detail_id']] ,
                        'code' => '',
                        'name' => $orderDetail['customer_name'] ? $orderDetail['customer_name'] . ( isset($orderDetail['object_id']) ? ($orderDetail['object_id'] == 1 ? ' - GV' : '') : '') : '',
                        'qty_order' => number_format($orderDetail['qtyProduct'], 2) ?? '',
                        'price_import' => $orderDetail['price'] ? number_format((float)$orderDetail['price']) : '',
                        'into_money' => $orderDetail['price'] ? number_format(((float)$orderDetail['qtyProduct'] ?? '') *  ((float)$orderDetail['price'] ?? '')) : '',
                        'note' => $orderDetail['comment'],
                    ];
                    $j++;

                }
            }
        }
        $data['dataTr'] = $dataTr;

        $data['pagination'] = $dataSupplier->appends(request()->except(['_token', '_pjax']))->links($this->templatePathAdmin . 'component.pagination');
        $data['resultItems'] = sc_language_render('admin.result_item', ['item_from' => $dataSupplier->firstItem(), 'item_to' => $dataSupplier->lastItem(), 'total' => $dataSupplier->total()]);
        $currentDay = nowDateString();
        $from_day = $dataSearch['from_to'] ? $dataSearch['from_to'] : $currentDay;
        $end_day = $dataSearch['end_to'] ? $dataSearch['end_to'] : $currentDay;
        $typeSearch = [
            1 => 'Mặt hàng Davicorp',
            2 => 'Hàng tươi Davicook',
            3 => 'Hàng khô Davicook',
        ];
        $departments = ShopDepartment::get();
        $optionDepartment = '';
        foreach ($departments as $key => $department) {
            $optionDepartment .= '<option  ' . (($dataSearch['department'] == $department->id) ? "selected" : "") . ' value="' . $department->id . '">' . $department->name . '</option>';
        }
        $optionTypeSearch = '';
        foreach ($typeSearch as $key => $value) {
            $optionTypeSearch .= '<option  ' . ( is_array($dataSearch['key_export']) ? ( in_array($key, $dataSearch['key_export']) ? "selected" : "") : "" ) . ' value="' . $key . '">' . $value . '</option>';
        }
        $data['topMenuRight'][] = '
                <form action="' . sc_route_admin('admin_report_import_2target.template_1.index') . '" id="button_search">
                <input type="hidden" name="check_filter" id="check_filter" value=""/>
                <input type="hidden" name="data_count" value="' . $countData . '" id="data_count">
                <input type="hidden" name="title" value="Mau1" id="title_report">
                <br>
                    <div class="input-group float-left">
                         <div class="col-md-2" >
                            <div class="form-group">
                                <div class="input-group ">
                                    <select id="key_export" style="width: 100% !important;" class="form-control d-none" name="key_export[]" multiple="multiple">
                                        ' . $optionTypeSearch . '
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2" >
                            <div class="form-group" >
                                <div class="input-group ">
                                    <select class="form-control" name="department" id="department" >
                                        <option value="">' . sc_language_render('admin.customer.type_customer') . '</option>
                                        ' . $optionDepartment . '
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2" >
                            <div class="form-group" >
                                <div class="input-group ">
                                    <input type="text" name="from_to" id="from_to" class="form-control input-sm date_time rounded-0" style="text-align: center"  placeholder="Chọn ngày" value="' . $from_day . '"/> 
                                </div>
                            </div>
                        </div>
                        <div class="input-group-addon" style="color: #93A7C1">Đến</div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <div class="input-group">
                                    <input type="text" name="end_to" id="end_to" class="form-control input-sm date_time rounded-0" style="text-align: center"  placeholder="Chọn ngày" value="' . $end_day . '"/> 
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2 col-2-custom">
                            <div class="form-group">
                                <div class="input-group">
                                <input type="text" name="keyword" id="keyword" class="form-control rounded-0 float-right" placeholder="Tên và mã KH, Tên và mã SP" value="' . $keyword . '">
                                    <div class="input-group-append">
                                        <button type="submit" class="btn btn-primary  btn-flat btn-search"><i class="fas fa-search"></i></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2" style="max-width: 360px; min-width: 360px">
                            <div class="form-group">
                                <div class="input-group">
                                <a class="btn  btn-success  btn-flat" title="" id="button_export_filter">
                                        <i class="fa fa-file-export" title="' . sc_language_render('category-export') . '"></i> Xuất Excel</a> &nbsp;&nbsp;
                                <a href="javascript:void(0)" class="btn btn-flat btn btn-info text-white" onclick="savePdf()"><i class="fas fa-file-pdf"></i> Xuất PDF</a> &nbsp;&nbsp;
                                <a href="javascript:void(0)" class="btn btn-flat btn btn-info text-white" onclick="showModalImportOrder(1)"><i class="fas fa-file-import"></i> Nhập Kho</a>
                                </div>
                            </div>
                        </div>
                        
                    </div>
                    
                </form>
                ';
        //=menuSearch
        $data['warehouse'] = AdminWarehouse::all();
        return view($this->templatePathAdmin . 'screen.report.import_price.index_template_1')
            ->with($data);
    }

    /**
     * Xuất excel mẫu 1 - Báo cáo nhập hàng 2 chỉ tiêu.
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function exportExcelImportPriceReport()
    {
        $dataSearch = [
            'from_to' => sc_clean(request('from_to') ?? ''),
            'end_to' => sc_clean(request('end_to') ?? ''),
            'keyword' => sc_clean(request('keyword') ?? ''),
            'department' => sc_clean(request('department') ?? ''),

        ];
        $from_to = $dataSearch['from_to'] ?? \Carbon\Carbon::make($dataSearch['from_to'])->format("d/m/Y");
        $end_to = $dataSearch['end_to'] ?? \Carbon\Carbon::make($dataSearch['end_to'])->format("d/m/Y");
        $fileName = 'BaoCaoHangNhapNhom2ChiTieu-'. $from_to .'-'.$end_to. '.xlsx';
        $fileName = str_replace("/","_",$fileName);
        $keyExport = explode(',', request('key_export')) ?? [];
        $attributes = [
            'name_admin' =>  Admin::user()->name,
            'time' =>  now(),
            'desc' =>  $fileName,
            'type' =>  'BC Nhap Hang Mau 1',
            'kind' =>  'Xuất Excel',
        ];
        AdminSystemChangeHistory::storeData($attributes);
        $dataPriceImport = $this->getDataPriceImport($keyExport, $dataSearch);
        $dataSupplier = $dataPriceImport->sortBy([
            'supplier_name',
            'sort',
            'product_name',
            'qtyProduct',
        ]);
        return Excel::download(new AdminImportReportExportTemplate($dataSupplier, $dataSearch), $fileName);
    }

    /**
     * Xuất Pdf mẫu 1 - Báo cáo nhập hàng 2 chỉ tiêu.
     * @return false|\Illuminate\Http\RedirectResponse|string|string[]|null
     */
    public function exportPdfImportPriceReport()
    {
        $dataSearch = [
            'from_to' => sc_clean(request('from_to') ?? ''),
            'end_to' => sc_clean(request('end_to') ?? ''),
            'keyword' => sc_clean(request('keyword') ?? ''),
            'department' => sc_clean(request('department') ?? ''),
        ];
        $keyExport = explode(',', request('key_export')) ?? [];
        $fileName = 'BCNhapHangMau1-'. $dataSearch['from_to'] .'-'. $dataSearch['end_to']. '.pdf';
        $fileName = str_replace("/","_",$fileName);
        $attributes = [
            'name_admin' =>  Admin::user()->name,
            'time' =>  now(),
            'desc' =>  $fileName,
            'type' =>  'BC Nhap Hang Mau 1',
            'kind' =>  'Xuất Pdf',
        ];
        AdminSystemChangeHistory::storeData($attributes);
        $dataPriceImport = $this->getDataPriceImport($keyExport, $dataSearch);
        $dataSupplier = $dataPriceImport->sortBy([
            'supplier_name',
            'sort',
            'product_name',
            'qtyProduct',
        ]);
        if (!count($dataSupplier) > 0) {
            return redirect()->back()->with('error', 'Không có dữ liệu');
        }
        $html = view($this->templatePathAdmin . 'print.report_import_price_template_one')
            ->with(['dataSupplier' => $dataSupplier, 'dataSearch' => $dataSearch])->render();
        $html = mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8');
        return $html;
    }

    /**
     * Báo cáo hàng nhập 2 chỉ tiêu - Chi tiết.
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function importDetailReport()
    {
        $data = [
            'title' => sc_language_render('admin.import.detail.report.title'),
            'subTitle' => '',
            'icon' => 'fa fa-indent',
            'urlDeleteItem' => '',
            'removeList' => '', // 1 - Enable function delete list item
            'buttonRefresh' => '', // 1 - Enable button refresh
            'buttonSort' => '', // 1 - Enable button sort
            'css' => '',
            'js' => '',
            'url_action' => '',
            'url_export_excel' => sc_route_admin('admin_report_import_2target.template_2.export_excel'),
            'url_export_pdf' => sc_route_admin('admin_report_import_2target.template_2.export_pdf')
        ];

        $listTh = [
            'stt' => sc_language_render('admin.report.stt'),
            'code' => sc_language_render('admin.import.report.supplies.code'),
            'name' => sc_language_render('admin.import.report.supplies.name'),
            'dvt' => 'ĐVT',
            'qty_order' => sc_language_render('admin.import.report.qty_order'),
            'price_import' => sc_language_render('admin.import.report.price_import'),
            'into_money' => sc_language_render('admin.import.report.into_money'),
            'note' => sc_language_render('admin.import.report.note'),

        ];
        $search_supplier = sc_clean(request('search_supplier') ?? '');

        $dataSearch = [
            'search_supplier' => $search_supplier,
            'from_to' => sc_clean(request('from_to') ?? ''),
            'end_to' => sc_clean(request('end_to') ?? ''),
            'key_export' => sc_clean(request('key_export') ?? []),
            'check_filter' => sc_clean(request('check_filter') ?? ''),
            'department' => sc_clean(request('department') ?? ''),
        ];
        $cssTh = [
            'stt' => 'text-align: center; width: 7%',
            'code' => 'text-align: center; width: 9%',
            'name' => ' width: 30%',
            'dvt' => 'text-align: center; width: 7%',
            'qty_order' => 'text-align: center; width: 9%',
            'price_import' => 'text-align: center; width: 9%',
            'into_money' => 'text-align: center; width: 9%',
            'note' => 'text-align: center;  width: 20%',
        ];
        $cssTd = [
            'stt' => 'text-align: center',
            'code' => 'text-align: center',
            'name' => '',
            'dvt' => 'text-align: center',
            'qty_order' => 'text-align: right',
            'price_import' => 'text-align: right',
            'into_money' => 'text-align: right',
            'note' => '',
        ];
        $data['cssTh'] = $cssTh;
        $data['cssTd'] = $cssTd;
        $data['listTh'] = $listTh;
        $dataTr = [];

        session()->forget('dataReportImportPriceTemplateTwo');

        if (empty($dataSearch['check_filter'])) {
            $dataPriceImport = collect([]);
        } else {
            $dataPriceImport = $this->getDataPriceImport($dataSearch['key_export'],$dataSearch);
        }

        $dataPriceImport = $dataPriceImport->sortBy([
            'supplier_name',
//            'sort',
//            'product_name',
//            'qtyProduct',
        ]);
        $dataSupplier = $dataPriceImport->groupBy('supplier_code');
        $arrItem = [];
        $arrSessionData = [];

        foreach ($dataSupplier as $keySupplier => $itemSupplier) {
            $arrItem[] = $itemSupplier->first();
            $arrSessionData[] = [
                'stt' => '',
                'code' => '',
                'name' => $itemSupplier->first()['supplier_name'],
                'dvt' => '',
                'qty_order' => '',
                'price_import' => '',
                'into_money' => '',
                'note' => '',
            ];;
            $dataTr[] = [
                'stt' => '',
                'code' => '',
                'name' => $keySupplier ? '<span style="font-weight: bold; background-color: yellow; text-transform: uppercase;">' . $itemSupplier->first()['supplier_name'] . '</span>' : '<span style="font-weight: bold; color: red">Nhà cung cấp đã bị xóa</span>',
                'dvt' => '',
                'qty_order' => '',
                'price_import' => '',
                'into_money' => '',
                'note' => '',
            ];
            $collectionDataProduct = new Collection();
            $dataProduct = $itemSupplier->groupBy('product_id', 'price');
            $i = 1;
            foreach ($dataProduct as $itemProducts) {
                $comment = '';
                foreach ($itemProducts as $row) {
                    if ($row['comment'] != '') {
                        $comment .= '<span>' . $row['customer_name'] . ' : ('. $row['qtyProduct'].') ' . $row['comment'] . '</span><br>';
                    }
                }
                $collectionDataProduct->push(
                    [
                        'sort' => $itemProducts->first()['sort'],
                        'supplier_code' => $itemProducts->first()['supplier_code'],
                        'code' => $itemProducts->first()['product_code'] ?? '',
                        'name' => $itemProducts->first()['product_name'] ?? '',
                        'product_unit' => $itemProducts->first()['product_unit'] ?? '',
                        'qty_order' => $itemProducts->sum('qtyProduct') ?? '',
                        'price_import' => $itemProducts->first()['price'],
                        'note' => $comment,
                    ]
                );
            }
            $collectionDataProduct = $collectionDataProduct->sortBy([
                'sort',
                'qty_order',
            ]);
            foreach ($collectionDataProduct as $key => $item){
                $arrItem[] = $item;
                $arrSessionData[] = $item;
                $dataTr[] = [
                    'stt' => $i,
                    'code' => $item['code'] ?? '',
                    'name' => $item['name'] ?? '',
                    'dvt' => $item['product_unit'] ?? '',
                    'qty_order' => isset($item['qty_order']) ? number_format($item['qty_order'],2) : '',
                    'price_import' => isset($item['price_import']) ? number_format($item['price_import']) : '',
                    'into_money' => isset($item['price_import']) ? number_format(((float)$item['price_import'] ?? '') * ((float)$item['qty_order'] ?? '')) : '',
                    'note' => $item['note'] ?? '',
                ];
                $i++;
            }
        }
        $countData = (count($arrSessionData));
        session()->put('dataReportImportPriceTemplateTwo', $arrSessionData);
//        phân trang
        $outputTr = [];
        $count = 0;
        $page = request('page') ?? 1;
        $ofsetEnd = (config('pagination.admin.medium') * $page);


        $ofsetStart = $ofsetEnd - (config('pagination.admin.medium') - 1);
        for ($i = ($ofsetStart == 1) ? 0 : $ofsetStart; $i <= $ofsetEnd; $i++) {

            if (empty($dataTr[$i])) {
                continue;
            } else {
                $tr = $dataTr[$i];
            }
            if ($tr['stt'] == '') {
                $outputTr[] = $tr;
                continue;
            }
            $outputTr[] = $tr;
            $count++;
            if ($count > config('pagination.admin.medium')) {
                break;
            }
        }

        $haveHeader = 0;

        if (!empty(array_first($outputTr)['stt'])) {
            $haveHeader = 1;
        }

        if ($haveHeader) {
            for ($i = $ofsetStart; $i >= 0; $i--) {
                if (empty($dataTr[$i]['stt'])) {
                    array_unshift($outputTr, $dataTr[$i]);
                    break;
                }
            }
        }
        $data['dataTr'] = $outputTr;
        $dataTmp = $this->paginate($arrItem);

        $data['pagination'] = $dataTmp->appends(request()->except(['_token', '_pjax']))->links($this->templatePathAdmin . 'component.pagination');
        $data['resultItems'] = sc_language_render('admin.result_item', ['item_from' => $dataTmp->firstItem(), 'item_to' => $dataTmp->lastItem(), 'total' => $dataTmp->total()]);
        $currentDay = nowDateString();
        $from_day = $dataSearch['from_to'] ? $dataSearch['from_to'] : $currentDay;
        $end_day = $dataSearch['end_to'] ? $dataSearch['end_to'] : $currentDay;
        $typeSearch = [
            1 => 'Mặt hàng Davicorp',
            2 => 'Hàng tươi Davicook',
            3 => 'Hàng khô Davicook',
        ];
        $optionTypeSearch = '';
        foreach ($typeSearch as $key => $value) {
            $optionTypeSearch .= '<option  ' . ( is_array($dataSearch['key_export']) ? ( in_array($key, $dataSearch['key_export']) ? "selected" : "") : "" ) . ' value="' . $key . '">' . $value . '</option>';
        }
        $departments = ShopDepartment::get();
        $optionDepartment = '';
        foreach ($departments as $key => $department) {
            $optionDepartment .= '<option  ' . (($dataSearch['department'] == $department->id) ? "selected" : "") . ' value="' . $department->id . '">' . $department->name . '</option>';
        }
        $data['topMenuRight'][] = '
                <form action="' . sc_route_admin('admin_report_import_2target.template_2.detail') . '" id="button_search">
                <input type="hidden" name="check_filter" id="check_filter" value=""/>
                <input type="hidden" name="data_count" value="' . $countData . '" id="data_count">
                <input type="hidden" name="title" value="Mau2" id="title_report">
                <br>
                    <div class="input-group float-left">
                         <div class="col-md-2" >
                            <div class="form-group" >
                                <div class="input-group ">
                                    <select id="key_export" style="width: 100%" class="form-control d-none" name="key_export[]" multiple="multiple">
                                        ' . $optionTypeSearch . '
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2" >
                            <div class="form-group" >
                                <div class="input-group ">
                                    <select class="form-control" name="department" id="department" >
                                        <option value="">' . sc_language_render('admin.customer.type_customer') . '</option>
                                        ' . $optionDepartment . '
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2" >
                            <div class="form-group" >
                                <div class="input-group ">
                                    <input type="text" name="from_to" id="from_to" class="form-control input-sm date_time rounded-0" style="text-align: center"  placeholder="Chọn ngày" value="' . $from_day . '"/> 
                                </div>
                            </div>
                        </div>
                        <div class="input-group-addon" style="color: #93A7C1">Đến</div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <div class="input-group">
                                    <input type="text" name="end_to" id="end_to" class="form-control input-sm date_time rounded-0" style="text-align: center"  placeholder="Chọn ngày" value="' . $end_day . '"/> 
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <div class="input-group">
                                <input type="text" name="search_supplier" id="search_supplier" class="form-control rounded-0 float-right" placeholder="Tìm tên, mã NCC" value="' . $search_supplier . '">
                                    <div class="input-group-append">
                                        <button type="submit" class="btn btn-primary  btn-flat btn-search"><i class="fas fa-search"></i></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2" style="max-width: 360px; min-width: 360px">
                            <div class="form-group">
                                <div class="input-group">
                                <a class="btn  btn-success  btn-flat" title="" id="button_export_filter">
                                        <i class="fa fa-file-export" title="' . sc_language_render('category-export') . '"></i> ' . sc_language_render('category-export') .
            '</a> &nbsp;&nbsp;
                                <a href="javascript:void(0)" class="btn btn-flat btn btn-info text-white" onclick="savePdf()"><i class="fas fa-file-pdf"></i>&nbsp;' . sc_language_render("admin.report.print_pdf") . '</a>&nbsp;&nbsp;
                                <a href="javascript:void(0)" class="btn btn-flat btn btn-info text-white" onclick="showModalImportOrder(2)"><i class="fas fa-file-import"></i> Nhập Kho</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
                ';
        $data['warehouse'] = AdminWarehouse::all();
        //=menuSearch
        return view($this->templatePathAdmin . 'screen.report.import_price.index_template_1')
            ->with($data);

    }

    /**
     * Xuất excel báo cáo hàng nhập 2 chỉ tiêu - Chi tiết.
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function exportExcelImportDetailReport()
    {
        $dataSearch = [
            'from_to' => sc_clean(request('from_to') ?? ''),
            'end_to' => sc_clean(request('end_to') ?? ''),
            'search_supplier' => sc_clean(request('search_supplier') ?? ''),
            'select_warehouse' => sc_clean(request('select_warehouse') ?? ''),
            'department' => sc_clean(request('department') ?? ''),
        ];

        $from_to = $dataSearch['from_to'] ?? \Carbon\Carbon::make($dataSearch['from_to'])->format("d/m/Y");
        $end_to = $dataSearch['end_to'] ?? \Carbon\Carbon::make($dataSearch['end_to'])->format("d/m/Y");
        $fileName = 'BaoCaoNhapHangChiTiet-'. $from_to .'-'.$end_to. '.xlsx';
        $fileName = str_replace("/","_",$fileName);
        $attributes = [
            'name_admin' =>  Admin::user()->name,
            'time' =>  now(),
            'desc' =>  $fileName,
            'type' =>  'BC Nhap Hang Mau 2 - Chi Tiet',
            'kind' =>  'Xuất Excel',
        ];
        AdminSystemChangeHistory::storeData($attributes);
//        $keyExport = explode(',', request('key_export')) ?? [];
//        $dataPriceImport = $this->getDataPriceImport($keyExport, $dataSearch);
//        $dataSupplier = $dataPriceImport->groupBy('supplier_code');

        return Excel::download(new AdminImportDetailReportExportTemplate($dataSupplier = [], $dataSearch), $fileName);
    }

    /**
     * In pdf báo cáo hàng nhập 2 chỉ tiêu - Chi tiết.
     * @return false|\Illuminate\Http\RedirectResponse|string|string[]|null
     */
    public function exportPdfImportDetailReport()
    {
        $dataSearch = [
            'from_to' => sc_clean(request('from_to') ?? ''),
            'end_to' => sc_clean(request('end_to') ?? ''),
            'search_supplier' => sc_clean(request('search_supplier') ?? ''),
            'select_warehouse' => sc_clean(request('select_warehouse') ?? ''),
            'department' => sc_clean(request('department') ?? ''),
        ];

        $fileName = 'BCNhapHangMau2ChiTiet-'. $dataSearch['from_to'] .'-'. $dataSearch['end_to']. '.pdf';
        $fileName = str_replace("/","_",$fileName);
        $attributes = [
            'name_admin' =>  Admin::user()->name,
            'time' =>  now(),
            'desc' =>  $fileName,
            'type' =>  'BC Nhap Hang Mau 2 - Chi Tiet',
            'kind' =>  'Xuất Pdf',
        ];
        AdminSystemChangeHistory::storeData($attributes);

//        $keyExport = explode(',', request('key_export')) ?? [];
//        $dataPriceImport = $this->getDataPriceImport($keyExport, $dataSearch);
//        if (!count($dataPriceImport) > 0) {
//            return redirect()->route('admin_report_import_2target.template_2.detail')->with('error', 'Không có dữ liệu');
//        }
//        $dataSupplier = $dataPriceImport->groupBy('supplier_code');
        $dataSupplier = [];
        $html = view($this->templatePathAdmin . 'print.report_import_price_template_two')
            ->with(['dataSupplier' => $dataSupplier, 'dataSearch' => $dataSearch])->render();
        $html = mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8');
        return $html;
    }


    /**
     * Handel Lấy dữ liệu đổ về báo cáo 2 chỉ tiêu hàng nhập.
     * @param $keyExport
     * @param $dataSearch
     * @return \Illuminate\Database\Eloquent\Collection|Collection
     */
    public function getDataPriceImport($keyExport, $dataSearch)
    {
        if ((in_array(1,$keyExport)) && (count($keyExport) == 1)) {
            # Mặt hàng Davicorp.
            return (new AdminOrder())->getListPriceImportProductDavicorp($dataSearch);
        }

        if ((in_array(2,$keyExport)) && (count($keyExport) == 1)) {
            # Mặt hàng tươi Davicook.
            return (new AdminOrder())->getListProductFreshToReportImportPrice($dataSearch, 1);
        }

        if ((in_array(3,$keyExport)) && (count($keyExport) == 1)) {
            # Mặt hàng khô Davicook.
            return (new AdminOrder())->getListProductDryToReportImportPrice($dataSearch);
        }

        if (empty(array_diff([1, 2], $keyExport)) && (count($keyExport) == 2)) {
            # Mặt hàng Daivcorp + Tươi Davicook.
            $getDataDavicorp = (new AdminOrder())->getListPriceImportProductDavicorp($dataSearch);
            $getDetailDavicookByStatusProductFresh = (new AdminOrder())->getListProductFreshToReportImportPrice($dataSearch, 1);
            return $getDataDavicorp->mergeRecursive($getDetailDavicookByStatusProductFresh);
        }

        if (empty(array_diff([1, 3], $keyExport)) && (count($keyExport) == 2)) {
            # Mặt hàng Daivcorp + Khô Davicook.
            $getDataDavicorp = (new AdminOrder())->getListPriceImportProductDavicorp($dataSearch);
            $getDetailDavicookByStatusProductDry = (new AdminOrder())->getListProductDryToReportImportPrice($dataSearch);
            return $getDataDavicorp->mergeRecursive($getDetailDavicookByStatusProductDry);
        }

        if (empty(array_diff([2, 3], $keyExport)) && (count($keyExport) == 2)) {
            # Mặt hàng Tươi Daivcook + Khô Davicook.
            $getDetailDavicookByStatusProductDry = (new AdminOrder())->getListProductDryToReportImportPrice($dataSearch);
            $getDetailDavicookByStatusProductFresh = (new AdminOrder())->getListProductFreshToReportImportPrice($dataSearch, 1);
            return $getDetailDavicookByStatusProductDry->mergeRecursive($getDetailDavicookByStatusProductFresh);
        }

        # Get data Corp + Tươi Khô Cook.
        $getDataDavicorp = (new AdminOrder())->getListPriceImportProductDavicorp($dataSearch);
        $getDetailDavicookByStatusProductFresh = (new AdminOrder())->getListProductFreshToReportImportPrice($dataSearch, 1);
        $getDetailDavicookByStatusProductDry = (new AdminOrder())->getListProductDryToReportImportPrice($dataSearch);
        $objDavicookOrderDetailMerge = $getDetailDavicookByStatusProductDry->mergeRecursive($getDetailDavicookByStatusProductFresh);

        return $getDataDavicorp->mergeRecursive($objDavicookOrderDetailMerge);
    }

    /**
     * Handel lấy dữ liệu đổ về in tem.
     * @param $keyExport
     * @param $dataSearch
     * @param null $ids
     * @return array|mixed
     */
    public function getDataStampsByFilter($keyExport, $dataSearch, $ids = null)
    {
        if ((in_array(1,$keyExport)) && (count($keyExport) == 1)) {
            # Mặt hàng Davicorp.
            return (new AdminOrder())->getStampList($dataSearch)['item'] ?? [];
        }

        if ((in_array(2,$keyExport)) && (count($keyExport) == 1)) {
            # Mặt hàng tươi Davicook.
            return (new AdminOrder())->getProductFreshDavicookToReportStamp($dataSearch, $ids, 1)['item'] ?? [];
        }

        if ((in_array(3,$keyExport)) && (count($keyExport) == 1)) {
            # Mặt hàng khô Davicook.
            return (new AdminOrder())->getProductDryDavicookToReportStamp($dataSearch, $ids)['item'] ?? [];
        }

        if (empty(array_diff([1, 2], $keyExport)) && (count($keyExport) == 2)) {
            # Mặt hàng Daivcorp + Tươi Davicook.
            $getDataDavicorp = (new AdminOrder())->getStampList($dataSearch)['item'] ?? [];
            $getDetailDavicookByStatusProductFresh = (new AdminOrder())->getProductFreshDavicookToReportStamp($dataSearch, $ids, 1)['item'] ?? [];
            return array_merge($getDataDavicorp, $getDetailDavicookByStatusProductFresh);
        }

        if (empty(array_diff([1, 3], $keyExport)) && (count($keyExport) == 2)) {
            # Mặt hàng Daivcorp + Khô Davicook.
            $getDataDavicorp = (new AdminOrder())->getStampList($dataSearch)['item'];
            $getDetailDavicookByStatusProductDry = (new AdminOrder())->getProductDryDavicookToReportStamp($dataSearch, $ids)["item"] ?? [];
            return array_merge($getDataDavicorp, $getDetailDavicookByStatusProductDry);
        }

        if (empty(array_diff([2, 3], $keyExport)) && (count($keyExport) == 2)) {
            # Mặt hàng Tươi Daivcook + Khô Davicook.
            $getDetailDavicookByStatusProductDry = (new AdminOrder())->getProductDryDavicookToReportStamp($dataSearch, $ids)["item"] ?? [];
            $getDetailDavicookByStatusProductFresh = (new AdminOrder())->getProductFreshDavicookToReportStamp($dataSearch, $ids, 1)['item'] ?? [];
            return array_merge($getDetailDavicookByStatusProductDry, $getDetailDavicookByStatusProductFresh);
        }

        # Get data Corp + Tươi + Khô Cook
        $getDataDavicorp = (new AdminOrder())->getStampList($dataSearch)['item'] ?? [];
        $objDavicookOrderDetailFresh = (new AdminOrder())->getProductFreshDavicookToReportStamp($dataSearch, $ids, 1)["item"] ?? [];
        $objDavicookOrderDetailDry = (new AdminOrder())->getProductDryDavicookToReportStamp($dataSearch, $ids)['item'] ?? [];
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
            $getDataDavicorp = (new AdminOrder())->getStampList($dataSearch, $ids);
            $data['listQr'] = array_merge($getDataDavicorp["qr"]->toArray() ?? [], $data['listQr']);
            $data['listItem'] = $getDataDavicorp['item'] ?? [];
            return $data;
        }

        if ((in_array(2,$keyExport)) && (count($keyExport) == 1)) {
            $getDetailDavicookByStatusProductFresh = (new AdminOrder())->getProductFreshDavicookToReportStamp($dataSearch, $ids, 1);
            $data['listQr'] = array_merge($getDetailDavicookByStatusProductFresh["qr"]->toArray() ?? [], $data['listQr']);
            $data['listItem'] = $getDetailDavicookByStatusProductFresh['item'] ?? [];
            return $data;
        }

        if ((in_array(3,$keyExport)) && (count($keyExport) == 1)) {
            # Mặt hàng tươi Davicook.
            $getDetailDavicookByStatusProductDry = (new AdminOrder())->getProductDryDavicookToReportStamp($dataSearch, $ids);
            $data['listQr'] = array_merge($getDetailDavicookByStatusProductDry["qr"]->toArray() ?? [], $data['listQr']);
            $data['listItem'] = $getDetailDavicookByStatusProductDry['item'] ?? [];
            return $data;
        }

        if (empty(array_diff([1, 2], $keyExport)) && (count($keyExport) == 2)) {
            # Mặt hàng Daivcorp + Tươi Davicook.
            $getDataDavicorp = (new AdminOrder())->getStampList($dataSearch, $ids);
            $getDetailDavicookByStatusProductFresh = (new AdminOrder())->getProductFreshDavicookToReportStamp($dataSearch, $ids, 1);
            $data['listQr'] = array_merge($getDataDavicorp["qr"]->toArray() ?? [], $data['listQr']);
            $data['listQr'] = array_merge($getDetailDavicookByStatusProductFresh["qr"]->toArray() ?? [], $data['listQr']);
            $data['listItem'] = array_merge($getDataDavicorp['item'] ?? [], $getDetailDavicookByStatusProductFresh['item'] ?? []);
            return $data;
        }

        if (empty(array_diff([1, 3], $keyExport)) && (count($keyExport) == 2)) {
            # Mặt hàng Daivcorp + Khô Davicook.
            $getDataDavicorp = (new AdminOrder())->getStampList($dataSearch, $ids);
            $getDetailDavicookByStatusProductDry = (new AdminOrder())->getProductDryDavicookToReportStamp($dataSearch, $ids);
            $data['listQr'] = array_merge($getDataDavicorp["qr"]->toArray() ?? [], $data['listQr']);
            $data['listQr'] = array_merge($getDetailDavicookByStatusProductDry["qr"]->toArray() ?? [], $data['listQr']);
            $data['listItem'] = array_merge($getDataDavicorp['item'] ?? [], $getDetailDavicookByStatusProductDry['item'] ?? []);
            return $data;
        }

        if (empty(array_diff([2, 3], $keyExport)) && (count($keyExport) == 2)) {
            # Mặt hàng Tươi Daivcook + Khô Davicook.
            $getDetailDavicookByStatusProductDry = (new AdminOrder())->getProductDryDavicookToReportStamp($dataSearch, $ids);
            $getDetailDavicookByStatusProductFresh = (new AdminOrder())->getProductFreshDavicookToReportStamp($dataSearch, $ids, 1);
            $data['listQr'] = array_merge($getDetailDavicookByStatusProductDry["qr"]->toArray() ?? [], $data['listQr']);
            $data['listQr'] = array_merge($getDetailDavicookByStatusProductFresh["qr"]->toArray() ?? [], $data['listQr']);
            $data['listItem'] = array_merge($getDetailDavicookByStatusProductDry['item'] ?? [], $getDetailDavicookByStatusProductFresh['item'] ?? []);
            return $data;
        }

        # Get data Corp + Tươi + Khô Cook
        $getDataDavicorp = (new AdminOrder())->getStampList($dataSearch, $ids);
        $objDavicookOrderDetailFresh = (new AdminOrder())->getProductFreshDavicookToReportStamp($dataSearch, $ids, 1);
        $objDavicookOrderDetailDry = (new AdminOrder())->getProductDryDavicookToReportStamp($dataSearch, $ids);
        $objDavicookOrderDetailMerge = array_merge($objDavicookOrderDetailFresh["item"] ?? [], $objDavicookOrderDetailDry['item'] ?? []);
        $data['listQr'] = array_merge($getDataDavicorp["qr"]->toArray() ?? [], $data['listQr']);
        $data['listQr'] = array_merge($objDavicookOrderDetailFresh["qr"]->toArray() ?? [], $data['listQr']);
        $data['listQr'] = array_merge($objDavicookOrderDetailDry["qr"]->toArray() ?? [], $data['listQr']);
        $data['listItem'] = array_merge($getDataDavicorp['item'] ?? [], $objDavicookOrderDetailMerge);
        return $data;
    }
}
