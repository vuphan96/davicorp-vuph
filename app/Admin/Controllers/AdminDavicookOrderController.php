<?php

namespace App\Admin\Controllers;

use App\Admin\Models\AdminDavicookOrder;
use App\Admin\Models\AdminDish;
use App\Admin\Models\AdminDriver;
use App\Admin\Models\AdminDriverCustomer;
use App\Admin\Models\AdminNotification;
use App\Admin\Models\AdminShopOrderChangeExtra;
use App\Exports\AdminExportOrderDavicook;
use App\Exports\ReturnOrder\AdminExportOrderReturn;
use App\Front\Models\ShopCustomer;
use App\Front\Models\ShopDavicookOrder;
use App\Front\Models\ShopDavicookOrderDetail;
use App\Front\Models\ShopDavicookOrderHistory;
use App\Front\Models\ShopDavicookOrderReturnHistory;
use App\Front\Models\ShopDavicookOrderStatus;
use App\Front\Models\ShopProductSupplier;
use App\Front\Models\ShopSupplier;
use App\Http\Requests\Admin\AdminDavicookOrderReturnRequest;
use App\Traits\OrderDavicookTraits;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Maatwebsite\Excel\Facades\Excel;
use SCart\Core\Admin\Admin;
use SCart\Core\Admin\Controllers\RootAdminController;
use App\Admin\Models\AdminProduct;
use SCart\Core\Front\Models\ShopPaymentStatus;
use SCart\Core\Front\Models\ShopShippingStatus;
use Dompdf\Dompdf;
use App\Front\Models\ShopDavicookCustomer;
use App\Front\Models\ShopDavicookDish;
use App\Front\Models\ShopDavicookMenu;
use App\Front\Models\ShopDavicookMenuDetail;
use App\Front\Models\ShopDavicookProductSupplier;
use App\Front\Models\ShopImportPriceboard;
use App\Front\Models\ShopImportPriceboardDetail;
use App\Front\Models\ShopProduct;
use App\Admin\Models\AdminDavicookCustomer;
use TheSeer\Tokenizer\Exception;
use Validator;
use App\Http\Requests\Admin\AdminOrderDavicookEditRequest;
use Illuminate\Support\Facades\Log;
use function Illuminate\Events\queueable;
use function Symfony\Component\DomCrawler\form;
use function Termwind\ValueObjects\format;

class AdminDavicookOrderController extends RootAdminController
{
    use OrderDavicookTraits;

    public $orderDavicookStatus;
    public $orderObjects;

    /**
     * AdminDavicookOrderController constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->orderDavicookStatus = ShopDavicookOrderStatus::getIdAll();
        $this->orderDishName = ShopDavicookDish::getIdAll();
        $this->orderDishCode = ShopDavicookDish::getDishCode();
    }

    /**
     * Show list davicook order.
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
            'js' => '',
            'is_orderlist' => 1,
            'permGroup' => 'davicook_order'
        ];

        //Process add content
        $data['menuRight'] = sc_config_group('menuRight', \Request::route()->getName());
        $data['menuLeft'] = sc_config_group('menuLeft', \Request::route()->getName());
        $data['topMenuRight'] = sc_config_group('topMenuRight', \Request::route()->getName());
        $data['topMenuLeft'] = sc_config_group('topMenuLeft', \Request::route()->getName());
        $data['blockBottom'] = sc_config_group('blockBottom', \Request::route()->getName());
        //List th
        $listTh = [
            'id' => 'ID',
            'menu_est_card_id' => 'Mã phiếu',
            'customer_name' => 'Tên khách hàng',
            'explain' => 'Diễn giải',
            'created_at' => 'Ngày đặt hàng',
            'delivery_time' => 'Ngày giao hàng',
            'export_date' => 'Ngày xuất khô',
            'total' => 'Tổng tiền',
            'status' => 'Trạng thái',
//            'purchase_priority_level' => 'Mức độ ưu tiên',
            'type' => 'Loại đơn',
            'action' => sc_language_render('action.title'),
        ];
        $cssTh = [
            'id' => 'min-width: 90px;',
            'menu_est_card_id' => 'min-width: 90px;',
            'customer_name' => 'width: auto; max-width: 240px',
            'explain' => 'text-align: center; width: 130px',
            'created_at' => 'max-width: 100px; white-space: normal; min-width: 100px;',
            'delivery_time' => 'max-width: 105px; white-space: normal; min-width: 105px;',
            'export_date' => 'max-width: 105px; white-space: normal; min-width: 105px;',
            'total' => 'text-align: right; min-width: 120px; max-width:120px',
            'status' => 'text-align: center; width: 130px',
//            'purchase_priority_level' => 'width: 140px',
            'customer_id' => 'display:none',
            'bill_date' => 'display:none',
            'type' => 'text-align: center; max-width: 60px; min-width:60px',
            'action' => 'text-align: center; width: 150px',
        ];
        //Customize collumn size and align
        $cssTd = [
            'id' => 'min-width: 90px',
            'menu_est_card_id' => 'min-width: 95px',
            'customer_name' => 'width: auto; max-width: 240px',
            'created_at' => 'text-align: center; ',
            'delivery_time' => 'width: 120px;',
            'total' => 'text-align: right;',
            'status' => 'text-align: center; width: 155px; padding-top: 10px; padding-bottom: 10px; vertical-align: top;',
//            'purchase_priority_level' => 'text-align: center; width: 150px',
            'customer_id' => 'display:none',
            'bill_date' => 'display:none',
            'action' => 'text-align: center; width: 400px',
            'type' => 'text-align: center; padding-top: 10px; padding-bottom: 10px; vertical-align: top;',
            'check_status' => 'display:none',
            'check_type' => 'display:none',
        ];
        $data['cssTd'] = $cssTd;
        $data['cssTh'] = $cssTh;

        //Sort input data
        $arrSort = [
            'created_at__desc' => 'Ngày đặt hàng giảm dần',
            'created_at__asc' => 'Ngày đặt hàng tăng dần',
            'delivery_date__desc' => 'Ngày giao hàng giảm dần',
            'delivery_date__asc' => 'Ngày giao hàng tăng dần',
            'total__desc' => 'Tổng tiền giảm dần',
            'total__asc' => 'Tổng tiền tăng dần',
        ];
        //Search
        $dataSearch = [
            'keyword' => sc_clean(request('keyword') ?? ''),
            'from_to' => sc_clean(request('from_to') ?? ''),
            'end_to' => sc_clean(request('end_to') ?? ''),
            'start_day' => sc_clean(request('start_day') ?? ''),
            'end_day' => sc_clean(request('end_day') ?? ''),
            'sort_order' => sc_clean(request('sort_order') ?? 'id_desc'),
            'arrSort' => $arrSort,
            'order_status' => sc_clean(request('order_status') ?? ''),
            'order_purchase_priority_level' => sc_clean(request('order_purchase_priority_level') ?? ''),
            'order_department' => sc_clean(request('order_department') ?? ''),
            'limit' => sc_clean(request('limit') ?? ''),
            'code' => sc_clean(request('code') ?? ''),
            'order_type' => sc_clean(request('order_type') ?? ''),
            'order_explain' => sc_clean(request('order_explain') ?? ''),
            'option_date' => sc_clean(request('option_date') ?? '')
        ];

        $dataTmp = (new AdminDavicookOrder)->getOrderDavicookListAdmin($dataSearch);
        $styleStatus = $this->orderDavicookStatus;
        array_walk($styleStatus, function (&$v, $k) {
            $v = '<span style="width: 87px" class="badge badge-' . (AdminDavicookOrder::$mapStyleStatus[$k] ?? 'light') . '">' . $v . '</span>';
        });
        $dataTr = [];
        $id = 0;
        $nameUrlOrderDavicook = URL::full();
        session()->put('nameUrlOrderDavicook', $nameUrlOrderDavicook);
        foreach ($dataTmp as $key => $row) {
            $id++;
            $dataMap = [
                'id' => $row->id_name . ' ' . (($row->edited ?? '') ? '<i class="fas fa-exclamation-triangle text-orange" title=""></i>' : ''),
                'menu_est_card_id' => $row->menu_card_est_code ?? '',
                'customer_name' => $row->customer_name ?? '',
                'explain' => $row->explain,
                'created_at' => isset($row->created_at) ? Carbon::make($row->created_at)->format('d/m/Y H:i:s') : '',
                'delivery_date' => isset($row->delivery_date) ? Carbon::make($row->delivery_date)->format('d/m/Y') : '',
                'export_date' => isset($row->export_date) ? Carbon::make($row->export_date)->format('d/m/Y') : '',
                'bill_date' => isset($row->bill_date) ? Carbon::make($row->bill_date)->format('d/m/Y') : '',
                'total' => sc_currency_render(round($row->total) ?? '', $row->currency ?? 'VND'),
                'status' => ($styleStatus[$row['status']] ?? $row['status']) . ($row->fast_sync_status ? '<br><span class="status-fast">Đã đồng bộ Fast</span>' : ''),
//                'purchase_priority_level' => $row->purchase_priority_level == 0 ? 'Bình thường' : '<span class="text-red">Cần đặt hàng ngay</span>',
                'type' => $row->type == 1 ? '<span class="badge badge-secondary">Nhu yếu phẩm</span>' : '<span class="badge badge-success">Món ăn</span>',
                'customer_id' => $row->customer_id,
                'check_status' => $row['status'],
                'check_type' => $row->type,
            ];

            ($row->type == 1)
            ?
            $dataMap['action'] = '
            <a data-perm="davicook_order:detail" href="' . sc_route_admin('admin.davicook_order.essential_order_detail', ['id' => $row['id'] ? $row['id'] : 'not-found-id']) . '"><span title="' . sc_language_render('action.edit') . '" type="button" class="btn btn-flat btn-sm btn-primary"><i class="fa fa-edit"></i></span></a>
           
            <span data-perm="davicook_order:delete" onclick="deleteItemDavicookOrder(\'' . $row['id'] . '\');"  title="' . sc_language_render('action.delete') . '" class="btn btn-flat btn-sm btn-danger"><i class="fas fa-trash-alt"></i></span>
            '
            :
            $dataMap['action'] = '
            <a data-perm="davicook_order:detail" href="' . sc_route_admin('admin.davicook_order.detail', ['id' => $row['id'] ? $row['id'] : 'not-found-id']) . '"><span title="' . sc_language_render('action.edit') . '" type="button" class="btn btn-flat btn-sm btn-primary"><i class="fa fa-edit"></i></span></a>
            <span data-perm="davicook_order:delete" onclick="deleteItemDavicookOrder(\'' . $row['id'] . '\');"  title="' . sc_language_render('action.delete') . '" class="btn btn-flat btn-sm btn-danger"><i class="fas fa-trash-alt"></i></span>
            ';
            $dataTr[$row['id']] = $dataMap;
        }
//        <button data-perm="davicook_order:print" onclick="printModal(\'' . $row['id'] . '\',\'' .Carbon::make($row->bill_date)->format('d/m/Y'). '\',\'' . $row->status . '\',\'' . $row->customer_id . '\',\'' .Carbon::make($row->delivery_date)->format('d/m/Y'). '\')"
//            title="' . sc_language_render('order.print.title') . '" class="btn btn-flat btn-sm btn-warning text-white"><i class="fas fa-print"></i></button>
//        <button data-perm="davicook_order:print" onclick="printModal(\'' . $row['id'] . '\',\'' .Carbon::make($row->bill_date)->format('d/m/Y'). '\',\'' . $row->status . '\',\'' . $row->customer_id . '\',\'' .Carbon::make($row->delivery_date)->format('d/m/Y'). '\')"
//            title="' . sc_language_render('order.print.title') . '" class="btn btn-flat btn-sm btn-warning text-white"><i class="fas fa-print"></i></button>

        $data['listTh'] = $listTh;
        $data['dataTr'] = $dataTr;
        $data['pagination'] = $dataTmp->appends(request()->except([
            '_token', '_pjax'
        ]))->links($this->templatePathAdmin.'component.pagination');

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
                                    <select name="select_limit" style="width: 50px; margin-bottom: 8px" id="select_limit_paginate">
                                        ' . $optionPaginate . '
                                    </select>
                                    <div style="padding-left: 10px">Của '.$dataTmp->total().' kết quả </div>
                                </div>
                            </div>';

        //menuRight
        $data['menuRight'][] = '
                            <a data-perm="davicook_order:export" href="#" class="btn btn-flat btn btn-primary" id="btn_change_status_order_product_dry"><i class="fa fa-layer-group"></i>&nbsp;'.sc_language_render("admin.davicook.exprot_product_dry").'</a>
                            <a data-perm="davicook_order:print" class="btn btn-flat btn btn-info text-white" style="background-color: #3a5d62 !important;" id="btn_update_supplier"><i class="fa fa-print"></i>&nbsp;Cập nhập NCC</a>
                            <a data-perm="davicook_order:print" href="#" class="btn btn-flat btn btn-info text-white" onclick="printModal()"><i class="fa fa-print"></i>&nbsp;In đơn hàng</a>
                            <a data-perm="davicook_order:print" href="#" class="btn btn-flat btn btn-warning text-white" onclick="printModalCombine()"><i class="fa fa-print"></i>&nbsp;In đơn gộp</a>
                            <!--<a data-perm="davicook_order:print" href="#" id="btn_export_order_return_davicook" class="btn btn-flat btn btn-warning text-white" ><i class="fa fa-print"></i>&nbsp;Xuất phiếu hoàn trả</a>-->
                            <div data-perm="davicook_order:create" class="dropdown">
                                <button class="dropbtn btn btn btn-success btn-flat"><i class="fa fa-plus" title="'.sc_language_render('action.add').'"></i></button>
                                <div id="create-order-dropdown" class="dropdown-content">
                                    <div class="container">
                                        <div class="panel-group" role="tablist" aria-multiselectable="true">
                                            <div class="panel panel-default">
                                                <div class="panel-heading" role="tab">
                                                    <h4 class="panel-title">
                                                        <a href="'.sc_route_admin('admin.davicook_order.create').'" class="btn btn-flat btn-create-order" style="margin-top: 10px;">
                                                            Đơn đặt món ăn
                                                        </a>
                                                    </h4>
                                                </div>
                                            </div>
                                            <div class="panel panel-default">
                                                <div class="panel-heading" role="tab">
                                                    <h4 class="panel-title">
                                                        <a href="'.sc_route_admin('admin.davicook_order.create_essential_order').'" class="btn btn-flat btn-create-order">
                                                            Đơn nhu yếu phẩm
                                                        </a>
                                                    </h4>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>';
        //=menuRight

        //menuSort
        $optionSort = '';
        foreach ($arrSort as $key => $sort) {
            $optionSort .= '<option  '.(($dataSearch['sort_order'] == $key) ? "selected" : "").' value="'.$key.'">'.$sort.'</option>';
        }
        $data['optionSort'] = $optionSort;
        $data['urlSort'] = sc_route_admin('admin.davicook_order.index',
            request()->except(['_token', '_pjax', 'sort_order']));
        //=menuSort

        //menuSearch
        $optionStatus = '';
        foreach ($this->orderDavicookStatus as $key => $status) {
            $optionStatus .= '<option  '.(($dataSearch['order_status'] == $key) ? "selected" : "").' value="'.$key.'">'.$status.'</option>';
        }
        $orderType = [
            '0' => 'Món ăn',
            '1' => 'Nhu yếu phẩm',
        ];
        $optionOrderType = '';
        foreach ($orderType as $key => $status) {
            $optionOrderType .= '<option  '.(($dataSearch['order_type'] == $key) ? "selected" : "").' value="'.$key.'">'.$status.'</option>';
        }
        $orderDavicookPurchasePriorityLevels = ShopDavicookOrder::$PurchasePriorityLevels; 
        $optionPurchasePriorityLevels = '';
        foreach ($orderDavicookPurchasePriorityLevels as $key => $level) {
            $optionPurchasePriorityLevels .= '<option  '.(($dataSearch['order_purchase_priority_level'] == $key) ? "selected" : "").' value="'.$key.'">'.$level.'</option>';
        }
        $optionExplain = '';
        $orderExplains = ShopDavicookOrder::$NOTE;
        foreach ($orderExplains as $key => $explain) {
            $optionExplain .= '<option  ' . (($dataSearch['order_explain'] == $explain) ? "selected" : "") . ' value="' . $explain . '">' . $explain . '</option>';
        }
        $data['topMenuRight'][] = '
                <form action="'.sc_route_admin('admin.davicook_order.index').'" id="button_search" autocomplete="off">
                <input type="hidden" name="limit" value="'. ($dataSearch['limit'] ?? '') .'" id="limit_paginate">
                <input type="hidden" name="id_export_return" id="id_export_return">
                    <div class="input-group float-left">
                        <div class="col-lg-2 col-md-2 col-sm-12">
                            <div class="form-group">
                                <label>Chọn ngày</label>
                                <div class="input-group">
                                    <select class="form-control rounded-0" name="option_date">
                                        <option value="1" ' . ($dataSearch["option_date"] == 1 ? "selected" : "") . '>Ngày giao hàng</option>
                                        <option value="2" ' . ($dataSearch["option_date"] == 2 ? "selected" : "") . '>Ngày xuất khô</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-12">
                            <div class="form-group">
                                <label>'.sc_language_render('action.from').':</label>
                                <div class="input-group">
                                <input type="text" name="from_to" id="from_to" class="form-control input-sm date_time rounded-0" data-date-format="dd-mm-yyyy" placeholder="Chọn ngày" value="' . request('from_to') . '"/> 
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-12">
                            <div class="form-group">
                                <label>'.sc_language_render('action.to').':</label>
                                <div class="input-group">
                                <input type="text" name="end_to" id="end_to" class="form-control input-sm date_time rounded-0" data-date-format="dd-mm-yyyy" placeholder="Chọn ngày" value="' . request('end_to') . '"/> 
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-12">
                            <div class="form-group">
                                <label>Mức độ ưu tiên:</label>
                                <div class="input-group">
                                <select class="form-control rounded-0" name="order_purchase_priority_level">
                                <option value="">Tất cả mức độ</option>
                                '.$optionPurchasePriorityLevels.'
                                </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-12">
                            <div class="form-group">
                                <label>'.sc_language_render('order.admin.status').':</label>
                                <div class="input-group">
                                <select class="form-control rounded-0" name="order_status">
                                <option value="">Tất cả trạng thái</option>
                                '.$optionStatus.'
                                </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-12">
                            <div class="form-group">
                                <label>Loại Đơn :</label>
                                <div class="input-group">
                                <select class="form-control rounded-0" name="order_type">
                                <option value="">Tất cả</option>
                                '.$optionOrderType.'
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
                        <div class="col-lg-2 col-md-2 col-sm-12">
                            <div class="form-group">
                                <label>Khách hàng:</label>
                                <div class="input-group">
                                    <input type="text" name="keyword" class="form-control rounded-0 float-right" placeholder="Tìm tên KH, mã KH" value="'.$dataSearch['keyword'].'">
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-12">
                            <div class="form-group">
                                <label>Mã đơn hàng</label>
                                <div class="input-group">
                                    <input type="text" name="code" class="form-control rounded-0 float-right" placeholder="Mã đơn hàng" value="'.$dataSearch['code'].'">
                                    <div class="input-group-append">
                                        <button type="submit" class="btn btn-primary  btn-flat"><i class="fas fa-search"></i></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                    </div>
                </form>';
        //=menuSearch
        return view($this->templatePathAdmin.'screen.davicook_order.index')
            ->with($data);
    }

    /**
     * Handle delete order davicook.
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteDavicookOrder()
    {
        if (!request()->ajax()) {
            return response()->json(['error' => 1, 'msg' => sc_language_render('admin.method_not_allow')]);
        }
        $ids = request('ids');
        $arrID = explode(',', $ids);
        AdminDavicookOrder::destroy($arrID);

        return response()->json(['error' => 0, 'msg' => sc_language_render('action.update_success')]);
    }

    /**
     * Handle print multiple order davicook.
     */
    public function printMultipleOrderDavicook()
    {
        $checkStatusPrint = request('type_print_status');
        $typeExport = request('type_export');
        $ids = explode(',', request('ids'));
        $isDetail = request('detail');

        if ($checkStatusPrint == 1) {
            return $this->printByMeal($ids, $typeExport, $isDetail);
        }

        if ( $checkStatusPrint == 2) {
            return $this->printByProductFresh($ids, $typeExport, $isDetail);
        }

        if ( $checkStatusPrint == 3) {
            return $this->printByProductDry($ids, $typeExport, $isDetail);
        }
    }

