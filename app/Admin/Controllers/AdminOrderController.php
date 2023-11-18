<?php

namespace App\Admin\Controllers;

use App\Admin\Models\AdminCustomer;
use App\Admin\Models\AdminDriver;
use App\Admin\Models\AdminDriverCustomer;
use App\Admin\Models\AdminEditTimePermission;
use App\Admin\Models\AdminNotification;
use App\Admin\Models\AdminOrder;
use App\Admin\Models\AdminProduct;
use App\Admin\Models\AdminProductPriceDetail;
use App\Admin\Models\AdminShopOrderChangeExtra;
use App\Admin\Models\AdminUnit;
use App\Exports\DavicorpOrder\AdminExportMultipleSheet;
use App\Exports\DavicorpOrder\AdminExportOrderToEinvoice;
use App\Exports\DavicorpOrder\AdminMultipleSheetSalesInvoiceListRealOrder;
use App\Exports\ReturnOrder\AdminExportOrderReturn;
use App\Front\Models\ShopCustomer;
use App\Front\Models\ShopDepartment;
use App\Front\Models\ShopGenId;
use App\Front\Models\ShopOrder;
use App\Front\Models\ShopOrderDetail;
use App\Front\Models\ShopOrderHistory;
use App\Front\Models\ShopOrderObject;
use App\Front\Models\ShopOrderReturnHistory;
use App\Front\Models\ShopOrderStatus;
use App\Front\Models\ShopProduct;
use App\Front\Models\ShopProductSupplier;
use App\Front\Models\ShopSupplier;
use App\Front\Models\ShopUserPriceboard;
use App\Front\Models\ShopEInvoice;
use App\Front\Models\ShopEInvoiceDetail;
use App\Http\Requests\Admin\AdminOrderDetailEditRequest;
use App\Http\Requests\Admin\AdminOrderEditRequest;
use App\Http\Requests\Admin\AdminOrderReturnRequest;
use App\Traits\OrderDavicookTraits;
use App\Traits\OrderTraits;
use Dompdf\Dompdf;
use http\Env\Response;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use SCart\Core\Admin\Admin;
use Illuminate\Support\Collection;
use SCart\Core\Admin\Controllers\RootAdminController;
use App\Exceptions\ImportException;
use Throwable;
use Validator;
use function Symfony\Component\DomCrawler\all;
use function Symfony\Component\HttpKernel\Debug\format;
use function Symfony\Component\HttpKernel\HttpCache\save;
use Illuminate\Http\Request;

class AdminOrderController extends RootAdminController
{
    public $statusPayment;
    public $statusOrder;
    public $orderObjects;
    public $orderCustomer;
    public $statusShipping;

    use OrderTraits;
    public function __construct()
    {
        parent::__construct();
        $this->statusOrder = ShopOrderStatus::getIdAll();
        $this->orderObjects = ShopOrderObject::getIdAll();
        $this->orderCustomer = ShopCustomer::getIdAll();
    }

    /**
     * Index interface.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function index()
    {
        $data = [
            'title' => sc_language_render('order.admin.list'),
            'subTitle' => '',
            'icon' => 'fa fa-indent',
            'urlDeleteItem' => sc_route_admin('admin_order.delete'),
            'urlCombineOrder' => sc_route_admin('admin_order.merge'),
            'removeList' => 1, // 1 - Enable function delete list item
            'buttonRefresh' => 1, // 1 - Enable button refresh
            'buttonSort' => 1, // 1 - Enable button sort
            'css' => '',
            'js' => '<script>$(".date_time").datepicker({ dateFormat: "' . config('admin.datepicker_format') . '" });</script>',
            'is_orderlist' => 1,
            'permGroup' => 'order'
        ];
        //Department
        $departments = ShopDepartment::all()->keyBy('id');
        //Process add content
        $data['menuRight'] = sc_config_group('menuRight', \Request::route()->getName());
        $data['menuLeft'] = sc_config_group('menuLeft', \Request::route()->getName());
        $data['topMenuRight'] = sc_config_group('topMenuRight', \Request::route()->getName());
        $data['topMenuLeft'] = sc_config_group('topMenuLeft', \Request::route()->getName());
        $data['blockBottom'] = sc_config_group('blockBottom', \Request::route()->getName());
        //List th
        $listTh = [
            'id' => 'ID',
            'customer_name' => 'Tên khách hàng',
            'object' => 'Đối tượng',
            'explain' => 'Diễn giải',
            'created_at' => 'Ngày đặt hàng',
            'bill_date' => 'Ngày trên đơn',
            'delivery_time' => 'Ngày giao hàng',
            'total' => 'Tổng tiền',
            'status' => 'Trạng thái',
            'priority_status' => 'Độ ưu tiên',
            'action' => sc_language_render('action.title'),
        ];
        $cssTh = [
            'total' => 'text-align: right',
            'status' => 'text-align: center',
            'priority_status' => 'text-align: center; max-width: 110px; word-wrap: break-word',
        ];
        $data['cssTh'] = $cssTh;

        //Customize collumn size and align
        $cssTd = [
            'id' => 'min-width: 100px',
            'customer_name' => 'width: auto; max-width: 240px',
            'object' => 'width: 100px',
            'explain' => 'text-align: left; width: 100px',
            'created_at' => 'width: 150px',
            'bill_date' => 'width: 150px',
            'delivery_time' => 'width: 150px',
            'total' => 'text-align: right; width: 120px',
            'status' => 'text-align: center; width: 155px; padding-top: 1px; padding-bottom: 10px; vertical-align: middle;',
            'status_id' => 'display: none',
            'priority_status' => 'text-align: center;',
            'action' => 'text-align: center; width: 350px',
            'customer_kind' => 'display: none',
        ];
        $data['cssTd'] = $cssTd;

        //Sort input data
        $arrSort = [
            'created_at__desc' => 'Ngày đặt hàng giảm dần',
            'created_at__asc' => 'Ngày đặt hàng tăng dần',
            'delivery_time__desc' => 'Ngày giao hàng giảm dần',
            'delivery_time__asc' => 'Ngày giao hàng tăng dần',
            'total__desc' => 'Tổng tiền giảm dần',
            'total__asc' => 'Tổng tiền tăng dần',
        ];
        //Search
        $dataSearch = [
            'keyword' => sc_clean(request('keyword') ?? ''),
            'from_to' => sc_clean(request('from_to')),
            'end_to' => sc_clean(request('end_to')),
            'start_day' => sc_clean(request('start_day') ?? ''),
            'end_day' => sc_clean(request('end_day') ?? ''),
            'sort_order' => sc_clean(request('sort_order') ?? 'id_desc'),
            'arrSort' => $arrSort,
            'order_status' => sc_clean(request('order_status') ?? ''),
            'order_purchase_priority_level' => sc_clean(request('order_purchase_priority_level') ?? ''),
            'order_department' => sc_clean(request('order_department') ?? ''),
            'order_explain' => sc_clean(request('order_explain') ?? ''),
            'order_object' => sc_clean(request('order_object') ?? ''),
            'limit' => sc_clean(request('limit') ?? ''),
            'code' => sc_clean(request('code') ?? ''),
            'customer' => sc_clean(request('customer') ?? ''),
            'option_date' => sc_clean(request('option_date') ?? '')
        ];

        $dataOutPutSearch = (new AdminOrder)->getOrderListAdmin($dataSearch);
        $sum = $dataOutPutSearch['sum'];
        $dataTmp = $dataOutPutSearch['orderList'];
        $nameUrl = URL::full();
        session()->put('nameUrl', $nameUrl);

        $styleStatus = $this->statusOrder;
        array_walk($styleStatus, function (&$v, $k) {
            $v = '<span style="width: 87px" class="badge badge-' . (AdminOrder::$mapStyleStatus[$k] ?? 'light') . '">' . $v . '</span>';
        });
        $dataTr = [];
        $id = 0;
        foreach ($dataTmp as $key => $row) {
            $id++;
            $dataMap = [
                'id' => ($this->checkDifferenceQty($row->details) == 1 ? '<span style="font-weight: bold">'.$row->id_name.'</span>' : $row->id_name) . ' ' . (($row->edited ?? '') ? '<i class="fas fa-exclamation-triangle text-orange" title=""></i>' : '' ),
                'customer_name' => $row->name ?? '',
                'customer_kind' => $row->customer->kind ?? 0,
                'object' => $row->object->name ?? '',
                'explain' => ($row->explain ?? '') ??  '',
                'created_at' => Carbon::make($row->created_at ?? '')->format('d/m/Y H:i:s'),Carbon::make($row->created_at ?? '')->format('d/m/Y H:i:s'),
                'bill_date' => Carbon::make($row->bill_date ?? '')->format('d/m/Y'),
                'delivery_time' => Carbon::make($row->delivery_time ?? '')->format('d/m/Y'),
                'total' => sc_currency_render(round($row->total) ?? '', $row->currency ?? 'VND'),
                'status' => ($styleStatus[$row['status']] ?? $row['status']) . ($row->fast_sync_status ? '<br><span class="status-fast">Đã đồng bộ Fast</span>' : ''),
                'status_id' => $row['status'] ?? 1,
                'priority_status' => $this->checkPriorityStatus($row->details) == 1 ? '<span style="color: #ff0000bf">Cần đặt hàng ngay</span>' : 'Bình thường',
                // 'priority_status' => ($row->purchase_priority_level == 1) ? '<span style="color: red">Cần đặt hàng ngay</span>' : 'Bình thường',
            ];

            $dataMap['action'] = '
                <a data-perm="order:detail" href="' . sc_route_admin('admin_order.detail', ['id' => $row['id'] ? $row['id'] : 'not-found-id']) . '"><span title="' . sc_language_render('action.edit') . '" type="button" class="btn btn-flat btn-sm btn-primary"><i class="fa fa-edit"></i></span></a>
                <span data-perm="order:clone" onclick="cloneOrder(\'' . $row['id'] . '\');" title="' . sc_language_render('action.clone') . '" type="button" class="btn btn-flat btn-sm btn-secondary"><i class="fa fa-clipboard"></i></span>
                <span data-perm="order:print" onclick="printModal(\'' . $row['id'] . '\')"  title="' . sc_language_render('order.print.title') . '" type="button" class="btn btn-flat btn-sm btn-warning text-white"><i class="fas fa-print"></i></span>
                <span data-perm="order:delete" onclick="deleteItem(\'' . $row['id'] . '\');"  title="' . sc_language_render('action.delete') . '" class="btn btn-flat btn-sm btn-danger"><i class="fas fa-trash-alt"></i></span>
                ';
            $dataTr[$row['id']] = $dataMap;
        }

        $data['listTh'] = $listTh;
        $data['dataTr'] = $dataTr;
        $data['pagination'] = $dataTmp->appends(request()->except(['_token', '_pjax']))->links($this->templatePathAdmin . 'component.pagination');
//        $data['resultItems'] = sc_language_render('admin.result_item', ['item_from' => $dataTmp->firstItem(), 'item_to' => $dataTmp->lastItem(), 'total' => $dataTmp->total()]);
        $optionPaginate = '';
        $arrayPaginate = [
            0 => 15,
            1 => 50,
            2 => 100,
            3 => 200,
        ];
        foreach ($arrayPaginate as $key => $value) {
            $optionPaginate .= '<option  ' . (($dataSearch['limit'] == $value) ? "selected" : "") . ' value="' . $value . '">' . $value . '</option>';
        }
        $data['resultItems'] = '
                            <div>
                                <div class="form-group" style="display: inline-flex">
                                    <label style="padding-right: 10px; font-weight: normal">Hiển thị</label>
                                    <select name="select_limit" class="form-control form-control-sm" style="width: 80px; margin-bottom: 8px" id="select_limit_paginate">
                                        ' . $optionPaginate . '
                                    </select>
                                    <div style="padding-left: 10px">Của '.$dataTmp->total().' kết quả </div>
                                </div>
                                <br>
                                <div style="font-weight: bold">Tổng giá trị đơn hàng : '.number_format($sum).'₫ </div>
                            </div>';
        //menuRight
        $data['menuRight'][] = '
        <a data-perm="order:sync" href="#" class="btn btn-sm btn-flat btn btn-danger" id="btnSync"><i class="fa fa-sync-alt"></i>&nbsp;Đồng bộ</a>
        <a data-perm="order:update_price" href="#" class="btn btn-sm btn-flat btn btn-info" id="btn_update_supplier"><i class="fa fa-sync-alt"></i>&nbsp;Cập nhập NCC</a>
        <a data-perm="order:update_price" href="#" class="btn btn-sm btn-flat btn btn-primary" id="btn_update_price_order" data-toggle="modal" data-target="#modal_update_price_order"><i class="fa fa-layer-group"></i>&nbsp;Cập nhật giá</a>
        <a data-perm="order:merge" href="#" class="btn btn-sm btn-flat btn btn-primary" id="btnCombine"><i class="fa fa-layer-group"></i>&nbsp;' . sc_language_render("admin.order.combine") . '</a>
        <a data-perm="order:print" href="#" class="btn btn-sm btn-flat btn btn-warning text-white" onclick="printModal()">
        <i class="fa fa-print"></i>&nbsp;' . sc_language_render("admin.order.print_invoice") . '</a>
        <!-- <a data-perm="order:print-return" id="btn_export_order_return" href="#" class="btn btn-sm btn-flat btn btn-warning text-white">
         <i class="fa fa-print"></i>&nbsp; Xuất đơn hoàn trả</a>-->
         <div class="dropdown"> 
            <button class="dropbtn btn btn-sm btn-success btn-flat"><i class="fa fa-file-export"></i>Xuất đơn hàng</button>
            <div id="create-order-dropdown" class="dropdown-content">
                <div class="container">
                    <div class="panel-group" role="tablist" aria-multiselectable="true">
                        <div class="panel panel-default">
                            <div class="panel-heading" role="tab">
                                <h4 class="panel-title">
                                    <a data-perm="order:export_order"  id="export_sales_invoice_list_real" class="btn btn-flat btn-create-order" style="margin-top: 10px;">
                                        ' . sc_language_render('admin.order.export') . '
                                    </a>
                                </h4>
                            </div>
                        </div>
                        <div class="panel panel-default">
                            <div class="panel-heading" role="tab">
                                <h4 class="panel-title">
                                    <a data-perm="order:export_order" id="export_excel_order_to_einvoice" class="btn btn-flat btn-create-order">
                                        Xuất đơn hàng
                                    </a>
                                </h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div> 
        <a  data-perm="order:create" href="' . sc_route_admin('admin_order.create') . '" class="btn btn-sm btn-success  btn-flat" title="New" id="button_create_new"><i class="fa fa-plus" title="' . sc_language_render('action.add') . '"></i></a>
        
        ';
        //=menuRight

        //menuSort
        $optionSort = '';
        foreach ($arrSort as $key => $sort) {
            $optionSort .= '<option  ' . (($dataSearch['sort_order'] == $key) ? "selected" : "") . ' value="' . $key . '">' . $sort . '</option>';
        }
        $data['optionSort'] = $optionSort;
        $data['urlSort'] = sc_route_admin('admin_order.index', request()->except(['_token', '_pjax', 'sort_order']));
        //=menuSort

        //menuSearch
        $optionStatus = '';
        foreach ($this->statusOrder as $key => $status) {
            $optionStatus .= '<option  ' . (($dataSearch['order_status'] == $key) ? "selected" : "") . ' value="' . $key . '">' . $status . '</option>';
        }
        $optionDepartment = '';
        foreach ($departments as $key => $department) {
            $optionDepartment .= '<option  ' . (($dataSearch['order_department'] == $key) ? "selected" : "") . ' value="' . $key . '">' . $department->name . '</option>';
        }

        $customers = ShopCustomer::where('status', 1)->get();
        $optionCustomer = '';
        foreach ($customers as $key => $customer) {
            $optionCustomer .= '<option  ' . (is_array($dataSearch['customer']) ? (in_array($customer->customer_code, $dataSearch['customer']) ? "selected" : "") : '' ) . ' value="' . $customer->customer_code . '">' . $customer->name . '</option>';
        }

        $orderExplains = ShopOrder::$NOTE;
        $optionExplain = '';
        foreach ($orderExplains as $key => $explain) {
            $optionExplain .= '<option  ' . (($dataSearch['order_explain'] == $explain) ? "selected" : "") . ' value="' . $explain . '">' . $explain . '</option>';
        }
        $orderDavicorpPurchasePriorityLevels = ShopOrder::$PurchasePriorityLevels; 
        $optionPurchasePriorityLevels = '';
        foreach ($orderDavicorpPurchasePriorityLevels as $key => $level) {
            $optionPurchasePriorityLevels .= '<option  '.(($dataSearch['order_purchase_priority_level'] == $key) ? "selected" : "").' value="'.$key.'">'.$level.'</option>';
        }
        $optionObject = '';
        foreach ($this->orderObjects as $key => $object) {
            $optionObject .= '<option  ' . (($dataSearch['order_object'] == $key) ? "selected" : "") . ' value="' . $key . '">' . $object . '</option>';
        }
        $data['topMenuRight'][] = '
                <form action="' . sc_route_admin('admin_order.index') . '" id="button_search" autocomplete="off">
                    <div class="row">
                    <input type="hidden" name="limit" value="'. ($dataSearch['limit'] ?? '') .'" id="limit_paginate">
                    <input type="hidden" name="id_export_return" id="id_export_return">
                        <div class="input-group float-left">
                            <div class="row">
                                <div class="col-lg-2 col-md-2 col-sm-12">
                                    <div class="form-group">
                                        <label>Chọn ngày</label>
                                        <div class="input-group">
                                            <select class="form-control rounded-0" name="option_date">
                                                <option value="1" ' . ($dataSearch["option_date"] == 1 ? "selected" : "") . '>Ngày giao hàng</option>
                                                <option value="2" ' . ($dataSearch["option_date"] == 2 ? "selected" : "") . '>Ngày trên hóa đơn</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-2 col-md-1 col-sm-6">
                                    <div class="form-group">
                                        <label>' . sc_language_render('action.from') . ':</label>
                                        <div class="input-group">
                                        <input type="text" name="from_to" id="from_to" class="form-control input-sm date_time rounded-0"  placeholder="Chọn ngày" value="' . request('from_to') . '"/> 
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-2 col-md-1 col-sm-6">
                                    <div class="form-group">
                                        <label>' . sc_language_render('action.to') . ':</label>
                                        <div class="input-group">
                                        <input type="text" name="end_to" id="end_to" class="form-control input-sm date_time rounded-0"  placeholder="Chọn ngày" value="' . request('end_to') . '"/> 
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-lg-2 col-md-2 col-sm-12">
                                    <div class="form-group">
                                        <label>' . sc_language_render('admin.order.object') . ':</label>
                                        <div class="input-group">
                                            <select class="form-control rounded-0" name="order_object">
                                                <option value="">---</option>
                                                ' . $optionObject . '
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-2 col-md-3 col-sm-12">
                                    <div class="form-group">
                                        <label>Mức độ ưu tiên:</label>
                                        <div class="input-group">
                                            <select class="form-control rounded-0" name="order_purchase_priority_level">
                                            <option value="">Tất cả mức độ</option>
                                            ' . $optionPurchasePriorityLevels . '
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-2 col-md-3 col-sm-12">
                                    <div class="form-group">
                                        <label>' . sc_language_render('order.admin.status') . ':</label>
                                        <div class="input-group">
                                            <select class="form-control rounded-0" name="order_status">
                                            <option value="">Tất cả trạng thái</option>
                                            ' . $optionStatus . '
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-2 col-md-2 col-sm-12">
                                    <div class="form-group">
                                        <label>' . sc_language_render('admin.order.explain') . ':</label>
                                        <div class="input-group">
                                            <select class="form-control rounded-0" name="order_explain">
                                                <option value="">---</option>
                                                ' . $optionExplain . '
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-2 col-md-3 col-sm-12">
                                    <div class="form-group">
                                        <label>' . sc_language_render('admin.order.department') . ':</label>
                                        <div class="input-group">
                                            <select class="form-control rounded-0" name="order_department">
                                                <option value="">---</option>
                                                ' . $optionDepartment . '
                                             </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-3 col-md-3 col-sm-12">
                                    <div class="form-group">
                                        <label>Chọn khách hàng:</label>
                                        <div class="input-group">
                                            <select id="customer_filter" style="width: 100%" class="form-control rounded-0" name="customer[]" multiple="multiple">
                                            ' . $optionCustomer . '
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-2 col-md-3 col-sm-12">
                                    <div class="form-group">
                                        <label>Khách hàng, Mã Đơn</label>
                                        <div class="input-group">
                                            <input type="text" name="code" class="form-control rounded-0 float-right" placeholder="Tên KH, mã KH, mã đơn" value="' . $dataSearch['code'] . '">
                                            <div class="input-group-append">
                                                <button id="btn-submit-search" type="submit" class="btn btn-primary btn-flat"><i class="fas fa-search"></i></button>
                                            </div>
                                        </div>
                                    </div>
                                </div>  
                            </div>
                         </div>
                    </div>
                </div>
            </form>';
        //=menuSearch
        return view($this->templatePathAdmin . 'screen.davicorp_order.index')
            ->with($data);
    }

    /**
     * xử lý xuất excel báo cáo bảng kê đơn hàng thực.
     */
    public function exportSalesInvoiceListRealOrder()
    {
        $dataSearch = [
            'from_to' => sc_clean(request('from_to_time') ?? ''),
            'end_to' => sc_clean(request('end_to_time') ?? ''),
        ];

        $ids = explode(',', request('ids'));

        //Query lấy data gộp theo cùng khách hàng
        $objOrder = new AdminOrder();
        $data = $objOrder->leftjoin(SC_DB_PREFIX . "shop_order_detail as sod", function($join){
            $join->on(SC_DB_PREFIX . "shop_order.id", "sod.order_id");
        });
        $data = $data
            ->whereIn(SC_DB_PREFIX . "shop_order.id", $ids)
            ->select(SC_DB_PREFIX . "shop_order.delivery_time",
                SC_DB_PREFIX . "shop_order.id_name",
                SC_DB_PREFIX . "shop_order.id",
                SC_DB_PREFIX . "shop_order.explain" ,
                SC_DB_PREFIX . "shop_order.customer_id",
                SC_DB_PREFIX . "shop_order.name",
                SC_DB_PREFIX . "shop_order.address",
                "sod.qty_reality as qty",
                "sod.product_unit as unit_name", "sod.product_name", "sod.price", "sod.total_price", "sod.product_id", "sod.product_code as sku" )
            ->orderBy(SC_DB_PREFIX . "shop_order.delivery_time", 'ASC')
            ->orderBy(SC_DB_PREFIX . "shop_order.id_name", 'ASC')
            ->orderBy("sod.created_at", 'ASC')
            ->get()->groupBy('customer_id');

        return Excel::download(new AdminMultipleSheetSalesInvoiceListRealOrder($dataSearch, $data), 'BẢNG KÊ HÓA ĐƠN BÁN HÀNG - BÊN THỰC ' . Carbon::now() . '.xlsx');
    }