    /**
     * Xuất từng đơn hàng theo suất ăn.
     * @param $ids
     * @param $typeExport
     * @param $isDetail
     * @return false|string|string[]|\Symfony\Component\HttpFoundation\BinaryFileResponse|void|null
     */
    public function printByMeal($ids, $typeExport, $isDetail)
    {
        $customerData = AdminDavicookOrder::with('customer')->whereIn('id', $ids)->where('type', 0)->get();

        if ($typeExport == 1) {
            if ($isDetail == 'detail') {
                $html = view($this->templatePathAdmin . 'print.order_davicook_print_by_meal_detail_template')
                    ->with(['data' => $customerData, 'isDetails' => $isDetail])->render();
                $html = mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8');
                $domPdf = new Dompdf();
                $domPdf->getOptions()->setChroot(public_path());
                $domPdf->loadHtml($html, 'UTF-8');
                $domPdf->setPaper('A5', 'portrait');
                $domPdf->render();

                return $domPdf->stream('Davicook suất ăn-' . now() . '.pdf', ["Attachment" => false]);
            }

            $html = view($this->templatePathAdmin . 'print.order_davicook_print_by_meal_template')
                ->with(['data' => $customerData])->render();
            $html = mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8');

            return $html;
        }

        return Excel::download(new AdminExportOrderDavicook($customerData, 'export_with_meal'), 'DavicookMeal - ' . Carbon::now() . '.xlsx');
    }

    /**
     * Xuất đơn hàng theo sản phẩm tươi sống.
     * Gộp đơn hàng theo customer_id và delivery_date.
     * @param $ids
     * @param $customerIds
     * @param $deliveryDates
     */
    public function printByProductFresh($ids, $typeExport, $isDetail)
    {
        $customerData = AdminDavicookOrder::with('customer')
            ->with(['details' => function ($query) {
                $query->where('product_type', 1);
                $query->where('total_bom','>', 0);
            }])->whereIn('id', $ids)->orderBy('customer_name', 'ASC')->get();

        if ($typeExport == 1) {
            if ($isDetail == 'detail') {
                $html = view($this->templatePathAdmin . 'print.order_davicook_print_by_status_product_detail_template')
                    ->with(['data' => $customerData])->render();
                $html = mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8');
                $domPdf = new Dompdf();
                $domPdf->getOptions()->setChroot(public_path());
                $domPdf->loadHtml($html, 'UTF-8');
                $domPdf->setPaper('A5', 'portrait');
                $domPdf->render();

                return $domPdf->stream('Davicook hàng tươi-' . now() . '.pdf', ["Attachment" => false]);
            }

            $html = view($this->templatePathAdmin . 'print.order_davicook_print_by_status_product_template')
                ->with(['data' => $customerData, 'isDetails' => $isDetail])->render();
            $html = mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8');

            return $html;
        }

        return Excel::download(new AdminExportOrderDavicook($customerData, 'export_with_product'), 'DavicookProductFresh - ' . Carbon::now() . '.xlsx');
    }

    /**
     * Xuất đơn hàng theo sản phẩm khô.
     * Gộp đơn hàng theo customer_id và bill_date.
     * @param $ids
     * @param $customerIds
     * @param $billDates
     */
    public function printByProductDry($ids, $typeExport, $isDetail)
    {
        $customerData = AdminDavicookOrder::with('customer')
            ->with(['details' => function ($query) {
                $query->where('product_type', 0);
                $query->where('total_bom','>', 0);
            }])->whereIn('id', $ids)->orderBy('customer_name', 'ASC')->get();

        if ($typeExport == 1) {
            if ($isDetail == 'detail') {
                $html = view($this->templatePathAdmin . 'print.order_davicook_print_by_status_product_detail_template')
                    ->with(['data' => $customerData])->render();
                $html = mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8');
                $domPdf = new Dompdf();
                $domPdf->getOptions()->setChroot(public_path());
                $domPdf->loadHtml($html, 'UTF-8');
                $domPdf->setPaper('A5', 'portrait');
                $domPdf->render();

                return $domPdf->stream('Davicook hàng khô-' . now() . '.pdf', ["Attachment" => false]);
            }

            $html = view($this->templatePathAdmin . 'print.order_davicook_print_by_status_product_template')
                ->with(['data' => $customerData])->render();

            $html = mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8');

            return $html;
        }

        return Excel::download(new AdminExportOrderDavicook($customerData, 'export_with_product'), 'DavicookProductDry - ' . Carbon::now() . '.xlsx');
    }

    public function printCombineMultipleOrderDavicook()
    {
        $checkStatusPrint = request('type_print_combine_status');
        $orderIds = explode(',', request('ids'));
        $typeExport = request('type_export_combine');
        if ($checkStatusPrint == 1) {
            return $this->printCombineByMeal($orderIds, $typeExport);
        }
        if ( $checkStatusPrint == 2) {
            return $this->printCombineByProductFresh($orderIds, $typeExport);
        }

        if ( $checkStatusPrint == 3) {
            return $this->printCombineByProductDry($orderIds, $typeExport);
        }
    }

    /**
     * In suất ăn gộp.
     * @param $orderIds
     * @param $typeExport
     * @return \Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\BinaryFileResponse|void
     */
    public function printCombineByMeal($orderIds, $typeExport) {
        $orderData = AdminDavicookOrder::with('customer')->whereIn('id', $orderIds)->where('type', 0)
            ->orderBy('customer_name', 'ASC')->get();
        if (!count($orderData) > 0) {
            return redirect()->route('admin.davicook_order.index')->with('error' , 'Đơn nhu yếu phẩm không thể in suất ăn!');
        }
        $orderData = $orderData->groupBy(['customer_id','bill_date','explain']);
        $sum = 0;
        $dataArray = $orderData->toArray();
        $dataArray = array_values($dataArray);
        foreach ($dataArray as $key => $value) {
            foreach ($value as $item) {
                $sum = $sum + count($item);
            }
        }
        // Check in pdf hoặc xuất excel.
        if ($typeExport == 1) {
            $html = view($this->templatePathAdmin . 'print.order_davicook_print_combine_by_meal_template')
                ->with(['data' => $orderData, 'totalData' => $sum])->render();
            $html = mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8');

            return $html;
        }

        return Excel::download(new AdminExportOrderDavicook($orderData, 'export_combine_with_meal'), 'Davicook - ' . Carbon::now() . '.xlsx');
    }

    /**
     * In hàng tươi - Cộng gộp đơn hàng.
     * @param $orderIds
     * @return mixed
     */
    public function printCombineByProductFresh($orderIds, $typeExport)
    {
        $objOrder = new AdminDavicookOrder();
        $nameTable = $objOrder->table;
        $dataTmp = $objOrder->join(SC_DB_PREFIX . "shop_davicook_order_detail as sod", function($join) use ($nameTable){
            $join->on($nameTable . ".id", "sod.order_id");
        })
            ->leftjoin(SC_DB_PREFIX . "shop_davicook_customer as sdc", function ($join) use ($nameTable){
                $join->on("sdc.id", $nameTable . ".customer_id");
            });
        $dataTmp = $dataTmp->whereIn($nameTable.".id", $orderIds);
        $dataTmp = $dataTmp->where("sod.product_type", 1);
        $dataTmp = $dataTmp->where("sod.total_bom",'>', 0);

        $dataTmp = $dataTmp
            ->select($nameTable . ".bill_date", $nameTable . ".id_name", $nameTable . ".customer_num", $nameTable . ".total",
                $nameTable . ".explain" , $nameTable . ".customer_id", $nameTable . ".customer_name", $nameTable . ".address",
                "sod.real_total_bom as qty", "sod.product_unit", "sod.product_name", "sod.product_id", "sdc.route")
            ->orderBy($nameTable.'.customer_name', 'ASC')
            ->orderBy("sod.created_at", "asc")->get();
        if (!count($dataTmp) > 0) {
            $html = view($this->templatePathAdmin . 'screen.error_template.error_product_print_combine_davicook')
                ->with(['error' => 'Không có sản phẩm là hàng tươi nào để in gộp. vui lòng check lại đơn hàng!'])->render();
            $html = mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8');

            return $html;
        }
        $dataTmp = $dataTmp->groupBy(['customer_id', 'bill_date', 'explain']);
        $sum = 0;
        $dataArray = $dataTmp->toArray();
        $dataArray = array_values($dataArray);
        foreach ($dataArray as $key => $value) {
            foreach ($value as $item) {
                $sum = $sum + count($item);
            }
        }
        if ($typeExport == 1) {
            $html = view($this->templatePathAdmin . 'print.order_davicook_print_combine_by_status_product_template')
                ->with(['data' => $dataTmp, 'totalData' => $sum])->render();
            $html = mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8');

            return $html;
        }

        return Excel::download(new AdminExportOrderDavicook($dataTmp, 'export_combine_with_product'), 'Davicook - ' . Carbon::now() . '.xlsx');
    }

    /**
     * In hàng khô - cộng gộp đơn hàng
     * @param $orderIds
     * @return mixed
     */
    public function printCombineByProductDry($orderIds, $typeExport)
    {
        $objOrder = new AdminDavicookOrder();
        $nameTable = $objOrder->table;
        $dataTmp = $objOrder->leftjoin(SC_DB_PREFIX . "shop_davicook_order_detail as sod", function($join) use ($nameTable){
            $join->on($nameTable . ".id", "sod.order_id");
        })
            ->leftjoin(SC_DB_PREFIX . "shop_davicook_customer as sdc", function ($join) use ($nameTable){
                $join->on("sdc.id", $nameTable . ".customer_id");
            });
        $dataTmp = $dataTmp->whereIn($nameTable.".id", $orderIds);
        $dataTmp = $dataTmp->where("sod.product_type", 0);
        $dataTmp = $dataTmp->where("sod.total_bom",'>', 0);

        $dataTmp = $dataTmp
            ->select($nameTable . ".bill_date",$nameTable . ".export_date", $nameTable . ".id_name", $nameTable . ".customer_num", $nameTable . ".total",
                $nameTable . ".explain" , $nameTable . ".customer_id", $nameTable . ".customer_name", $nameTable . ".address",
                "sod.real_total_bom as qty", "sod.product_unit", "sod.product_name", "sod.product_id", "sdc.route")
            ->orderBy($nameTable.'.customer_name', 'ASC')
            ->orderBy("sod.created_at", "asc")->get();

        if (!count($dataTmp) > 0) {
            $html = view($this->templatePathAdmin . 'screen.error_template.product_not_found_for_print_davicook_error')
                ->with(['error' => 'Không có sản phẩm là hàng khô nào để in gộp. vui lòng check lại đơn hàng!'])->render();
            $html = mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8');

            return $html;
        }
        $dataTmp = $dataTmp->groupBy(['customer_id', 'export_date', 'explain']);
        $sum = 0;
        $dataArray = $dataTmp->toArray();
        $dataArray = array_values($dataArray);
        foreach ($dataArray as $key => $value) {
            foreach ($value as $item) {
                $sum = $sum + count($item);
            }
        }
        if ($typeExport == 1) {
            $html = view($this->templatePathAdmin . 'print.order_davicook_print_combine_by_status_product_template')
                ->with(['data' => $dataTmp, 'totalData' => $sum])->render();
            $html = mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8');

            return $html;
        }

        return Excel::download(new AdminExportOrderDavicook($dataTmp, 'export_combine_with_product'), 'Davicook - ' . Carbon::now() . '.xlsx');
    }

    /**
     * Show display order return.
     * @param $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     */
    public function returnOrder($id)
    {
        $order = AdminDavicookOrder::getOrderDavicookAdmin($id);
        if (!$order) {
            return redirect()->route('admin.data_not_found')->with(['url' => url()->full()]);
        }

        // function take products and merge duplicate products
        $productsDetails = (new ShopDavicookOrderDetail())->getProductDavicookOrderDetail($id);

        $linkView = $order->type == 0 ? '.screen.davicook_order.return' : '.screen.davicook_order.return_essential';
        $title = $order->type == 0 ? sc_language_render('admin_order.return_title').' - Món ăn' : sc_language_render('admin_order.return_title').' - Nhu yếu phẩm';

        return view($this->templatePathAdmin.$linkView)->with(
            [
                "title" => $title,
                "subTitle" => '',
                'icon' => 'fa fa-file-text-o',
                "order" => $order,
                "productDetails" => $productsDetails,
            ]
        );
    }

    public function postReturn(AdminDavicookOrderReturnRequest $request)
    {
        $data = $request->validated();
        $order = ShopDavicookOrder::find($data['order_id']);
        $returned = 0;
        DB::beginTransaction();
        $productList = [];
        try {
            foreach ($data['qty'] as $detail_id => $qty) {
                if ($qty <= 0) {
                    continue;
                }

                $ordersDetail = ShopDavicookOrderDetail::find($detail_id);
                $typeUnit = $ordersDetail->product->unit->type ?? 0;
                if (!$ordersDetail) {
                    throw new \Exception('Không tìm thấy chi tiết đơn '.$order->id_name. '.id='.$detail_id);
                }
                if ($qty > $ordersDetail->real_total_bom) {
                    throw new \Exception('Số lượng trả lại không được lớn hơn số lượng hiện tại');
                }
                if ($order->type == 1) {
                    $productList[] = '- Nguyên liệu: '.$ordersDetail->product_name.'. Số lượng trả: '.$qty;
                } else {
                    $nameType = $ordersDetail->type == 1 ? 'Đơn bổ sung' : 'Đơn chính';
                    $productList[] ="- {$nameType}. Món ăn: {$ordersDetail->dish_name}. Nguyên liệu: {$ordersDetail->product_name} -> Số lượng {$qty}" ;
                }

                if ($typeUnit == 1) {
                    if (is_float($qty/1)) {
                        throw new \Exception("Nguyên liệu - {$ordersDetail->product_name} - Trong món ăn \" {$ordersDetail->dish_name} \" bắt buộc nhập số nguyên. Vui lòng kiểm tra lại!");
                    }
                }

                $returned += $qty * $ordersDetail->import_price;
                $returnHistory = new ShopDavicookOrderReturnHistory([
                    'id' => sc_uuid(),
                    'order_id' => $data['order_id'],
                    'order_id_name' =>$order->id_name,
                    'product_id' => $ordersDetail->product_id,
                    'product_name' => $ordersDetail->product_name,
                    'type' => $ordersDetail->type,
                    'dish_name' => $ordersDetail->dish_name,
                    'dish_code' => $ordersDetail->dish_id,
                    'product_code' => $ordersDetail->product_code,
                    'product_unit' => $ordersDetail->product_unit,
                    'category_id' => $ordersDetail->category_id,
                    'customer_id' => $order->customer_id,
                    'customer_code' => $order->customer_code,
                    'customer_name' => $order->customer_name,
                    'product_kind' => $ordersDetail->product_type,
                    'supplier_id' => $ordersDetail->supplier_id,
                    'detail_id' => $ordersDetail->id,
                    'original_qty' => $ordersDetail->real_total_bom,
                    'return_qty' => $qty,
                    'import_price' => $ordersDetail->import_price,
                    'admin_id' => Admin::user()->id ?? '',
                    'return_total' => $qty * $ordersDetail->import_price,
                ]);
                $returnHistory->save();
                if ($order->type == 1) {
                    $ordersDetail->real_total_bom = $ordersDetail->real_total_bom - $qty;
                    $ordersDetail->total_bom = $ordersDetail->total_bom - $qty;
                    $ordersDetail->amount_of_product_in_order = ($ordersDetail->total_bom - $qty) * $ordersDetail->import_price;
                    $ordersDetail->save();

                } else {
                    $ordersDetail->real_total_bom = $ordersDetail->real_total_bom - $qty;
                    $ordersDetail->save();
                }

            }
            $order->subtotal = $order->subtotal - $returned;
            $order->total = $order->total - $returned;
            $order->save();

            $dataHistory = new ShopDavicookOrderHistory([
                'order_id' => $data['order_id'],
                'content' => "Trả hàng: <br>" . implode("<br>", $productList),
                'admin_id' => Admin::user()->id ?? '',
                'order_status_id' => $order->status,
                'add_date' => now()
            ]);
            $dataHistory->save();
        } catch (\Throwable $e) {
            DB::rollBack();
            return redirect()->back()->with(['error' => $e->getMessage()]);
        }
        DB::commit();

        return redirect()->back()->with(['success' => 'Trả hàng thành công']);
    }