    public function exportExcelOrderToEinvoice()
    {
        $ids = explode(',', request('ids'));
        $objOrder = new AdminOrder();
        $nameTable = $objOrder->table;
        $nameTableCustomer = SC_DB_PREFIX . 'shop_customer';
        $data = $objOrder->leftjoin(SC_DB_PREFIX . "shop_order_detail as sod", function($join){
            $join->on(SC_DB_PREFIX . "shop_order.id", "sod.order_id");
        })->leftjoin(SC_DB_PREFIX . 'shop_customer as scm', function ($join) use ($nameTable) {
            $join->on('scm.id', $nameTable . '.customer_id');
        });

        $data = $data->where(SC_DB_PREFIX . "shop_order.status",1)
            ->whereIn(SC_DB_PREFIX . "shop_order.id", $ids)
            ->select(
                $nameTable . ".delivery_time",
                $nameTable . ".bill_date",
                $nameTable . ".id_name",
                $nameTable . ".explain" ,
                $nameTable . ".customer_code",
                $nameTable . ".name as customer_name",
                $nameTable.'.object_id',
                "sod.qty", "sod.product_unit", "sod.product_name", "sod.price", "sod.total_price", "sod.product_code",
                'scm.teacher_code', 'scm.student_code'
            )
            ->orderBy(SC_DB_PREFIX . "shop_order.id_name", 'ASC')
            ->get();

        return Excel::download(new AdminExportOrderToEinvoice($data), 'HÓA ĐƠN BÁN HÀNG ' . Carbon::now() . '.xlsx');
    }

    public function create(Request $request)
    {
        $delivery_time = $request->get('delivery_time');
        if (!($delivery_time)) {
            $delivery_time = date('d/m/Y', strtotime('tomorrow'));
        }

        $bill_date = $request->get('bill_date');
        if (!($bill_date)) {
            $bill_date = date('d/m/Y', strtotime('tomorrow'));
        }


        $customers = ShopCustomer::where('status', '=', '1')->get();
        $data = [
            'title' => 'Tạo hóa đơn',
            'subTitle' => '',
            'title_description' => 'Tạo hóa đơn',
            'icon' => 'fa fa-plus',
            'delivery_time' => $delivery_time,
            'bill_date' => $bill_date,
            'customers' => $customers,
            'orderObjects' => $this->orderObjects,
            'orderExplains' => ShopOrder::$NOTE
        ];
        session()->forget('num');
        return view($this->templatePathAdmin . 'screen.davicorp_order.order_add')
            ->with($data);
    }

    public function postCreate()
    {
        $data = request()->all();
        $validate = [
            'customer_id' => 'required',
            'object_id' => 'required',
            'delivery_time' => 'required',
            'bill_date' => 'required'
        ];

        $messages = [
            'customer_id.required' => "Vui lòng chọn khách hàng",
            'object_id.required' => "Vui lòng chọn đối tượng",
            'delivery_time.required' => "Vui lòng ngày giao hàng",
            'bill_date.required' => "Vui lòng chọn ngày in hóa đơn",
        ];


        $validator = Validator::make($data, $validate, $messages);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $customer = ShopCustomer::find($data['customer_id']);
        if (!$customer){
            return response()->json(['error' => 1, 'msg' => 'Khách hàng không tồn tại hoặc đã bị xóa!', 'detail' => '']);
        }

        //Create new order
        $dataInsert = [
            'customer_id' => $data['customer_id'] ?? "",
            'status' => '1',
            'name' => $customer->name,
            'customer_code' => $customer->customer_code,
            'email' => $data['email'] ?? '',
            'phone' => $data['phone'] ?? '',
            'address' => $data['address'] ?? '',
            'object_id' => $data['object_id'],
            'explain' => $data['explain'] ?? '',
            'comment' => $data['comment'] ?? '',
            'delivery_time' => $data['delivery_time'],
            'bill_date' => $data['bill_date'],
            'id_name' => (new ShopOrder())->getNextId()
        ];
        $dataInsert = sc_clean($dataInsert, [], true);
        $order = ShopOrder::create($dataInsert);
        $idOrder = $order->id;
        return redirect()->route('admin_order.detail',['id' => $idOrder ? $idOrder : ''])->with('success', sc_language_render('action.create_success'));
    }

    /**
     * Clone order davicorp
     */
    public function postClone()
    {
        $id = request('id') ?? '';
        $order = AdminOrder::findOrFail($id);
        if (!$order) {
            throw new ImportException('Không tìm thấy đơn hàng');
        }
        $customer = ShopCustomer::Where('customer_code', $order->customer_code)->first();
        if (!$customer) {
            throw new ImportException('Lỗi nhân bản. Không tìm thấy khách hàng!');
        }

        if ($order->explain == 'Hàng đợt 2') {
            $customerDriver = AdminDriverCustomer::where('customer_id', $order->customer_id)->where('type_order', 2)->first();
        } else {
            $customerDriver = AdminDriverCustomer::where('customer_id', $order->customer_id)->where('type_order', 1)->first();
        }
        $driver = AdminDriver::where('id', $customerDriver->staff_id ?? '')->first();

        DB::connection(SC_CONNECTION)->beginTransaction();
        try {
            $dataOrder = \Illuminate\Support\Arr::except($order->toArray(), ['id', 'edited', 'created_at', 'updated_at', 'fast_sync_status', 'comment']);
            $dataOrder['id_name'] = (new ShopOrder())->getNextId();
            $dataOrder['name'] = $customer->name;
            $dataOrder['customer_num'] = $customer->order_num;
            $dataOrder['drive_id'] = $driver->id ?? '';
            $dataOrder['drive_code'] = $driver->id_name ?? '';
            $dataOrder['drive_address'] = $driver->address ?? '';
            $dataOrder['drive_name'] = $driver->full_name ?? '';
            $dataOrder['drive_phone'] = $driver->phone ?? '';
            $dataOrder['customer_short_name'] = $customer->short_name;
            $dataOrder['address'] = $customer->address;
            $dataOrder['customer_code'] = $customer->customer_code;
            $dataOrder['email'] = $customer->email == '' ? '0' : $customer->email;
            $dataOrder['phone'] = $customer->phone == '' ? '0' : $customer->phone;
            $dataOrder['customer_id'] = $customer->id;
            $dataOrder['is_order_admin'] = 1;
            $dataOrderDetails = \Illuminate\Support\Arr::except($order->details->toArray(), ['id', 'order_id', 'created_at', 'updated_at', 'comment']);
            $newOrder = (new AdminOrder)->fill($dataOrder);
            if (!$newOrder->save()) {
                throw new ImportException('Nhân bản thất bại');
            }
            foreach ($dataOrderDetails as $key => $orderDetail) {
                $supplier_id = ShopProductSupplier::getSupplierOfProductAndCustomer($orderDetail['product_id'], $customer->id)->supplier_id ?? '';
                $supplier = ShopSupplier::find($supplier_id);
                $orderDetail['id'] = sc_uuid();
                $orderDetail['order_id'] = $newOrder->id;
                $orderDetail['supplier_id'] = $supplier_id;
                $orderDetail['comment'] = '';
                $orderDetail['supplier_name'] = $supplier->name ?? '';
                $orderDetail['supplier_code'] = $supplier->supplier_code ?? '';
                $orderDetail['created_at'] = now()->addSecond($key + 5);
                $newOrderDetail = (new ShopOrderDetail)->fill($orderDetail);
                if (!$newOrderDetail->save()) {
                    throw new ImportException("Lỗi nhân bản. Vui lòng kiểm tra lại");
                }
            }
            # Lưu lịch sử thông báo
            $notification = new AdminNotification();
            $notification->title = "Đơn hàng mới";
            $notification->content = "Admin đã đặt thành công đơn hàng số #" . $newOrder->id_name . " với tổng số tiền " . number_format($order->subtotal);
            $notification->id_order = $newOrder->id;
            $notification->order_code = $newOrder->id_name;
            $notification->customer_code = $customer->customer_code;
            $notification->customer_name = $customer->name;
            $notification->desc = $customer->name . " tạo đơn hàng mới.";
            $notification->order_type = 1;
            $notification->edit_type = 1;
            $notification->display = 0;
            $notification->save();

            //Add history
            $dataHistory = [
                'order_id' => $newOrder->id,
                'order_code' => $newOrder->id_name,
                'title' => 'Tạo đơn hàng mới',
                'content' => 'Tạo đơn hàng mới',
                'admin_id' => Admin::user()->id,
                'user_name' => Admin::user()->name,
                'is_admin' => 1,
                'order_status_id' => $order->status,
            ];

            (new AdminOrder)->addOrderHistory($dataHistory);
        } catch (Throwable $e) {
            DB::connection(SC_CONNECTION)->rollBack();
            return response()->json(['error' => 1, 'msg' => $e->getMessage()]);
        }
        DB::connection(SC_CONNECTION)->commit();
        return response()->json(['error' => 0, 'msg' => sc_language_render('customer.admin.clone_success')]);
    }

    /**
     * Create new order davicorp
     */
    public function postCreateOrder()
    {
        $data = request()->all();
        $addIds = request('add_id');
        $addPrices = request('add_price');
        $addQtys = request('add_qty');
        $add_qty_reality = request('add_qty_reality');
        $addComments = request('add_comment');
        $items = [];
        $productList = [];

        DB::beginTransaction();
        try {
            // Check null product
            if (empty($addIds)) {
                throw new \Exception('Chưa có sản phẩm nào được thêm!');
            }
            if (in_array('', $addIds, true)) {
                throw new \Exception('Sản phẩm không được để trống, vui lòng kiểm tra lại chi tiết hóa đơn!');
            }

            // Check null qty, product_price value
            foreach ($addPrices as $p) {
                $add_product_prices[] = str_replace(",", "", $p);
            }
            if (in_array(null, $addQtys, true) || in_array(null, $addPrices, true)) {
                throw new \Exception('Giá, số lượng của sản phẩm không được để trống!');
            }

            $sum = 0;
            $customer = ShopCustomer::find($data['customer_id']);
            if (!$customer) {
                throw new \Exception('Khách hàng không tồn tại hoặc đã bị xóa!');
            }
            if ($data['explain'] == 'Hàng đợt 2') {
                $customerDriver = AdminDriverCustomer::where('customer_id', $data['customer_id'])->where('type_order', 2)->first();
            } else {
                $customerDriver = AdminDriverCustomer::where('customer_id', $data['customer_id'])->where('type_order', 1)->first();
            }
            $driver = AdminDriver::where('id', $customerDriver->staff_id ?? '')->first();
            $customer_id = $data['customer_id'];
            $billDate = \Carbon\Carbon::createFromFormat('Y-m-d', $data['bill_date'])->toDateString();
            $shopUserPriceBoard = ShopUserPriceboard::with('customers')
                ->whereHas('customers', function (Builder $query) use ($customer_id) {
                    $query->where('customer_id', $customer_id);
                })
                ->whereDate('start_date', '<=', $billDate)
                ->whereDate('due_date', '>=', $billDate)
                ->first();
            $dataInsert = [
                'customer_id' => $data['customer_id'] ?? "",
                'status' => $shopUserPriceBoard ? 1 : 2,
                'name' => $customer->name ?? '',
                'customer_short_name' => $customer->short_name ?? '',
                'customer_num' => $customer->order_num ?? '',
                'department_id' => $customer->department_id ?? '',
                'customer_code' => $customer->customer_code ?? '',
                'drive_id' => $driver->id ?? '',
                'drive_code' => $driver->id_name ?? '',
                'drive_address' => $driver->address ?? '',
                'drive_name' => $driver->full_name ?? '',
                'drive_phone' => $driver->phone ?? '',
                'email' => $customer->email ?? '',
                'phone' => $customer->phone ?? '',
                'address' => $customer->address ?? '',
                'object_id' => $data['object_id'],
                'explain' => $data['explain'] ?? '',
                'comment' => $data['comment'] ?? '',
                'delivery_time' => $data['delivery_time'],
                'bill_date' => $data['bill_date'],
                'is_order_admin' => 1,
                'id_name' => (new ShopOrder())->getNextId()
            ];

            $dataInsert = sc_clean($dataInsert, [], true);
            $order = ShopOrder::create($dataInsert);
            $idOrder = $order->id;

            // Insert detail
            foreach ($addIds as $key => $id) {
                if ($id && $addQtys[$key]) {
                    $supplier_id = ShopProductSupplier::getSupplierOfProductAndCustomer($id, $data['customer_id'])->supplier_id ?? '';
                    $supplier = ShopSupplier::find($supplier_id);
                    $product = AdminProduct::getProductAdmin($id);
                    if (!$product) {
                        return response()->json(['error' => 1, 'msg' => sc_language_render('admin.data_not_found_detail', ['msg' => '#' . $id]), 'detail' => '']);
                    }
                    $productList[] = $product->name;
                    // ramdom id barcode
                    $id_barcode = str_pad(mt_rand(0, 999999999999), 12, '0', STR_PAD_LEFT);
                    $items[] = array(
                        'id' => sc_uuid(),
                        'order_id' => $idOrder,
                        'id_barcode'=> $id_barcode,
                        'product_id' => $id,
                        'product_code' => $product->sku,
                        'product_name' => $product->name,
                        'product_short_name' => $product->short_name,
                        'product_num' => $product->order_num,
                        'product_unit' => $product->unit->name ?? '',
                        'product_kind' => $product->kind,
                        'category_id' => $product->category_id,
                        'product_priority_level' => $product->purchase_priority_level,
                        'supplier_id' => $supplier_id,
                        'supplier_name' => $supplier->name ?? '',
                        'supplier_code' => $supplier->supplier_code ?? '',
                        'qty' => round($addQtys[$key], 2),
                        'qty_reality' => $add_qty_reality[$key] > 0 ? round($add_qty_reality[$key], 2) : round($addQtys[$key], 2),
                        'price' => $addPrices[$key],
                        'total_price' => $addPrices[$key] * round($addQtys[$key], 2),
                        'reality_total_price' => $addPrices[$key] * round($addQtys[$key], 2),
                        'comment' => $addComments[$key],
                        'created_at' => now()->addSecond($key + 3),
                    );
                    $sum += $addPrices[$key] * $addQtys[$key];
                }
            }

            if ($items) {
                (new ShopOrderDetail)->addNewDetail($items);
                // Update Subtotal
                AdminOrder::updateSubTotal($idOrder);
            }

            # Lưu lịch sử thông báo
            $notification = new AdminNotification();
            $notification->title = "Đơn hàng mới";
            $notification->content = "Admin đã đặt thành công đơn hàng số #" . $order->id_name . " với tổng số tiền " . number_format($sum);
            $notification->id_order = $order->id;
            $notification->order_code = $order->id_name;
            $notification->customer_code = $customer->customer_code;
            $notification->customer_name = $customer->name;
            $notification->desc = $customer->name . " tạo đơn hàng mới.";
            $notification->order_type = 1;
            $notification->edit_type = 1;
            $notification->display = 0;
            $notification->save();

            //Add history
            $dataHistory = [
                'order_id' => $order->id,
                'order_code' => $order->id_name,
                'title' => 'Tạo đơn hàng mới',
                'content' => 'Tạo đơn hàng mới',
                'admin_id' => Admin::user()->id,
                'user_name' => Admin::user()->name,
                'is_admin' => 1,
                'order_status_id' => $order->status,
            ];

            (new AdminOrder)->addOrderHistory($dataHistory);
        } catch (\Throwable $e) {
            DB::rollBack();
            $messages = $e->getMessage();
            return response()->json(['error' => 1, 'msg' => $messages]);
        }
        DB::commit();
        return response()->json(['error' => 0, 'msg' => 'Đặt hàng thành công!', 'order_id' => $idOrder]);
    }