    /**
     * Hoàn tác trả hàng.
     * @return \Illuminate\Http\JsonResponse
     */
    public function undoReturnEssentialOrder()
    {
        $detail_id = request('detail_id');
        $return_id = request('return_id');
        $detail = ShopDavicookOrderDetail::find($detail_id);
        $realTotalBom = $detail->real_total_bom;
        $totalBom = $detail->total_bom;
        $history = ShopDavicookOrderReturnHistory::find($return_id);
        try {
            $order = ShopDavicookOrder::find($history->order_id);
            if ($detail) {
                $detail->real_total_bom = $realTotalBom + $history->return_qty;
                $detail->total_bom = $totalBom + $history->return_qty;
                $detail->amount_of_product_in_order = ($totalBom + $history->return_qty) * $detail->import_price;
                $detail->save();
            } else {
                $supplier_id = ShopProductSupplier::getSupplierOfProductAndCustomer($history->product_id, $order->customer_id)->supplier_id ?? '';
                $supplier = ShopSupplier::find($supplier_id);
                $product = AdminProduct::getProductAdmin($history->product_id);
                if (!$product) {
                    throw new \Exception('Không tìm thấy thông tin sản phẩm '.$history->product_name);
                }
                $id_barcode = str_pad(mt_rand(0, 999999999999), 12, '0', STR_PAD_LEFT);
                $insertData = [
                    'id' => sc_uuid(),
                    'order_id' => $history->order_id,
                    'id_barcode'=> $id_barcode,
                    'dish_id' => '',
                    'category_id' => $product->category_id,
                    'product_priority_level' => $product->purchase_priority_level,
                    'product_code' => $product->sku,
                    'product_id' => $history->product_id,
                    'dish_name' => 'Nhu yếu phẩm',
                    'product_name' => $product->name,
                    'product_num' => $product->order_num,
                    'product_short_name' => $product->short_name,
                    'product_unit' => $product->unit->name,
                    'product_type' => $product->kind,
                    'supplier_id' => $supplier_id,
                    'supplier_code' => $supplier->supplier_code ?? '',
                    'supplier_name' => $supplier->name ?? '',
                    'bom' => 0,
                    'bom_origin' => 0,
                    'qty' => 1,
                    'amount_of_product_in_order' => $history->return_total,
                    'total_bom' => $history->return_qty,
                    'real_total_bom' => $history->return_qty,
                    'import_price' => $history->improt_price,
                    'comment' => '',
                    'created_at' => now(),
                    'customer_id' => $order->customer_id,
                    'type' => 0
                ];
                (new ShopDavicookOrderDetail)->addNewDetail($insertData);
            }
            AdminDavicookOrderController::updateTotalOrder($history->order_id);
            $dataHistory = [
                'title' => 'Hoàn tác trả hàng',
                'order_id' => $detail->order_id,
                'content' => "Hoàn tác trả hàng: Nguyên liệu - {$history->product_name} -> Số lượng: " . $history->return_qty,
                'admin_id' => Admin::user()->id,
            ];
            (new ShopDavicookOrder)->addOrderHistory($dataHistory);
            $history->delete();
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['error' => 1, 'msg' => $e->getMessage()]);
        }
        DB::commit();
        return response()->json(['error' => 0, 'msg' => 'Hoàn tác thành công']);
    }

    /**
     * Hoàn tác trả hàng.
     * @return \Illuminate\Http\JsonResponse
     */
    public function undoReturnOrder()
    {
        $detail_id = request('detail_id');
        $return_id = request('return_id');
        try {
            $history = ShopDavicookOrderReturnHistory::find($return_id);
//            $order = ShopDavicookOrder::find($history->order_id);
            $detail = ShopDavicookOrderDetail::find($detail_id);
            $realTotalBom = $detail->real_total_bom;
            $totalBom = $detail->total_bom;
            if (!$detail) {
                throw new \Exception('lỗi món ăn này đã bị xóa Không thể hoàn tác. Món ăn: '.$history->dish_name);
            }

            if ($detail) {
                $detail->real_total_bom = $realTotalBom + $history->return_qty;
                $detail->total_bom = $totalBom + $history->return_qty;
                $detail->amount_of_product_in_order = ($totalBom + $history->return_qty) * $detail->import_price;
                $detail->save();
            }

            AdminDavicookOrderController::updateTotalOrder($history->order_id);
            $dataHistory = [
                'title' => 'Hoàn tác trả hàng',
                'order_id' => $detail->order_id,
                'content' => "Hoàn tác trả hàng: Món ăn - {$history->dish_name} . Nguyên liệu - {$history->product_name} -> Số lượng: " . $history->return_qty,
                'admin_id' => Admin::user()->id,
            ];
            (new ShopDavicookOrder)->addOrderHistory($dataHistory);
            $history->delete();
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['error' => 1, 'msg' => $e->getMessage()]);
        }
        DB::commit();
        return response()->json(['error' => 0, 'msg' => 'Hoàn tác thành công']);
    }

    /**
     * Store History order return.
     * @param  AdminDavicookOrderReturnRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postReturns(AdminDavicookOrderReturnRequest $request)
    {
        $data = $request->validated();

        $order = ShopDavicookOrder::find($data['order_id']);
        $returned = 0;
        DB::beginTransaction();
        try {

            foreach ($data['qty'] as $id => $qty) {
                if ($qty <= 0) {
                    continue;
                }
                $ordersDetails = ShopDavicookOrderDetail::where('order_id', $data['order_id'])->where('product_id',
                    $id)->get();

                $sum = (new ShopDavicookOrderDetail())->getSumQtyDetailProduct($data['order_id'], $id);

                if (!$ordersDetails) {
                    throw new \Exception('Không tìm thấy đơn hàng '.$data['order_id']);
                }

                if ($qty > $sum) {
                    throw new \Exception('Số lượng trả lại không được lớn hơn số lượng hiện tại');
                }

                $ordersDetail = $ordersDetails->first();
                // Save data to Order History
                $returned += $qty * $ordersDetail->import_price;
                $returnHistory = new ShopDavicookOrderReturnHistory([
                    'order_id' => $data['order_id'],
                    'product_id' => $id,
                    'product_name' => $ordersDetail->product_name,
                    'product_code' => $ordersDetail->product_code,
                    'product_unit' => $ordersDetail->product_unit,
                    'detail_id' => $ordersDetail->id,
                    'original_qty' => $sum,
                    'return_qty' => $qty,
                    'import_price' => $ordersDetail->import_price,
                    'admin_id' => Admin::user()->id ?? '',
                    'return_total' => $qty * $ordersDetail->import_price,
                ]);
                $returnHistory->save();

                 // FIXME : (Chỉnh sửa lại sau)
                 foreach ($ordersDetails as $key => $value) {
                     if ($value->real_total_bom <= $qty) {
                         $qty = $qty - $value->real_total_bom;
                         $value->real_total_bom = 0;
                         $value->save();
                         continue;
                     } else {
                         $value->real_total_bom = $value->real_total_bom - $qty;
                         $value->save();
                         break;
                     }
                 }

                if (!$ordersDetail->save() && !$returnHistory->save() && !$order->save()) {
                    throw new \Exception('Có lỗi xảy ra khi cập nhật chi tiết đơn hàng. Vui lòng kiểm tra lại');
                }
            }

            $dataHistory = new ShopDavicookOrderHistory([
                'order_id' => $data['order_id'],
                'content' => "Trả hàng",
                'admin_id' => Admin::user()->id ?? '',
                'order_status_id' => $order->status,
                'add_date' => now()
            ]);
            $dataHistory->save();

            // Update total order.
            $order->subtotal = $order->subtotal - $returned;
            $order->total = $order->total - $returned;
            $order->save();

            if (!$dataHistory->save() && !$order->save() && !$order->save()) {
                throw new \Exception('Có lỗi xảy ra khi cập nhật chi tiết đơn hàng. Vui lòng kiểm tra lại');
            }

        } catch (\Throwable $e) {
            return redirect()->back()->with(['error' => $e->getMessage()]);
        }

        DB::commit();

        return redirect()->back()->with(['success' => 'Trả hàng thành công']);
    }

    public function printOrderReturn($order_id) {
        // Find order
        $orderData = AdminDavicookOrder::with(['returnHistory', 'details'])->find($order_id);
        if (!count($orderData->toArray())) {
            return redirect()->route('admin.data_not_found')->with(['url' => url()->full()]);
        }
        $html = view($this->templatePathAdmin . 'print.print_order_return_davicook_template')
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
            'from_to' => sc_clean(request('from_to') ?? ''),
            'end_to' => sc_clean(request('end_to') ?? ''),
            'order_status' => sc_clean(request('order_status') ?? ''),
            'order_purchase_priority_level' => sc_clean(request('order_purchase_priority_level') ?? ''),
            'code' => sc_clean(request('code') ?? ''),
            'order_type' => sc_clean(request('order_type') ?? ''),
        ];
        $date['start'] = $dataSearch['from_to'];
        $date['end'] = $dataSearch['end_to'];
        if (!empty($ids)) {
            $data = ShopDavicookOrder::with('returnHistory', 'returnHistory.detail')->orderBy('delivery_date', 'asc')->orderBy('customer_name', 'asc')->findMany($ids);
        } else {
            if (empty($dataSearch['keyword']) && empty($dataSearch['from_to']) && empty($dataSearch['end_to']) && empty($dataSearch['order_status'])
                && empty($dataSearch['order_purchase_priority_level']) && empty($dataSearch['code']) && empty($dataSearch['order_type'])) {
                return redirect()->back()->with(['error' => 'Vui lòng lọc dữ liệu trước khi xuất đơn hoàn trả!']);
            }

            $data = (new AdminDavicookOrder())->getReturnOrderDavicook($dataSearch);

            if ($data->count() > 1000) {
                return redirect()->back()->with(['error' => 'Dữ liệu quá tải vui lòng chọn lại trường lọc!']);
            }
        }

        return Excel::download(new AdminExportOrderReturn($data, $date, 'davicook'), 'Phiếu hoàn trả - ' . Carbon::now() . '.xlsx');
    }

    /**
     * @return bool|\Illuminate\Http\RedirectResponse
     */
    public function changeStatusOrderDavicookProductDry()
    {
        $arrIds = explode( ',', request('exportIds'));
        $date = request('export_date');

        $export_date = Carbon::createFromFormat('d/m/Y', $date);
        if (!$export_date || $export_date->format('d/m/Y') != $date) {
            return redirect()->back()->with(['error' => 'Định dạng ngày không hợp lệ. Vui lòng chọn lại']);
        }
        DB::beginTransaction();
        try {
            foreach ($arrIds as $id) {
                $order = AdminDavicookOrder::where('id', $id)->where(function ($q){
                    $q->orwhere('status', 0)
                        ->orWhere('status', 1);
                })->first();

                if (!$order) {
                    throw new \Exception('Có đơn hàng không thể xuất khô, Vui lòng kiểm tra lại');
                }

                if ($order) {
                    $order->status = 2;
                    $order->export_date = $export_date;
                    $order->export_operation_date = now();
                    $order->save();
                }
            }

        } catch (\Throwable $e) {
            return redirect()->back()->with(['error' => $e->getMessage()]);
        }

        DB::commit();

        return redirect()->back();
    }

    /**
     *  Màn hình tạo mới đơn hàng davicook
     */
    public function create()
    {
        $dishs = ShopDavicookDish::where('status',1)->get();
        $customers = ShopDavicookCustomer::where('status',1)->get();
        $data = [
            'title' => 'Tạo hóa đơn',
            'subTitle' => '',
            'title_description' => 'Tạo hóa đơn',
            'icon' => 'fa fa-plus',
            'customers' => $customers,
            'orderObjects' => $this->orderObjects,
            'orderExplains' => ShopDavicookOrder::$NOTE,
            'dishs' => $dishs,
        ];
        return view($this->templatePathAdmin . 'screen.davicook_order.add')->with($data);
    }

    /**
     *  Màn hình tạo mới đơn hàng nhu yếu phẩm davicook
     */
    public function createEssentialOrder()
    {
        $customers = ShopDavicookCustomer::where('status', 1)->get();
        $data = [
            'title' => 'Tạo hóa đơn',
            'subTitle' => '',
            'title_description' => 'Tạo hóa đơn',
            'icon' => 'fa fa-plus',
            'customers' => $customers,
            'orderObjects' => $this->orderObjects,
            'orderExplains' => ShopDavicookOrder::$NOTE,
        ];
        return view($this->templatePathAdmin . 'screen.davicook_order.add_essential')->with($data);
    }


    /**
     *  Xứ lí tạo mới đơn hàng davicook
     */
    public function postCreate()
    {
        $data = request()->all();
        $id_barcode = str_pad(mt_rand(0, 999999999999), 12, '0', STR_PAD_LEFT);
        
        // Main order
        $add_dish_id = request('add_dish_id') ?? [];
        $add_product_id = request('add_product_id') ?? [];
        $add_product_name = request('add_product_name') ?? [];
        $add_product_unit = request('add_product_unit') ?? [];
        $add_product_type = request('add_product_type') ?? [];
        $add_total_bom = request('add_total_bom') ?? [];
        $import_price = request('add_import_price') ?? [];
        $add_comment = request('add_comment') ?? [];
        $add_bom = request('add_bom') ?? [];
        $bom_origin = request('bom_origin') ?? [];
        $add_qty = request('add_qty') ?? [];
       

        // Extra order
        $add_dish_id_extra_order = request('add_dish_id_extra_order') ?? [];
        $add_product_id_extra_order = request('add_product_id_extra_order') ?? [];
        $add_number_product_extra_order = request('add_number_product_extra_order') ?? [];
        $add_product_name_extra_order = request('add_product_name_extra_order') ?? [];
        $add_product_unit_extra_order = request('add_product_unit_extra_order') ?? [];
        $add_product_type_extra_order = request('add_product_type_extra_order') ?? [];
        $add_total_bom_extra_order = request('add_total_bom_extra_order') ?? [];
        $import_price_extra_order = request('add_import_price_extra_order') ?? [];
        $add_comment_extra_order = request('add_comment_extra_order') ?? [];
        $items = [];
        $extra_items = [];

        DB::beginTransaction();
        try {
            // Check null dish
            if ($data['explain'] != 'Hàng đợt 2') {
                if (empty($add_dish_id)) {
                    throw new \Exception('Chưa có món ăn nào ở đơn chính được thêm!');
                }
            } else {
                if (empty($add_dish_id_extra_order)) {
                    throw new \Exception('Đối với đơn hàng đợt 2. Vui lòng tạo đơn bổ sung!');
                }
            }

            // Check duplicate data
            if (count(array_unique($add_dish_id)) < count($add_dish_id)) {
                $mess = '';
                $array_count_values = array_count_values($add_dish_id);
                foreach ($array_count_values as $k => $v) {
                    if ($v > 1) $mess .= '- ' . (ShopDavicookDish::where('id', $k)->first()->name ?? '') . '<br>';
                }
                throw new \Exception('Bạn không thể thêm món ăn trùng lặp!<br>' . $mess);
            }
            // Check null bom, import_price value
            foreach ($import_price as $p) {
                $add_import_price[] = str_replace(",", "", $p);
            }
            foreach ($import_price_extra_order as $pe) {
                $add_import_price_extra_order[] = str_replace(",", "", $pe);
            }

            if ($data['explain'] != 'Hàng đợt 2') {
                if (in_array(null, $add_bom, true)) {
                    throw new \Exception('Định lượng, nguyên liệu suất không được để trống!');
                }
            } else {
                if (in_array(null, $add_product_id_extra_order, true)) {
                    throw new \Exception('Nguyên liệu đơn bổ sung chưa dược chọn, vui lòng kiểm tra lại!');
                }
            }
            $customer = ShopDavicookCustomer::find($data['customer_id']);
            if (!$customer) {
                throw new \Exception('Khách hàng không tồn tại hoặc đã bị xóa!');
            }

            if ($data['explain'] == 'Hàng đợt 2') {
                $customerDriver = AdminDriverCustomer::where('customer_id', $data['customer_id'])->where('type_order', 2)->first();
            } else {
                $customerDriver = AdminDriverCustomer::where('customer_id', $data['customer_id'])->where('type_order', 1)->first();
            }
            $driver = AdminDriver::where('id', $customerDriver->staff_id ?? '')->first();
            // Insert order
            $dataInsert = [
                'id_name' => (new ShopDavicookOrder())->getNextId(),
                'customer_id' => $data['customer_id'] ?? "",
                'customer_name' => $customer->name,
                'customer_code' => $customer->customer_code,
                'customer_short_name' => $customer->short_name,
                'customer_num' => $customer->order_num,
                'drive_id' => $driver->id ?? '',
                'drive_code' => $driver->id_name ?? '',
                'drive_address' => $driver->address ?? '',
                'drive_name' => $driver->full_name ?? '',
                'drive_phone' => $driver->phone ?? '',
                'price_of_servings' => $customer->serving_price,
                'number_of_servings' => $data['number_of_servings'],
                'number_of_extra_servings' => $data['number_of_extra_servings'] ?? 0,
                'number_of_reality_servings' => $data['number_of_servings'] + ($data['number_of_extra_servings'] ?? 0),
                'delivery_date' => $data['delivery_time'],
                'bill_date' => $data['bill_date'],
                'email' => $customer->email ?? '',
                'phone' => $customer->phone ?? '',
                'address' => $data['address'] ?? '',
                'explain' => $data['explain'] ?? '',
                'comment' => $data['comment'] ?? '',
                'status' => 1,
                'type' => 0
            ];
            $dataInsert = sc_clean($dataInsert, [], true);
            $order = ShopDavicookOrder::create($dataInsert);
            $customer_id = $order->customer_id;
            $order_id = $order->id;

            // Insert detail main order 
            $c = 0;
            for ($i = 0; $i < count($add_dish_id); $i++) {
                if ($add_dish_id[$i] == null) {
                    unset($add_dish_id[$i]);
                } else {
                    $menu = ShopDavicookMenu::where('dish_id', $add_dish_id[$i])->where('customer_id', $customer_id)->first();
                    $idMenu = $menu->id;
                    $count_product = count(ShopDavicookMenuDetail::where('menu_id', $idMenu)->get());
                    for ($j = 0; $j < $count_product; $j++) {
                        $product = AdminProduct::getProductAdmin($add_product_id[$j + $c]);
                        if (!$product) {
                            return response()->json(['error' => 1, 'msg' => 'Món ăn có nguyên liệu đã bị xóa không thể đặt hàng', 'detail' => '']);
                        }
                        $total_cost_item = $add_total_bom[$j + $c] * $add_import_price[$j + $c]; // total_cost item (total_cost = bom*qty*import_price)
                        $supplier_id = ShopDavicookProductSupplier::getSupplierOfProductAndCustomer($add_product_id[$j + $c], $customer_id)->supplier_id ?? '';
                        $supplier = ShopSupplier::find($supplier_id);
                        $getDishName = $this->orderDishName; // Get dish name order detail
                        $dishName = $getDishName[$add_dish_id[$i]] ?? '';
                        $dishNameOrder = $getDishNameOrder[$add_dish_id[$i]] ?? '';
                        $items[] = array(
                            'id' => sc_uuid(),
                            'order_id' => $order_id,
                            'id_barcode'=> $id_barcode,
                            'dish_id' => $add_dish_id[$i],
                            'category_id' => $product->category_id,
                            'product_priority_level' => $product->purchase_priority_level,
                            'product_code' => $product->sku,
                            'product_id' => $add_product_id[$j + $c],
                            'dish_name' => $dishName,
                            'product_name' => $add_product_name[$j + $c],
                            'product_num' => $product->order_num,
                            'product_short_name' => $product->short_name,
                            'product_unit' => $add_product_unit[$j + $c],
                            'product_type' => $add_product_type[$j + $c],
                            'supplier_id' => $supplier_id,
                            'supplier_code' => $supplier->supplier_code ?? '',
                            'supplier_name' => $supplier->name ?? '',
                            'bom' => $add_bom[$j + $c],
                            'bom_origin' => $bom_origin[$j + $c],
                            'qty' => $add_qty[$j + $c],
                            'amount_of_product_in_order' => $total_cost_item,
                            'total_bom' => $add_total_bom[$j + $c],
                            'real_total_bom' => $add_total_bom[$j + $c],
                            'import_price' => $add_import_price[$j + $c],
                            'comment' => $add_comment[$j + $c],
                            'created_at' => now()->addSeconds($j*2),
                            'customer_id' => $customer_id,
                            'type' => 0
                        );
                    }
                    $c = $c + $count_product;
                }
            }
            if ($items) {
                (new ShopDavicookOrderDetail)->addNewDetail($items);
            }

            // Insert detail extra order 
            $c = 0;
            for ($i = 0; $i < count($add_dish_id_extra_order); $i++) {
                if ($add_dish_id_extra_order[$i] == null) {
                    unset($add_dish_id_extra_order[$i]);
                } else {
                    for ($j = 0; $j < $add_number_product_extra_order[$i]; $j++) {
                        $product = AdminProduct::getProductAdmin($add_product_id_extra_order[$j + $c]);
                        if (!$product) {
                            return response()->json(['error' => 1, 'msg' => 'Món ăn có nguyên liệu đã bị xóa không thể đặt hàng', 'detail' => '']);
                        }
                        $total_cost_item = ($add_total_bom_extra_order[$j + $c]) * ($add_import_price_extra_order[$j + $c]);  // total_cost item (total_cost = bom*qty*import_price)
                        $supplier_id = ShopDavicookProductSupplier::getSupplierOfProductAndCustomer($add_product_id_extra_order[$j + $c], $customer_id)->supplier_id ?? '';
                        $supplier = ShopSupplier::find($supplier_id);
                        $getDishName = $this->orderDishName;
                        $dishName = $getDishName[$add_dish_id_extra_order[$i]];

                        $extra_items[] = array(
                            'id' => sc_uuid(),
                            'order_id' => $order_id,
                            'id_barcode'=> $id_barcode,
                            'dish_id' => $add_dish_id_extra_order[$i],
                            'category_id' => $product->category_id,
                            'product_priority_level' => $product->purchase_priority_level,
                            'product_code' => $product->sku,
                            'product_id' => $add_product_id_extra_order[$j + $c],
                            'dish_name' => $dishName,
                            'product_name' => $add_product_name_extra_order[$j + $c],
                            'product_num' => $product->order_num,
                            'product_short_name' => $product->short_name,
                            'product_unit' => $add_product_unit_extra_order[$j + $c] ?? '',
                            'product_type' => $add_product_type_extra_order[$j + $c] ?? '',
                            'supplier_id' => $supplier_id,
                            'supplier_code' => $supplier->supplier_code ?? '',
                            'supplier_name' => $supplier->name ?? '',
                            'bom' => 0,
                            'bom_origin' => 0,
                            'qty' => $add_total_bom_extra_order[$j + $c],
                            'amount_of_product_in_order' => $total_cost_item,
                            'total_bom' => $add_total_bom_extra_order[$j + $c],
                            'real_total_bom' => $add_total_bom_extra_order[$j + $c],
                            'import_price' => $add_import_price_extra_order[$j + $c],
                            'comment' => $add_comment_extra_order[$j + $c] ?? '',
                            'created_at' => now()->addSeconds($j*2),
                            'customer_id' => $customer_id,
                            'type' => 1
                        );
                    }
                    $c = $c + $add_number_product_extra_order[$i];
                }
            }
            if ($extra_items) {
                (new ShopDavicookOrderDetail)->addNewDetail($extra_items);
            }

            // Update total order
            AdminDavicookOrderController::updateTotalOrder($order_id);

            // Update purchase_priority_level order if product has purchase_priority_level = 1
            AdminDavicookOrder::UpdatePurchasePriorityLevel($order_id);

            # Lưu lịch sử thông báo
            $notification = new AdminNotification();
            $notification->title = "Đơn hàng mới";
            $notification->content = "Admin đã đặt thành công đơn hàng số #" . $order->id_name . " với tổng số tiền " . number_format($order->total, 2);
            $notification->id_order = $order->id;
            $notification->order_code = $order->id_name;
            $notification->customer_code = $customer->customer_code;
            $notification->customer_name = $customer->name;
            $notification->desc = $customer->name . " tạo đơn hàng mới.";
            $notification->order_type = 2;
            $notification->edit_type = 1;
            $notification->display = 0;
            $notification->save();
            logger($notification);
            $dataHistory = new ShopDavicookOrderHistory([
                'order_id' => $order->id,
                'content' => "Tạo đơn hàng",
                'admin_id' => Admin::user()->id ?? '',
                'order_status_id' => $order->status,
                'add_date' => now()
            ]);
            $dataHistory->save();
            
        } catch (\Throwable $e) {
            DB::rollBack();
            $messages = $e->getMessage();
            return response()->json(['error' => 1, 'msg' => $messages]);
        }
        DB::commit();
        return response()->json(['error' => 0, 'msg' => 'Đặt hàng thành công!', 'order_id' => $order_id]);
    }


     /**
     * Xứ lí tạo mới đơn hàng nhu yếu phẩm davicook
     */
    public function postCreateEssentialOrder()
    {
        $data = request()->all();
        $customer_id = request('customer_id');
        $add_product_id = request('add_product_id') ?? [];
        $add_product_name = request('add_product_name') ?? [];
        $add_product_unit = request('add_product_unit') ?? [];
        $add_product_type = request('add_product_type') ?? [];
        $import_price = request('add_import_price') ?? [];
        $add_product_qty = request('add_product_qty') ?? [];
        $add_product_comment = request('add_product_comment') ?? [];
        $items = [];

        DB::beginTransaction();
        try {
            // Check null product
            if (empty($add_product_id)) {
                return response()->json(['error' => 1, 'msg' => 'Chưa có sản phẩm nào được thêm!']);
            }

            // Check null qty, product_price value
            foreach ($import_price as $p) {
                $add_import_price[] = str_replace(",", "", $p);
            }
            if (in_array(null, $add_product_qty, true)) {
                return response()->json(['error' => 1, 'msg' => 'Số lượng của sản phẩm không được để trống!']);
            }

            $customer = ShopDavicookCustomer::find($data['customer_id']);
            if (!$customer) {
                return response()->json(['error' => 1, 'msg' => 'Khách hàng không tồn tại hoặc đã bị xóa!']);
            }

            if ($data['explain'] == 'Hàng đợt 2') {
                $customerDriver = AdminDriverCustomer::where('customer_id', $data['customer_id'])->where('type_order', 2)->first();
            } else {
                $customerDriver = AdminDriverCustomer::where('customer_id', $data['customer_id'])->where('type_order', 1)->first();
            }
            $driver = AdminDriver::where('id', $customerDriver->staff_id ?? '')->first();

            // Insert order
            $data_insert = [
                'id_name' => (new ShopDavicookOrder())->getNextId(),
                'customer_id' => $data['customer_id'] ?? "",
                'customer_name' => $customer->name,
                'customer_code' => $customer->customer_code,
                'customer_short_name' => $customer->short_name,
                'customer_num' => $customer->order_num,
                'price_of_servings' => $customer->serving_price,
                'drive_id' => $driver->id ?? '',
                'drive_code' => $driver->id_name ?? '',
                'drive_address' => $driver->address ?? '',
                'drive_name' => $driver->full_name ?? '',
                'drive_phone' => $driver->phone ?? '',
                'number_of_servings' => 0,
                'number_of_extra_servings' => 0,
                'delivery_date' => $data['delivery_time'],
                'bill_date' => $data['bill_date'],
                'email' => $customer->email ?? '',
                'phone' => $customer->phone ?? '',
                'address' => $data['address'] ?? '',
                'explain' => $data['explain'] ?? '',
                'comment' => $data['comment'] ?? '',
                'status' => 1,
                'type' => 1
            ];

            $data_insert = sc_clean($data_insert, [], true);
            $order = ShopDavicookOrder::create($data_insert);
            $order_id = $order->id;

            // Insert detail
            foreach ($add_product_id as $key => $id) {
                if ($id && $add_product_qty[$key]) {
                    $supplier_id = ShopDavicookProductSupplier::getSupplierOfProductAndCustomer($add_product_id[$key], $customer_id)->supplier_id ?? '';
                    $supplier = ShopSupplier::find($supplier_id);
                    $product = ShopProduct::with('unit')->where('id', $id)->first();
                    $typeUnit = $product->unit->type ?? 0;
                    if (!$product) {
                        return response()->json(['error' => 1, 'msg' => sc_language_render('admin.data_not_found_detail', ['msg' => '#' . $id]), 'detail' => '']);
                    }
                    $totalBom = $typeUnit == 1 ? ceil($add_product_qty[$key]) : $add_product_qty[$key];
                    $id_barcode = str_pad(mt_rand(0, 999999999999), 12, '0', STR_PAD_LEFT);
                    $items[] = array(
                        'id' => sc_uuid(),
                        'order_id' => $order_id,
                        'id_barcode'=> $id_barcode,
                        'dish_id' => '',
                        'category_id' => $product->category_id,
                        'product_priority_level' => $product->purchase_priority_level,
                        'product_code' => $product->sku,
                        'product_id' => $add_product_id[$key],
                        'dish_name' => 'Nhu yếu phẩm',
                        'product_name' => $add_product_name[$key],
                        'product_num' => $product->order_num,
                        'product_short_name' => $product->short_name,
                        'product_unit' => $add_product_unit[$key] ?? '',
                        'product_type' => $add_product_type[$key] ?? '',
                        'supplier_id' => $supplier_id,
                        'supplier_code' => $supplier->supplier_code ?? '',
                        'supplier_name' => $supplier->name ?? '',
                        'bom' => 0,
                        'bom_origin' => 0,
                        'qty' => 1,
                        'amount_of_product_in_order' => $totalBom * $add_import_price[$key],
                        'total_bom' => $totalBom,
                        'real_total_bom' => $totalBom,
                        'import_price' => $add_import_price[$key],
                        'comment' => $add_product_comment[$key],
                        'created_at' => now()->addSeconds($key * 2),
                        'customer_id' => $customer_id,
                        'type' => 0
                    );
                }
            }
            if ($items) {
                ShopDavicookOrderDetail::insert($items);
                // Update total order
                AdminDavicookOrderController::updateTotalOrder($order_id);
                // Update purchase_priority_level order if product has purchase_priority_level = 1
                AdminDavicookOrder::UpdatePurchasePriorityLevel($order_id);
            }
            # Thông báo đơn hàng
            $notification = new AdminNotification();
            $notification->title = "Đơn hàng mới";
            $notification->content = "Admin đã đặt thành công đơn hàng số #" . $order->id_name . " với tổng số tiền " . number_format($order->total, 2);
            $notification->id_order = $order->id;
            $notification->order_code = $order->id_name;
            $notification->customer_code = $customer->customer_code;
            $notification->customer_name = $customer->name;
            $notification->desc = $customer->name . " tạo đơn hàng mới.";
            $notification->order_type = 2;
            $notification->edit_type = 1;
            $notification->display = 0;
            $notification->save();

            # Lịch sử đơn hàng
            $dataHistory = new ShopDavicookOrderHistory([
                'order_id' => $order->id,
                'content' => "Tạo đơn hàng",
                'admin_id' => Admin::user()->id ?? '',
                'order_status_id' => $order->status,
                'add_date' => now()
            ]);
            $dataHistory->save();
        } catch (\Throwable $e) {
            DB::rollBack();
            $messages = $e->getMessage();
            return response()->json(['error' => 1, 'msg' => $messages]);
        }
        DB::commit();
        return response()->json(['error' => 0, 'msg' => 'Đặt hàng thành công!', 'order_id' => $order_id]);
    }


    // Screen order detail item
    public function detail($id)
    {
        $selected_customer= ShopDavicookOrder::where('id',$id)->first();
        $customers = ShopDavicookCustomer::where('status','1')->where('id','<>',$selected_customer->customer_id)->get();
        $order = AdminDavicookOrder::getOrderDavicookAdmin($id);
        $dish_order = ShopDavicookOrderDetail::select('dish_id','dish_name')->where('order_id',$id)->where('type',0)->distinct()->get();
        $dish_extra_order = ShopDavicookOrderDetail::select('dish_id','dish_name')->where('order_id',$id)->where('type',1)->distinct()->get();
        $orderNote = ShopDavicookOrder::$NOTE;
        $order['history'] = ShopDavicookOrderHistory::where('order_id', 'like', $id)->orderBy('add_date', 'Desc')->get();
        $total_cost = ShopDavicookOrderDetail::where('order_id',$id)->sum('amount_of_product_in_order');
        $total_return_order = ShopDavicookOrderReturnHistory::where('order_id',$id)->sum('return_total');
        $get_order = ShopDavicookOrder::where('id',$id)->first();
        $number_of_serving = ($get_order) ? $get_order->number_of_servings : 0;

        if (!$order) {
            return redirect()->route('admin.data_not_found')->with(['url' => url()->full()]);
        }

        // Return order history
        $return_order_histotry = ShopDavicookOrderReturnHistory::where('order_id',$id)->get();
        $checkHasOrderReturn = (count($return_order_histotry) > 0) ? 1 : 0;

        // Get dishlist
        $dish_in_order = $dish_order->pluck('dish_id');
        $dish_in_extra_order = $dish_extra_order->pluck('dish_id');
        $dishs = ShopDavicookMenu::where('customer_id',$selected_customer->customer_id)->whereNotIn('dish_id',$dish_in_order)->get();
        $dishs_extra_order = ShopDavicookMenu::where('customer_id',$selected_customer->customer_id)->whereNotIn('dish_id',$dish_in_extra_order)->get();

        $data = [
            "title" => sc_language_render('order.order_detail'),
            "subTitle" => '',
            'icon' => 'fa fa-file-text-o',
            "order" => $order,
            'dishs' => $dishs,
            'orderNote' => $orderNote,
            'customers' => $customers,
            'dish_order' => $dish_order,
            "statusOrder" => $this->orderDavicookStatus,
            'dish_extra_order' => $dish_extra_order,
            'orderDishName' => $this->orderDishName,
            'orderDishCode' => $this->orderDishCode,
            'returnOrderHistory' =>  $return_order_histotry,
            'checkHasOrderReturn' => $checkHasOrderReturn,
            'dishs_extra_order' => $dishs_extra_order,
            'total_cost' => $total_cost,
            'number_of_serving' => $number_of_serving,
            'total_return_order' => $total_return_order,
            'editable' => $this->checkOrderEditable($order)
        ];

        return view($this->templatePathAdmin . 'screen.davicook_order.edit')->with($data);
    }

    // Screen order detail item
    public function essentialOrderDetail($id)
    {
        $selected_customer= ShopDavicookOrder::where('id',$id)->first();
        $customers = ShopDavicookCustomer::where('status','1')->where('id','<>',$selected_customer->customer_id)->get();
        $order = AdminDavicookOrder::getOrderDavicookAdmin($id);
        $dish_order = ShopDavicookOrderDetail::select('dish_id','dish_name')->where('order_id',$id)->where('type',0)->distinct()->get();
        $dish_extra_order = ShopDavicookOrderDetail::select('dish_id','dish_name')->where('order_id',$id)->where('type',1)->distinct()->get();
        $orderNote = ShopDavicookOrder::$NOTE;
        $order['history'] = ShopDavicookOrderHistory::where('order_id', 'like', $id)->orderBy('add_date', 'Desc')->get();
        $total_cost = ShopDavicookOrderDetail::where('order_id',$id)->sum('amount_of_product_in_order');
        $total_return_order = ShopDavicookOrderReturnHistory::where('order_id',$id)->sum('return_total');
        $get_order = ShopDavicookOrder::where('id',$id)->first();
        $number_of_serving = ($get_order) ? $get_order->number_of_servings : 0;

        if (!$order) {
            return redirect()->route('admin.data_not_found')->with(['url' => url()->full()]);
        }

        // Return order history
        $return_order_histotry = ShopDavicookOrderReturnHistory::where('order_id',$id)->get();
        $checkHasOrderReturn = (count($return_order_histotry) > 0) ? 1 : 0;

        // Get dishlist
        $dishs_order = ShopDavicookOrderDetail::where('order_id',$id)->where('type',0)->distinct()->get();
        $dish_in_order = [];
        foreach($dishs_order as $k => $v) {
            $dish_in_order[] = $v->dish_id;
        }
        $dishs = ShopDavicookMenu::where('customer_id',$selected_customer->customer_id)->whereNotIn('dish_id',$dish_in_order)->get();
        $dishs_extra_order = ShopDavicookMenu::where('customer_id',$selected_customer->customer_id)->get();
        $data = [
            "title" => sc_language_render('order.order_detail'),
            "subTitle" => '',
            'icon' => 'fa fa-file-text-o',
            "order" => $order,
            'dishs' => $dishs,
            'orderNote' => $orderNote,
            'customers' => $customers,
            'dish_order' => $dish_order,
            "statusOrder" => $this->orderDavicookStatus,
            'dish_extra_order' => $dish_extra_order,
            'orderDishName' => $this->orderDishName,
            'orderDishCode' => $this->orderDishCode,
            'returnOrderHistory' =>  $return_order_histotry,
            'checkHasOrderReturn' => $checkHasOrderReturn,
            'dishs_extra_order' => $dishs_extra_order,
            'total_cost' => $total_cost,
            'number_of_serving' => $number_of_serving,
            'total_return_order' => $total_return_order,
            'editable' => $this->checkOrderEditable($order)
        ];
        return view($this->templatePathAdmin . 'screen.davicook_order.edit_essential')->with($data);
    }

    /**
     * Xử lý cập nhật đơn hàng davicook
     */
    //TODO
    public function postOrderUpdate(AdminOrderDavicookEditRequest $request)
    {
        $data = $request->validated();
        $id_barcode = str_pad(mt_rand(0, 999999999999), 12, '0', STR_PAD_LEFT);
        
        try {
            $id = $data['pk'];
            $code = $data['name'];
            $value = $data['value'];
            
            $order = AdminDavicookOrder::findOrFail($id);
            $dataOrderDetailAfterUpdate = ShopDavicookOrderDetail::Where('order_id',$id)->where('type', 0)->get()->toArray();
            if (!$order) {
                return response()->json(['error' => 1, 'msg' => 'Hóa đơn không tồn tại hoặc đã bị xóa!', 'detail' => '']);
            }

            $itemUpdate = '';
            $oldValue = $order->{$code};
            $newValue = $value;

            if ($code == 'comment') {
                $itemUpdate = 'ghi chú đơn hàng';
            } else if ($code == 'status') {
                $itemUpdate = 'trạng thái';
                if ($value == 2 ) {
                   return response()->json(['error' => 1, 'msg' => 'Không thể đổi trạng thái xuất khô. Vui lòng xuất khô ở màng danh sách!']);
                }
                if ($value == 7) {
                    $dataOrderDetailCancle = ShopDavicookOrderDetail::Where('order_id',$id)->get()->toArray();
                    foreach ($dataOrderDetailCancle as $item) {
                        $dataExtra = [
                            'order_id' => $id,
                            'id_barcode'=> $id_barcode,
                            'order_name' => $order->id_name,
                            'delivery_date_origin' => $order->status == 2 ? $order->export_date : $order->delivery_date,
                            'product_id' => $item['product_id'],
                            'product_name' => $item['product_name'],
                            'product_short_name' => $item['product_short_name'],
                            'product_num' => $item['product_num'],
                            'product_unit' => $item['product_unit'],
                            'qty' => $item['total_bom'],
                            'qty_change' => $item['total_bom'],
                            'content' => "Đơn hàng hủy",
                            'type_content' => 6,
                            'customer_code' => $order->customer_code,
                            'customer_name' => $order->customer_name,
                            'create_order' => date('d-m-Y H:i:s', strtotime($order->created_at)),
                            'order_detail_id' => $item['id'],
                            'category_id' => $item['category_id'],
                            'product_code' => $item['product_code'],
                            'kind' => $item['product_type'],
                            'note' => $item['comment'] ?? '',
                            'status' => $order->status,
                            'type_order' => 2,
                        ];
                        (new AdminShopOrderChangeExtra())->create($dataExtra);
                    }
                }
                $oldStatus = ShopDavicookOrderStatus::find($oldValue);
                $newStatus = ShopDavicookOrderStatus::find($value);
                $oldValue = $oldStatus ? $oldStatus->name : '';
                $newValue = $newStatus ? $newStatus->name : '';
            } else if ($code == 'number_of_servings') {
                $itemUpdate = 'Số lượng suất ăn chính';
                $items = ShopDavicookOrderDetail::Where('order_id',$id)->where('type', 0)->get();
                if ($items) {
                    foreach($items as $v) {
                        $item = ShopDavicookOrderDetail::find($v->id);
                        $unit_type = $item->product->unit->type ?? 0;
                        $item->qty = $newValue;
                        $item->amount_of_product_in_order = roundTotalBom($newValue*$v->bom, $unit_type)*$v->import_price;
                        $item->total_bom = roundTotalBom($newValue*$v->bom, $unit_type);
                        $item->real_total_bom = roundTotalBom($newValue*$v->bom, $unit_type);
                        $item->save();
                    }
                }
                $kind = $order->export_date ? 3 : 2 ;
                foreach ($dataOrderDetailAfterUpdate as $element) {
                    $dataExtra = [
                        'order_id' => $id,
                        'id_barcode'=> $id_barcode,
                        'delivery_date_origin' => $order->status == 2 ? $order->export_date : $order->delivery_date,
                        'order_name' => $order->id_name,
                        'product_id' => $element['product_id'],
                        'product_name' => $element['product_name'],
                        'product_short_name' => $element['product_short_name'],
                        'product_num' => $element['product_num'],
                        'product_unit' => $element['product_unit'],
                        'qty' => $element['total_bom'],
                        'qty_change' => $element['total_bom'],
                        'content' => "Chỉnh sửa số lượng suất ăn chính",
                        'type_content' => 5,
                        'customer_code' => $order->customer_code,
                        'customer_name' => $order->customer_name,
                        'create_order' => date('d-m-Y H:i:s', strtotime($order->created_at)),
                        'order_detail_id' => $element['id'],
                        'category_id' => $element['category_id'],
                        'product_code' => $element['product_code'],
                        'kind' => $element['product_type'],
                        'note' => $element['comment'] ?? '',
                        'status' => $order->status,
                        'type_order' => 2,
                    ];
                    (new AdminShopOrderChangeExtra())->create($dataExtra);
                }

                $order->number_of_reality_servings = $order->number_of_extra_servings + $newValue;
                $order->save();
            } else if ($code == 'number_of_extra_servings') {
                $itemUpdate = 'Số lượng suất ăn bổ sung';
                $order->number_of_reality_servings = $order->number_of_servings + $newValue;
                $order->save();
            } else if ($code == 'explain') {
                $itemUpdate = 'diễn giải';
            } else if ($code == 'delivery_date') {
                AdminShopOrderChangeExtra::where('order_id', $id)->update([
                    'content' => 'Đơn hàng hủy',
                ]);
                $itemUpdate = 'Thời gian giao hàng';
            } else if ($code == 'bill_date') {
                $itemUpdate = 'Ngày trên hóa đơn';
            } else if ($code == 'bom') {
                $itemUpdate = 'Định lượng';
            } else if ($code == 'import_price') {
                $itemUpdate = 'Giá nhập';
            } else if ($code == 'email') {
                $itemUpdate = 'Email';
            } else if ($code == 'qty') {
                $itemUpdate = 'Số lượng';
            } else if ($code == 'number_of_reality_servings') {
                $itemUpdate = 'Số lượng suất ăn thực tế';
            }

            $maxLen = 50;
            $oldValue = $oldValue ? $oldValue : '';
            $newValue = $newValue ? $newValue : '';
            $oldValue = strlen($oldValue) > $maxLen ? substr($oldValue, $maxLen) : $oldValue;
            $newValue = strlen($newValue) > $maxLen ? substr($newValue, $maxLen) : $newValue;
            $contentHistory = 'Sửa ' . $itemUpdate . ' : ' . $oldValue . ' -> ' . $newValue;

            if (!$order->update([$code => $value])) {
                return response()->json(['error' => 1, 'msg' => sc_language_render('action.update_fail')]);
            }

            // Update order edited status
            $order->update(['edited' => 1]);

            // Update total order
            AdminDavicookOrderController::updateTotalOrder($id);

            // Add history
            $dataHistory = [
                'order_id' => $id,
                'content' => $contentHistory,
                'admin_id' => Admin::user()->id
            ];
            (new AdminDavicookOrder)->addOrderHistory($dataHistory);

            $arrayReturn = [
                'history' => AdminDavicookOrderController::loadHistory($id),
                'error' => 0,
                'msg' => sc_language_render('action.update_success')
            ];
        } catch (\Throwable $e) {
            $arrayReturn = ['error' => 1, 'msg' => $e->getMessage()];
        }

        return response()->json($arrayReturn);
    }


    // Get customer info (select customer)
    public function getInfoCustomer() {
        $id = request('id');
        $order_id = request('order_id');
        $shopCustomer = ShopDavicookCustomer::find($id);
        if ($shopCustomer) {
            $result = $shopCustomer->toJson();
        }
        return $result;
    }


    // Update customer info (select customer)
    public function updateInfoCustomer() {
        $id = request('id');
        $order_id = request('order_id');
        $shopCustomer = ShopDavicookCustomer::find($id);
        if ($shopCustomer) {
            $dataUpdate = [
                'customer_id' => $shopCustomer->id,
                'customer_name' => $shopCustomer->name,
                'address' => $shopCustomer->address,
                'phone' => $shopCustomer->phone,
                'email' => $shopCustomer->email,
                'edited' => 1
            ];
            ShopDavicookOrder::where('id',$order_id)->update($dataUpdate);

            //update customer_id on davicook_order_detail
            $dataOrderDetailUpdate = [
                'customer_id' => $shopCustomer->id,
            ];
            ShopDavicookOrderDetail::where('order_id',$order_id)->update($dataOrderDetailUpdate);

            //Add history
            $dataHistory = [
                'order_id' => $order_id,
                'content' => 'Thay đổi thông tin khách hàng: ' .$shopCustomer->name,
                'admin_id' => Admin::user()->id
            ];
            (new AdminDavicookOrder)->addOrderHistory($dataHistory);

            return response()->json([
                'error' => 0,
                'msg' => sc_language_render('action.update_success')
            ]);
        }

    }

    /**
     * Xử lý cập nhật chi tiết đơn hàng davicook
     */
    // TODO
    public function postOrderDetailUpdate(AdminOrderDavicookEditRequest $request)
    {
        $data = $request->validated();
        try {
            $id = $data['pk'];
            $field = $data['name'];
            $value = $data['value'];
            $item = ShopDavicookOrderDetail::find($id);
            $fieldOrg = $item->{$field};
            $orderId = $item->order_id;
            $item->{$field} = $value;

            if ($field == 'qty' || $field == 'bom') {
                $unit_type = $item->product->unit->type ?? 0;
                $item->total_bom = roundTotalBom($value * (($field == 'qty') ? $item->bom : $item->qty), $unit_type);
                $item->real_total_bom = roundTotalBom($value * (($field == 'qty') ? $item->bom : $item->qty), $unit_type);
                $item->amount_of_product_in_order  = roundTotalBom($value * (($field == 'qty') ? $item->bom : $item->qty), $unit_type) * $item->import_price;
            }
            if ($field == 'import_price') {
                $item->amount_of_product_in_order  = ($value * $item->total_bom);
                $display = 'Giá nhập';
            }
            if ($field == 'bom') {
                $display = 'Định lượng';
            }
            if ($field == 'total_bom') {
                $item->amount_of_product_in_order  = $value * $item->import_price;
                $item->real_total_bom = $value;
                $display = 'Nguyên liệu suất/Số lượng';
            }
            if ($field == 'qty') {
                $display = 'Số lượng';
            }
            if ($field == 'import_price') {
                $display = 'Giá nhập';
            }
            if ($field == 'comment') {
                $display = 'Ghi chú';
            }
            $item->save();
            $item = $item->fresh();

            // --- ORDER ----
            $total_return_order = ShopDavicookOrderReturnHistory::where('order_id', $orderId)->sum('return_total');
            $total_cost = ShopDavicookOrderDetail::where('order_id', $orderId)->sum('amount_of_product_in_order');
            $order = AdminDavicookOrder::getOrderDavicookAdmin($orderId);
            // Update order edited status
            $order->update(['edited' => 1]);
            Log::info($order);
            // Update total order
            AdminDavicookOrderController::updateTotalOrder($orderId);
            // Update purchase_priority_level order if product has purchase_priority_level = 1
            AdminDavicookOrder::UpdatePurchasePriorityLevel($orderId);
            $arrItem = $item->toArray();
            if($display == 'Định lượng' || $display == 'Nguyên liệu suất/Số lượng') {
                $kind = $order->export_date ? 3 : 2 ;
                $id_barcode = str_pad(mt_rand(0, 999999999999), 12, '0', STR_PAD_LEFT);
                $dataExtra = [
                    'order_id' => $order->id,
                    'id_barcode'=> $id_barcode,
                    'delivery_date_origin' => $order->status == 2 ? $order->export_date : $order->delivery_date,
                    'order_name' => $order->id_name,
                    'product_id' => $arrItem['product_id'],
                    'product_name' => $arrItem['product_name'],
                    'product_short_name' => $arrItem['product_short_name'],
                    'product_num' => $arrItem['product_num'],
                    'product_unit' => $arrItem['product_unit'],
                    'qty' => $arrItem['total_bom'],
                    'qty_change' => $arrItem['total_bom'],
                    'content' => $display == 'Định lượng' ? "Chỉnh sửa định lượng món ăn" : "Chỉnh sửa số lượng sản phẩm",
                    'type_content' => 1,
                    'customer_code' => $order->customer_code,
                    'customer_name' => $order->customer_name,
                    'create_order' => date('d-m-Y H:i:s', strtotime($order->created_at)),
                    'order_detail_id' => $arrItem['id'],
                    'category_id' => $arrItem['category_id'],
                    'product_code' => $arrItem['product_code'],
                    'kind' => $arrItem['product_type'],
                    'note' => $arrItem['comment'] ?? '',
                    'status' => $order->status,
                    'type_order' => 2,
                ];
                (new AdminShopOrderChangeExtra())->create($dataExtra);
            }

            //Add history
            $dataHistory = [
                'order_id' => $orderId,
                'content' => sc_language_render('product.edit_product') . ' ' . $item->product_name . ': ' . $display . ' thay đổi ' . $fieldOrg . ' -> ' . $value,
                'admin_id' => Admin::user()->id,
                'order_status_id' => $order->status,
            ];
            (new AdminDavicookOrder)->addOrderHistory($dataHistory);

            $orderUpdated = $order->fresh();
            // --- END UPDATE ORDER ----

            $arrayReturn = [
                'error' => 0,
                'detail' => [
                    'total' => sc_currency_render(round($orderUpdated->total - $total_return_order) ?? 0, 'vnd'),
                    'total_cost' => sc_currency_render(round($total_cost) ?? 0, 'vnd'),
                    'item_total_bom' => (checkRoundedIntTotalBom($item->qty*$item->bom, ($item->product->unit->type ?? 0))) 
                                        ?
                                        '<b>'.number_format(roundTotalBom($item->qty*$item->bom, $item->product->unit->type ?? 0), 2, '.', '').'</b>'
                                        :
                                        number_format(roundTotalBom($item->qty*$item->bom, $item->product->unit->type ?? 0), 2, '.', ''),
                    'item_total_cost' => sc_currency_render(round($item->amount_of_product_in_order ?? 0), 'vnd'),
                    'item_unit' => $item->product_unit ?? "",
                    'item_id' => $id,
                    'subtotal' => sc_currency_format($orderUpdated->subtotal),
                ],
                'msg' => sc_language_render('action.update_success'),
                'history' => AdminDavicookOrderController::loadHistory($orderId)
            ];

            $notification = new AdminNotification();
            $notification->title = "Sửa đơn hàng";
            $notification->content = 'Admin sửa chi tiết đơn hàng #' . $order->id_name;
            $notification->id_order = $order->id;
            $notification->order_code = $order->id_name;
            $notification->customer_code = $order->customer_code;
            $notification->customer_name = $order->customer_name;
            $notification->desc = $order->customer_name. " sửa đơn hàng.";
            $notification->order_type = 2;
            $notification->edit_type = 2;
            $notification->display = 0;
            $notification->save();

        } catch (\Throwable $e) {
            $arrayReturn = ['error' => 1, 'msg' => $e->getMessage()];
        }
        return response()->json($arrayReturn);
    }

    /**
     * Lấy thông tin món ăn (nguyên liệu) ở chi tiết đơn hàng davicook
     */
    public function getInfoDish()
    {
        $id = request('id');
        $cId = request('cId');
        $oId = request('oId');
        $delivery_time = (new AdminDavicookOrder())->findOrFail($oId)->delivery_date;
        $key = rand();
        $out_of_stock = 0;
        $msg_out_of_stock = '';
       
        // Dish code
        $getDishCode = $this->orderDishCode;
        $arrayReturn['dish_code'] =  $getDishCode[$id] ?? '';
        $get_order = ShopDavicookOrder::where('id',$oId)->first();
        if($get_order){
            $number_of_serving = $get_order->number_of_servings;
        }else{
            $number_of_serving = 0;
        }

        $menu = ShopDavicookMenu::where('dish_id',$id)->where('customer_id',$cId)->first();
        if ($menu) {
            $get_product_by_dish = $menu->details;
            $arrayReturn['products'] = '<td class="pro_by_dish">';
            foreach($get_product_by_dish as $k =>$v){
                if ($v->product->status == 0) {
                    $out_of_stock = 1;
                    $msg_out_of_stock = $v->product->name .' - Hết hàng!';
                }
                $productName = $v->product->name ?? 'Nguyên liệu đã bị xóa';
                $productType = $v->product->kind ?? '';
                $productUnit = $v->product->unit->name ?? '';
                $arrayReturn['products'] .='    <div class="edit_pro_id_'.$k+$key.'">
                                                    <input readonly name="add_product_name[]" type="text" class="form-control"  value="'. $productName.'">
                                                    <input type="hidden" name="add_product_unit[]" type="text" class="form-control"  value="'. $productUnit.'">
                                                    <input type="hidden" name="add_product_type[]" type="text" class="form-control"  value="'. $productType.'">
                                                    <input type="hidden" name="add_product_id[]"  type="text" class="form-control"  value="'.$v->product_id.'">
                                                </div>';
            }
            $arrayReturn['products'] .='</td>
                                            <td class="pro_by_dish">';
            foreach($get_product_by_dish as $k =>$v){
                $arrayReturn['products'] .='    <div class="edit_pro_id_'.$k+$key.'">
                                                    <input '.($v->product->status == 1 ? '' : "readonly").' onKeyup="update_total('.$k+$key.','.($v->product->unit->type ?? 0).');" name="add_bom[]" onInput="limitDecimalPlaces(event,7)" type="number" class="add_bom_'.$k+$key.' form-control" min="0" step="0.0000001" value="'.($v->product->status == 1 ? $v->qty : 0).'"></input>
                                                    <input type="hidden" name="bom_origin[]" value="'.($v->product->status == 1 ? $v->qty : 0).'"></input>
                                                </div>';
            }
            $arrayReturn['products'] .='</td>
                                            <td class="pro_by_dish">';
            foreach($get_product_by_dish as $k =>$v){
                $arrayReturn['products'] .='    <div class="edit_pro_id_'.$k+$key.'">
                                                    <input onKeyup="update_total('.$k+$key.','.($v->product->unit->type ?? 0).');this.value = Math.abs(this.value);" name="add_qty[]" type="number" step="1" min="0" class="add_qty_'.$k+$key.' form-control"  value="'.$number_of_serving.'">
                                                </div>';
            }
            $arrayReturn['products'] .='</td>
                                            <td class="pro_by_dish">';
            foreach($get_product_by_dish as $k =>$v){
                $arrayReturn['products'] .='    <div class="edit_pro_id_'.$k+$key.'">
                                                    <input readonly type="number" name="add_total_bom[]" min=0 step="1" class="form-control add_total_bom_'.$k+$key.'"  value="'.roundTotalBom(($v->product->status == 1 ? $v->qty : 0) * $number_of_serving, $v->product->unit->type ?? 0).'">
                                                </div>';
            }
            $arrayReturn['products'] .='</td>
                                        <td class="pro_by_dish">';
            foreach($get_product_by_dish as $k =>$v) {
                // Get import price
                $import_price = (new AdminDavicookCustomer())->getImportPrice($cId,$v->product_id,$delivery_time);
                // product have import product = 0 then show noti
                if ($import_price==0) {
                    $arrayReturn['error'] = 1;
                    $arrayReturn['msg'] = 'Bảng giá nhập của nguyên liệu chưa được cập nhập!';
                }
                $arrayReturn['products'] .='    <div class="edit_pro_id_'.$k+$key.'">
                                                    <input readonly onKeyup="formatCurrency($(this)); update_total('.$k+$key.','.($v->product->unit->type ?? 0).');" name="add_import_price[]" type="text" min=0 step="1" class="import_price add_import_price_'.$k+$key.' form-control"  value="'.number_format(round($import_price,0)).'">
                                                </div>';
            }
            $arrayReturn['products'] .='</td>
                                            <td class="pro_by_dish">';
            foreach($get_product_by_dish as $k =>$v){
                // Get import price
                $import_price = (new AdminDavicookCustomer())->getImportPrice($cId,$v->product_id,$delivery_time);
                $arrayReturn['products'] .='    <div class="edit_pro_id_'.$k+$key.'">
                                                    <input readonly name="add_total_cost[]" type="text" class="form-control amount_of_product_in_order add_total_cost_'.$k+$key.'"  value="'. number_format(roundTotalBom(($v->product->status == 1 ? $v->qty : 0) * $number_of_serving, $v->product->unit->type ?? 0) * $import_price) .'">
                                                </div>';
            }
            $arrayReturn['products'] .='</td>
                                            <td class="pro_by_dish">';
            foreach($get_product_by_dish as $k =>$v){
                $arrayReturn['products'] .='    <div class="edit_pro_id_'.$k+$key.'">
                                                    <input name="add_comment[]" type="text" class="form-control"  value="">
                                                </div>';
            }
            $arrayReturn['products'] .='</td>';

        }else {
            return response()->json(['error' => 1, 'msg' => sc_language_render('admin.data_not_found_detail', ['msg' => '#product:' . $id]), 'detail' => '']);
        }
        $arrayReturn['out_of_stock'] = $out_of_stock;
        $arrayReturn['msg_out_of_stock'] = $msg_out_of_stock;

        return response()->json($arrayReturn);
    }

    /**
     * Xử lí thêm mới món ăn đến đơn đặt hàng ở chi tiết đơn hàng davicook
     */
    //todo
    public function postAddItem()
    {
        $data = request()->all();
        $id_barcode = str_pad(mt_rand(0, 999999999999), 12, '0', STR_PAD_LEFT);
        $add_dish_id = request('add_dish_id');
        $add_product_id = request('add_product_id');
        $add_product_name = request('add_product_name');
        $add_product_unit = request('add_product_unit');
        $add_product_type = request('add_product_type');
        $add_bom = request('add_bom');
        $add_total_bom = request('add_total_bom');
        $add_qty = request('add_qty');
        $bom_origin = request('bom_origin');
        $import_price = request('add_import_price');
        $add_comment = request('add_comment');
        $orderId = request('order_id');
        $customerId = request('cId');
        $order = AdminDavicookOrder::findOrFail($orderId);
        $items = [];

        // Check null dish
        if(empty($add_dish_id)) {
            return response()->json(['error' => 1, 'msg' => sc_language_render('Bạn chưa chọn món ăn nào', ['msg' => '#']), 'detail' => '']);
        }
        // Check duplicate data
        $arr_unique = array_unique($add_dish_id);
        if(count($arr_unique)<count($add_dish_id)) {
            return response()->json(['error' => 1, 'msg' => sc_language_render('Bạn không thể thêm món trùng lặp', ['msg' => '#']), 'detail' => '']);
        }

        // Check null bom, import_price value
        foreach($import_price as $p) {
            $add_import_price[] = str_replace(",", "", $p);
        }
        if(in_array(null, $add_import_price, true) || in_array(null, $add_bom, true)) {
            return response()->json(['error' => 1, 'msg' => sc_language_render('Định lượng, giá nhập không được để trống!', ['msg' => '#']), 'detail' => '']);
        }

        // Add item
        $c = 0;
        $kind = $order->export_date ? 3 : 2 ;
        for($i=0;$i<count($add_dish_id);$i++) {
            if ($add_dish_id[$i]===null) {
                unset($add_dish_id[$i]);
            }else {
                $menu = ShopDavicookMenu::where('dish_id',$add_dish_id[$i])->where('customer_id',$customerId)->first();
                $idMenu = $menu->id;
                $count_product = count(ShopDavicookMenuDetail::where('menu_id',$idMenu)->get());
                for($j=0;$j<$count_product;$j++) {
                    $product = AdminProduct::getProductAdmin($add_product_id[$j+$c]);
                    if (!$product) {
                        return response()->json(['error' => 1, 'msg' => 'Món ăn có nguyên liệu đã bị xóa không thể đặt hàng', 'detail' => '']);
                    }
                    // total_cost item (total_cost = bom*qty*import_price)
                    $total_cost_item = $add_total_bom[$j+$c]*$add_import_price[$j+$c];

                    // Get dish name order detail
                    $getDishNameOrder = $this->orderDishName;
                    $dishNameOrder = $getDishNameOrder[$add_dish_id[$i]] ?? '';
                    $supplier_id = ShopDavicookProductSupplier::getSupplierOfProductAndCustomer($add_product_id[$j+$c], $customerId)->supplier_id ?? '';
                    $supplier = ShopSupplier::find($supplier_id);
                    $items[] = array(
                        'id' => sc_uuid(),
                        'order_id' => $orderId,
                        'id_barcode'=> $id_barcode,
                        'dish_id' => $add_dish_id[$i],
                        'category_id' => $product->category_id,
                        'product_priority_level' => $product->purchase_priority_level,
                        'product_code' => $product->sku,
                        'product_id' => $add_product_id[$j+$c],
                        'dish_name' => $dishNameOrder,
                        'product_name' => $add_product_name[$j+$c],
                        'product_short_name' => $product->short_name,
                        'product_num' => $product->order_num,
                        'product_unit' => $add_product_unit[$j+$c],
                        'product_type' => $add_product_type[$j+$c],
                        'supplier_id' => $supplier_id,
                        'supplier_code' => $supplier->supplier_code ?? '',
                        'supplier_name' => $supplier->name ?? '',
                        'bom' => $add_bom[$j+$c],
                        'qty' => $add_qty[$j+$c],
                        'bom_origin' => $bom_origin[$j+$c],
                        'amount_of_product_in_order' => $total_cost_item,
                        'real_total_bom' => $add_total_bom[$j+$c],
                        'total_bom' => $add_total_bom[$j+$c],
                        'import_price' => $add_import_price[$j+$c],
                        'comment' => $add_comment[$j+$c],
                        'customer_id' => $data['customer_id'] ?? "",
                        'created_at' => now()->addSeconds($j * 2),
                    );
                }
                $c = $c + $count_product;
                $getDishName = $this->orderDishName;
                $dishName = $getDishName[$add_dish_id[$i]] ?? 'Món ăn đã bị xóa';
                $dishList[] = $dishName;
            }
        }
        if ($items) {
            try {
                (new ShopDavicookOrderDetail)->addNewDetail($items);
                // Add history
                $dataHistory = [
                    'order_id' => $orderId,
                    'content' => "Thêm món ăn: " . implode("<br>", $dishList),
                    'admin_id' => Admin::user()->id
                ];
                foreach($items as $item) {
                    $dataExtra = [
                        'order_id' => $order->id,
                        'id_barcode'=> $id_barcode,
                        'delivery_date_origin' => $order->status == 2 ? $order->export_date : $order->delivery_date,
                        'order_name' => $order->id_name,
                        'product_id' =>  $item['product_id'],
                        'product_name' =>  $item['product_name'],
                        'product_short_name' => $item['product_short_name'],
                        'product_num' => $item['product_num'],
                        'product_unit' => $item['product_unit'],
                        'qty' => $item['total_bom'],
                        'qty_change' => $item['total_bom'],
                        'content' => "Thêm mới món ăn",
                        'type_content' => 7,
                        'customer_code' => $order->customer_code,
                        'customer_name' => $order->customer_name,
                        'create_order' => date('d-m-Y H:i:s', strtotime($order->created_at)),
                        'order_detail_id' =>$item['id'],
                        'category_id' => $item['category_id'],
                        'product_code' => $item['product_code'],
                        'kind' => $item['product_type'],
                        'note' => $item['comment']?? '',
                        'status' => $order->status,
                        'type_order' => 2,
                    ];
                    (new AdminShopOrderChangeExtra())->create($dataExtra);
                }
                (new AdminDavicookOrder)->addOrderHistory($dataHistory);

                // Update total order
                AdminDavicookOrderController::updateTotalOrder($orderId);

                // Update purchase_priority_level order if product has purchase_priority_level = 1
                AdminDavicookOrder::UpdatePurchasePriorityLevel($orderId);

                // Update order edited status
                $order->update(['edited' => 1]);

                $notification = new AdminNotification();
                $notification->title = "Sửa đơn hàng";
                $notification->content = 'Admin thêm chi tiết đơn hàng #' . $order->id_name;
                $notification->id_order = $order->id;
                $notification->order_code = $order->id_name;
                $notification->customer_code = $order->customer_code;
                $notification->customer_name = $order->customer_name;
                $notification->desc = $order->customer_name . " thêm chi tiết đơn hàng.";
                $notification->order_type = 2;
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

    /**
     * Xử lí thêm mới nhu yếu phẩm đến chi tiết đơn hàng davicook
     */
    public function postAddItemEssentialOrder()
    {
        $data = request()->all();
        $id_barcode = str_pad(mt_rand(0, 999999999999), 12, '0', STR_PAD_LEFT);
        $customer_id = request('customer_id');
        $add_product_id = request('add_product_id') ?? [];
        $add_product_name = request('add_product_name') ?? [];
        $add_product_unit = request('add_product_unit') ?? [];
        $add_product_type = request('add_product_type') ?? [];
        $import_price = request('add_import_price') ?? [];
        $add_product_qty = request('add_product_qty') ?? [];
        $add_product_comment = request('add_product_comment') ?? [];
        $order_id = request('order_id');
        $order = AdminDavicookOrder::findOrFail($order_id);
        $kind = $order->export_date ? 3 : 2 ;
        $items = [];

        // Check null product
        if (empty($add_product_id)) {
            return response()->json(['error' => 1, 'msg' => 'Chưa có sản phẩm nào được thêm!']);
        }

        // Check null qty, product_price value
        foreach ($import_price as $p) {
            $add_import_price[] = str_replace(",", "", $p);
        }
        if (in_array(null, $add_product_qty, true)) {
            return response()->json(['error' => 1, 'msg' => 'Số lượng của sản phẩm không được để trống!']);
        }

        $customer = ShopDavicookCustomer::find($data['customer_id']);
        if (!$customer) {
            return response()->json(['error' => 1, 'msg' => 'Khách hàng không tồn tại hoặc đã bị xóa!']);
        }

        // Insert detail
        foreach ($add_product_id as $key => $id) {
            if ($id && $add_product_qty[$key]) {
                $supplier_id = ShopDavicookProductSupplier::getSupplierOfProductAndCustomer($add_product_id[$key], $customer_id)->supplier_id ?? '';
                $supplier = ShopSupplier::find($supplier_id);
                $product = ShopProduct::with('unit')->where('id', $id)->first();
                $typeUnit = $product->unit->type ?? 0;
                if (!$product) {
                    return response()->json(['error' => 1, 'msg' => sc_language_render('admin.data_not_found_detail', ['msg' => '#' . $id]), 'detail' => '']);
                }
                $totalBom = $typeUnit == 1 ? ceil($add_product_qty[$key]) : $add_product_qty[$key];
                $productList[] = $add_product_name[$key]; 
                $items[] = array(
                    'id' => sc_uuid(),
                    'order_id' => $order_id,
                    'id_barcode'=> $id_barcode,
                    'dish_id' => '',
                    'category_id' => $product->category_id,
                    'product_priority_level' => $product->purchase_priority_level,
                    'product_code' => $product->sku,
                    'product_id' => $add_product_id[$key],
                    'dish_name' => 'Nhu yếu phẩm',
                    'product_name' => $add_product_name[$key],
                    'product_num' => $product->order_num,
                    'product_short_name' => $product->short_name,
                    'product_unit' => $add_product_unit[$key] ?? '',
                    'product_type' => $add_product_type[$key] ?? '',
                    'supplier_id' => $supplier_id,
                    'supplier_code' => $supplier->supplier_code ?? '',
                    'supplier_name' => $supplier->name ?? '',
                    'bom' => 0,
                    'bom_origin' => 0,
                    'qty' => 1,
                    'amount_of_product_in_order' => $totalBom * $add_import_price[$key],
                    'total_bom' => $totalBom,
                    'real_total_bom' => $totalBom,
                    'import_price' => $add_import_price[$key],
                    'comment' => $add_product_comment[$key],
                    'created_at' => now()->addSeconds($key * 2),
                    'customer_id' => $customer_id,
                    'type' => 0
                );
            }
        }
        if ($items) {
            try {
                ShopDavicookOrderDetail::insert($items);
                // Add history
                $dataHistory = [
                    'order_id' => $order_id,
                    'content' => "Thêm sản phẩm: " . implode("<br>", $productList),
                    'admin_id' => Admin::user()->id
                ];
                (new AdminDavicookOrder)->addOrderHistory($dataHistory);
                foreach($items as $item) {
                    $dataExtra = [
                        'order_id' => $order->id,
                        'id_barcode'=> $id_barcode,
                        'delivery_date_origin' => $order->status == 2 ? $order->export_date : $order->delivery_date,
                        'order_name' => $order->id_name,
                        'product_id' =>  $item['product_id'],
                        'product_name' =>  $item['product_name'],
                        'product_short_name' => $item['product_short_name'],
                        'product_num' => $item['product_num'],
                        'product_unit' => $item['product_unit'],
                        'qty' => $item['total_bom'],
                        'qty_change' => $item['total_bom'],
                        'content' => "Thêm mới sản phẩm",
                        'type_content' => 8,
                        'customer_code' => $order->customer_code,
                        'customer_name' => $order->customer_name,
                        'create_order' => date('d-m-Y H:i:s', strtotime($order->created_at)),
                        'order_detail_id' =>$item['id'],
                        'category_id' => $item['category_id'],
                        'product_code' => $item['product_code'],
                        'kind' => $item['product_type'],
                        'note' => $item['comment'] ?? '',
                        'status' => $order->status,
                        'type_order' => 2,
                    ];
                    (new AdminShopOrderChangeExtra())->create($dataExtra);
                }

                // Update total order
                AdminDavicookOrderController::updateTotalOrder($order_id);

                // Update purchase_priority_level order if product has purchase_priority_level = 1
                AdminDavicookOrder::UpdatePurchasePriorityLevel($order_id);

                // Update order edited status
                $order = AdminDavicookOrder::findOrFail($order_id);
                $order->update(['edited' => 1]);

                $notification = new AdminNotification();
                $notification->title = "Sửa đơn hàng";
                $notification->content = 'Admin thêm chi tiết đơn hàng #' . $order->id_name;
                $notification->id_order = $order->id;
                $notification->order_code = $order->id_name;
                $notification->customer_code = $order->customer_code;
                $notification->customer_name = $order->customer_name;
                $notification->desc = $order->customer_name . " thêm chi tiết đơn hàng.";
                $notification->order_type = 2;
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

    /**
     * Xử lí thêm mới món ăn bổ sung đến đơn đặt hàng ở chi tiết đơn hàng davicook
     */
    public function postAddItemExtraOrder()
    {
        $data = request()->all();
        $add_dish_id_extra_order = request('add_dish_id_extra_order') ?? [];
        $add_product_id_extra_order = request('add_product_id_extra_order') ?? [];
        $add_number_product_extra_order = request('add_number_product_extra_order') ?? [];
        $add_product_name_extra_order = request('add_product_name_extra_order') ?? [];
        $add_product_unit_extra_order = request('add_product_unit_extra_order') ?? [];
        $add_product_type_extra_order = request('add_product_type_extra_order') ?? [];
        $add_total_bom_extra_order = request('add_total_bom_extra_order') ?? [];
        $import_price_extra_order = request('add_import_price_extra_order') ?? [];
        $add_comment_extra_order = request('add_comment_extra_order') ?? [];
        $order_id = request('order_id');
        $customer_id = request('customer_id');
        $extra_items = [];

        // Check null
        if(empty($add_dish_id_extra_order)) {
            return response()->json(['error' => 1, 'msg' => sc_language_render('Bạn chưa chọn món ăn nào', ['msg' => '#']), 'detail' => '']);
        }

        // Check duplicate data
        $arr_unique = array_unique($add_dish_id_extra_order);
        if(count($arr_unique)<count($add_dish_id_extra_order)) {
            return response()->json(['error' => 1, 'msg' => sc_language_render('Bạn không thể thêm món trùng lặp!', ['msg' => '#']), 'detail' => '']);
        }

        // Check null bom, import_price value
        foreach ($import_price_extra_order as $pe) {
            $add_import_price_extra_order[] = str_replace(",", "", $pe);
        }
        if (in_array(null, $add_total_bom_extra_order, true)) {
            return response()->json(['error' => 1, 'msg' => 'Nguyên liệu suất không được để trống!']);
        }
        if (in_array(null, $add_product_id_extra_order, true)) {
            return response()->json(['error' => 1, 'msg' => 'Nguyên liệu đơn bổ sung chưa dược chọn, vui lòng kiểm tra lại!']);
        }

        // Insert detail extra order 
        $c = 0;
        for ($i = 0; $i < count($add_dish_id_extra_order); $i++) {
            if ($add_dish_id_extra_order[$i] == null) {
                unset($add_dish_id_extra_order[$i]);
            } else {
                for ($j = 0; $j < $add_number_product_extra_order[$i]; $j++) {
                    $product = AdminProduct::getProductAdmin($add_product_id_extra_order[$j + $c]);
                    if (!$product) {
                        return response()->json(['error' => 1, 'msg' => 'Món ăn có nguyên liệu đã bị xóa không thể đặt hàng', 'detail' => '']);
                    }
                    $total_cost_item = ($add_total_bom_extra_order[$j + $c]) * ($add_import_price_extra_order[$j + $c]);  // total_cost item (total_cost = bom*qty*import_price)
                    $supplier_id = ShopDavicookProductSupplier::getSupplierOfProductAndCustomer($add_product_id_extra_order[$j + $c], $customer_id)->supplier_id ?? '';
                    $supplier = ShopSupplier::find($supplier_id);
                    $getDishName = $this->orderDishName;
                    $dishName = $getDishName[$add_dish_id_extra_order[$i]];

                    $extra_items[] = array(
                        'id' => sc_uuid(),
                        'order_id' => $order_id,
                        'id_barcode'=> $id_barcode,
                        'dish_id' => $add_dish_id_extra_order[$i],
                        'category_id' => $product->category_id,
                        'product_priority_level' => $product->purchase_priority_level,
                        'product_code' => $product->sku,
                        'product_id' => $add_product_id_extra_order[$j + $c],
                        'dish_name' => $dishName,
                        'product_name' => $add_product_name_extra_order[$j + $c],
                        'product_num' => $product->order_num,
                        'product_short_name' => $product->short_name,
                        'product_unit' => $add_product_unit_extra_order[$j + $c] ?? '',
                        'product_type' => $add_product_type_extra_order[$j + $c] ?? '',
                        'supplier_id' => $supplier_id,
                        'supplier_code' => $supplier->supplier_code ?? '',
                        'supplier_name' => $supplier->name ?? '',
                        'bom' => 0,
                        'bom_origin' => 0,
                        'qty' => 1,
                        'amount_of_product_in_order' => $total_cost_item,
                        'total_bom' => $add_total_bom_extra_order[$j + $c],
                        'real_total_bom' => $add_total_bom_extra_order[$j + $c],
                        'import_price' => $add_import_price_extra_order[$j + $c],
                        'comment' => $add_comment_extra_order[$j + $c] ?? '',
                        'created_at' => now()->addSeconds($j * 2),
                        'customer_id' => $customer_id,
                        'type' => 1
                    );
                }
                $c = $c + $add_number_product_extra_order[$i];

                $getDishName = $this->orderDishName;
                $dishName = $getDishName[$add_dish_id_extra_order[$i]] ?? 'Món ăn đã bị xóa';
                $dishList[] = $dishName;
            }
        }
        if ($extra_items) {
            try {
                (new ShopDavicookOrderDetail)->addNewDetail($extra_items);
                // Add history
                $dataHistory = [
                    'order_id' => $order_id,
                    'content' => "Thêm món ăn: " . implode("<br>", $dishList),
                    'admin_id' => Admin::user()->id
                ];
                (new AdminDavicookOrder)->addOrderHistory($dataHistory);

                // Update total order
                AdminDavicookOrderController::updateTotalOrder($order_id);

                // Update purchase_priority_level order if product has purchase_priority_level = 1
                AdminDavicookOrder::UpdatePurchasePriorityLevel($order_id);

                // Update order edited status
                $order = AdminDavicookOrder::findOrFail($order_id);
                $order->update(['edited' => 1]);

                $notification = new AdminNotification();
                $notification->title = "Sửa đơn hàng";
                $notification->content = 'Admin thêm chi tiết đơn hàng đơn hàng #' . $order->id_name;
                $notification->id_order = $order->id;
                $notification->order_code = $order->id_name;
                $notification->customer_code = $order->customer_code;
                $notification->customer_name = $order->customer_name;
                $notification->desc = $order->customer_name . " thêm chi tiết đơn hàng.";
                $notification->order_type = 2;
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

    /**
     * Xóa hết món ăn, nguyên liệu khi thay đổi khách hàng (chi tiết đơn hàng davicook)
     */
    public function postDeleteAllItem() 
    {
        try {
            $oId = request('oId') ?? "";
            $itemDetail = ShopDavicookOrderDetail::where('order_id', $oId)->get();

            if ($itemDetail) {
                //Remove item from shop order detail
                ShopDavicookOrderDetail::where('order_id', $oId)->delete();
            }

            // Update total order
            AdminDavicookOrderController::updateTotalOrder($oId);

            // Update purchase_priority_level order if product has purchase_priority_level = 1
            AdminDavicookOrder::UpdatePurchasePriorityLevel($oId);

            // Update order status when product import_price = 0
//            AdminDavicookOrder::UpdateStatusOrder($oId);

            return response()->json(['error' => 0]);
        } catch (\Throwable $e) {
            return response()->json(['error' => 1, 'msg' => 'Error: ' . $e->getMessage()]);
        }
    }

    /**
     * Xóa món ăn chi tiết đơn hàng davicook
     */
    public function postDeleteItem()
    {
        try {
            $data = request()->all();
            $dId = $data['dId'] ?? "";
            $oId = $data['oId'] ?? "";
            $detail_order_id = $data['detail_order_id'] ?? "";
            $order_detail_type = $data['order_detail_type'] ?? "";
            $getDishName = $this->orderDishName;
            $dishName = $getDishName[$dId] ?? 'Món ăn đã bị xóa';

            $itemDetail = ShopDavicookOrderDetail::where('dish_id', $dId)->where('order_id', $oId)->get();
            if (!$itemDetail) {
                return response()->json(['error' => 1, 'msg' => sc_language_render('admin.data_not_found_detail', ['msg' => 'detail#' . $dId]), 'detail' => '']);
            }

            if ($detail_order_id) {
                $itemDetail = ShopDavicookOrderDetail::where('id', $detail_order_id)
                            ->where('order_id', $oId);
            } else if ($order_detail_type == 0) {
                $itemDetail = ShopDavicookOrderDetail::where('dish_id', $dId)
                            ->where('order_id', $oId)
                            ->where('type', 0);
            } else {
                $itemDetail = ShopDavicookOrderDetail::where('dish_id', $dId)
                            ->where('order_id', $oId)
                            ->where('type', 1);
            }
            //Remove item from shop order detail
            $order = AdminDavicookOrder::findOrFail($oId);
            $arrItemDetail = $itemDetail->get()->toArray();
            foreach ($arrItemDetail as $item) {
                $dataExtra = [
                    'order_id' => $oId,
                    'delivery_date_origin' => $order->status == 2 ? $order->export_date : $order->delivery_date,
                    'order_name' => $order->id_name,
                    'product_id' => $item['product_id'],
                    'product_name' => $item['product_name'],
                    'product_short_name' => $item['product_short_name'],
                    'product_num' => $item['product_num'],
                    'product_unit' => $item['product_unit'],
                    'qty' => $item['total_bom'],
                    'qty_change' => $item['total_bom'],
                    'content' => "Xóa nguyên liệu",
                    'type_content' => 10,
                    'customer_code' => $order->customer_code,
                    'customer_name' => $order->customer_name,
                    'create_order' => date('d-m-Y H:i:s', strtotime($order->created_at)),
                    'order_detail_id' => $item['id'],
                    'category_id' => $item['category_id'],
                    'product_code' => $item['product_code'],
                    'kind' => $item['product_type'],
                    'note' => $item['comment'] ?? '',
                    'status' => $order->status,
                    'type_order' => 2,
                ];
                (new AdminShopOrderChangeExtra())->create($dataExtra);
            }
            $itemDetail->delete();
            
            //Update total order
            AdminDavicookOrderController::updateTotalOrder($oId);

            // Update purchase_priority_level order if product has purchase_priority_level = 1
            AdminDavicookOrder::UpdatePurchasePriorityLevel($oId);
            
            // Add history
            $dataHistory = [
                'order_id' => $oId,
                'content' => 'Xóa món ăn ' . $dishName,
                'admin_id' => Admin::user()->id
            ];
            (new AdminDavicookOrder)->addOrderHistory($dataHistory);

            // Update order edited status
            $order = AdminDavicookOrder::findOrFail($oId);
            $order->update(['edited' => 1]);

            $notification = new AdminNotification();
            $notification->title = "Sửa đơn hàng";
            $notification->content = 'Admin xóa chi tiết đơn hàng #' . $order->id_name;
            $notification->id_order = $order->id;
            $notification->order_code = $order->id_name;
            $notification->customer_code = $order->customer_code;
            $notification->customer_name = $order->customer_name;
            $notification->desc = $order->customer_name. " sửa đơn hàng.";
            $notification->order_type = 1;
            $notification->edit_type = 3;
            $notification->display = 0;
            $notification->save();

            return response()->json(['error' => 0, 'msg' => 'Xóa món ăn thành công']);
        } catch (\Throwable $e) {
            return response()->json(['error' => 1, 'msg' => 'Error: ' . $e->getMessage()]);
        }
    }

    /**
     * Xóa nhu yếu phâm chi tiết đơn hàng davicook
     */
    public function postDeleteItemEssentialOrder()
    {
        try {
            $data = request()->all();
            $order_id = $data['oId'] ?? "";
            $detail_order_id = $data['detail_order_id'] ?? "";
            $product_name = ShopDavicookOrderDetail::where('id', $detail_order_id)->first()->product_name;
            
            $itemDetail = ShopDavicookOrderDetail::where('order_id', $order_id)->get();
            if (!$itemDetail) {
                return response()->json(['error' => 1, 'msg' => sc_language_render('admin.data_not_found_detail')]);
            }

            if ($data['detail_order_id']) {
                $itemDetail = ShopDavicookOrderDetail::where('id', $data['detail_order_id'])
                            ->where('order_id', $order_id);
            }
            $order = AdminDavicookOrder::findOrFail($order_id);
            $arrItemDetail = $itemDetail->get()->toArray();
            foreach ($arrItemDetail as $item) {
                $dataExtra = [
                    'order_id' => $order_id,
                    'delivery_date_origin' => $order->status == 2 ? $order->export_date : $order->delivery_date,
                    'order_name' => $order->id_name,
                    'product_id' => $item['product_id'],
                    'product_name' => $item['product_name'],
                    'product_short_name' => $item['product_short_name'],
                    'product_num' => $item['product_num'],
                    'product_unit' => $item['product_unit'],
                    'qty' => $item['total_bom'],
                    'qty_change' => $item['total_bom'],
                    'content' => "Xóa sản phẩm",
                    'type_content' => 11,
                    'customer_code' => $order->customer_code,
                    'customer_name' => $order->customer_name,
                    'create_order' => date('d-m-Y H:i:s', strtotime($order->created_at)),
                    'order_detail_id' => $item['id'],
                    'category_id' => $item['category_id'],
                    'product_code' => $item['product_code'],
                    'kind' => $item['product_type'],
                    'note' => $item['comment'] ?? '',
                    'status' => $order->status,
                    'type_order' => 2,
                ];
                (new AdminShopOrderChangeExtra())->create($dataExtra);
            }

            //Remove item from shop order detail
            $itemDetail->delete();
            
            //Update total order
            AdminDavicookOrderController::updateTotalOrder($order_id);

            // Update purchase_priority_level order if product has purchase_priority_level = 1
            AdminDavicookOrder::UpdatePurchasePriorityLevel($order_id);

            // Update order status when product import_price = 0
//            AdminDavicookOrder::UpdateStatusOrder($order_id);
            
            // Add history
            $dataHistory = [
                'order_id' => $order_id,
                'content' => 'Xóa sản phẩm ' . $product_name ?? 'Sản phẩm đã bị xóa',
                'admin_id' => Admin::user()->id
            ];
            (new AdminDavicookOrder)->addOrderHistory($dataHistory);

            // Update order edited status
            $order = AdminDavicookOrder::findOrFail($order_id);
            $order->update(['edited' => 1]);

            $notification = new AdminNotification();
            $notification->title = "Sửa đơn hàng";
            $notification->content = 'Admin xóa chi tiết đơn hàng #' . $order->id_name;
            $notification->id_order = $order->id;
            $notification->order_code = $order->id_name;
            $notification->customer_code = $order->customer_code;
            $notification->customer_name = $order->customer_name;
            $notification->desc = $order->customer_name. " sửa đơn hàng.";
            $notification->order_type = 1;
            $notification->edit_type = 3;
            $notification->display = 0;
            $notification->save();

            return response()->json(['error' => 0, 'msg' => 'Xóa sản phẩm thành công']);
        } catch (\Throwable $e) {
            return response()->json(['error' => 1, 'msg' => 'Error: ' . $e->getMessage()]);
        }
    }

    /**
     *  Cập nhật giá nguyên liệu chi tiết đơn hàng davicook
     */
    public function updateNewPriceProductOrder()
    {
        $id = request('id');
        $dataOrder = (new AdminDavicookOrder())->with('details')->findOrFail($id);
        $dataOrderDetail = $dataOrder->details;
        $dataUpdate = '';

        // Check import price board
        $get_suppliers = ShopDavicookProductSupplier::where('customer_id',$dataOrder->customer_id)->get();
        $supplier = [];
        foreach($get_suppliers as $sup) {
            $supplier[] = $sup->supplier_id;
        }
        // $price_board_id = ShopImportPriceboard::whereIn('supplier_id',array_unique($supplier))->where('start_date','<=',$dataOrder->delivery_date)->where('end_date','>=',$dataOrder->delivery_date)->get();
        // if(count($price_board_id)==0) {
        //     return redirect()->back()->with(['error' => 'Bảng giá của nguyên liệu chưa được cập nhật!']);
        // }

        DB::beginTransaction();
        try {
            $import_prices = [];
            foreach ($dataOrderDetail as $itemDetail) {
                $import_price = (new AdminDavicookCustomer())->getImportPrice($dataOrder->customer_id,$itemDetail->product_id,$dataOrder->delivery_date) ?? 0;
                $import_prices[] = $import_price;
                $dataUpdate = [
                    'import_price' => $import_price,
                    'amount_of_product_in_order' => $import_price * $itemDetail->total_bom
                ];
                if ($dataUpdate) {
                    $result = ShopDavicookOrderDetail::find($itemDetail->id)->update($dataUpdate);
                    if (!$result) {
                        throw new Exception('Đã có lỗi xảy ra trong quá trình cập nhật!');
                    }
                } 
            }

            // Update status order with draft order 
            // $order =ShopDavicookOrder::find($dataOrder->id);
            // if (!in_array(0, $import_prices) && ($order->status == 0)) {
            //     $update_status = ShopDavicookOrder::find($dataOrder->id)->update(['status' => 1]);
            // } else {
            //     $update_status = ShopDavicookOrder::find($dataOrder->id)->update(['status' => 0]);
            // }
            // if (!$update_status) {
            //     throw new Exception('Đã có lỗi xảy ra trong quá trình cập nhật!');
            // }

            //Update total order
            AdminDavicookOrderController::updateTotalOrder($id);

        } catch (\Throwable $e) {
            DB::rollBack();
            $messages = $e->getMessage();
            return redirect()->back()->with(['error' => $messages]);
        }
        DB::commit();
        return redirect()->back()->with(['success' => sc_language_render('admin.menu.edit_success')]);
    }

    /**
     * Lấy thông tin nguyên liệu của món ăn tạo mới đơn hàng davicook
     */
    public function getProductDishCreateOrder()
    {
        $id = request('dId');
        $cId = request('cId');
        $number_of_servings = request('number_of_servings');
        $delivery_time = request('delivery_time');
        $out_of_stock = 0;
        $msg_out_of_stock = '';

        // Dish code
        $getDishCode = ($this->orderDishCode);
        $arrayReturn['dish_code'] =  $getDishCode[$id] ?? '';

        $menu = ShopDavicookMenu::where('dish_id', $id)->where('customer_id', $cId)->first();
        if ($menu) {
            $idMenu = $menu->id;
            $get_product_by_dish = ShopDavicookMenuDetail::where('menu_id', $idMenu)->get();
            $key = rand();

            $arrayReturn['products'] = '<td class="pro_by_dish">
                                                    <input type="hidden" name="add_order_id"  type="text" class="form-control add_order_id"  value="">
                                                    <input type="hidden" name="add_customer_id"  type="text" class="form-control add_customer_id"  value="">';
            foreach ($get_product_by_dish as $k => $v) {
                if ($v->product->status == 0) {
                    $out_of_stock = 1;
                    $msg_out_of_stock = $v->product->name .' - Hết hàng!';
                }
                $productName = $v->product->name ?? 'Nguyên liệu đã bị xóa';
                $productType = $v->product->kind ?? '';
                $productUnit = $v->product->unit->name ?? '';
                $arrayReturn['products'] .= '   <div class="edit_pro_id_' . $key + $k . '">
                                                    <input readonly type="text" name="add_product_name[]" class="form-control"  value="' . $productName . '">
                                                    <input type="hidden" name="add_product_unit[]" class="form-control"  value="' . $productUnit . '">
                                                    <input type="hidden" name="add_product_type[]" class="form-control"  value="' . $productType . '">
                                                    <input type="hidden" name="add_product_id[]"  type="text" class="form-control"  value="' . $v->product_id . '">
                                                </div>';
            }
            $arrayReturn['products'] .= '</td>
                                         <td class="pro_by_dish">';
            foreach ($get_product_by_dish as $k => $v) {
                $arrayReturn['products'] .= '   <div class="edit_pro_id_' . $key + $k . '">
                                                    <input type="number" '.($v->product->status == 1 ? '' : "readonly").' name="add_bom[]" onKeyup="update_total(' . $key + $k . ',' . ($v->product->unit->type ?? 0) . ')" min="0" step="0.0000001" onInput="limitDecimalPlaces(event,7)" class="add_bom_' . $key + $k . ' form-control" value="' . ($v->product->status == 1 ? $v->qty : 0) . '"></input>
                                                    <input name="bom_origin[]" type="hidden" value="' . ($v->product->status == 1 ? $v->qty : 0) . '"></input>
                                                </div>';
            }
            $arrayReturn['products'] .= '</td>
                                        <td class="pro_by_dish">';
            foreach ($get_product_by_dish as $k => $v) {
                $arrayReturn['products'] .= '   <div class="edit_pro_id_' . $key + $k . '">
                                                    <input readonly type="number" name="add_qty[]" onChange="update_total(' . $key + $k . ',' . ($v->product->unit->type ?? 0) . ');" step="1" min="0" class="update_numb_of_serving numb_of_serving add_qty_' . $key + $k . ' form-control" value="">
                                                </div>';
            }
            $arrayReturn['products'] .= '</td>
                                        <td class="pro_by_dish">';
            foreach ($get_product_by_dish as $k => $v) {
                    $arrayReturn['products'] .= '<div class="edit_pro_id_' . $key + $k . '">
                                                    <input readonly type="number" name="add_total_bom[]" min=0 step="1" class="form-control add_total_bom_' . $key + $k . '"  value="' . roundTotalBom($number_of_servings * ($v->product->status == 1 ? $v->qty : 0), $v->product->unit->type ?? 0) . '">
                                                </div>';
            }
            $arrayReturn['products'] .= '</td>
                                        <td class="pro_by_dish">';
            foreach ($get_product_by_dish as $k => $v) {
                // Get import price
                $import_price = (new AdminDavicookCustomer())->getImportPrice($cId, $v->product_id, $delivery_time);
                // product have import product = 0 then show noti
                if ($import_price == 0) {
                    $arrayReturn['error'] = 1;
                    $arrayReturn['msg'] = 'Bảng giá nhập của nguyên liệu chưa được cập nhập!';
                }
                $productSku = $v->product->sku ?? '';
                $arrayReturn['products'] .= '   <div class="edit_pro_id_' . $key + $k . '">
                                                    <input readonly name="add_import_price[]" onKeyup="formatCurrency($(this)); update_total(' . $key + $k . ',' . ($v->product->unit->type ?? 0) . ');" min=0 step="0.01" class="import_price add_import_price_' . $key + $k . ' update_change_' . $productSku . ' form-control"  value="' . number_format(round($import_price, 0)) . '">
                                                </div>';
            }
            $arrayReturn['products'] .= '</td>
                                        <td class="pro_by_dish">';
            foreach ($get_product_by_dish as $k => $v) {
                // Get import price
                $import_price = (new AdminDavicookCustomer())->getImportPrice($cId, $v->product_id, $delivery_time);
                $arrayReturn['products'] .= '   <div class="edit_pro_id_' . $key + $k . '">
                                                    <input readonly name="add_total_cost[]" class="form-control amount_of_product_in_order add_total_cost_' . $key + $k . ' sum_total" value="' . number_format(roundTotalBom($number_of_servings * $v->qty, $v->product->unit->type ?? 0) * $import_price) . '">
                                                </div>';
            }
            $arrayReturn['products'] .= '</td>
                                        <td class="pro_by_dish">';
            foreach ($get_product_by_dish as $k => $v) {
                $arrayReturn['products'] .= '   <div class="edit_pro_id_' . $key + $k . '">
                                                    <input name="add_comment[]" type="text" class="form-control"  value="">
                                                </div>';
            }
            $arrayReturn['products'] .= '</td>';
        } else {
            $arrayReturn = ['error' => 1, 'msg' => sc_language_render('admin.data_not_found_detail')];
        }
        $arrayReturn['out_of_stock'] = $out_of_stock;
        $arrayReturn['msg_out_of_stock'] = $msg_out_of_stock;
        return response()->json($arrayReturn);
    }

    /**
     * Lấy món ăn tạo mới đơn hàng davicook
     */
    public function getDishByCutomerCreateOrder() {
        $cId = request('cId');
        $dishs = ShopDavicookMenu::where('customer_id',$cId)->get();
        if (count($dishs)>0) {
            $getDishName = $this->orderDishName;
            $arrayReturn['dish'] = '
                            <tr class="select-dish">
                                <td class="dish_no" style="text-align: center; width: 80px;"></td>
                                <td style="width:80px"><input type="text" disabled id="dish_code" class="add_dish_code form-control"></td>
                                <td id="add_td">
                                <select onChange="selectDish($(this));" class="add_dish_id form-control select2" name="add_dish_id[]" style="width:100% !important;">';
            $arrayReturn['dish'] .='<option selected disabled hidden value="">Chọn món ăn</option>';
                                    foreach ($dishs as $dId => $dish) {
                                            if ($dishStatus = $dish->dish->status ?? 0 == 1) {
                                                $dishName = $getDishName[$dish->dish_id] ?? 'Món ăn đã bị xóa';
                                                $arrayReturn['dish'] .='<option  value="'.$dish->dish_id.'" >'.$dishName.'</option>'; 
                                            } 
                                    }
            $arrayReturn['dish']  .='
                                    </select>
                                </td>
                                <td class="add_rowspan" style="text-align:center"><button id="select_dish_button" type="button" onclick="$(this).parent().parent().remove(); update_sum_total_cost(); updateDishNo();" class="btn btn-danger btn-md btn-flat" data-title="Delete"><i class="fa fa-times" aria-hidden="true"></i></button></td>
                            </tr>
                            ';
            $arrayReturn['dish'] = str_replace("\n", '', $arrayReturn['dish']);
            $arrayReturn['dish'] = str_replace("\t", '', $arrayReturn['dish']);
            $arrayReturn['dish'] = str_replace("\r", '', $arrayReturn['dish']);
            $arrayReturn['dish'] = str_replace("'", '"', $arrayReturn['dish']);
        } else {
            $arrayReturn = ['error' => 1, 'msg' => 'Khách hàng chưa có món ăn nào trong menu!'];
        }
        return response()->json($arrayReturn);
    }

     /**
     * Lấy món ăn tạo mới đơn hàng bổ sung davicook
     */
    public function getDishByCutomerCreateExtraOrder() {
        $customer_id = request('customer_id');
        $dishs = ShopDavicookMenu::where('customer_id',$customer_id)->get();
        if (count($dishs)>0) {
            $getDishName = $this->orderDishName;
            $arrayReturn['dish'] = '
                            <tr class="select-dish">
                                <td class="dish_no_extra_order" style="text-align: center; width: 80px;"></td>
                                <td style="width:80px">
                                    <input type="text" disabled id="dish_code" class="add_dish_code_extra_order form-control">
                                    <input type="hidden" name="add_number_product_extra_order[]" class="add_number_product_extra_order" value="0">
                                </td>
                                <td id="add_td_extra_order">
                                    <select onChange="selectDishExtraOrder($(this));" class="add_dish_id_extra_order form-control select2" name="add_dish_id_extra_order[]" >';
            $arrayReturn['dish'] .='    <option selected disabled hidden value="">Chọn món ăn</option>';
                                        foreach ($dishs as  $dish) {
                                            if ($dishStatus = $dish->dish->status ?? 0 == 1) {
                                                $dishName = $getDishName[$dish->dish_id] ?? 'Món ăn đã bị xóa';
                                                $arrayReturn['dish'] .='<option  value="'.$dish->dish_id.'" >'.$dishName.'</option>'; 
                                            } 
                                        }
            $arrayReturn['dish']  .='
                                    </select>
                                </td>
                                <td class="add_rowspan" style="text-align:center"><button id="select_dish_button" type="button" onclick="$(this).parent().parent().remove(); update_sum_total_cost(); updateDishNoExtraOrder();" class="btn btn-danger btn-md btn-flat" data-title="Delete"><i class="fa fa-times" aria-hidden="true"></i></button></td>
                            </tr>
                            ';
            $arrayReturn['dish'] = str_replace("\n", '', $arrayReturn['dish']);
            $arrayReturn['dish'] = str_replace("\t", '', $arrayReturn['dish']);
            $arrayReturn['dish'] = str_replace("\r", '', $arrayReturn['dish']);
            $arrayReturn['dish'] = str_replace("'", '"', $arrayReturn['dish']);
        } else {
            $arrayReturn = ['error' => 1, 'msg' => 'Khách hàng chưa có món ăn nào trong menu!'];
        }
        return response()->json($arrayReturn);
    }

     /**
     * Thêm món ăn tạo mới đơn hàng bổ sung davicook
     */
    public function getProductDishExtraOrder() 
    {
        $customer_id = request('customer_id');
        $dish_id = request('dish_id');
        $dishs = ShopDavicookMenu::where('customer_id',$customer_id)->get();
        $products = ShopProduct::all();
        $getDishCode = ($this->orderDishCode);
        $arrayReturn['dish_code'] =  $getDishCode[$dish_id] ?? '';
        $key = rand();

        if ($dishs) {
            $arrayReturn['dish'] =  '   <td class="td-select-dish">
                                            <div class="product_key_'.$key.' remove_item_'.$key.' mb-2">
                                                <select onChange="selectProductExtraOrder('.$key.', $(this))" type="text" name="add_product_id_extra_order[]" class="form-control add_product_id_extra_order_'.$key.' remove_item_'.$key.' select2" style="width:100% !important;">';
            $arrayReturn['dish'] .= '               <option selected readonly hidden value="">Chọn nguyên liệu</option>';
                                                    if(isset($products)) {
                                                        foreach ($products as $pId => $product) {
                                                            if ($product->status == 1) {
                                                                $arrayReturn['dish']  .='<option value="'.$product->id.'">'.$product->name.'</option>';
                                                            } else {
                                                                $arrayReturn['dish']  .='<option value="'.$product->id.'">'.$product->name.' - Hết hàng!</option>';
                                                            }
                                                        }
                                                    }
            $arrayReturn['dish'] .= '           </select>
                                                <input type="hidden" name="add_product_unit_extra_order[]" class="add_product_unit_extra_order_'.$key.'"value="">
                                                <input type="hidden" name="add_product_type_extra_order[]" class="add_product_type_extra_order_'.$key.'"value="">
                                                <input type="hidden" name="add_product_name_extra_order[]" class="add_product_name_extra_order_'.$key.'" value="">
                                            </div>
                                            <div class="add-product-name">
                                                <button onClick="addProduct($(this))" type="button" class="btn btn-flat btn-success mb-2"
                                                    title="Thêm mới nguyên liệu"><i
                                                    class="fa fa-plus"></i> Thêm nguyên liệu
                                                </button>
                                            </div>                                                
                                        </td>
                                        <td class="td-select-dish">
                                            <div class="product_key_'.$key.' remove_item_'.$key.' mb-2">
                                                <input type="number" onKeyup="update_total_extra_order('.$key.');" name="add_total_bom_extra_order[]" min=0 step="1" class="form-control add_total_bom_extra_order_'.$key.'"  value="">
                                            </div>
                                            <div class="add-product-total_bom"></div>
                                        </td>
                                        <td class="td-select-dish">
                                            <div class="product_key_'.$key.' remove_item_'.$key.' mb-2">
                                                <input readonly type="text" class="form-control add_product_unit_extra_order_'.$key.'"  value="">
                                            </div>
                                            <div class="add-product-unit"></div>
                                        </td>
                                        <td class="td-select-dish">
                                            <div class="product_key_'.$key.' remove_item_'.$key.' mb-2">
                                                <input readonly name="add_import_price_extra_order[]" onKeyup="formatCurrency($(this)); update_total_extra_order('.$key.');" min=0 step="0.01" class="import_price add_import_price_extra_order_'.$key.' form-control"  value="">
                                            </div>
                                            <div class="add-product-import_price"></div>
                                        </td>
                                        <td class="td-select-dish">
                                            <div class="product_key_'.$key.' remove_item_'.$key.' mb-2">
                                                <input readonly name="add_total_cost_extra_order[]" class="form-control amount_of_product_in_order add_total_cost_extra_order_'.$key.' sum_total" value="">
                                            </div>
                                            <div class="add-product-total_cost"></div>
                                        </td>
                                        <td class="td-select-dish">
                                            <div class="product_key_'.$key.' remove_item_'.$key.' mb-2">
                                                <input name="add_comment_extra_order[]" type="text" class="form-control"  value="">
                                            </div>
                                            <div class="add-product-comment"></div>
                                        </td>
                                        <td class="td-select-dish">
                                            <div class="product_key_'.$key.' remove_item_'.$key.' delete-item mb-2">
                                                <button class="btn btn-secondary btn-md btn-flat" onclick="deleteProduct('.$key.',$(this)); update_sum_total_cost();" type="button" data-title="Delete"><i class="fa fa-times" aria-hidden="true"></i></button>
                                            </div>              
                                            <div class="add-product-delete"></div>
                                        </td>';
            $arrayReturn['dish'] = str_replace("\n", '', $arrayReturn['dish']);
            $arrayReturn['dish'] = str_replace("\t", '', $arrayReturn['dish']);
            $arrayReturn['dish'] = str_replace("\r", '', $arrayReturn['dish']);
            $arrayReturn['dish'] = str_replace("'", '"', $arrayReturn['dish']);
        } else {
            $arrayReturn = ['error' => 1, 'msg' => 'Khách hàng chưa có món ăn nào trong menu!'];
        }
        return response()->json($arrayReturn);
    }
    
    /**
     * Lấy nguyên liệu tạo mới đơn hàng bổ sung davicook
     **/
    public function addProductExtraOrder() 
    {
        $customer_id = request('customer_id');
        $number_of_extra_servings = request('number_of_extra_servings');
        $delivery_time = request('delivery_time');
        $products = ShopProduct::all();
        $key = rand();
        if ($products) {
            $arrayReturn['product'] = '
                            <div class="product_key_'.$key.' remove_item_'.$key.' mb-2">
                                <input type="hidden" name="add_order_id_extra_order" type="text" class="form-control add_order_id" value="">
                                <input type="hidden" name="add_customer_id_extra_order"  type="text" class="form-control add_customer_id"  value="">
                                    <select type="text" name="add_product_id_extra_order[]" onChange="selectProductExtraOrder('.$key.', $(this))" class="form-control add_product_id_extra_order_'.$key.' select2" style="width:100% !important;">';
            $arrayReturn['product'] .=' <option selected readonly hidden value="">Chọn nguyên liệu</option>';
                                        if(isset($products)) {
                                            foreach ($products as $pId => $product) {
                                                if ($product->status == 1) {
                                                    $arrayReturn['product']  .='<option value="'.$product->id.'">'.$product->name.'</option>';
                                                } else {
                                                    $arrayReturn['product']  .='<option value="'.$product->id.'">'.$product->name.' - Hết hàng!</option>';
                                                }
                                            }
                                        }
            $arrayReturn['product'] .='        
                                    </select>
                                    <input type="hidden" name="add_product_unit_extra_order[]" class="add_product_unit_extra_order_'.$key.'"value="">
                                    <input type="hidden" name="add_product_type_extra_order[]" class="add_product_type_extra_order_'.$key.'"value="">
                                    <input type="hidden" name="add_product_name_extra_order[]" class="add_product_name_extra_order_'.$key.'" value="">
                            </div>';
            $arrayReturn['total_bom'] = 
                            '<div class="product_key_'.$key.' remove_item_'.$key.' mb-2">
                                <input type="number" onKeyup="update_total_extra_order('.$key.');" name="add_total_bom_extra_order[]" min=0 step="1" class="form-control add_total_bom_extra_order_'.$key.'"  value="">
                            </div>';
            $arrayReturn['product_unit'] = 
                            '<div class="product_key_'.$key.' remove_item_'.$key.' mb-2">
                                <input readonly type="text" class="form-control add_product_unit_extra_order_'.$key.'"value="">
                            </div>';
            $arrayReturn['import_price'] = 
                            '<div class="product_key_'.$key.' remove_item_'.$key.' mb-2">
                                <input readonly name="add_import_price_extra_order[]" onKeyup="formatCurrency($(this)); update_total_extra_order('.$key.');" min=0 step="0.01" class="import_price add_import_price_extra_order_'.$key.' form-control"  value="">
                            </div>';
            $arrayReturn['total_cost'] = 
                            '<div class="product_key_'.$key.' remove_item_'.$key.' mb-2">
                                <input readonly name="add_total_cost_extra_order[]" class="form-control amount_of_product_in_order add_total_cost_extra_order_'.$key.' sum_total" value="">
                            </div>';
            $arrayReturn['comment'] = 
                            '<div class="product_key_'.$key.' remove_item_'.$key.' mb-2">
                                <input name="add_comment_extra_order[]" type="text" class="form-control"  value="">
                            </div>';
            $arrayReturn['delete'] = 
                            '<div class="product_key_'.$key.' remove_item_'.$key.' mb-2 delete-item">
                                <button class="btn btn-secondary btn-md btn-flat" onclick="deleteProduct('.$key.', $(this)); update_sum_total_cost();" type="button" data-title="Delete"><i class="fa fa-times" aria-hidden="true"></i></button>
                            </div>';
        } else {
            $arrayReturn = ['error' => 1, 'msg' => 'Không có sản phẩm nào hợp lệ!'];
        }
        return response()->json($arrayReturn);
    }

    /**
     * Lấy thông tin sản phẩm tạo đơn bổ sung davicook
     */
    public function getProductInfoExtraOrder() {
        $out_of_stock = 0;
        $msg_out_of_stock = '';
        $product_id = request('product_id');
        $customer_id = request('customer_id');
        $delivery_time = request('delivery_time');
        $product = ShopProduct::where("id", $product_id)->first();
        if ($product->status == 0) {
            $out_of_stock = 1;
            $msg_out_of_stock = $product->name .' - Hết hàng!';
        }
        $arrayReturn['product_name'] = $product->name ?? '';
        $arrayReturn['product_sku'] = $product->sku ?? '';
        $arrayReturn['product_type'] = $product->kind ?? '';
        $arrayReturn['product_unit'] =  $product->unit->name ?? '';
        $arrayReturn['import_price'] =  (new AdminDavicookCustomer())->getImportPrice($customer_id,$product_id,$delivery_time);
        $arrayReturn['out_of_stock'] = $out_of_stock;
        $arrayReturn['msg_out_of_stock'] = $msg_out_of_stock;

        return response()->json($arrayReturn);
    }

    public static function getImPortPrice($customer_id,$product_id,$delivery_time) {
        $import_price = 0;

        $supplier_id = ShopDavicookProductSupplier::where('customer_id',$customer_id)->where('product_id',$product_id)->first();
       
        if($supplier_id){
            $price_board_id = ShopImportPriceboard::where('supplier_id',$supplier_id->supplier_id)->where('start_date','<=',$delivery_time)->where('end_date','>=',$delivery_time)->first();
            if($price_board_id) {
                $import_prices = ShopImportPriceboardDetail::where('priceboard_id',$price_board_id->id)->where('product_id',$product_id)->first();
                if($import_prices) {
                    $import_price = $import_prices->price;
                }
            }
        }
        return $import_price;
    }

    /**
     * Update import price when change delivery time (Create order davicook).
     **/
    public function updateImportPriceByDeliveryTime()
    {
        $customer_id = request('customer_id');
        $delivery_time = request('delivery_time');
        $add_product_ids = request('add_product_id');

        if ($customer_id === null) {
            return;
        }
        $arrayReturn['error'] = 0;
        $products = ShopProduct::whereIn('id', array_unique($add_product_ids))->get();
        foreach ($products as $v) {
            $productSKU = $v->sku ?? '';
            $arrayReturn[$productSKU] = (new AdminDavicookCustomer())->getImportPrice($customer_id, $v->id, $delivery_time);
            if ($arrayReturn[$productSKU] == 0) {
                $arrayReturn['error'] = 1;
                $arrayReturn['msg'] = 'Bảng giá của nguyên liệu chưa được cập nhật!';
            }
        }
        if ($arrayReturn['error'] == 0) {
            $arrayReturn['msg'] = 'Giá nguyên liệu đã được cập nhật lại!';
        }
        return response()->json($arrayReturn);
    }

    // Update total order
    public static function updateTotalOrder($order_id) {
        $total_cost = ShopDavicookOrderDetail::where('order_id',$order_id)->sum('amount_of_product_in_order');
        $order = AdminDavicookOrder::getOrderDavicookAdmin($order_id);
        if (!$order) {
            return response()->json(['error' => 1, 'msg' => sc_language_render('admin.data_not_found_detail', ['msg' => 'order#' . $order_id]), 'detail' => '']);
        } else {
            $order->total = $total_cost - $order->returnHistory->sum('return_total') ?? 0;
            $order->subtotal = $total_cost - $order->returnHistory->sum('return_total') ?? 0;
            $order->save();
            $order = $order->fresh();
        }
    }

    // Load history when update detail
    public static function loadHistory($id) {
        $order_history = ShopDavicookOrderHistory::where('order_id',$id)->orderBy('add_date', 'Desc')->get();
        $arrayReturn = '';
        if ($order_history) {
            foreach($order_history as $v) {
                $arrayReturn .= '<tr>
                                    <td>'.$v->add_date.'</td>
                                    <td>'.$v->getEditor().'</td>
                                    <td>'.$v->content.'</td>
                                 </tr>';
            }
            $arrayReturn = str_replace("\n", '', $arrayReturn);
            $arrayReturn = str_replace("\t", '', $arrayReturn);
            $arrayReturn = str_replace("\r", '', $arrayReturn);
            $arrayReturn = str_replace("'", '"', $arrayReturn);
        }
        return $arrayReturn;       
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
        DB::beginTransaction();
        try {
            foreach ($idList as $key => $id) {
                $dataOrder = (new AdminDavicookOrder())->with('details')->findOrFail($id);
                foreach ($dataOrder->details as $item) {
                    $supplier = ShopDavicookProductSupplier::where('product_id',$item->product_id)->where('customer_id',$dataOrder->customer_id)
                        ->whereDate('updated_at','<=', $dataOrder->delivery_date)->first();
                    if ($supplier) {
                        $supplierInfo = ShopSupplier::find($supplier->supplier_id);
                        $dataUpdate = [
                            'supplier_id' => $supplier->supplier_id,
                            'supplier_code' => $supplierInfo->supplier_code,
                            'supplier_name' => $supplierInfo->name,
                        ];
                        ShopDavicookOrderDetail::findOrFail($item->id)->update($dataUpdate);
                    }
                }
                //Add history
                $dataHistory = [
                    'order_id' => $id,
                    'title' => 'Cập nhập NCC',
                    'content' => 'Cập nhập NCC',
                    'admin_id' => Admin::user()->id,
//                    'user_name' => Admin::user()->name,
//                    'order_code' => $dataOrder->id_name,
//                    'is_admin' => 1,
                ];
                (new AdminDavicookOrder)->addOrderHistory($dataHistory);
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
    * Thêm sản phẩm đơn nhu yếu phẩm davicook
    **/
    public function getProductEssentialOrder() 
    {
        $products = ShopProduct::all();
        $product_no = request('product_no') ? request('product_no')+1 : '';
        $key = rand();
        if ($products) {
            $arrayReturn['product'] = '
                                <tr class="select-product">
                                    <td class="product_no check-product-no" style="text-align: center; width: 80px;">'.$product_no.'</td>
                                    <td style="width: 80px">
                                        <input type="text" readonly class="add_product_sku_'.$key.' form-control">
                                    </td>
                                    <td id="add_td">
                                        <select onChange="selectProduct('.$key.',$(this));" class="add_product_id form-control select2" name="add_product_id[]" style="width: 100% !important;">';
                                        if ($products) {
                                            $arrayReturn['product'] .='<option selected disabled hidden value="">Chọn sản phẩm</option>';
                                            foreach ($products as $product) {
                                                if ($product->status == 1) {

                                                    $arrayReturn['product'] .='<option value="'.$product->id.'" >'.$product->name.' ('.$product->sku.')</option>';
                                                } else {
                                                    $arrayReturn['product'] .='<option value="0" >'.$product->name.' (Hết hàng)</option>';
                                                }
                                            }    
                                        } else {
                                            $arrayReturn['product'] .='<option selected disabled hidden value="">Chưa có món ăn</option>';
                                        }
            $arrayReturn['product'] .='
                                        </select>
                                        <input type="hidden" name="add_product_unit[]" class="add_product_unit_'.$key.'"value="">
                                        <input type="hidden" name="add_product_type[]" class="add_product_type_'.$key.'"value="">
                                        <input type="hidden" name="add_product_name[]" class="add_product_name_'.$key.'" value="">
                                    </td>
                                    <td>
                                        <input name="add_product_qty[]" onKeyup="update_total('.$key.');" type="number" min="0" step="1" class="add_product_qty_'.$key.' form-control" value="">
                                    </td>
                                    <td>
                                        <input readonly type="text" class="add_product_unit_'.$key.' form-control" value="">
                                    </td>
                                    <td>
                                        <input readonly name="add_import_price[]" onKeyup="formatCurrency($(this)); update_total('.$key.');" min=0 class="import_price add_import_price_'.$key.' form-control"  value="">
                                    </td>
                                    <td>
                                        <input readonly name="add_total[]" class="form-control amount_of_product_in_order add_total_'.$key.' sum_total" value="">
                                    </td>
                                    <td>
                                        <input name="add_product_comment[]" type="text" class="form-control"  value="">
                                    </td>
                                    <td class="add_rowspan" style="text-align:center"><button type="button" onclick="$(this).parent().parent().remove(); checkRemoveDOM(); updateProductNo();" class="btn btn-danger btn-md btn-flat" data-title="Delete"><i class="fa fa-times" aria-hidden="true"></i></button></td>
                                </tr>
                                ';
            $arrayReturn['product'] = str_replace("\n", '', $arrayReturn['product']);
            $arrayReturn['product'] = str_replace("\t", '', $arrayReturn['product']);
            $arrayReturn['product'] = str_replace("\r", '', $arrayReturn['product']);
            $arrayReturn['product'] = str_replace("'", '"', $arrayReturn['product']);
        } else {
            $arrayReturn = ['error' => 1, 'msg' => 'Khách hàng chưa có món ăn nào trong menu!'];
        }
        return response()->json($arrayReturn);
    }
    
}