    /**
     * Order detail
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    public function detail($id)
    {
        $order = AdminOrder::getOrderAdmin($id);
        $customers = ShopCustomer::where('status','1')->get();
        if (!$order) {
            return redirect()->route('admin.data_not_found')->with(['url' => url()->full()]);
        }

        $orderNote = ShopOrder::$NOTE;
        $order['history'] = ShopOrderHistory::where('order_id', 'like', $order->id)->orderBy('add_date', 'Desc')->get();

        $products = (new AdminOrder)->getProductByCustomerPriceBoard($order->bill_date, $order->customer_id, $order->object_id);
        $editable = $this->checkOrderEditable($order);

        return view($this->templatePathAdmin . 'screen.davicorp_order.order_edit')->with(
            [
                "title" => sc_language_render('order.order_detail'),
                "subTitle" => '',
                'icon' => 'fa fa-file-text-o',
                "order" => $order,
                "products" => $products,
                "statusOrder" => $this->statusOrder,
                "statusPayment" => $this->statusPayment,
                "statusShipping" => $this->statusShipping,
                'orderObjects' => $this->orderObjects,
                'orderCustomer' => $this->orderCustomer,
                'orderNote' => $orderNote,
                'customers' => $customers,
                'editable' => $editable
            ]
        );
    }

    public function postOrderUpdate(AdminOrderEditRequest $request)
    {
        $id = request('pk');
        $code = request('name');
        $value = request('value');
        $order = AdminOrder::findOrFail($id);
        if (!$order) {
            return response()->json(['error' => 1, 'msg' => 'Hóa đơn không tồn tại hoặc đã bị xóa!', 'detail' => '']);
        }

        if (!$this->checkOrderEditable($order)) {
            return response()->json(['error' => 1, 'msg' => 'Bạn không có quyền thao tác với đơn hàng này!', 'detail' => '']);
        }
        $dataOrderDetails = ShopOrderDetail::where('order_id', $id)->get()->toArray();
        $itemUpdate = '';
        $oldValue = $order->{$code};
        $newValue = $value;

        if ($code == 'comment') {
            $itemUpdate = 'ghi chú đơn hàng';
        } else if ($code == 'status') {
            $itemUpdate = 'trạng thái';
            $oldStatus = ShopOrderStatus::find($oldValue);
            $newStatus = ShopOrderStatus::find($value);
            $oldValue = $oldStatus ? $oldStatus->name : '';
            $newValue = $newStatus ? $newStatus->name : '';
            if($value == 7){
                $notification = new AdminNotification();
                $notification->title = "Hủy đơn hàng";
                $notification->content = "Admin đã hủy đơn hàng số #" .$order->id_name;
                $notification->id_order = $order->id;
                $notification->order_code = $order->id_name;
                $notification->customer_code = $order->customer_code;
                $notification->customer_name = $order->name;
                $notification->desc = $order->name. " hủy đơn hàng.";
                $notification->order_type = 1;
                $notification->edit_type = 4;
                $notification->display = 0;
                $notification->save();
            }
        } else if ($code == 'customer_id') {
            $itemUpdate = 'tên khách hàng';
            $oldValue = AdminOrder::findOrFail($id)->name;
            $newValue = AdminCustomer::findOrFail($value)->name;
            $newShortName = AdminCustomer::findOrFail($value)->short_name;
        } else if ($code == 'address') {
            $itemUpdate = 'địa chỉ';
        } else if ($code == 'phone') {
            $itemUpdate = 'số điện thoại';
        } else if ($code == 'delivery_time') {
            $itemUpdate = 'Thời gian giao hàng';
        } else if ($code == 'bill_date') {
//            $customer_id = $order->customer_id;
//            $shopUserPriceBoard = ShopUserPriceboard::with('customers')
//                ->whereHas('customers', function (Builder $query) use ($customer_id) {
//                    $query->where('customer_id', $customer_id);
//                })
//                ->whereDate('start_date', '<=', $value)
//                ->whereDate('due_date', '>=', $value)
//                ->first();
//            $order->status = $shopUserPriceBoard ? 1 : 2;
//            $order->save();
            $itemUpdate = 'Ngày trên hóa đơn';
        } else if ($code == 'email') {
            $itemUpdate = 'email';
        } else if ($code == 'object_id') {
            $itemUpdate = 'đối tượng';
            $oldObject = ShopOrderObject::find($oldValue);
            $newObject = ShopOrderObject::find($value);
            $oldValue = $oldObject ? $oldObject->name : '';
            $newValue = $newObject ? $newObject->name : '';
        } else if ($code == 'explain') {
            $itemUpdate = 'diễn giải';
        }

        $maxLen = 50;
        $oldValue = $oldValue ? $oldValue : '';
        $newValue = $newValue ? $newValue : '';
        $oldValue = strlen($oldValue) > $maxLen ? substr($oldValue, $maxLen) : $oldValue;
        $newValue = strlen($newValue) > $maxLen ? substr($newValue, $maxLen) : $newValue;
        $contentHistory = 'Sửa ' . $itemUpdate . ' : ' . $oldValue . ' -> ' . $newValue;

        if ($code == 'customer_id') {
            if (!$order->update([$code => $value, 'name' => $newShortName, 'edited' => 1])) {
                return response()->json(['error' => 1, 'msg' => sc_language_render('action.update_fail')]);
            }
        } else {
            if (!$order->update([$code => $value, 'edited' => 1])) {
                return response()->json(['error' => 1, 'msg' => sc_language_render('action.update_fail')]);
            }
        }

        if ($code == 'delivery_time') {
            AdminShopOrderChangeExtra::where('order_id', $id)->update([
                'content' => 'Đơn hàng hủy',
            ]);
        }
        if($value == 7) {
            foreach ($dataOrderDetails as $detail ) {
                $id_barcode = str_pad(mt_rand(0, 999999999999), 12, '0', STR_PAD_LEFT);
                $dataChange = [
                    'order_id' => $id,
                    'delivery_date_origin' => $order->delivery_time,
                    'order_name' => $order->id_name,
                    'product_id' => $detail['product_id'],
                    'product_name' => $detail['product_name'],
                    'product_short_name' => $detail['product_short_name'],
                    'product_num' => $detail['product_num'],
                    'product_unit' => $detail['product_unit'],
                    'qty' => $detail['qty_reality'],
                    'qty_change' => $detail['qty_reality'],
                    'content' => "Đơn hàng hủy",
                    'type_content' => 6,
                    'customer_code' => $order->customer_code,
                    'customer_name' => $order->customer_name,
                    'create_order' => date('d-m-Y H:i:s', strtotime($order->created_at)),
                    'order_detail_id' => $detail['id'],
                    'category_id' => $detail['category_id'],
                    'product_code' => $detail['product_code'],
                    'note' => $detail['comment'],
                    'status' => $order->status,
                    'type_order' => 1,
                ];
                (new AdminShopOrderChangeExtra())->create($dataChange);
            }
        }
        //Add history
        $dataHistory = [
            'order_id' => $id,
            'title' => 'Chỉnh sửa ' . $itemUpdate,
            'content' => $contentHistory,
            'admin_id' => Admin::user()->id,
            'user_name' => Admin::user()->name,
            'order_code' => $order->id_name,
            'is_admin' => 1,
        ];
        (new AdminOrder)->addOrderHistory($dataHistory);

        return response()->json([
            'history' => (new AdminOrder)->loadHistory($id),
            'error' => 0,
            'msg' => sc_language_render('action.update_success')
        ]);
    }

    /**
     * Thay đổi khách hàng trong đơn hàng
     * @return \Illuminate\Http\JsonResponse
     */
    public function postOrderUpdateCustomerInfo() {
        $order_id = request('order_id');
        $customer_id = request('customer_id');
        $order = AdminOrder::findOrFail($order_id);
        $customer = ShopCustomer::find($customer_id);
        if (!$order) {
            return response()->json(['error' => 1, 'msg' => 'Hóa đơn không tồn tại hoặc đã bị xóa!', 'detail' => '']);
        }
        if (!$customer) {
            return response()->json(['error' => 1, 'msg' => 'Khách hàng không tồn tại hoặc đã bị xóa!']);
        }
        if (!$this->checkOrderEditable($order)) {
            return response()->json(['error' => 1, 'msg' => 'Bạn không có quyền thao tác với đơn hàng này!', 'detail' => '']);
        }

        $einvoice = ShopEInvoice::where('order_id',$order->id_name)->first();
        if ($einvoice) {
            return response()->json(['error' => 1, 'msg' => 'Đơn hàng này đã được đồng bộ trước đó nên không thể thay đổi thông tin khách hàng!']);
        }
        $order->customer_id = $customer_id;
        $order->name = $customer->name ?? '';
        $order->customer_short_name = $customer->short_name ?? '';
        $order->department_id = $customer->department_id ?? '';
        $order->customer_code = $customer->customer_code ?? '';
        $order->customer_num = $customer->order_num ?? '';
        $order->email = $customer->email ?? '';
        $order->phone = $customer->phone ?? '';
        $order->address = $customer->address ?? '';
        $order->edited = 1;
        $order->save();

        //Add history
        $dataHistory = [
            'order_id' => $order_id,
            'title' => 'Chỉnh sửa',
            'content' => 'Chỉnh sửa thông tin khách hàng',
            'admin_id' => Admin::user()->id,
            'user_name' => Admin::user()->name,
            'order_code' => $order->id_name,
            'is_admin' => 1,
        ];
        (new AdminOrder)->addOrderHistory($dataHistory);

        return response()->json([
            'history' => (new AdminOrder)->loadHistory($order_id),
            'error' => 0,
            'msg' => sc_language_render('action.update_success')
        ]);
    }

    /**
     * process update order
     * @return [json]           [description]
     */
    public function postOrderDetailUpdate(AdminOrderDetailEditRequest $request)
    {
        $data = $request->validated();
        try {
            $id = $data['pk'];
            $field = $data['name'];
            $value = $data['value'];
            $item = ShopOrderDetail::find($id);
            $fieldOrg = $item->{$field};
            $orderId = $item->order_id;
            $item->{$field} = $value;
            $idProduct = $item->product_id;
            $itemProduct = ShopProduct::where('id', $idProduct)->first();
            $nameProduct = $itemProduct->name;
            //Update total
            if ($field == 'price') {
                $item->total_price = $value * $item->qty;
                $item->reality_total_price = $value * $item->qty_reality;
                $display = 'Giá';
            }

            if ($field == 'qty') {
                $item->qty_reality = round($value, 2);
                $item->qty = round($value, 2);
                $item->reality_total_price = round($value, 2) * $item->price;
                $item->total_price = round($value, 2) * $item->price;
//                dd(round($value, 2) * $item->price);
                $display = 'Số lượng';
            }

            if ($field == 'qty_reality') {
                $item->qty_reality = round($value, 2);
                $item->reality_total_price = round($value, 2) * $item->price;
                $display = 'Số lượng thực tế';
            }

            if ($field == 'comment') {
                $display = 'Ghi chú';
            }

            $item->save();
            $item = $item->fresh(); // Refresh data
            $order = AdminOrder::getOrderAdmin($orderId);

            if (!$order) {
                return response()->json(['error' => 1, 'msg' => sc_language_render('admin.data_not_found_detail', ['msg' => '#order:' . $orderId]), 'detail' => '']);
            }

            if (!$this->checkOrderEditable($order)) {
                return response()->json(['error' => 1, 'msg' => 'Bạn không có quyền thao tác với đơn hàng này!', 'detail' => '']);
            }

            //Add history
            $dataHistory = [
                'order_id' => $orderId,
                'title' => 'Chỉnh sửa chi tiết đơn hàng',
                'content' => sc_language_render('product.edit_product') . ' ' . $nameProduct . ': ' . $display . ' thay đổi ' . $fieldOrg . ' -> ' . $value,
                'admin_id' => Admin::user()->id,
                'order_status_id' => $order->status,
                'user_name' => Admin::user()->name,
                'order_code' => $order->id_name,
                'is_admin' => 1,
            ];
            (new AdminOrder)->addOrderHistory($dataHistory);

            $value_change = 0;
            //TODO
            if ($display == 'Số lượng') {
                    $id_barcode = str_pad(mt_rand(0, 999999999999), 12, '0', STR_PAD_LEFT);
                    $value_change = $value - $fieldOrg ;
                $dataChange = [
                    'order_id' => $orderId,
                    'delivery_date_origin' => $order->delivery_time,
                    'order_name' => $order->id_name,
                    'product_id' => $idProduct,
                    'product_name' => $nameProduct,
                    'product_short_name' => $itemProduct->short_name,
                    'product_num' => $itemProduct->order_num,
                    'product_unit' => $item->product_unit,
                    'qty' => $item->qty,
                    'qty_change' => $value_change,
                    'content' => "Chỉnh sửa số lượng",
                    'type_content' => 2,
                    'customer_code' => $order->customer_code,
                    'customer_name' => $order->customer_name,
                    'create_order' => date('d-m-Y H:i:s', strtotime($order->created_at)),
                    'order_detail_id' => $id,
                    'category_id' => $item->category_id,
                    'product_code' => $item->product_code,
                    'note' => $item->comment ?? '',
                    'status' => $order->status,
                    'type_order' => 1,
                ];
                (new AdminShopOrderChangeExtra())->create($dataChange);
            }

            if ($field == 'qty_reality') {
                $value_change = $value - $fieldOrg ;
                $id_barcode = str_pad(mt_rand(0, 999999999999), 12, '0', STR_PAD_LEFT);
                $dataChange = [
                    'order_id' => $orderId,
                    'delivery_date_origin' => $order->delivery_time,
                    'order_name' => $order->id_name,
                    'product_id' => $idProduct,
                    'product_name' => $nameProduct,
                    'product_short_name' => $itemProduct->short_name,
                    'product_num' => $itemProduct->order_num,
                    'product_unit' => $item->product_unit,
                    'qty' => $item->qty_reality,
                    'qty_change' => $value_change,
                    'content' => "Chỉnh sửa số lượng thực tế",
                    'type_content' => 3,
                    'customer_code' => $order->customer_code,
                    'customer_name' => $order->customer_name,
                    'create_order' => date('d-m-Y H:i:s', strtotime($order->created_at)),
                    'order_detail_id' => $id,
                    'category_id' => $item->category_id,
                    'product_code' => $item->product_code,
                    'note' => $item->comment ?? '',
                    'status' => $order->status,
                    'type_order' => 1,
                ];
                (new AdminShopOrderChangeExtra())->create($dataChange);
            }


            // Update total price
            AdminOrder::updateSubTotal($orderId);

            //fresh order info after update
            $orderUpdated = $order->fresh();

            $notification = new AdminNotification();
            $notification->title = "Sửa đơn hàng";
            $notification->content = 'Admin sửa chi tiết đơn hàng #' . $order->id_name;
            $notification->id_order = $order->id;
            $notification->order_code = $order->id_name;
            $notification->customer_code = $order->customer_code;
            $notification->customer_name = $order->name;
            $notification->desc = $order->name. " sửa đơn hàng.";
            $notification->order_type = 1;
            $notification->edit_type = 2;
            $notification->display = 0;
            $notification->save();

            if ($orderUpdated->balance == 0 && $orderUpdated->total != 0) {
                $style = 'style="color:#0e9e33;font-weight:bold;"';
            } elseif ($orderUpdated->balance < 0) {
                $style = 'style="color:#ff2f00;font-weight:bold;"';
            } else {
                $style = 'style="font-weight:bold;"';
            }

            $blance = '<tr ' . $style . ' class="data-balance"><td>' . sc_language_render('order.totals.balance') . ':</td><td align="right">' . sc_currency_format($orderUpdated->total) . '</td></tr>';
            $arrayReturn = [
                'error' => 0,
                'detail' => [
                    'total' => sc_currency_render(($orderUpdated->total ?? 0), 'vnd'),
                    'item_total_price' => sc_currency_render(($item->total_price ?? 0), 'vnd'),
                    'item_id' => $id,
                    'subtotal' => sc_currency_format($orderUpdated->subtotal),
                    'balance' => $blance,
                ],
                'msg' => sc_language_render('action.update_success'),
                'history' => (new AdminOrder)->loadHistory($orderId)
            ];
        } catch (\Throwable $e) {
            $arrayReturn = ['error' => 1, 'msg' => $e->getMessage()];
        }
        return response()->json($arrayReturn);

    }

    public function postDeleteItem()
    {
        try {
            $data = request()->all();
            $pId = $data['pId'] ?? "";
            $itemDetail = (new ShopOrderDetail)->where('id', $pId)->first();
//            dd($itemDetail);
            if (!$itemDetail) {
                return response()->json(['error' => 1, 'msg' => sc_language_render('admin.data_not_found_detail', ['msg' => 'detail#' . $pId]), 'detail' => '']);
            }
            $orderId = $itemDetail->order_id;
            $order = AdminOrder::getOrderAdmin($orderId);
            if (!$order) {
                return response()->json(['error' => 1, 'msg' => sc_language_render('admin.data_not_found_detail', ['msg' => 'order#' . $orderId]), 'detail' => '']);
            }

            if (!$this->checkOrderEditable($order)) {
                return response()->json(['error' => 1, 'msg' => 'Bạn không có quyền thao tác với đơn hàng này!', 'detail' => '']);
            }
            $result = (new AdminShopOrderChangeExtra())->where('order_id', $itemDetail->order_id)->where('product_code', $itemDetail->product_code)->delete();
            $itemDetail->delete(); //Remove item from shop order detail
            
            // Update total price
            AdminOrder::updateSubTotal($orderId);

            // Update purchase_priority_level order if product has purchase_priority_level = 1
            AdminOrder::UpdatePurchasePriorityLevel($orderId);

            $notification = new AdminNotification();
            $notification->title = "Sửa đơn hàng";
            $notification->content = 'Admin xóa chi tiết đơn hàng #' . $order->id_name;
            $notification->id_order = $order->id;
            $notification->order_code = $order->id_name;
            $notification->customer_code = $order->customer_code;
            $notification->customer_name = $order->name;
            $notification->desc = $order->name. " sửa đơn hàng.";
            $notification->order_type = 1;
            $notification->edit_type = 3;
            $notification->display = 0;
            $notification->save();
            //TODO
            //add in tem bổ sung
            $dataChange = [
                'order_id' => $order->id,
                'delivery_date_origin' => $order->delivery_time,
                'order_name' => $order->id_name,
                'product_id' => $itemDetail->product_id,
                'product_name' => $itemDetail->product_name,
                'product_short_name' => $itemDetail->product_short_name,
                'product_num' => $itemDetail->product_num,
                'product_unit' => $itemDetail->product_unit,
                'qty' => $itemDetail->qty_reality,
                'qty_change' => $itemDetail->qty_reality,
                'content' => "Xóa sản phẩm",
                'type_content' => 11,
                'customer_code' => $order->customer_code,
                'customer_name' => $order->customer_name,
                'create_order' => date('d-m-Y H:i:s', strtotime($order->created_at)),
                'order_detail_id' => $itemDetail->id,
                'category_id' => $itemDetail->category_id,
                'product_code' => $itemDetail->product_code,
                'note' => $itemDetail->comment ?? '',
                'status' => $order->status,
                'type_order' => 1,
            ];
            (new AdminShopOrderChangeExtra())->create($dataChange);
            //Add history
            $dataHistory = [
                'title' => 'Chỉnh sửa chi tiết',
                'order_id' => $orderId,
                'content' => 'Xóa sản phẩm ' . $itemDetail->product_name,
                'admin_id' => Admin::user()->id,
                'user_name' => Admin::user()->name,
                'order_code' => $order->id_name,
                'is_admin' => 1,
            ];
            (new AdminOrder)->addOrderHistory($dataHistory);
            return response()->json(['error' => 0, 'msg' => sc_language_render('action.update_success')]);
        } catch (\Throwable $e) {
            return response()->json(['error' => 1, 'msg' => 'Error: ' . $e->getMessage()]);
        }
    }


    /*
    Delete list order ID
    Need mothod destroy to boot deleting in model
    */
    public function deleteList()
    {
        if (!request()->ajax()) {
            return response()->json(['error' => 1, 'msg' => sc_language_render('admin.method_not_allow')]);
        }
        $ids = request('ids');
        $arrID = explode(',', $ids);
        $arrDontPermission = [];
        foreach ($arrID as $id) {
            $order = ShopOrder::findOrFail($id);
            if (!$this->checkOrderEditable($order)) {
                return response()->json(['error' => 1, 'msg' => 'Bạn không có quyền thao tác với đơn hàng này!', 'detail' => '']);
            }
            if (!$this->checkPermisisonItem($id)) {
                $arrDontPermission[] = $id;
            }
        }

        // Check đơn hàng đã đồng bộ hóa đơn điện tử thì không thể xóa
        $orderIdName = ShopOrder::whereIn('id', $arrID)->get()->pluck('id_name')->toArray();
        $invoice = ShopEInvoice::whereIn('order_id', $orderIdName)->first();
        if ($invoice) {
            return response()->json(['error' => 1, 'msg' => sc_language_render('einvoice.already.exists.not.delete')]);
        }

        if (count($arrDontPermission)) {
            return response()->json(['error' => 1, 'msg' => sc_language_render('admin.remove_dont_permisison') . ': ' . json_encode($arrDontPermission)]);
        } else {
            AdminOrder::destroy($arrID);
            return response()->json(['error' => 0, 'msg' => sc_language_render('action.update_success')]);
        }
    }

    /**
     * In pdf + excel đơn hàng davicorp
     * @return \Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\BinaryFileResponse|void
     */
    public function printOrder()
    {
        $ids = explode(',', request('ids'));
        $isDetail = request('detail');
        $typePrint = request('type_print') ?? 1;
        $orderData = AdminOrder::with('details','departments', 'customer', 'customer.department', 'details.product', 'details.product.unit')
            ->orderBy('name')->orderBy('bill_date', 'DESC')->findMany($ids);
        if (!count($orderData) > 0) {
            return redirect()->route('admin.data_not_found')->with(['url' => url()->full()]);
        }
        if (count($orderData) > 200) {
            return redirect()->back()->with(['error' => 'Dữ liệu quá tải vui lòng chọn lại trường lọc!']);
        }

        if ($typePrint == 1) {
            if ($isDetail == 'detail') {
                $html = view($this->templatePathAdmin . 'screen.davicorp_order.print_pdf.order_detail_template')
                    ->with(['data' => $orderData, 'isDetail' => $isDetail])->render();
                $html = mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8');
                $domPdf = new Dompdf();
                $domPdf->getOptions()->setChroot(public_path());
                $domPdf->loadHtml($html, 'UTF-8');
                $domPdf->setPaper('A5', 'portrait');
                $domPdf->render();

                return $domPdf->stream('hoadon_' . now() . '.pdf', ["Attachment" => false]);
            }

            $html = view($this->templatePathAdmin . 'screen.davicorp_order.print_pdf.order_template')
                ->with(['data' => $orderData])->render();
            $html = mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8');

            return $html;
        }

        return Excel::download(new AdminExportMultipleSheet($orderData, 'print'), 'Đơn hàng - ' . Carbon::now() . '.xlsx');
    }

    /**
     * In đơn gộp
     * @return \Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\BinaryFileResponse|void
     */
    public function printCombineOrder()
    {
        $ids = explode(',', request('ids'));
        $typePrint = request('type_print') ?? 1;
        //Query lấy data gộp theo cùng khách hàng
        $objOrder = new AdminOrder();
        $tableName = $objOrder->table;
        $data = $objOrder->leftjoin(SC_DB_PREFIX . "shop_order_detail as sod", function($join) use ($tableName){
            $join->on($tableName . ".id", "sod.order_id");
        })
        ->leftjoin(SC_DB_PREFIX . "shop_department as sd", function($join) use ($tableName){
            $join->on($tableName . ".department_id", "sd.id");
        });
        $data = $data->whereIn($tableName . ".id", $ids)
            ->where("sod.qty", '>', 0)
            ->select($tableName . ".bill_date", $tableName . ".id_name", $tableName . ".object_id", $tableName . ".id", $tableName . ".customer_num", $tableName . ".total",
                $tableName . ".explain" , $tableName . ".customer_id", $tableName . ".name",
                $tableName . ".address",
                "sod.qty", "sod.product_unit as unit_name", "sod.product_name", "sod.price", "sod.total_price",
                "sod.product_id",
                "sod.created_at",
                "sd.name as department_name",
                "sd.address as department_address",
                "sd.contact as department_contact",
                "sd.image")
            ->orderBy($tableName . ".id_name", "desc")
            ->orderBy('sod.created_at', 'ASC')
            ->get();
        $mergeData = $data->groupBy(['customer_id', 'bill_date', 'object_id', 'explain']);

        if (!count($data) > 0) {
            return redirect()->route('admin.data_not_found')->with(['url' => url()->full()]);
        }

        $sum = 0;
        $dataArray = $mergeData->toArray();
        $dataArray = array_values($dataArray);
        foreach ($dataArray as $key => $value) {
            foreach ($value as $item) {
                foreach ($item as $subItem) {
                    $sum = $sum + count($subItem);
                }
            }
        }
        if ($typePrint == 1) {
            $html = view($this->templatePathAdmin . 'screen.davicorp_order.print_pdf.combine_order_template')
                ->with(['data' => $mergeData, 'totalData' => $sum])->render();
            $html = mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8');

            return $html;
        }

        return Excel::download(new AdminExportMultipleSheet($mergeData, 'print_combine'), 'Đơn hàng - ' . Carbon::now() . '.xlsx');
    }

    /**
     * In pdf chi tiết đơn hoàn trả
     * @param $id
     * @return \Illuminate\Http\RedirectResponse|void
     */
    public function printReturn($id)
    {
        // Find order
        $orderData = AdminOrder::with(['returnHistory', 'details'])->find($id);
        if (!count($orderData->toArray())) {
            return redirect()->route('admin.data_not_found')->with(['url' => url()->full()]);
        }
        $html = view($this->templatePathAdmin . 'screen.davicorp_order.print_pdf.order_return_template')
            ->with(['data' => $orderData])->render();
        $html = mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8');
        $domPdf = new Dompdf();
        $domPdf->getOptions()->setChroot(public_path());
        $domPdf->loadHtml($html, 'UTF-8');
        $domPdf->setPaper('A5', 'portrait');
        $domPdf->render();
        return $domPdf->stream('hoadontrahang_' . now() . 'pdf', ["Attachment" => false]);
    }

    /**
     * Xuất phiếu đơn hoàn trả.
     * @return \Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function exportListOrderReturn()
    {
        $ids = [];
        if (!empty(request('id_export_return'))) {
            $ids = explode(',', request('id_export_return'));
        }
        $dataSearch = [
            'keyword' => sc_clean(request('keyword') ?? ''),
            'from_to' => sc_clean(request('from_to')),
            'end_to' => sc_clean(request('end_to')),
            'order_status' => sc_clean(request('order_status') ?? ''),
            'order_purchase_priority_level' => sc_clean(request('order_purchase_priority_level') ?? ''),
            'order_department' => sc_clean(request('order_department') ?? ''),
            'order_explain' => sc_clean(request('order_explain') ?? ''),
            'order_object' => sc_clean(request('order_object') ?? ''),
            'code' => sc_clean(request('code') ?? ''),
            'customer' => sc_clean(request('customer') ?? ''),
            'option_date' => sc_clean(request('option_date') ?? '')
        ];
        $date['start'] = $dataSearch['from_to'];
        $date['end'] = $dataSearch['end_to'];
        if (!empty($ids)) {
            $data = ShopOrder::with('returnHistory', 'returnHistory.detail')->orderBy('delivery_time', 'asc')->orderBy('name', 'asc')->findMany($ids);
        } else {
            if (empty($dataSearch['order_department']) && empty($dataSearch['from_to']) && empty($dataSearch['end_to'])  && empty($dataSearch['order_object'])
                && empty($dataSearch['order_explain']) && empty($dataSearch['order_status']) && empty($dataSearch['order_purchase_priority_level']) && empty($dataSearch['code']) && empty($dataSearch['customer'])) {
                return redirect()->back()->with(['error' => 'Vui lòng lọc dữ liệu trước khi xuất đơn hoàn trả!']);
            }

            $data = (new AdminOrder)->getReturnOrderDavicorp($dataSearch);

            if ($data->count() > 1000) {
                return redirect()->back()->with(['error' => 'Dữ liệu quá tải vui lòng chọn lại trường lọc!']);
            }
        }

        return Excel::download(new AdminExportOrderReturn($data, $date, 'davicorp'), 'Phiếu hoàn trả - ' . Carbon::now() . '.xlsx');
    }

    /**
     * Handel gộp đơn hàng
     * Điều kiện: Delivery_date, Customer_id, Object ->phải giống nhau
     * @return \Illuminate\Http\JsonResponse
     */
    public function combineOrder()
    {
        $ids = explode(',', request('ids'));

        if (!count($ids)) {
            return response()->json(['error' => 1, 'msg' => sc_language_render('admin.order.combine_data_empty', ['msg' => json_encode($ids)]), 'detail' => sc_language_render('admin.order.combine_data_empty_details')]);
        }
        
        $listMerge = AdminOrder::orderBy('created_at', 'DESC')->findMany($ids);
        foreach ($listMerge as $orderMerge){
            if (!$this->checkOrderEditable($orderMerge)) {
                return response()->json(['error' => 1, 'msg' => 'Bạn không có quyền thao tác với đơn hàng này!', 'detail' => '']);
            }
        }
        $listOrderIdName = data_get($listMerge->toArray(), "*.id_name");

        if (count($listMerge) < 2) {
            return response()->json(['error' => 1, 'msg' => sc_language_render('admin.order.combine_data_not_meet_requirement', ['msg' => json_encode($ids)]), 'detail' => sc_language_render('admin.order.combine_data_not_meet_requirement')]);
        }

        $listEInvoice = ShopEInvoice::whereIn('order_id', $listOrderIdName)->get();

        if (count($listEInvoice) >= 1) {
            return response()->json(['error' => 1, 'msg' => sc_language_render('einvoice.combine_data_empty', ['msg' => json_encode($ids)]), 'detail' => sc_language_render('admin.order.combine_data_empty_details')]);
        }


        $customers = array_unique($listMerge->pluck('customer_id')->toArray() ?? []);
        $object = array_unique($listMerge->pluck('object_id')->toArray() ?? []);
        $delivery_time = array_unique($listMerge->pluck('delivery_time')->toArray() ?? []);

        if (count($customers) > 1 || count($object) > 1 || count($delivery_time) > 1) {
            return response()->json(['error' => 1, 'msg' => sc_language_render('admin.order.combine_data_not_meet_requirement', ['msg' => json_encode($ids)]), 'detail' => sc_language_render('admin.order.combine_data_requirement')]);
        }

        $detailMerge = ShopOrderDetail::whereIn('order_id', $ids)->get();
        if (!count($detailMerge) > 1) {
            return response()->json(['error' => 1, 'msg' => sc_language_render('admin.order.combine_data_notfound', ['msg' => json_encode($ids)]), 'detail' => sc_language_render('admin.order.combine_data_notfound')]);
        }

        DB::beginTransaction();
        $error = 0;
        //Combine price and comment
        $tempSubtotal = 0;
        $tempTotal = 0;
        $actualTotalPrice = 0;
        $tempComment = '';

        // Calculate price and comment, history
        foreach ($listMerge as $order) {
            $tempSubtotal += $order->subtotal;
            $tempTotal += $order->total;
            $actualTotalPrice += $order->actual_total_price;
            $tempComment .= ' -' . $order->comment ?? '';
            // Merge history
            foreach ($order->history ?? [] as $history) {
                $history->order_id = $listMerge[0]->id;
                $history->save();
            }
        }
        // Set calculated price for order
        $listMerge[0]->total = $tempTotal;
        $listMerge[0]->subtotal = $tempSubtotal;
        $listMerge[0]->actual_total_price = $actualTotalPrice;
        $listMerge[0]->comment = $tempComment;

        if ($listMerge[0]->save()) { // done save
            $detailMerge = $detailMerge->groupBy(['product_id', 'price']);
            foreach ($detailMerge as $key => $valueMerge) {
                foreach ($valueMerge as $keyPrice => $item) {
                    if ($item->count() == 1) {
                        $detail = ShopOrderDetail::find($item->first()->id);
                        $detail->order_id = $listMerge[0]->id;
                        $detail->save();
                    } else {
                        // Gộp ghi chú đơn hàng
                        $commentCombineSub = '';
                        $j = 0;
                        $commentItem = '';
                        foreach ($item as $keyItem => $value) {
                            if ($value->comment != '') {
                                $j++;
                                $commentItem = $value->comment;
                                $commentCombineSub .= number_format($value->qty, 2). ' : ' . $value->comment . ', ';
                            }
                        }
                        $subItem = $item->first();
                        $detail = ShopOrderDetail::find($subItem->id);
                        $detail->order_id = $listMerge[0]->id;
                        $detail->qty = $item->sum('qty');
                        $detail->qty_reality = $item->sum('qty_reality');
                        $detail->total_price = $item->sum('total_price');
                        $detail->reality_total_price = $item->sum('reality_total_price');
                        $detail->comment = ($j == 1) ? $commentItem : ( ($commentCombineSub != '') ?
                            ( rtrim($commentCombineSub, ", ") ) : '' );
                        $detail->save();
                        $item->shift();
                        foreach ($item->all() as $value) {
                            $detailDelete = ShopOrderDetail::find($value->id);
                            $detailDelete->delete();
                        }
                    }
                }

            }
        } else { // Catch error
            $error = 1;
        } 
        //Add history
        $dataHistory = [
            'title' => 'Gộp đơn',
            'order_id' => $listMerge[0]->id,
            'content' => "Gộp đơn hàng (" . implode(', ', $listOrderIdName) . ")",
            'admin_id' => Admin::user()->id,
            'user_name' => Admin::user()->name,
            'order_code' => $order->id_name,
            'is_admin' => 1,
            'order_status_id' => $listMerge[0]->status,
        ];

        (new AdminOrder)->addOrderHistory($dataHistory);
        // Destroy all another order except first order
        unset($ids[0]);
        ShopOrder::destroy($ids);

        if ($error) { //Catch error
            DB::rollBack();
            return response()->json(['error' => 1, 'msg' => sc_language_render('admin.order.combine_unknown_error', ['msg' => json_encode($ids)]), 'detail' => sc_language_render('admin.order.combine_unknown_error')]);
        }
        DB::commit();
        return response()->json(['error' => 0, 'msg' => sc_language_render('admin.order.combined')]);
    }
    /**
     * Handle sync order davicorp to e-invoice
     */
    public function syncOrderToEInvoice()
    {
        $ids = explode(',', request('ids'));
        if (!count($ids)) {
            return response()->json(['error' => 1, 'msg' => sc_language_render('admin.order.sync_data_empty', ['msg' => json_encode($ids)]), 'detail' => sc_language_render('admin.order.sync_data_empty_details')]);
        }

        // Check previously synced e-invoices
        $noti = '';
        foreach ($ids as $id) {
            $order_by_id = ShopOrder::where('id', $id)->get();
            foreach ($order_by_id as $orderItem){
                if (!$this->checkOrderEditable($orderItem)) {
                    return response()->json(['error' => 1, 'msg' => 'Bạn không có quyền thao tác với đơn hàng này!', 'detail' => '']);
                }
            }

            $einvoice_by_id = ShopEInvoice::where('order_id', $order_by_id->first()->id_name)->get();
            if (count($einvoice_by_id) > 0) {
                $noti .= '<br/>' . '- ' . $order_by_id->first()->id_name ?? 'Đơn hàng đã bị xóa';
            }
        }
        if ($noti !== '') {
            return response()->json(['error' => 1, 'msg' => 'Đơn hàng đã được đồng bộ trước đó, vui lòng kiểm tra lại!' . $noti . '']);
        }

        $now = Carbon::now();

        DB::beginTransaction();
        try {
            foreach ($ids as $id) {
                $order = AdminOrder::getOrderAdmin($id);
                $customer = AdminCustomer::Where('customer_code', $order->customer_code)->first();
                $customer_kind = $customer->kind ?? 0;
                // create new e-invoice
                $idEinvoice =  ShopEInvoice::insertGetId([
                    'order_id' => $order->id_name ?? '',
                    'customer_code' => $order->customer_code ?? '',
                    'customer_name' => $order->name ?? '',
                    'customer_address' => $order->address ?? '',
                    'id_name' => ShopGenId::genNextId('einvoice'),
                    'invoice_date' => now(),
                    'total_amount' => $order->total ?? 0,
                    'customer_kind' => $customer->kind ?? 0,
                    'sync_system' => ($customer->kind != 0) ? 'fast' : 'einv',
                    'tax_code' => $order->customer->tax_code ?? '',
                    'process_status' => 0,
                    'mode_run' => 0,
                    'priority' => 0,
                    'delivery_date' =>  $order->delivery_time ?? '',
                    'created_at' =>  $now
                ]);

                // create new e-invoice detail
                $order_details = ShopOrderDetail::with('product')->where('order_id', $id)->get();
                foreach ($order_details->groupBy(['product_code', 'price']) as $orderForCode) {
                    foreach ($orderForCode as $detail) {
                        if ($detail->sum('qty') > 0 ) {
                            if ($customer_kind == 1) {
                                $tax_no = $detail->first()->product->tax_company ?? 0;
                            }else if($customer_kind == 2) {
                                $tax_no = $detail->first()->product->tax_school ?? 0;
                            }else {
                                $tax_no = $detail->first()->product->tax_default ?? 0;
                            }
                            $pretax_price = ($detail->first()->price) / ( 1 + $tax_no/100);
                            $tax_amount = ($detail->first()->price * $detail->sum('qty')) - ($pretax_price * $detail->sum('qty'));
                            $idEinvoiceDetail =  ShopEInvoiceDetail::insertGetId([
                                'einv_id' => $idEinvoice ?? '',
                                'product_code' => $detail->first()->product_code ?? '',
                                'product_name' => $detail->first()->product->bill_name ?? $detail->first()->product_name,
                                'unit' => $detail->first()->product_unit ?? '',
                                'qty' => $detail->sum('qty') ?? 0,
                                'price' => $detail->first()->price ?? 0,
                                'tax_no' => $tax_no ?? 0,
                                'pretax_price' => $pretax_price,
                                'tax_amount' => $tax_amount,
                                'created_at' => $now
                            ]);
                        }
                    }
                }
            }
        } catch (\Throwable $e) {
            DB::rollBack();
            $messages = $e->getMessage();
            return response()->json(['error' => 1, 'msg' => $messages]);
        }
        DB::commit();
        return response()->json(['error' => 0, 'msg' => 'Đồng bộ đơn hàng thành công!']);
    }

    /**
     * View trả hàng.
     * @param $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     */
    public function return($id)
    {
        $order = AdminOrder::getOrderAdmin($id);

        if (!$order) {
            return redirect()->route('admin.data_not_found')->with(['url' => url()->full()]);
        }

        $products = (new AdminProduct)->getProductSelectAdmin();
        return view($this->templatePathAdmin . 'screen.davicorp_order.order_return')->with(
            [
                "title" => sc_language_render('admin_order.return_title'),
                "subTitle" => '',
                'icon' => 'fa fa-file-text-o',
                "order" => $order,
                "products" => $products,
            ]
        );
    }

    /**
     * Xử lý trả hàng.
     * @param AdminOrderReturnRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postReturn(AdminOrderReturnRequest $request)
    {
        $data = $request->validated();
        DB::beginTransaction();
        try {
            $detail_edit = [];
            $order = ShopOrder::with('details')->find($data['order_id']);
            if (!$order) {
                throw new \Exception('Không tìm thấy đơn hàng ' . $data['order_id']);
            }
            if (!$this->checkOrderEditable($order)) {
                return redirect()->back()->with("error", "Bạn không có quyền thao tác với đơn hàng này");
            }

            $returned = 0;
            foreach ($data['qty'] as $id => $qty) {
                $detail = $order->details->find($id);
                $typeUnit = $detail->product->unit->type ?? 0;

                if ($qty <= 0) {
                    continue;
                }

                if ($qty > $detail->qty) {
                    throw new \Exception('Số lượng trả hàng lớn hơn số lượng trên hóa đơn. Vui lòng kiểm tra lại!');
                }
                if ($qty > $detail->qty_reality) {
                    throw new \Exception('Số lượng trả hàng lớn hơn số lượng trên hóa đơn. Vui lòng kiểm tra lại!');
                }

                if ($typeUnit == 1) {
                    if (is_float($qty/1)) {
                        throw new \Exception('Sản phẩm -'. $detail->product_name .'- bắt buộc nhập số nguyên. Vui lòng kiểm tra lại!');
                    }
                }

                $detail->qty = $detail->qty - $qty;
                $detail->qty_reality = $detail->qty_reality - $qty;
                $detail->total_price = $detail->price * $detail->qty;
                $detail->reality_total_price = $detail->price * $detail->qty_reality;
                $return_total = $qty * $detail->price;
                $returned += $qty * $detail->price;
                // Return History
                $returnHistory = new ShopOrderReturnHistory([
                    'id' => sc_uuid(),
                    'order_id' => $detail->order_id,
                    'order_id_name' => $order->id_name,
                    'detail_id' => $detail->id,
                    'product_id' => $detail->product_id,
                    'product_name' => $detail->product_name,
                    'product_code' => $detail->product_code,
                    'customer_id' => $order->customer_id,
                    'customer_code' => $order->customer_code,
                    'customer_name' => $order->name,
                    'product_kind' => $detail->product_kind,
                    'category_id' =>$detail->category_id,
                    'supplier_id' =>$detail->supplier_id,
                    'product_unit' => $detail->product_unit,
                    'price' => $detail->price,
                    'original_qty' => $detail->qty,
                    'return_qty' => $qty,
                    'admin_id' => Admin::user()->id ?? '',
                    'return_total' => $return_total
                ]);
                $returnHistory->save();
                if (!$detail->save() && !$returnHistory->save()) {
                    throw new \Exception('Có lỗi xảy ra khi cập nhật chi tiết đơn hàng. Vui lòng kiểm tra lại');
                }
            }
            //Total, Subtotal of order.
            $order->subtotal = $order->subtotal - $returned;
            $order->total = $order->total - $returned;
            $order->actual_total_price = $order->actual_total_price - $returned;
            $dataHistory = [
                'title' => 'Chỉnh sửa trả hàng',
                'order_id' => $detail->order_id,
                'content' => "Trả hàng",
                'admin_id' => Admin::user()->id,
                'user_name' => Admin::user()->name,
                'order_code' => $order->id_name,
                'is_admin' => 1,
                'order_status_id' => $order->status,
            ];
            (new AdminOrder)->addOrderHistory($dataHistory);

            if (!$order->save()) {
                throw new \Exception('Có lỗi xảy ra khi cập nhật đơn hàng. Vui lòng kiểm tra lại');
            }
        } catch (\Throwable $e) {
            return redirect()->back()->with(['error' => $e->getMessage()]);
        }
        DB::commit();
        return redirect()->back()->with(['success' => 'Trả hàng thành công']);
    }

    /**
     * Hoàn tác trả hàng.
     * @return \Illuminate\Http\JsonResponse
     */
    public function undoReturnOrder()
    {
        $detail_id = request('detail_id');
        $return_id = request('return_id');
        $detail = ShopOrderDetail::find($detail_id);
        $qtyOrigin = $detail->qty;
        $qtyReaOrigin = $detail->qty_reality;
        $history = ShopOrderReturnHistory::find($return_id);
        try {
            $order = ShopOrder::find($history->order_id);
            if ($detail) {
                $detail->qty = $qtyOrigin + $history->return_qty;
                $detail->total_price = ($qtyOrigin + $history->return_qty) * $detail->price;
                $detail->qty_reality = $qtyReaOrigin + $history->return_qty;
                $detail->reality_total_price = ($qtyReaOrigin + $history->return_qty) * $detail->price;
                $detail->save();
            } else {
                $supplier_id = ShopProductSupplier::getSupplierOfProductAndCustomer($history->product_id, $order->customer_id)->supplier_id ?? '';
                $supplier = ShopSupplier::find($supplier_id);
                $product = AdminProduct::getProductAdmin($history->product_id);
                if (!$product) {
                    throw new \Exception('Không tìm thấy thông tin sản phẩm '.$history->product_name);
                }
                $insertData = [
                    'id' => sc_uuid(),
                    'order_id' => $history->order_id,
                    'product_id' => $history->product_id,
                    'product_code' => $product->sku,
                    'product_name' => $product->name,
                    'product_short_name' => $product->short_name,
                    'product_num' => $product->order_num,
                    'product_unit' => $product->unit->name,
                    'category_id' => $product->category_id,
                    'product_priority_level' => $product->purchase_priority_level,
                    'supplier_id' => $supplier_id,
                    'supplier_name' => $supplier->name ?? '',
                    'supplier_code' => $supplier->supplier_code ?? '',
                    'qty' => $history->return_qty,
                    'qty_reality' => $history->return_qty,
                    'price' => $history->price,
                    'total_price' => $history->return_total,
                    'reality_total_price' => $history->return_total,
                    'comment' => '',
                    'created_at' => now(),
                ];
                (new ShopOrderDetail)->addNewDetail($insertData);
            }
            AdminOrder::updateSubTotal($history->order_id);
            $dataHistory = [
                'title' => 'Hoàn tác trả hàng',
                'order_id' => $detail->order_id,
                'content' => "Hoàn tác trả hàng sản phẩm - {$history->product_name} -> Số lượng: " . $history->return_qty,
                'admin_id' => Admin::user()->id,
                'user_name' => Admin::user()->name,
                'order_code' => $order->id_name,
                'is_admin' => 1,
                'order_status_id' => 1,
            ];
            (new AdminOrder)->addOrderHistory($dataHistory);
            $history->delete();
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['error' => 1, 'msg' => $e->getMessage()]);
        }
        DB::commit();
        return response()->json(['error' => 0, 'msg' => 'Hoàn tác thành công']);
    }

    /**
     * Check permisison item
     */
    public function checkPermisisonItem($id)
    {
        return AdminOrder::getOrderAdmin($id);
    }

    public function getListProduct($id)
    {
        try {
            $customer = ShopCustomer::findOrFail($id);
            $products = $customer->productSuppliers;
            if ($customer->priceBoardDetails->first()) {
                $shopUserPriceBoard = ShopUserPriceboard::findOrFail($customer->priceBoardDetails->first()->user_priceboard_id);
            }

            // price_product_not_update: 0-da update; 1-chua update
            $price_product_not_update = 1;

            $price_teacher_array = [];
            $price_student_array = [];

            foreach ($products as $product) {
                if (isset($shopUserPriceBoard)) {
                    $productItem = AdminProductPriceDetail::where('product_id', $product->product_id)->where('product_price_id', $shopUserPriceBoard->product_price_id)->first();
                    if (\Carbon\Carbon::now()->format('Y-m-d') < $shopUserPriceBoard->due_date) {
                        $price_product_not_update = 0;
                    }
                } else {
                    $productItem = "";
                }

                $productDetail = ShopProduct::where('id', $product->product_id)->first();
                $product_unit = AdminUnit::find($productDetail->unit_id);
                $productDescriptionName = \App\Front\Models\ShopProductDescription::where('product_id', $productDetail->id)->where('lang', 'vi')->first();
                if ($productItem) {
                    if (Carbon::now()->format('Y-m-d') < $shopUserPriceBoard->due_date) {
                        $price_teacher = $productItem->price_1;
                        $price_student = $productItem->price_2;
                    } else {
                        $price_teacher = $price_student = 0;
                    }
                } else {
                    $price_teacher = $price_student = 0;
                }

                if ($price_product_not_update == 1) {
                    $price_teacher = $price_student = 0;
                }

                $data_teacher = [
                    'product_id' => $productDetail->id,
                    'product_name' => $productDescriptionName->name ?? "",
                    'product_price' => $price_teacher,
                    'unit' => $product_unit->name ?? ""
                ];
                array_push($price_teacher_array, $data_teacher);
                $data_student = [
                    'product_id' => $productDetail->id,
                    'product_name' => $productDescriptionName->name ?? "",
                    'product_price' => $price_student,
                    'unit' => $product_unit->name ?? ""
                ];
                array_push($price_student_array, $data_student);
            }

            $data['price_teacher_array'] = collect($price_teacher_array)->sortByDesc('product_name')->reverse()->values()->toArray();
            $data['price_student_array'] = collect($price_student_array)->sortByDesc('product_name')->reverse()->values()->toArray();
            $data['price_product_not_update'] = $price_product_not_update;
            return $data;
        } catch (\Exception $error) {
            return [];
        }
    }

    public function updateNewPriceProductOrder()
    {
        $id = request('id');
        // Lay bang gia hien tai dang gan cho KH
        $dataNewPriceList = (new AdminOrder())->getNewPriceListProduct($id);
        if (empty($dataNewPriceList)) {
            $dataOldPriceList = (new AdminOrder())->getOldPriceListProduct($id);
            if(empty($dataOldPriceList)) {
                return redirect()->back()->with(['error' => sc_language_render('admin.order.notify_update_price')]);
            }
        }
        // Lay chi tiet cua hoa don
        $dataOrder = (new AdminOrder())->with('details')->findOrFail($id);

        if (!$this->checkOrderEditable($dataOrder)) {
            return response()->json(['error' => 1, 'msg' => 'Bạn không có quyền thao tác với đơn hàng này!', 'detail' => '']);
        }

        $shopProductSupplier = ShopProductSupplier::where('customer_id', $dataOrder->customer_id)->get()->pluck('product_id')->toArray();
        $dataOrderDetail = $dataOrder->details;
        $dataPriceProduct = (!empty($dataNewPriceList)) ? $dataNewPriceList->priceBoard->prices : $dataOldPriceList->priceBoard->prices;
        $arrProductPrice = data_get($dataPriceProduct, '*.product_id');
        $idsProduct = '';
        $nameProduct = '';
        foreach ($dataOrderDetail as $itemProduct) {
            $checkProductPriceList = in_array($itemProduct->product_id, $arrProductPrice);
            if ($dataNewPriceList) {
                $itemPrice = $dataNewPriceList->priceBoard->prices->where('product_id', $itemProduct->product_id)->first();
                if ($itemPrice) {
                    if ($dataOrder->object_id == 1 && $itemPrice->price_1 == 0) {
                        $nameProduct .=  '<li> ' . $itemProduct->product_name . '</li>';
                        $idsProduct .= $itemProduct->product_id . ',';
                    }
                    if ($dataOrder->object_id == 2 && $itemPrice->price_2 == 0) {
                        $nameProduct .=  '<li> ' . $itemProduct->product_name . '</li>';
                        $idsProduct .= $itemProduct->product_id . ',';
                    }
                }
            }
            if (!$checkProductPriceList || !(in_array($itemProduct->product_id, $shopProductSupplier) )) {
                $nameProduct .=  '<li> ' . $itemProduct->product_name . '</li>';
                $idsProduct .= $itemProduct->product_id . ',';
            }
        }
        $error = '';
        $dataUpdate = '';
        $total = 0;
        DB::beginTransaction();
        try {
            $this->updatePriceOrderStatusDraft($dataPriceProduct, $dataOrderDetail, $dataOrder, $dataNewPriceList, $id);
            //Add history
            $dataHistory = [
                'order_id' => $id,
                'title' => 'Cập nhập giá tiền',
                'content' => 'Cập nhập giá tiền',
                'admin_id' => Admin::user()->id,
                'user_name' => Admin::user()->name,
                'order_code' => $dataOrder->id_name,
                'is_admin' => 1,
            ];
            (new AdminOrder)->addOrderHistory($dataHistory);
        } catch (\Throwable $e) {
            DB::rollBack();
            $messages = $e->getMessage();
            return redirect()->back()->with(['error' => $messages]);
        }
        DB::commit();
        return redirect()->back()->with(['success' => sc_language_render('admin.menu.edit_success'), 'product_name' => $nameProduct, 'idsProduct' => $idsProduct]);
    }
    public function deleteOldProductOrder()
    {
        $ids = request('id');
        $orderId = request('order_id');
        $arrId = explode(',', $ids);
        $arrId = array_unique(array_filter($arrId));
        $resultDelect = (new ShopOrderDetail())->where('order_id', $orderId)->whereIn('product_id', $arrId)->delete();
        AdminOrder::updateSubTotal($orderId);
        if ($resultDelect) {
            return redirect()->back()->with(['success'=> sc_language_render('action.success')]);
        }
    }

    public function getInfoProduct()
    {
        $id = request('id');
        $order_id = request('order_id');
        $product = AdminProduct::getProductAdmin($id);
        if (!$product) {
            return response()->json(['error' => 1, 'msg' => sc_language_render('admin.data_not_found_detail', ['msg' => '#product:' . $id]), 'detail' => '']);
        }
        if ($product->status == 0) {
            return response()->json([
                'error' => 1,
                'msg' => $product->name . ' '. $product->sku . ' - Hết hàng, vui lòng chọn sản phẩm thay thế!',
            ]);
        }
        $dataOrder = (new AdminOrder())->with('details')->findOrFail($order_id);
        $dataNewPriceList = (new AdminOrder())->getNewPriceListProduct($order_id);
        $dataPriceProduct = $dataNewPriceList->priceBoard->prices ?? [];
        
        $arrayReturn['price'] = 0;
        foreach($dataPriceProduct as $item) {
            if($item->product_id==$id) {
                if ($dataOrder->object_id == 1) {
                    $arrayReturn['price'] = $item->price_1 ?? 0;
                } else {
                    $arrayReturn['price'] = $item->price_2 ?? 0;
                }
                break;
            }
        }

        $arrayReturn['sku'] = $product->sku;
        $arrayReturn['unit'] = $product->unit->name ?? '';
        $arrayReturn['unit_type'] = $product->unit->type ?? 0;
        $arrayReturn['minimum_qty_norm'] = $product->minimum_qty_norm ?? 0;
        return response()->json($arrayReturn);
    }


    public function getProductListCreateOrder() {
        $customer_id = request('customer_id');
        $bill_date = request('bill_date');
        $products = AdminOrder::getListProductForCustomerByBillDate($bill_date, $customer_id);
        return response()->json($products);
    }

    public function postAddItem()
    {
        $addIds = request('add_id');
        // $addIds = explode(',', request('product_id'));
        $add_price = request('add_price');
        $add_qty = request('add_qty');
        $add_qty_reality = request('add_qty_reality');
        $add_comment = request('add_comment');
        $orderId = request('order_id');
        $items = [];
        $productList = [];

        $order = AdminOrder::getOrderAdmin($orderId);
        $customer_id = $order->customer_id;

        if (empty($addIds)) {
            return response()->json(['error' => 1, 'msg' => 'Chưa có sản phẩm nào được thêm!']);
        }
        if (in_array('', $addIds, true)) {
            return response()->json(['error' => 1, 'msg' => 'Sản phẩm không được để trống, vui lòng kiểm tra lại chi tiết hóa đơn!']);
        }

        if (in_array(null, $add_qty, true) || in_array(null, $add_price, true)) {
            return response()->json(['error' => 1, 'msg' => 'Giá, số lượng không được để trống!']);
        }

        foreach ($addIds as $key => $id) {
            if ($id && $add_qty[$key]) {
                $supplier_id = ShopProductSupplier::getSupplierOfProductAndCustomer($id, $customer_id)->supplier_id ?? '';
                $supplier = ShopSupplier::find($supplier_id);
                $product = AdminProduct::getProductAdmin($id);
//                $price = ShopImportPriceboard::getImportPriceDetail($supplier_id, $order->bill_date, $id, 1);
                if (!$product) {
                    return response()->json(['error' => 1, 'msg' => sc_language_render('admin.data_not_found_detail', ['msg' => '#' . $id]), 'detail' => '']);
                }
                $productList[] = $product->name;
                $id_barcode = str_pad(mt_rand(0, 999999999999), 12, '0', STR_PAD_LEFT);
                $items[] = array(
                    'id' => sc_uuid(),
                    'order_id' => $orderId,
                    'id_barcode'=> $id_barcode,
                    'product_id' => $id,
                    'product_code' => $product->sku,
                    'product_name' => $product->name,
                    'product_short_name' => $product->short_name,
                    'product_num' => $product->order_num,
                    'product_unit' => $product->unit->name,
                    'category_id' => $product->category_id,
                    'product_priority_level' => $product->purchase_priority_level,
                    'supplier_id' => $supplier_id,
                    'supplier_name' => $supplier->name ?? '',
                    'supplier_code' => $supplier->supplier_code ?? '',
                    'qty' => round($add_qty[$key], 2),
                    'qty_reality' => round($add_qty[$key], 2),
                    'price' => $add_price[$key],
//                    'import_price' => $price,
                    'total_price' => $add_price[$key] * round($add_qty[$key], 2),
                    'reality_total_price' => $add_price[$key] * round($add_qty[$key], 2),
                    'comment' => $add_comment[$key],
                    'created_at' => now()->addSeconds($key * 2),
                );
            }
        }
        if ($items) {
            try {
                (new ShopOrderDetail)->addNewDetail($items);
                // Add history
                $dataHistory = [
                    'title' => 'Chỉnh sửa chi tiết',
                    'order_id' => $orderId,
                    'content' => "Thêm sản phẩm: " . implode("<br>", $productList),
                    'user_name' => Admin::user()->name,
                    'order_code' => $order->id_name,
                    'is_admin' => 1,
                    'admin_id' => Admin::user()->id,
                ];
                (new AdminOrder)->addOrderHistory($dataHistory);
                // Update Subtotal
                AdminOrder::updateSubTotal($orderId);

                foreach ($items as $item) {
                    $dataExtra = [
                    'order_id' => $orderId,
                    'delivery_date_origin' => $order->delivery_time,
                    'order_name' => $order->id_name,
                    'product_id' => $item['product_id'],
                    'product_name' => $item['product_name'],
                    'product_short_name' => $item['product_short_name'],
                    'product_num' => $item['product_num'],
                    'product_unit' => $item['product_unit'],
                    'qty' => $item['qty'],
                    'qty_change' => $item['qty'],
                    'content' => "Thêm mới sản phẩm",
                    'type_content' => 8,
                    'customer_code' => $order->customer_code,
                    'customer_name' => $order->customer_name,
                    'create_order' => date('d-m-Y H:i:s', strtotime($order->created_at)),
                    'order_detail_id' => $item['id'],
                    'category_id' => $item['category_id'],
                    'product_code' => $item['product_code'],
                    'note' => $item['comment'] ?? '',
                    'status' => $order->status,
                    'type_order' => 1,
                ];
                (new AdminShopOrderChangeExtra())->create($dataExtra);
                }


                // Update purchase_priority_level order if product has purchase_priority_level = 1
//                AdminOrder::UpdatePurchasePriorityLevel($orderId);

                $notification = new AdminNotification();
                $notification->title = "Sửa đơn hàng";
                $notification->content = 'Admin thêm chi tiết đơn hàng #' . $order->id_name;
                $notification->id_order = $order->id;
                $notification->order_code = $order->id_name;
                $notification->customer_code = $order->customer_code;
                $notification->customer_name = $order->name;
                $notification->desc = $order->name. " thêm chi tiết đơn hàng.";
                $notification->order_type = 1;
                $notification->edit_type = 2;
                $notification->display = 0;
                $notification->save();

                return response()->json(['error' => 0, 'msg' => sc_language_render('action.update_success')]);
            } catch (\Throwable $e) {
                return response()->json(['error' => 1, 'msg' => 'Error: ' . $e->getMessage()]);
            }
        }
        return response()->json(['error' => 0, 'msg' => sc_language_render('action.update_success')]);
    }

    public function getInfoCustomer()
    {
        $id = request('id');
        session()->forget('num');
        $shopCustomer = ShopCustomer::find($id);
        return $shopCustomer->toJson();
    }

    /**
     * In pdf ghi chú đơn hàng
     */
    public function printNote(){
        $ids = request('ids');
        $idList = explode(',', $ids);
        $orders = AdminOrder::whereIn('id', $idList)->orderBy('name')->orderBy('id_name', 'DESC')->get();
        $outputArray = [];

        //Solve data
        foreach ($orders as $order){
            $orderItem = [];
            $orderItem['name'] = $order->object_id == 1 ? $order->name . ' - GV' : $order->name;
            $orderItem['id_name'] = $order->id_name ?? "";
            $orderItem['note_details'] = [];
            $orderItem['comment'] = ($order->explain ? $order->explain . ' : ' : '') . ($order->comment ? ", " . $order->comment : "");
            foreach ($order->details as $detail) {
                if (!empty($detail->comment)) {
                    $tempNoteItem = !empty($detail->comment) ? (($detail->product ?  $detail->product->getName() : "") . '(' . $detail->qty . ') : { '
                        . $detail->comment . ' }')  : ($detail->product ?  $detail->product->getName() : "") . '(' . $detail->qty . ')';
                    $orderItem['note_details'][] = $tempNoteItem;
                }
            }
            $outputArray[] = $orderItem;
        }

        // Print
        $html = view($this->templatePathAdmin . 'screen.davicorp_order.print_pdf.order_note_template')
            ->with(['data' => $outputArray])->render();
        $html = mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8');
        $domPdf = new Dompdf();
        $domPdf->getOptions()->setChroot(public_path());
        $domPdf->loadHtml($html, 'UTF-8');
        $domPdf->setPaper('A4', 'portrait');
        $domPdf->render();
        return $domPdf->stream('ghichudonhang_' . now() . '.pdf', ["Attachment" => false]);
    }

    /**
     * Cập nhập lại giá sản phẩm trong màng hình list order.
     * @return \Illuminate\Http\JsonResponse
     */
    public function updatePriceMultipleOrder()
    {
        $ids = request('id');
        if (empty($ids)) {
            return response()->json(['error' => 1, 'msg' => 'Vui lòng chọn ít nhất 1 đơn hàng']);
        }
        $idList = explode(',', $ids);

        DB::beginTransaction();
        try {
            foreach ($idList as $key => $id) {
                $order = ShopOrder::findOrFail($id);
                if (!$this->checkOrderEditable($order)) {
                    return response()->json(['error' => 1, 'msg' => 'Bạn không có quyền thao tác với đơn hàng này!', 'detail' => '']);
                }
                $dataNewPriceList = (new AdminOrder())->getNewPriceListProduct($id); // Lấy báo giá hiện tại gán cho khách hàng
                $dataOrder = (new AdminOrder())->with('details')->findOrFail($id);
//                if ($dataOrder->status == 1) {
//                    continue;
//                }
                $dataOrderDetail = $dataOrder->details; // lấy chi tiết đơn hàng
                if (empty($dataNewPriceList)) { // check bảng giá mới có tồn tại không
                    $dataOldPriceList = (new AdminOrder())->getOldPriceListProduct($id); // lấy báo giá được gán gần nhất
                    if(empty($dataOldPriceList)) {
                        continue;
                    }
                }
                $dataPriceProduct = (!empty($dataNewPriceList)) ? $dataNewPriceList->priceBoard->prices : $dataOldPriceList->priceBoard->prices; // lấy bảng giá của mỗi khách hàng được gán
                $arrProductOrderDetail = data_get($dataOrderDetail, '*.product_id'); // Lấy sản phẩm trong chi tiết đơn hàng
                $arrProductPrice = data_get($dataPriceProduct, '*.product_id'); // lấy sp trong bảng giá
                $count = 0;
                foreach ($arrProductOrderDetail as $itemProduct) {
                    $checkProductPriceList = in_array($itemProduct, $arrProductPrice);
                    if (!$checkProductPriceList) {
                        $count += 1;
                    }
                }
                if ($count > 0) {
                    continue;
                }
                $this->updatePriceOrderStatusDraft($dataPriceProduct, $dataOrderDetail, $dataOrder, $dataNewPriceList, $id);
                //Add history
                $dataHistory = [
                    'order_id' => $id,
                    'title' => 'Cập nhập giá tiền',
                    'content' => 'Cập nhập giá tiền',
                    'admin_id' => Admin::user()->id,
                    'user_name' => Admin::user()->name,
                    'order_code' => $order->id_name,
                    'is_admin' => 1,
                ];
                (new AdminOrder)->addOrderHistory($dataHistory);
            }
        } catch (\Throwable $e) {
            DB::rollBack();
            $messages = $e->getMessage();
            return response()->json(['error' => 1, 'msg' => $messages]);
        }
        DB::commit();
        return response()->json(['error' => 0, 'msg' => sc_language_render('admin.menu.edit_success')]);

    }
    public function updatePriceOrderStatusDraft($dataPriceProduct, $dataOrderDetail, $dataOrder, $dataNewPriceList, $id) {
        foreach ($dataPriceProduct as $item) {
            foreach ($dataOrderDetail as $itemDetail) {
                if ($item->product_id == $itemDetail->product_id) {
                    if ($dataOrder->object_id == 1) {
                        $dataUpdate = [
                            'price' => (!empty($dataNewPriceList)) ? $item->price_1 : 0,
                            'total_price' => (!empty($dataNewPriceList)) ?  $item->price_1 * $itemDetail->qty : 0,
                            'reality_total_price' => (!empty($dataNewPriceList)) ?  $item->price_1 * $itemDetail->qty_reality : 0,
                        ];
                    } else {
                        $dataUpdate = [
                            'price' => (!empty($dataNewPriceList)) ? $item->price_2 : 0,
                            'total_price' =>(!empty($dataNewPriceList)) ? $item->price_2 * $itemDetail->qty : 0,
                            'reality_total_price' =>(!empty($dataNewPriceList)) ? $item->price_2 * $itemDetail->qty_reality : 0,
                        ];
                    }
                    if ($dataUpdate) {
                        $objReturnOrder = ShopOrderReturnHistory::where('detail_id', $itemDetail->id)->get();
                        foreach ($objReturnOrder as $itemReturnOrder) {
                            $idReturnOrder = $itemReturnOrder->id;
                            $qtyReturn = $itemReturnOrder->return_qty;
                            $totalReturn = $qtyReturn * $dataUpdate['price'];
                            ShopOrderReturnHistory::findOrFail($idReturnOrder)->update(['return_total'=>$totalReturn]);
                        }
                        $result = ShopOrderDetail::findOrFail($itemDetail->id)->update($dataUpdate);
                        if (!$result) {
                            throw new Exception('Đã có lỗi xảy ra trong quá trình cập nhật!');
                        }
                    }
                }
            }
        }
        $detail = ShopOrderDetail::where('order_id', $id)->get();
        $total = $detail->sum('total_price');
        $actual_total_price = $detail->sum('reality_total_price');
        if (!empty($dataNewPriceList)) {
            $updateTotalPriceOrder = $dataOrder->update(['total' => $total, 'actual_total_price' => $actual_total_price, 'status' => 1]);
        } else {
            $updateTotalPriceOrder = $dataOrder->update(['total' => $total, 'actual_total_price' => $actual_total_price, 'status' => 2]);
        }

        if (!$updateTotalPriceOrder) {
            throw new Exception('Đã có lỗi xảy ra trong quá trình cập nhật!');
        }
    }

    /**
     * Cập nhập nhà cung cấp.
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateSupplierOrderDetail()
    {
        $ids = request('ids');
        if (empty($ids)) {
            return response()->json(['error' => 1, 'msg' => 'Vui lòng chọn ít nhất 1 đơn hàng']);
        }
        $idList = explode(',', $ids);
        $orderTableDetail = (new ShopOrderDetail())->getTable();
        DB::beginTransaction();
        try {
            foreach ($idList as $key => $id) {
                $dataOrder = (new AdminOrder())->with('details')->findOrFail($id);
                foreach ($dataOrder->details as $item) {
                    $supplier = ShopProductSupplier::where('product_id',$item->product_id)->where('customer_id',$dataOrder->customer_id)
                        ->whereDate('updated_at','<=', $dataOrder->delivery_time)->first();
                    if ($supplier) {
                        $supplierInfo = ShopSupplier::find($supplier->supplier_id);
                        DB::table($orderTableDetail)
                            ->where('id', $item->id)
                            ->limit(1)
                            ->update([
                                'supplier_id' => $supplier->supplier_id,
                                'supplier_code' => $supplierInfo->supplier_code,
                                'supplier_name' => $supplierInfo->name,
                            ]);

                    }
                }
                //Add history
                $dataHistory = [
                    'order_id' => $id,
                    'title' => 'Cập nhập NCC',
                    'content' => 'Cập nhập NCC',
                    'admin_id' => Admin::user()->id,
                    'user_name' => Admin::user()->name,
                    'order_code' => $dataOrder->id_name,
                    'is_admin' => 1,
                ];
                (new AdminOrder)->addOrderHistory($dataHistory);
            }
        } catch (\Throwable $e) {
            DB::rollBack();
            $messages = $e->getMessage();
            return response()->json(['error' => 1, 'msg' => $messages]);
        }
        DB::commit();
        return response()->json(['error' => 0, 'msg' => sc_language_render('admin.menu.edit_success')]);
    }

    /**
     * Check trạng thái ưu tiền đơn hàng
     * @param $id
     * @return string
     */
    public function checkPriorityStatus($details) {
        $item_detail = $details->where('product_priority_level', 1)->first();

        return isset($item_detail) ? 1 : 0;
    }

    /**
     * Check trạng thái ưu tiền đơn hàng
     * @param $id
     * @return string
     */
    public function checkDifferenceQty($details) {
        foreach ($details as $item) {
            if ($item->qty != $item->qty_reality) {
                return 1;
            }
        }

        return 0;
    }
}
