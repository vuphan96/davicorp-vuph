<?php

namespace App\Admin\Controllers;

use App\Admin\Models\AdminDavicookMenuEstimateCard;
use App\Admin\Models\AdminDavicookMenuCardChildren;
use App\Admin\Models\AdminDavicookMenuCardChilldenDetail;
use App\Admin\Models\AdminDavicookOrder;
use App\Exceptions\ImportException;
use App\Exports\DavicookMenuCard\AdminExportMenuCard;
use App\Front\Models\ShopDavicookOrder;
use App\Front\Models\ShopDavicookOrderDetail;
use App\Front\Models\ShopSupplier;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Maatwebsite\Excel\Facades\Excel;
use SCart\Core\Admin\Controllers\RootAdminController;
use Dompdf\Dompdf;
use App\Front\Models\ShopDavicookCustomer;
use App\Front\Models\ShopDavicookDish;
use App\Front\Models\ShopDavicookMenu;
use App\Front\Models\ShopDavicookMenuDetail;
use App\Front\Models\ShopDavicookProductSupplier;
use App\Front\Models\ShopProduct;
use App\Admin\Models\AdminDavicookCustomer;
use Throwable;
use Validator;
use function Symfony\Component\DomCrawler\all;

class AdminDavicookMenuCardController extends RootAdminController
{
    public $menuEstimateCard;
    public $menuCard;
    public $menuCardDetail;
    public $orderDishCode;
    public $orderDishName;
    public $dataAllDish;
    public $dataAllDishOfCustomer;
    public $dataAllDishOfCustomerDetail;
    public $dataAllProduct;
    public $customer;

    /**
     * AdminDavicookOrderController constructor.
     * @param AdminDavicookMenuEstimateCard $menuEstimateCard
     * @param AdminDavicookMenuCardChildren $menuCard
     * @param AdminDavicookMenuCardChilldenDetail $menuCardDetail
     */
    public function __construct(AdminDavicookMenuEstimateCard $menuEstimateCard, AdminDavicookMenuCardChildren $menuCard, AdminDavicookMenuCardChilldenDetail $menuCardDetail)
    {
        parent::__construct();
        $this->menuEstimateCard = $menuEstimateCard;
        $this->menuCard = $menuCard;
        $this->menuCardDetail = $menuCardDetail;
        $this->orderDishName = ShopDavicookDish::getIdAll();
        $this->orderDishCode = ShopDavicookDish::getDishCode();
        $this->dataAllDish = ShopDavicookDish::get();
        $this->dataAllDishOfCustomer = ShopDavicookMenu::get();
        $this->dataAllDishOfCustomerDetail = ShopDavicookMenuDetail::get();
        $this->dataAllProduct = ShopProduct::with('unit', 'category')->get();
        $this->customer = new AdminDavicookCustomer();
    }

    /**
     * Show list davicook order.
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function index()
    {
        $data = [
            'title' => 'Phiếu ước lượng món ăn',
            'subTitle' => '',
            'icon' => 'fa fa-indent',
            'urlDeleteItem' => sc_route_admin('admin_order.delete'),
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
            'id' => 'Mã phiếu',
            'card_name' => 'Tên phiếu',
            'customer_name' => 'Tên khách hàng',
            'created_at' => 'Ngày tạo phiếu',
            'date' => 'Ngày hiệu lực',
            'type_object' => 'Đối tượng',
            'is_combine' => 'Loại phiếu',
            'status_sync' => 'Trạng thái',
            'total_cost' => 'Tổng tiền cost',
            'action' => sc_language_render('action.title'),
        ];
        $cssTh = [
            'id' => 'min-width: 95px; max-width: 95px',
            'card_name' => 'min-width: 150px',
            'customer_name' => 'width: auto; max-width: 280px; min-width:280px',
            'created_at' => 'max-width: 100px; white-space: normal; min-width: 100px;',
            'date' => 'width: 300px; ',
            'type_object' => 'text-align: center; width: 130px',
            'is_combine' => 'text-align: center; width: 130px',
            'status_sync' => 'text-align: center; width: 180px',
            'total_cost' => 'Tổng tiền code',
            'action' => sc_language_render('action.title'),
        ];
        //Customize collumn size and align
        $cssTd = [
            'id' => '',
            'card_name' => 'width: auto; max-width: 240px',
            'customer_name' => 'width: 160px',
            'delivery_time' => 'width: 120px; ',
            'date' => 'text-align: left;',
            'type_object' => 'text-align: center;',
            'is_combine' => 'text-align: center;',
            'status_sync' => 'text-align: center; width: 155px; padding-bottom: 10px;',
            'total_cost' => 'text-align: center; width: 150px',
            'action' => 'text-align: center;',
            'status' => 'display: none;',
        ];
        $data['cssTd'] = $cssTd;
        $data['cssTh'] = $cssTh;

        //Sort input data
        $arrSort = [
            'created_at__desc' => 'Ngày tạo phiếu giảm dần',
            'created_at__asc' => 'Ngày tạo phiếu tăng dần',
            'total_cost__desc' => 'Tổng tiền giảm dần',
            'total_cost__asc' => 'Tổng tiền tăng dần',
        ];
        //Search
        $dataSearch = [
            'keyword' => sc_clean(request('keyword') ?? ''),
            'start_date' => sc_clean(request('start_date') ?? ''),
            'end_date' => sc_clean(request('end_date') ?? ''),
            'sort_order' => sc_clean(request('sort_order') ?? 'id_desc'),
            'arrSort' => $arrSort,
            'status_sync' => sc_clean(request('status_sync') ?? ''),
            'limit' => sc_clean(request('limit') ?? ''),
            'is_combine' => sc_clean(request('is_combine') ?? ''),
            'type_object' => sc_clean(request('type_object') ?? ''),
        ];

        $dataTmp = $this->menuEstimateCard::getDataDavicookMenuCardList($dataSearch);

        $styleStatus = $this->menuEstimateCard::$typeStatusSync;
        array_walk($styleStatus, function (&$v, $k) {
            $v = '<span style="width: 87px" class="badge badge-' . (AdminDavicookMenuEstimateCard::$mapStyleStatus[$k] ?? 'light') . '">' . $v . '</span>';
        });
        $dataTr = [];
        $id = 0;
        $nameUrlDavicookMenuCard = URL::full();
        session()->put('nameUrlDavicookMenuCard', $nameUrlDavicookMenuCard);
        foreach ($dataTmp as $key => $row) {
            $id++;
            $dataMap = [
                'id' => $row->id_name,
                'card_name' => $row->card_name ?? '',
                'customer_name' => $row->customer_name,
                'created_at' => isset($row->created_at) ? Carbon::make($row->created_at)->format('d/m/Y H:i:s') : '',
                'date' => Carbon::make($row->start_date)->format('d/m/Y') . ' - ' . Carbon::make($row->end_date)->format('d/m/Y'),
                'type_object' => $row->type_object == 1 ? '<span style="padding: 3px;" >Phiếu cô</span>' : '<span style="padding: 3px; font-weight: bold" >Phiếu cháu</span>',
                'is_combine' => $row->is_combine == 1 ? '<span style="color: blue;" >Phiếu gộp</span>' : '<span>Phiếu thường</span>',
                'status_sync' => $styleStatus[$row->status_sync],
                'total_cost' => sc_currency_render(round($row->total_cost) ?? '', 'VND'),
                'status' => $row->status_sync,
            ];
            $flag = $row->is_combine;
            $routeDetail = sc_route_admin('admin.davicook_menu_card.edit_menu_card', ['id' => $row['id'] ? $row['id'] : 'not-found-id', 'type' => $row->type_object, 'is_combine' => $row->is_combine]);
                $dataMap['action'] = '
                    <a data-perm="davicook_menu_card:detail" href="' . $routeDetail . '">
                        <span title="' . sc_language_render('action.edit') . '" type="button" class="btn btn-flat btn-sm btn-primary">
                            <i class="fa fa-edit"></i>
                        </span>
                    </a>
                    <span data-perm="davicook_menu_card:clone" onclick="cloneMenuCard(\'' . $row['id'] . '\');" title="' . sc_language_render('action.clone') . '" type="button" class="btn btn-flat btn-sm btn-secondary '.($flag == 1 ? "d-none" : "").' ">
                        <i class="fa fa-clipboard"></i>
                    </span>

                    <span data-perm="davicook_menu_card:delete" onclick="deleteDavicookMenuCard(\'' . $row['id'] . '\');"  title="' . sc_language_render('action.delete') . '" class="btn btn-flat btn-sm btn-danger">
                        <i class="fas fa-trash-alt"></i>
                    </span>
                ';
            $dataTr[$row->id] = $dataMap;
        }
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
                            <a data-perm="davicook_menu_card:sync" href="#" class="btn btn-flat btn btn-danger" id="btnSync"><i class="fa fa-sync-alt"></i>&nbsp;Tạo đơn hàng</a>
                            <a data-perm="davicook_menu_card:combine" href="#" class="btn btn-flat btn btn-info" onclick="combineMenuCard()"><i class="fa fa-layer-group"></i>&nbsp;Gộp phiếu</a>
                            <a data-perm="davicook_menu_card:export" href="#" class="btn btn-flat btn btn-primary" id="btn_export_excel"><i class="fa fa-layer-group"></i>&nbsp;Xuất Excel</a>
                            <a data-perm="davicook_menu_card:print" href="#" class="btn btn-flat btn btn-info text-white" id="btn_preview_pdf"><i class="fa fa-print"></i>&nbsp;Xuất PDF</a>
                            <div data-perm="davicook_menu_card:create" class="dropdown">
                                <button class="dropbtn btn btn btn-success btn-flat"><i class="fa fa-plus" title="'.sc_language_render('action.add').'"></i></button>
                                <div id="create-order-dropdown" class="dropdown-content">
                                    <div class="container">
                                        <div class="panel-group" role="tablist" aria-multiselectable="true">
                                            <div class="panel panel-default">
                                                <div class="panel-heading" role="tab">
                                                    <h4 class="panel-title">
                                                        <a href="'.sc_route_admin('admin.davicook_menu_card.create_for_teacher').'" class="btn btn-flat btn-create-order" style="margin-top: 10px;">
                                                            Tạo phiếu Cô
                                                        </a>
                                                    </h4>
                                                </div>
                                            </div>
                                            <div class="panel panel-default">
                                                <div class="panel-heading" role="tab">
                                                    <h4 class="panel-title">
                                                        <a href="'.sc_route_admin('admin.davicook_menu_card.create_for_student').'" class="btn btn-flat btn-create-order">
                                                            Tạo phiếu Cháu
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
        $data['urlSort'] = sc_route_admin('admin.davicook_menu_card.index',
            request()->except(['_token', '_pjax', 'sort_order']));
        //=menuSort

        //menuSearch
        $optionStatus = '';
        foreach ($this->menuEstimateCard::$typeStatusSync as $key => $status) {
            $optionStatus .= '<option  '.(($dataSearch['status_sync'] == $key) ? "selected" : "").' value="'.$key.'">'.$status.'</option>';
        }
        $data['topMenuRight'][] = '
                <form action="'.sc_route_admin('admin.davicook_menu_card.index').'" id="button_search" autocomplete="off">
                <input type="hidden" name="limit" value="'. ($dataSearch['limit'] ?? '') .'" id="limit_paginate">
                    <div class="input-group" style="justify-content: flex-end;">
                        <div class="col-lg-2 col-md-2 col-sm-12">
                            <div class="form-group">
                                <label>'.sc_language_render('action.from').':</label>
                                <div class="input-group">
                                <input type="text" name="start_date" id="start_date" class="form-control input-sm date_time rounded-0" data-date-format="dd-mm-yyyy" placeholder="Chọn ngày" value="' . request('start_date') . '"/> 
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-12">
                            <div class="form-group">
                                <label>'.sc_language_render('action.to').':</label>
                                <div class="input-group">
                                <input type="text" name="end_date" id="end_date" class="form-control input-sm date_time rounded-0" data-date-format="dd-mm-yyyy" placeholder="Chọn ngày" value="' . request('end_date') . '"/> 
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-12">
                            <div class="form-group">
                                <label>'.sc_language_render('order.admin.status').':</label>
                                <div class="input-group">
                                    <select class="form-control rounded-0" name="status_sync">
                                        <option value="">Tất cả trạng thái</option>
                                        '.$optionStatus.'
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-12">
                            <div class="form-group">
                                <label>Đối tượng:</label>
                                <div class="input-group">
                                    <select class="form-control rounded-0" name="type_object">
                                        <option value="">Tất cả</option>
                                        <option value="1" '.($dataSearch['type_object'] == 1 ? "selected" : "").' >Phiếu cô</option>
                                        <option value="2" '.($dataSearch['type_object'] == 2 ? "selected" : "").' >Phiếu cháu</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-12">
                            <div class="form-group">
                                <label>Loại phiếu:</label>
                                <div class="input-group">
                                    <select class="form-control rounded-0" name="is_combine">
                                        <option value="">Tất cả</option>
                                        <option value="1" '.($dataSearch['is_combine'] == 1 ? "selected" : "").' >Phiếu gộp</option>
                                        <option value="0" '.($dataSearch['is_combine'] == 0 ? "selected" : "").' >Phiếu thường</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-12">
                            <div class="form-group">
                                <label>Mã phiếu : </label>
                                <div class="input-group">
                                    <input type="text" name="keyword" class="form-control rounded-0 float-right" placeholder="Mã tên phiếu, Mã tên khách hàng" value="'.$dataSearch['keyword'].'">
                                    <div class="input-group-append">
                                        <button type="submit" class="btn btn-primary  btn-flat"><i class="fas fa-search"></i></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>';
        //=menuSearch
        return view($this->templatePathAdmin.'screen.davicook_menu_card.index')
            ->with($data);
    }

    /**
     * Handle delete Phiếu ước lượng món ăn.
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete()
    {
        if (!request()->ajax()) {
            return response()->json(['error' => 1, 'msg' => sc_language_render('admin.method_not_allow')]);
        }
        DB::beginTransaction();
        try {
            $ids = request('ids');
            $arrID = explode(',', $ids);
            $detailIds =  $this->menuCard::whereIn('menu_card_estimate_id', $arrID)->get()->pluck('id');
            $this->menuCardDetail::whereIn('menu_card_id', $detailIds)->delete();
            $this->menuEstimateCard::destroy($arrID);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'error' => 1,
                'msg' => 'Xóa thất bại!'
            ]);
        }

        DB::commit();
        return response()->json(['error' => 0, 'msg' => sc_language_render('action.update_success')]);
    }

    /**
     * Handle delete xóa phiếu con.
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteMenuCardDetail()
    {
        if (!request()->ajax()) {
            return response()->json(['error' => 1, 'msg' => sc_language_render('admin.method_not_allow')]);
        }
        $menu_card_id = request('menu_card_id');
        DB::beginTransaction();
        try {
                $menuCard = $this->menuCard::find($menu_card_id);
                $idMenuEstCard = $menuCard->menu_card_estimate_id;
                $menuCard->delete();
                $this->updateTotalCost(null, $idMenuEstCard);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'error' => 1,
                'msg' => sc_language_render('action.update_fail')
            ]);
        }
        DB::commit();
        return response()->json(['error' => 0, 'msg' => sc_language_render('action.update_success')]);
    }

    /**
     * Handle delete xóa món ăn trong phiếu con.
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteDishOfMenuCardDetail()
    {
        if (!request()->ajax()) {
            return response()->json(['error' => 1, 'msg' => sc_language_render('admin.method_not_allow')]);
        }

        $dish_id = request('dish_id');
        $menu_card_id = request('menu_card_id');

        DB::beginTransaction();
        try {
            $this->menuCardDetail::where('dish_id',$dish_id)->where('menu_card_id', $menu_card_id)->delete();
            $this->updateTotalCost($menu_card_id, null);

        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'error' => 1,
                'msg' => sc_language_render('action.update_fail')
            ]);
        }

        DB::commit();
        return response()->json(['error' => 0, 'msg' => sc_language_render('action.update_success')]);

    }

    /**
     * Show view tạo phiếu ước lượng cho học sinh (Cháu).
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function createMenuCardForStudent()
    {
        $dishs = ShopDavicookDish::where('status',1)->get();
        $customers = ShopDavicookCustomer::where('status',1)->get();
        $data = [
            'title' => 'Tạo hóa đơn',
            'subTitle' => '',
            'title_description' => 'Tạo hóa đơn',
            'icon' => 'fa fa-plus',
            'customers' => $customers,
            'dishs' => $dishs,
        ];

        return view($this->templatePathAdmin . 'screen.davicook_menu_card.create.create_for_student')->with($data);
    }

    /**
     * Show view tạo phiếu ước lượng cho giáo viên (Cô)
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function createMenuCardForTeacher()
    {
        $dishs = ShopDavicookDish::where('status',1)->get();
        $customers = ShopDavicookCustomer::where('status',1)->get();
        $data = [
            'title' => 'Tạo hóa đơn',
            'subTitle' => '',
            'title_description' => 'Tạo hóa đơn',
            'icon' => 'fa fa-plus',
            'customers' => $customers,
            'dishs' => $dishs,
        ];

        return view($this->templatePathAdmin . 'screen.davicook_menu_card.create.create_for_teacher')->with($data);
    }

    /**
     * Lấy thông tin món ăn theo Customer.
     * @return \Illuminate\Http\JsonResponse
     */
    public function getDishByCustomer() {
        $customer_id = request('customer_id');
        if (!$customer_id) {
            $arrayReturn = ['error' => 1, 'msg' => 'Lỗi không tìm thấy khách hàng!'];
        }
        $key = request('key');
        $randNum = rand();
        $dishs = ShopDavicookMenu::where('customer_id',$customer_id)->get();
        if (count($dishs)>0) {
            $getDishName = $this->orderDishName;
            $arrayReturn['dish'] = '
                            <tr class="select-dish">
                                <td class="dish_no_'.$key.' dish_no" style="text-align: center; width: 80px;"></td>
                                <td class="add_date_for_dish_'.$key.'"></td>
                                <td style="width:80px"><input type="text" disabled id="dish_code" class="add_dish_code_'.$key.' form-control"></td>
                                <td id="add_td_product_'.$key.'">
                                <select onChange="getProductBySelectDish($(this), '.$key.');" class="select_add_dish_id_'.$key.' form-control select2" id="dish_id_'.$randNum.'" style="width:100% !important;">';
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
                                <td class="add_rowspan" style="text-align:center"><button id="select_dish_button" type="button" onclick="$(this).parent().parent().remove(); updateSubTotalCostAndTotalCost('.$key.'); updateDishNo('.$key.'); checkRemoveDOM('.$key.');" class="btn btn-danger btn-md btn-flat" data-title="Delete"><i class="fa fa-times" aria-hidden="true"></i></button></td>
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
     * Lấy thông tin nguyên liệu theo món ăn.
     * @return \Illuminate\Http\JsonResponse
     */
    public function getProductByDish()
    {
        $customer_id = request('customer_id');
        $type_object = request('type_object');
        $dish_id = request('dish_id');
        $is_spice = request('is_spice');
        $number_of_servings = request('number_of_servings');
        $delivery_time = Carbon::createFromFormat('d/m/Y', request('date'));
        $key_card = request('key');
        $randNum = rand();

        // Dish code
        $getDishCode = $this->orderDishCode;
        $arrayReturn['dish_code'] =  $getDishCode[$dish_id] ?? '';
        $arrayReturn['dish_id'] =  $dish_id ?? '';

        $menu = ShopDavicookMenu::where('dish_id', $dish_id)->where('customer_id', $customer_id)->first();
        if ($menu) {
            $idMenu = $menu->id;
            $get_product_by_dish = ShopDavicookMenuDetail::where('menu_id', $idMenu)->get();
            $arrayReturn['products'] = '';
            if ($type_object == 2) {
                $arrayReturn['products'] .= '
                                <td class="pro_by_dish_'.$key_card.'"> 
                                    <select class=" form-control select2 select_add_product_gift_'.$key_card.'" id="product_gift_'.$key_card.'_'.$dish_id.'" name="product_gift['.$key_card.']['.$dish_id.']" style="width:100% !important;">
                                        <option selected value="0">Món ăn</option>
                                        <option value="1">Quà chiều</option>
                                    </select>
                                </td>
            ';
            }
            $arrayReturn['products'] .= '<td class="pro_by_dish_'.$key_card.'">';
            foreach ($get_product_by_dish as $k => $v) {
                $arrayReturn['products'] .= '
                                <input type="text" name="date_for_product[' . $key_card . '][]" value="' . request('date') . '" id="product_date_' . $key_card . '_'.$randNum.'_'.$v->id.'" class="form-control date_time">';
            }

            $arrayReturn['products'] .= '</td><td class="pro_by_dish_'.$key_card.'">
                                                   ';
            foreach ($get_product_by_dish as $k => $v) {
                $productName = $v->product->name ?? 'Nguyên liệu đã bị xóa';
                if ($v->product->status == 0) {
                    $arrayReturn['msgProductOff'] = $productName. ' - Hết hàng!';
                }
                $productUnit = $v->product->unit->name ?? '';
                $arrayReturn['products'] .= '   <div class="add_pro_id_'.$key_card.' check-null-product">
                                                    <p class="form-control input-readonly" style="overflow-x: auto">' . $productName . ' (' . $productUnit .')</p>
                                                    <input type="hidden" name="product_id['.$key_card.'][]"  type="text" class="form-control"  value="' . $v->product_id . '"> 
                                                    <input type="hidden" name="sub_dish_id['.$key_card.'][]" value="'.$dish_id.'">
                                                </div>';
            }
            $arrayReturn['products'] .= '</td>
                                         <td class="pro_by_dish_'.$key_card.'">';
            foreach ($get_product_by_dish as $k => $v) {
                $arrayReturn['products'] .= '   <div class="edit_pro_id_'.$key_card.' ">
                                                    <input '.($v->product->status == 0 ? "readonly" : "").' type="number" name="bom['.$key_card.'][]" onKeyup="updateTotalAmountInline(' . $key_card . ',' . ($k+$randNum) . ',' . ($v->product->unit->type ?? 0) . '); updateSubTotalCostAndTotalCost('.$key_card.');" min="0" step="0.0000001" onInput="limitDecimalPlaces(event,7)" 
                                                    class="add_bom_'.$key_card.'_'.($k+$randNum).' form-control" value="' . ($v->product->status == 0 ? 0 : $v->qty) . '">
                                                    <input  name="bom_origin['.$key_card.'][]" type="hidden" value="' . $v->qty . '">
                                                     <input type="hidden" name="is_spice['.$key_card.'][]" value="'.$v->is_spice.'">
                                                     <p class="change_num_of_ser_'.$key_card.' number_of_servings_'.$key_card.'_'.($k+$randNum).' d-none" onChange="updateTotalAmountInline(' . $key_card . ',' . ($k+$randNum) . ',' . ($v->product->unit->type ?? 0) . ');"> </p>
                                                </div>';
            }
            $arrayReturn['products'] .= '</td>
                                        <td class="pro_by_dish_'.$key_card.'">';
            foreach ($get_product_by_dish as $k => $v) {
                $arrayReturn['products'] .= '<div class="total_bom_'.$key_card.' sub_item_product_card_'.$key_card.'">
                                                    <input readonly type="number" name="total_bom['.$key_card.'][]" min=0 step="1" 
                                                    class="form-control add_total_bom_'.$key_card.'_'.($k+$randNum).'"  value="' . roundTotalBom($number_of_servings * ($v->product->status == 0 ? 0 : $v->qty), $v->product->unit->type ?? 0) . '">
                                                </div>';
            }

            $arrayReturn['products'] .= '</td>
                                        <td class="pro_by_dish_'.$key_card.'">';
            foreach ($get_product_by_dish as $k => $v) {
                // Get import price
                $import_price = (new AdminDavicookCustomer())->getImportPriceToLatestPriceTable($customer_id, $v->product_id, $delivery_time);
                // product have import product = 0 then show noti
                if ($import_price == 0) {
                    $arrayReturn['error'] = 1;
                    $arrayReturn['msg'] = 'Bảng giá nhập của nguyên liệu chưa được cập nhập!';
                }
                $arrayReturn['products'] .= '   <div class="import_price_' . $key_card . '">
                                                    <input type="hidden" class="add_import_price_'.$key_card.'_'.($k+$randNum).'" readonly name="import_price['.$key_card.'][]" value="' . $import_price .'">
                                                    <p class="form-control input-readonly">' . number_format(round($import_price, 0)) . '</p>
                                                </div>';
            }
            $arrayReturn['products'] .= '</td>
                                        <td class="pro_by_dish_'.$key_card.'">';
            foreach ($get_product_by_dish as $k => $v) {
                // Get import price
                $import_price = (new AdminDavicookCustomer())->getImportPriceToLatestPriceTable($customer_id, $v->product_id, $delivery_time);
                $arrayReturn['products'] .= '   <div class="sub_cost_' . $key_card . '">
                                                    <input readonly type="hidden"
                                                    class="number_amount_of_product_in_order_'.$key_card.'_'.($k+$randNum).' number_amount_of_product_in_order_'.$key_card.' number_amount_of_product_in_order" value="' .roundTotalBom($number_of_servings * ($v->product->status == 0 ? 0 : $v->qty), $v->product->unit->type ?? 0) * $import_price . '">
                                                    <p class="form-control amount_of_product_in_order_'.$key_card.'_'.($k+$randNum).' input-readonly">' . number_format(roundTotalBom($number_of_servings * ($v->product->status == 0 ? 0 : $v->qty), $v->product->unit->type ?? 0) * $import_price) . '</p>
                                                </div>';
            }
            $arrayReturn['products'] .= '</td>
                                        <td class="pro_by_dish_'.$key_card.'">';
            foreach ($get_product_by_dish as $k => $v) {
                $arrayReturn['products'] .= '   <div class="comment_' . $key_card . '">
                                                    <input name="add_comment['.$key_card.'][]" type="text" class="form-control"  value="">
                                                </div>';
            }
            $arrayReturn['products'] .= '</td>';
        } else {
            $arrayReturn = ['error' => 1, 'msg' => sc_language_render('admin.data_not_found_detail')];
        }

        return response()->json($arrayReturn);
    }

    /**
     * Store data submit tạo phiếu món ăn.
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storeMenuCard(Request $request)
    {
        $allProduct = $this->dataAllProduct;
        $allDish = $this->dataAllDish;
        $allDishOfCustomer = $this->dataAllDishOfCustomer;
        $dayOfWeek = $this->menuEstimateCard::$weekMap;
        # Parent Card
        $type_object = request('type_object');
        $card_name = request('card_name');
        $customer_id = request('customer_id');
        $start_date = Carbon::createFromFormat('d/m/Y', request('start_date'));
        $end_date = Carbon::createFromFormat('d/m/Y', request('end_date'));
        $week_no = request('week_no');

        # Sub card
        $number_of_servings = request('number_of_servings');
        $date = request('date');
        $sub_dish_id = request('sub_dish_id');
        $product_id = request('product_id');
        $product_gift = request('product_gift') ?? [];
        $date_for_product = request('date_for_product') ?? [];
        $bom = request('bom');
        $bom_origin = request('bom_origin');
        $total_bom = request('total_bom');
        $import_price = request('import_price');
        $comment = request('add_comment');
        $date_for_dish = request('delivery_time') ?? [];
        $is_spice = request('is_spice') ?? [];
        $sub_total_cost = request('sub_total_cost') ?? [];
        $customer = ShopDavicookCustomer::find($customer_id);
        DB::beginTransaction();
        try {
            $dataInsertMenuEstCard = [
                'id_name' => $this->menuEstimateCard->getNextId(),
                'customer_id' => $customer_id,
                'customer_name' => $customer->name,
                'customer_code' => $customer->customer_code,
                'price_of_servings' => $customer->serving_price,
                'total_number_of_servings' => array_sum($number_of_servings),
                'start_date' => $start_date,
                'end_date' => $end_date,
                'week_no' => $week_no,
                'total_cost' => array_sum($sub_total_cost),
                'status_sync' => 0,
                'type_object' => $type_object,
                'card_name' => $card_name,
                'created_at' => now(),
                'updated_at' => now(),
            ];
            $dataInsertMenuEstCard = sc_clean($dataInsertMenuEstCard, [], true);
            $menuEstCardId = $this->menuEstimateCard::insertGetId($dataInsertMenuEstCard);

            foreach ($product_id as $key => $sub_product) {
                $formatDate = Carbon::createFromFormat('d/m/Y', $date[$key]);
                $dataInsertMenuCardChildren = [
                    'menu_card_estimate_id' => $menuEstCardId,
                    'number_of_servings' => $number_of_servings[$key],
                    'sub_total_cost' => $sub_total_cost[$key],
                    'date' => $formatDate,
                    'bill_date' => $formatDate,
                    'th_day' => $dayOfWeek[$formatDate->dayOfWeek],
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
                $idMenuCard = $this->menuCard::insertGetId($dataInsertMenuCardChildren);
                foreach ($sub_product as $subKey => $subProductId) {
                    $dish = $allDish->where('id', $sub_dish_id[$key][$subKey])->first();
                    $menu = $allDishOfCustomer->where('customer_id', $customer_id)->where('dish_id', $sub_dish_id[$key][$subKey])->first();

                    if (!$dish) {
                        throw new \Exception('Không tìm thấy món ăn');
                    }

                    if (!$menu) {
                        throw new \Exception('Món ăn không tồn tại trong danh sách khách hàng!');
                    }

                    $product = $allProduct->where('id', $subProductId)->first();
                    if (!$product) {
                        throw new \Exception('Sản phẩm trong nguyên liệu bị xóa');
                    }
                    $formatDateForDish = Carbon::createFromFormat('d/m/Y', $date_for_dish[$key][$sub_dish_id[$key][$subKey]]);
                    $dataInsertMenuCardChildrenDetail = [
                        'menu_card_id' => $idMenuCard,
                        'date_for_dish' => $formatDateForDish,
                        'dish_id' => $sub_dish_id[$key][$subKey],
                        'dish_code' => $dish->code,
                        'dish_name' => $dish->name,
                        'product_id' => $subProductId,
                        'product_code' => $product->sku,
                        'product_name' => $product->name,
                        'product_unit' => $product->unit->name,
                        'product_num' => $product->order_num,
                        'product_short_name' => $product->short_name,
                        'product_type' => $product->kind,
                        'product_priority_level' => $product->purchase_priority_level,
                        'product_gift' => $product_gift[$key][$sub_dish_id[$key][$subKey]] ?? 0,
                        'type_export_menu' => $menu->is_export_menu ?? 1,
                        'qty_cooked_dish' => $menu->qty_cooked_dish ?? '',
                        'is_spice' => $is_spice[$key][$subKey],
                        'bom' => $bom[$key][$subKey],
                        'date_for_product' => Carbon::createFromFormat('d/m/Y', $date_for_product[$key][$subKey]),
                        'bom_origin' => $bom_origin[$key][$subKey],
                        'total_bom' => $total_bom[$key][$subKey],
                        'import_price' => $import_price[$key][$subKey],
                        'amount_of_product_in_order' => $import_price[$key][$subKey] * $bom[$key][$subKey] * $number_of_servings[$key],
                        'number_of_servings' => $number_of_servings[$key],
                        'comment' => $comment[$key][$subKey],
                        'created_at' => now()->addSeconds($subKey*2),
                        'updated_at' => now()->addSeconds($subKey*2),
                    ];
                    $this->menuCardDetail::insert($dataInsertMenuCardChildrenDetail);
                }
            }
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::info($e);
            return redirect()->back()->with(['error' => 'Dữ liệu món ăn gửi lên quá lớn. Vui lòng thử lại!']);
        }

        DB::commit();
        return redirect()->route('admin.davicook_menu_card.index')->with(['success' => 'Tạo phiếu thành công!']);
    }

    /**
     * Show view edit
     * Type = 1 giáo viên.
     * type = 2 học sinh.
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     */
    public function editMenuCard()
    {
        $id = request('id');
        $type = request('type');
        $is_combine = request('is_combine');
        $name = $is_combine == 1 ? '.combine.'.($type == 1 ? 'combine_for_teacher' : 'combine_for_student') : '.edit.'.($type == 1 ? 'edit_for_teacher' : 'edit_for_student');
        $menuEstCard = AdminDavicookMenuEstimateCard::with(['details' => function ($query) {
                                $query->orderBy('date', 'asc');
                            }])->with(['details.children' => function ($query) {
                                $query->orderBy('created_at', 'desc');
                            }])
                        ->find($id);

        if (!$menuEstCard) {
            return redirect()->route('admin.data_not_found')->with(['url' => url()->full()]);
        }
        $dish = ShopDavicookMenu::where('customer_id',$menuEstCard->customer_id)->get();
        $getDishName = $this->orderDishName;
        $data = [
            "title" => "Chi tiết phiếu",
            "subTitle" => '',
            'icon' => 'fa fa-file-text-o',
            'dishForCustomer' => $dish,
            'dishName' => $getDishName,
            "statusSync" => $this->menuEstimateCard::$typeStatusSync,
            "menuEstCard" => $menuEstCard,
            'orderDishName' => $this->orderDishName,
            'orderDishCode' => $this->orderDishCode,
        ];
        return view($this->templatePathAdmin . 'screen.davicook_menu_card' . $name)->with($data);
    }

    /**
     * Lưu món ăn trong chi tiết từng phiếu.
     * @return \Illuminate\Http\JsonResponse
     */
    public function storeDishForMenuCardDetail()
    {
        $allProduct = $this->dataAllProduct;
        $allDish = $this->dataAllDish;
        $allDishOfCustomer = $this->dataAllDishOfCustomer;
        $key = request('menu_card_id');
        $customer_id = request('customer_id');
        $number_of_servings = request('number_of_servings');
        # Sub card
        $date_for_dish = request('delivery_time') ?? [];
        $date_for_product = request('date_for_product') ?? [];
        $sub_dish_id = request('sub_dish_id');
        $product_id = request('product_id');
        $product_gift = request('product_gift') ?? [];
        $bom = request('bom');
        $bom_origin = request('bom_origin');
        $total_bom = request('total_bom');
        $import_price = request('import_price');
        $comment = request('add_comment');
        $is_spice = request('is_spice') ?? [];
        DB::beginTransaction();
        try {
            foreach ($product_id[$key] as $subKey => $subProductId) {
                    $dish = $allDish->where('id', $sub_dish_id[$key][$subKey])->first();
                    $menu = $allDishOfCustomer->where('customer_id', $customer_id)->where('dish_id', $sub_dish_id[$key][$subKey])->first();
                    if (!$menu) {
                        throw new \Exception('Món ăn không tồn tại trong danh sách khách hàng!');
                    }
                    if (!$dish) {
                        throw new \Exception('Không tìm thấy món ăn');
                    }
                    $product = $allProduct->where('id', $subProductId)->first();
                    if (!$product) {
                        throw new \Exception('Sản phẩm trong nguyên liệu bị xóa');
                    }
                    $formatDateForDish = Carbon::createFromFormat('d/m/Y', $date_for_dish[$key][$sub_dish_id[$key][$subKey]]);
                    $dataInsertMenuCardChildrenDetail = [
                        'menu_card_id' => $key,
                        'dish_id' => $sub_dish_id[$key][$subKey],
                        'dish_code' => $dish->code,
                        'dish_name' => $dish->name,
                        'product_id' => $subProductId,
                        'product_code' => $product->sku,
                        'product_name' => $product->name,
                        'product_unit' => $product->unit->name,
                        'product_num' => $product->order_num,
                        'product_short_name' => $product->short_name,
                        'product_type' => $product->kind,
                        'product_priority_level' => $product->purchase_priority_level,
                        'product_gift' => $product_gift[$key][$sub_dish_id[$key][$subKey]] ?? 0,
                        'type_export_menu' => $menu->is_export_menu ?? 1,
                        'is_spice' => $is_spice[$key][$subKey],
                        'date_for_dish' => $formatDateForDish,
                        'date_for_product' => Carbon::createFromFormat('d/m/Y', $date_for_product[$key][$subKey]) ?? $formatDateForDish,
                        'qty_cooked_dish' => $menu->qty_cooked_dish ?? '',
                        'bom' => $bom[$key][$subKey],
                        'bom_origin' => $bom_origin[$key][$subKey],
                        'total_bom' => $total_bom[$key][$subKey],
                        'import_price' => $import_price[$key][$subKey],
                        'amount_of_product_in_order' => $import_price[$key][$subKey] * $bom[$key][$subKey] * $number_of_servings,
                        'number_of_servings' => $number_of_servings,
                        'comment' => $comment[$key][$subKey],
                        'created_at' => now()->addSeconds($subKey*2),
                        'updated_at' => now()->addSeconds($subKey*2),
                    ];
                    $this->menuCardDetail::insert($dataInsertMenuCardChildrenDetail);
                }
            $this->updateTotalCost($key, null);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::info($e);
            return response()->json([
                'error' => 1,
                'msg' => 'Lỗi vui lòng thử lại!'
            ]);
        }
        DB::commit();

        return response()->json(['error' => 0, 'msg' => 'Thêm món ăn thành công!',]);
    }

    /**
     * Thêm mới phiếu con ở màng chi tiết phiếu
     * @return \Illuminate\Http\JsonResponse
     */
    public function storeNewMenuCardForDetail()
    {
        $id = request('id');
        $allProduct = $this->dataAllProduct;
        $customer_id = request('customer_id');
        $allDishOfCustomer = $this->dataAllDishOfCustomer;
        $allDish = $this->dataAllDish;
        $dayOfWeek = $this->menuEstimateCard::$weekMap;
        # Sub card
        $number_of_servings = request('number_of_servings');
        $date = request('date');
        $date_for_dish = request('delivery_time') ?? [];
        $sub_dish_id = request('sub_dish_id');
        $product_id = request('product_id');
        $product_gift = request('product_gift') ?? [];
        $bom = request('bom');
        $bom_origin = request('bom_origin');
        $date_for_product = request('date_for_product') ?? [];
        $total_bom = request('total_bom');
        $import_price = request('import_price');
        $comment = request('add_comment');
        $is_spice = request('is_spice') ?? [];
        DB::beginTransaction();
        try {
            foreach ($product_id as $key => $sub_product) {
                $formatDate = Carbon::createFromFormat('d/m/Y', $date[$key]);
                $dataInsertMenuCardChildren = [
                    'menu_card_estimate_id' => $id,
                    'number_of_servings' => $number_of_servings[$key],
                    'sub_total_cost' => 0,
                    'date' => $formatDate,
                    'bill_date' => $formatDate,
                    'th_day' => $dayOfWeek[$formatDate->dayOfWeek],
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
                $idMenuCard = $this->menuCard::insertGetId($dataInsertMenuCardChildren);
                foreach ($sub_product as $subKey => $subProductId) {
                    $dish = $allDish->where('id', $sub_dish_id[$key][$subKey])->first();
                    $menu = $allDishOfCustomer->where('customer_id', $customer_id)->where('dish_id', $sub_dish_id[$key][$subKey])->first();

                    if (!$dish) {
                        throw new \Exception('Không tìm thấy món ăn');
                    }
                    $product = $allProduct->where('id', $subProductId)->first();
                    if (!$product) {
                        throw new \Exception('Sản phẩm trong nguyên liệu bị xóa');
                    }
                    $formatDateForDish = Carbon::createFromFormat('d/m/Y', $date_for_dish[$key][$sub_dish_id[$key][$subKey]]);
                    $dataInsertMenuCardChildrenDetail = [
                        'menu_card_id' => $idMenuCard,
                        'dish_id' => $sub_dish_id[$key][$subKey],
                        'dish_code' => $dish->code,
                        'dish_name' => $dish->name,
                        'product_id' => $subProductId,
                        'product_code' => $product->sku,
                        'product_name' => $product->name,
                        'product_unit' => $product->unit->name,
                        'product_num' => $product->order_num,
                        'product_short_name' => $product->short_name,
                        'product_type' => $product->kind,
                        'product_priority_level' => $product->purchase_priority_level,
                        'product_gift' => $product_gift[$key][$sub_dish_id[$key][$subKey]] ?? 0,
                        'type_export_menu' => $menu->is_export_menu,
                        'qty_cooked_dish' => $menu->qty_cooked_dish,
                        'is_spice' => $is_spice[$key][$subKey],
                        'bom' => $bom[$key][$subKey],
                        'bom_origin' => $bom_origin[$key][$subKey],
                        'date_for_dish' => $formatDateForDish,
                        'date_for_product' => Carbon::createFromFormat('d/m/Y', $date_for_product[$key][$subKey]) ?? $formatDateForDish,
                        'total_bom' => $total_bom[$key][$subKey],
                        'import_price' => $import_price[$key][$subKey],
                        'amount_of_product_in_order' => $import_price[$key][$subKey] * $bom[$key][$subKey] * $number_of_servings[$key],
                        'number_of_servings' => $number_of_servings[$key],
                        'comment' => $comment[$key][$subKey],
                        'created_at' => now()->addSeconds($subKey*2),
                        'updated_at' => now()->addSeconds($subKey*2),
                    ];
                    $this->menuCardDetail::insert($dataInsertMenuCardChildrenDetail);
                }
                $this->updateTotalCost($idMenuCard, $id);
            }
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::info($e);
            return response()->json([
                'error' => 1,
                'msg' => 'Lỗi dữ liệu món ăn quá lớn. Vui lòng thử lại!'
            ]);
        }

        DB::commit();

        return response()->json(['error' => 0, 'msg' => 'Thêm phiếu mới thành công!', 'id' => $id]);
    }

    /**
     * Sửa suất ăn từng phiếu
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateNumberOfServings()
    {
        $allProduct = $this->dataAllProduct;

        $id = request('pk');
        $value = request('value');
        $menuCard = $this->menuCard::with('children')->find($id);
        DB::beginTransaction();
        try {
            $menuCard->number_of_servings = $value;
            $menuCard->save();
            foreach ($menuCard->children as $item) {
                $product = $allProduct->where('id', $item->product_id)->first();
                $item->amount_of_product_in_order = roundTotalBom($value*$item->bom, $product->unit->type ?? 0) * $item->import_price;
                $item->total_bom = roundTotalBom($value*$item->bom, $product->unit->type ?? 0);
                $item->number_of_servings = $value;
                $item->save();
            }
            $this->updateTotalCost($id, null);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::info($e);
            return response()->json([
                'error' => 1,
                'msg' => 'Sửa suất ăn lỗi!'
            ]);
        }
        DB::commit();

        return response()->json(['error' => 0, 'msg' => 'Thay đổi suất ăn thành công. Dữ liệu đang cập nhập lại!']);
    }

    /**
     * Thay đổi món ăn.
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateChangDishForMenuCard()
    {
        $allProduct = $this->dataAllProduct;
        $allDish = $this->dataAllDish;
        $allDishOfCustomer = $this->dataAllDishOfCustomer;
        $newDishId = request('new_dish_id');
        $oldDishId = request('old_dish_id');
        $menuCardId = request('menu_card_id');
        $customer_id = request('customer_id');
        $menu = $allDishOfCustomer->where('dish_id', $newDishId)->where('customer_id', $customer_id)->first();
        $dish = $allDish->where('id', $newDishId)->first();
        $idMenu = $menu->id;
        $get_product_by_dish = ShopDavicookMenuDetail::where('menu_id', $idMenu)->get();
        DB::beginTransaction();
        try {
            $menuCard = $this->menuCard::find($menuCardId);
            $date = $menuCard->date;
            $oldMenuCardDetail = $this->menuCardDetail::where('menu_card_id', $menuCardId)->where('dish_id', $oldDishId)->first();
            $flag =  $this->menuCardDetail::where('menu_card_id', $menuCardId)->where('dish_id', $newDishId)->first();
            if ($flag) {
                throw new \Exception('Món ăn đã tồn tại trên phiếu con!');
            }
            foreach ($get_product_by_dish as $key => $value) {
                $import_price = (new AdminDavicookCustomer())->getImportPriceToLatestPriceTable($customer_id, $value->product_id, $date);
                $product = $allProduct->where('id', $value->product_id)->first();
                $totalBom = roundTotalBom($menuCard->number_of_servings * $value->qty, $product->unit->type ?? 0);
                $dataInsertMenuCardChildrenDetail = [
                    'menu_card_id' => $menuCardId,
                    'dish_id' => $newDishId,
                    'dish_code' => $dish->code,
                    'dish_name' => $dish->name,
                    'product_id' => $value->product_id,
                    'product_code' => $product->sku,
                    'product_name' => $product->name,
                    'product_unit' => $product->unit->name,
                    'product_num' => $product->order_num,
                    'product_short_name' => $product->short_name,
                    'product_type' => $product->kind,
                    'product_priority_level' => $product->purchase_priority_level,
                    'product_gift' => $oldMenuCardDetail->product_gift,
                    'date_for_dish' => $oldMenuCardDetail->date_for_dish,
                    'date_for_product' => $oldMenuCardDetail->date_for_product,
                    'type_export_menu' => $menu->is_export_menu ?? 1,
                    'is_spice' => $value->is_spice,
                    'qty_cooked_dish' => $menu->qty_cooked_dish ?? '',
                    'bom' => $value->qty,
                    'bom_origin' => $value->qty,
                    'total_bom' => $totalBom,
                    'import_price' => $import_price,
                    'amount_of_product_in_order' => $menuCard->number_of_servings * $value->qty * $import_price,
                    'number_of_servings' => $menuCard->number_of_servings,
                    'comment' => '',
                    'created_at' => $oldMenuCardDetail->created_at,
                ];
                $this->menuCardDetail::insert($dataInsertMenuCardChildrenDetail);
            }
            $this->menuCardDetail::where('menu_card_id', $menuCardId)->where('dish_id', $oldDishId)->delete();
            $this->updateTotalCost($menuCardId, null);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::info($e);
            return response()->json([
                'error' => 1,
                'msg' => $e->getMessage() ?? '',
            ]);
        }
        DB::commit();

        return response()->json(['error' => 0, 'msg' => 'Thay đổi món ăn thành công. Dữ liệu đang cập nhập lại!']);
    }

    /**
     * Update các thông tin chi tiết trên từng phiếu
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function updateItemMenuCardDetail()
    {
        $allProduct = $this->dataAllProduct;
        $id = request('pk');
        $value = request('value');
        $name = request('name');
        DB::beginTransaction();
        try {
            if ($name == 'bom') {
                $menuCardDetail = $this->menuCardDetail::find($id);
                $product = $allProduct->where('id', $menuCardDetail->product_id)->first();
                $menuCardDetail->bom = $value;
                $menuCardDetail->amount_of_product_in_order = roundTotalBom($value*$menuCardDetail->number_of_servings, $product->unit->type ?? 0) * $menuCardDetail->import_price;
                $menuCardDetail->total_bom = roundTotalBom($value*$menuCardDetail->number_of_servings, $product->unit->type ?? 0);
                $menuCardDetail->save();
                $this->updateTotalCost($menuCardDetail->menu_card_id, null);
            }

            if ($name == 'comment') {
                $menuCardDetail = $this->menuCardDetail::find($id);
                $menuCardDetail->comment = $value;
                $menuCardDetail->save();
            }

            if ($name == 'date') {
                $menuCard = $this->menuCard::find($id);
                $menuEstCard = $this->menuEstimateCard::find($menuCard->menu_card_estimate_id);
                $flag_date = $menuEstCard->details->where('date', $value)->first();

                if ($flag_date) {
                    return response()->json(['error' => 1, 'msg' => 'Ngày được chọn đã tồn tại trong phiếu!']);
                }

                if ($value < $menuEstCard->start_date || $value > $menuEstCard->end_date) {
                    return response()->json(['error' => 1, 'msg' => 'Ngày được chọn không nằm trong khoản ngày hiệu lực']);
                }
                
                if ($value != '') {
                    $menuCard->date = $value;
                    $menuCard->bill_date = $value;
                    $menuCard->save();
                    $this->menuCardDetail::where('menu_card_id', $id)->update(
                        [
                            'date_for_dish' => $value,
                            'date_for_product' => $value,
                        ]
                    );
                }
            }

            if ($name == 'bill_date') {
                $menuCard = $this->menuCard::find($id);
                if ($value != '') {
                    $menuCard->bill_date = $value;
                    $menuCard->save();
                }
            }

            if ($name == 'date_for_dish') {
                $menuCardDetail = $this->menuCardDetail::find($id);
                $this->menuCardDetail::where('dish_id', $menuCardDetail->dish_id)->where('menu_card_id', $menuCardDetail->menu_card_id)->update([
                    'date_for_dish' => $value,
                    'date_for_product' => $value,
                ]);
            }

            if ($name == 'date_for_product') {
                $menuCardDetail = $this->menuCardDetail::find($id);
                $menuCardDetail->update([
                    'date_for_product' => $value,
                ]);
            }

            if ($name == 'product_gift') {
                $menuCardDetail = $this->menuCardDetail::find($id);
                $this->menuCardDetail::where('dish_id', $menuCardDetail->dish_id)->where('menu_card_id', $menuCardDetail->menu_card_id)->update([
                   'product_gift' => $value,
                ]);
            }

            if ($name == 'week_no') {
                $this->menuEstimateCard::where('id', $id)->update([
                    'week_no' => $value,
                ]);
            }

            if ($name == 'card_name') {
                if ($value != '') {
                    $this->menuEstimateCard::where('id', $id)->update([
                        'card_name' => $value,
                    ]);
                }
            }

            if ($name == 'start_date') {
                $menuEstCard = $this->menuEstimateCard::find($id);
                if ($value > $menuEstCard->end_date) {
                    return response()->json(['error' => 1, 'msg' => 'Ngày bắt đầu không được lớn hơn ngày kết thúc']);
                }
                if ($value != '') {
                    $menuEstCard->start_date = $value;
                    $menuEstCard->save();
                }
            }

            if ($name == 'end_date') {
                $menuEstCard = $this->menuEstimateCard::find($id);
                if ($value < $menuEstCard->start_date) {
                    return response()->json(['error' => 1, 'msg' => 'Ngày kết thúc không được nhỏ hơn ngày bắt đầu']);
                }
                if ($value != '') {
                    $menuEstCard->end_date = $value;
                    $menuEstCard->save();
                }
            }
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::info($e);
            return response()->json([
                'error' => 1,
                'msg' => 'Chỉnh sửa thất bại!'
            ]);
        }
        DB::commit();

        return response()->json(['error' => 0, 'msg' => 'Chỉnh sửa thành công. Dữ liệu đang cập nhập lại!']);
    }

    /**
     * Đồng bộ phiếu qua đơn hàng davicook.
     * @return \Illuminate\Http\JsonResponse
     */
    public function orderDavicookSync()
    {
        $dataAllProduct = $this->dataAllProduct;
        $ids = explode(',', request('ids'));

        DB::beginTransaction();
        try {
            AdminDavicookOrder::whereIn('menu_card_est_id', $ids)->delete();
            $menuEstCard = $this->menuEstimateCard::with(['details' => function($q){
                $q->orderBy('date', 'asc');
            }])->with('details.children')->findMany($ids);

            $this->menuEstimateCard::whereIn('id', $ids)->update([
                'status_sync' => 1,
                'sync_date' => now(),
            ]);
            foreach ($menuEstCard as $item) {
                $customer = AdminDavicookCustomer::where('id', $item->customer_id)->orWhere('customer_code', $item->customer_code)->first();
                if (!$customer) {
                    throw new ImportException('khách hàng không tồn tại!');
                }
                if ($item->is_combine == 1) {
                    throw new ImportException('Phiếu gộp không thể đồng bộ!');
                }
                foreach ($item->details as $details) {
                    $arrDateOfOrder = [];
                    foreach ($details->children->groupBy('date_for_dish') as $keyDate => $SubItem) {
                        $sum = 0;
                        $flagDish = '';
                        $detailId = '';
                        $sumDetail = 0;
                        $insertOrder = [
                            'menu_card_est_id' => $item->id,
                            'menu_card_est_code' => $item->id_name,
                            'id_name' => (new ShopDavicookOrder())->getNextId(),
                            'customer_code' => $customer->customer_code,
                            'customer_id' => $customer->id,
                            'customer_name' => $customer->name,
                            'customer_num' => $customer->order_num,
                            'customer_short_name' => $customer->short_name,
                            'email' => $customer->email,
                            'phone' => $customer->phone,
                            'address' => $customer->address,
                            'price_of_servings' => $customer->serving_price,
                            'explain' => 'Đơn chính',
                            'status' => 1,
                            'type' => 0,
                            'total' => $SubItem->sum('amount_of_product_in_order'),
                            'subtotal' => $SubItem->sum('amount_of_product_in_order'),
                            'number_of_servings' => $details->number_of_servings,
                            'number_of_reality_servings' => $details->number_of_servings,
                            'number_of_extra_servings' => 0,
                            'bill_date' => $details->bill_date,
                            'delivery_date' => $keyDate,
                        ];
                        $orderDavicook = AdminDavicookOrder::create($insertOrder);
                        foreach ($SubItem as $value) {

                            $product = $dataAllProduct->where('id', $value->product_id)->first();
                            $supplier_id = ShopDavicookProductSupplier::getSupplierOfProductAndCustomer($product->id ?? $value->product_id, $customer->id)->supplier_id ?? '';
                            $supplier = ShopSupplier::find($supplier_id);
                            $dataInsertDetail = [
                                'order_id' => $orderDavicook->id,
                                'type' => 0,
                                'customer_id' => $customer->id,
                                'dish_name' => $value->dish_name,
                                'dish_id' => $value->dish_id,
                                'bom' => ($value->date_for_dish != $value->date_for_product) ? 0 :  $value->bom,
                                'bom_origin' => $value->bom_origin,
                                'total_bom' => ($value->date_for_dish != $value->date_for_product) ? 0 :  $value->total_bom,
                                'real_total_bom' => $value->total_bom,
                                'amount_of_product_in_order' => ($value->date_for_dish != $value->date_for_product) ? 0 :  $value->amount_of_product_in_order,
                                'import_price' => $value->import_price,
                                'qty' => $value->number_of_servings,
                                'product_priority_level' => $product->purchase_priority_level ?? $value->product_priority_level,
                                'product_type' => $product->kind ?? $value->product_type,
                                'product_code' => $product->sku ?? $value->product_code,
                                'product_id' => $product->id ?? $value->product_id,
                                'product_name' => $product->name ?? $value->product_name,
                                'product_short_name' => $product->short_name ?? $value->product_short_name,
                                'product_unit' => $product->unit->name ?? $value->product_unit,
                                'product_num' => $product->order_num ?? $value->product_num,
                                'supplier_id' => $supplier_id,
                                'supplier_code' => $supplier->supplier_code ?? '',
                                'supplier_name' => $supplier->name ?? '',
                                'category_id' => $product->category->id ?? '',
                                'comment' => $value->comment,
                            ];
                            $sum += ($value->date_for_dish != $value->date_for_product) ? 0 :  $value->amount_of_product_in_order;
                            ShopDavicookOrderDetail::create($dataInsertDetail);

                            if ($value->date_for_dish != $value->date_for_product) {
                                if (!array_key_exists($value->date_for_product, $arrDateOfOrder)) {
                                    $insertOrderExtra = [
                                        'menu_card_est_id' => $item->id,
                                        'menu_card_est_code' => $item->id_name,
                                        'id_name' => (new ShopDavicookOrder())->getNextId(),
                                        'customer_code' => $customer->customer_code,
                                        'customer_id' => $customer->id,
                                        'customer_name' => $customer->name,
                                        'customer_num' => $customer->order_num,
                                        'customer_short_name' => $customer->short_name,
                                        'email' => $customer->email,
                                        'phone' => $customer->phone,
                                        'address' => $customer->address,
                                        'price_of_servings' => $customer->serving_price,
                                        'explain' => 'Đơn chính',
                                        'status' => 1,
                                        'type' => 0,
                                        'total' => $SubItem->sum('amount_of_product_in_order'),
                                        'subtotal' => $SubItem->sum('amount_of_product_in_order'),
                                        'number_of_servings' => 0,
                                        'number_of_reality_servings' => 0,
                                        'number_of_extra_servings' => 0,
                                        'bill_date' => $details->bill_date,
                                        'delivery_date' => $value->date_for_product,
                                    ];
                                    $orderDavicookExtra = AdminDavicookOrder::create($insertOrderExtra);
                                    $arrDateOfOrder[$value->date_for_product] = $orderDavicookExtra->id;
                                }

                                $dataInsertDetailExtra = [
                                    'order_id' => $arrDateOfOrder[$value->date_for_product],
                                    'type' => 1,
                                    'customer_id' => $customer->id,
                                    'dish_name' => $value->dish_name,
                                    'dish_id' => $value->dish_id,
                                    'bom' => 0,
                                    'bom_origin' => 0,
                                    'total_bom' => $value->total_bom,
                                    'real_total_bom' => $value->total_bom,
                                    'amount_of_product_in_order' => $value->amount_of_product_in_order,
                                    'import_price' => $value->import_price,
                                    'qty' => $value->number_of_servings,
                                    'product_priority_level' => $product->purchase_priority_level ?? $value->product_priority_level,
                                    'product_type' => $product->kind ?? $value->product_type,
                                    'product_code' => $product->sku ?? $value->product_code,
                                    'product_id' => $product->id ?? $value->product_id,
                                    'product_name' => $product->name ?? $value->product_name,
                                    'product_short_name' => $product->short_name ?? $value->product_short_name,
                                    'product_unit' => $product->unit->name ?? $value->product_unit,
                                    'product_num' => $product->order_num ?? $value->product_num,
                                    'supplier_id' => $supplier_id,
                                    'supplier_code' => $supplier->supplier_code ?? '',
                                    'supplier_name' => $supplier->name ?? '',
                                    'category_id' => $product->category->id ?? '',
                                    'comment' => $value->comment,
                                ];
                                $sumDetail += $value->amount_of_product_in_order;
                                ShopDavicookOrderDetail::create($dataInsertDetailExtra);
                                AdminDavicookOrder::where('id', $arrDateOfOrder[$value->date_for_product])->update([
                                    'total' => $sumDetail,
                                    'subtotal' => $sumDetail,
                                ]);
                            }
                        }
                        $orderDavicook->total = $sum;
                        $orderDavicook->subtotal = $sum;
                        $orderDavicook->save();
                    }
                }
            }
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::info($e);
            return response()->json(['error' => 1, 'msg' => $e->getMessage()]);
        }
        DB::commit();
        return response()->json(['error' => 0, 'msg' => 'Tạo đơn hàng thành công!']);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     * @throws ImportException
     */
    public function cloneMenuCard()
    {
        $id = request('id');
        $menuEstCard = $this->menuEstimateCard::with('details', 'details.children')->find($id);
        if (!$menuEstCard) {
            throw new ImportException('Không tìm thấy phiếu!');
        }

        $customer = AdminDavicookCustomer::where('customer_code', $menuEstCard->customer_id)->orWhere('customer_code', $menuEstCard->customer_code)->first();
        if (!$customer) {
            throw new ImportException('Lỗi nhân bản. Không tìm thấy khách hàng!');
        }

        DB::beginTransaction();
        try {
            $dataCloneMenuEstCard = \Illuminate\Support\Arr::except($menuEstCard->toArray(), ['id', 'id_name', 'created_at', 'updated_at', 'details', 'sync_date', 'status_sync']);
            $dataCloneMenuEstCard['id_name'] = $this->menuEstimateCard->getNextId();
            $dataCloneMenuEstCard['customer_code'] = $customer->name;
            $dataCloneMenuEstCard['customer_code'] = $customer->customer_code;
            $dataCloneMenuEstCard['customer_id'] = $customer->id;
            $dataCloneMenuEstCard['created_at'] = now();
            $dataCloneMenuEstCard['updated_at'] = now();
            $idNewMenuEstCard = $this->menuEstimateCard::insertGetId($dataCloneMenuEstCard);
            foreach ($menuEstCard->details as $item) {
                $dataInsertMenuCard = \Illuminate\Support\Arr::except($item->toArray(), ['id', 'children', 'menu_card_estimate_id', 'created_at', 'updated_at']);
                $dataInsertMenuCard['menu_card_estimate_id'] = $idNewMenuEstCard;
                $dataInsertMenuCard['created_at'] = now();
                $dataInsertMenuCard['updated_at'] = now();
                $idNewMenuCard = $this->menuCard::insertGetId($dataInsertMenuCard);
                foreach ($item->children as $detail) {
                    $dataInsertMenuCardDetail = \Illuminate\Support\Arr::except($detail->toArray(), ['id', 'menu_card_id', 'created_at', 'updated_at']);
                    $dataInsertMenuCardDetail['menu_card_id'] = $idNewMenuCard;
                    $dataInsertMenuCardDetail['created_at'] = now();
                    $dataInsertMenuCardDetail['updated_at'] = now();
                    $this->menuCardDetail::insertGetId($dataInsertMenuCardDetail);
                }
            }

        } catch (Throwable $e) {
            DB::rollBack();
            Log::info($e);
            return response()->json(['error' => 1, 'msg' => $e->getMessage()]);
        }
        DB::commit();
        return response()->json(['error' => 0, 'msg' => sc_language_render('customer.admin.clone_success')]);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function combineMenuCard()
    {
        $ids = request('ids');

        DB::beginTransaction();
        try {
            $menuEstCard = $this->menuEstimateCard::with(['details' => function($q){
                $q->orderBy('date', 'asc');
            }])->with('details.children')->orderBy('start_date', 'ASC')->findMany($ids);
            $start_date = $menuEstCard->sortBy('start_date')->first()->start_date;
            $end_date = $menuEstCard->sortByDesc('end_date')->first()->end_date;
            $is_combine = $menuEstCard->pluck('is_combine')->unique()->toArray();
            $customer_id = $menuEstCard->pluck('customer_id')->unique();
            $type_object = $menuEstCard->pluck('type_object')->unique();

            if (count($customer_id) > 1 || count($type_object) > 1) {
                throw new ImportException('Phiếu gộp phải cùng khách hàng và cùng loại phiếu!');
            }

            if (in_array(1, $is_combine)) {
                throw new ImportException('Tồn tại phiếu đã gộp. Không thể gộp tiếp!');
            }

            $dataInsertMenuEstCard = [
                'id_name' => $this->menuEstimateCard->getNextId(),
                'customer_id' => $menuEstCard->first()->customer_id,
                'customer_name' => $menuEstCard->first()->customer_name,
                'customer_code' => $menuEstCard->first()->customer_code,
                'price_of_servings' => $menuEstCard->first()->price_of_servings,
                'total_number_of_servings' => $menuEstCard->sum('total_number_of_servings'),
                'start_date' => $start_date,
                'end_date' => $end_date,
                'is_combine' => 1,
                'week_no' => null,
                'total_cost' => $menuEstCard->sum('total_cost'),
                'status_sync' => 0,
                'type_object' => $menuEstCard->first()->type_object,
                'card_name' => $menuEstCard->first()->card_name,
                'created_at' => now(),
                'updated_at' => now(),
            ];
            $dataInsertMenuEstCard = sc_clean($dataInsertMenuEstCard, [], true);
            $menuEstCardId = $this->menuEstimateCard::insertGetId($dataInsertMenuEstCard);
            foreach ($menuEstCard as $item) {
                foreach ($item->details as $detail) {
                    $dataInsertMenuCardChildren = [
                        'menu_card_estimate_id' => $menuEstCardId,
                        'number_of_servings' => $detail->number_of_servings,
                        'sub_total_cost' => $detail->sub_total_cost,
                        'date' => $detail->date,
                        'bill_date' => $detail->bill_date,
                        'th_day' => $detail->th_day,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                    $idMenuCard = $this->menuCard::insertGetId($dataInsertMenuCardChildren);
                    foreach ($detail->children as $SubItem) {
                        $dataInsertMenuCardChildrenDetail = [
                            'menu_card_id' => $idMenuCard,
                            'date_for_dish' => $SubItem->date_for_dish,
                            'date_for_product' => $SubItem->date_for_product,
                            'dish_id' => $SubItem->dish_id,
                            'dish_code' => $SubItem->dish_code,
                            'dish_name' => $SubItem->dish_name,
                            'product_id' => $SubItem->product_id,
                            'product_code' => $SubItem->product_code,
                            'product_name' => $SubItem->product_name,
                            'product_unit' => $SubItem->product_unit,
                            'product_num' => $SubItem->product_num,
                            'product_short_name' => $SubItem->product_short_name,
                            'product_type' => $SubItem->product_type,
                            'product_priority_level' => $SubItem->product_priority_level,
                            'product_gift' => $SubItem->product_gift,
                            'type_export_menu' => $SubItem->type_export_menu,
                            'qty_cooked_dish' => $SubItem->qty_cooked_dish,
                            'is_spice' => $SubItem->is_spice,
                            'bom' => $SubItem->bom,
                            'bom_origin' => $SubItem->bom_origin,
                            'total_bom' => $SubItem->total_bom,
                            'import_price' => $SubItem->import_price,
                            'amount_of_product_in_order' => $SubItem->amount_of_product_in_order,
                            'number_of_servings' => $SubItem->number_of_servings,
                            'comment' => $SubItem->comment,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                        $this->menuCardDetail::insert($dataInsertMenuCardChildrenDetail);
                    }
                }
            }
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error($e);
            return response()->json(['error' => 1, 'msg' => $e->getMessage()]);
        }
        DB::commit();
        return response()->json(['error' => 0, 'msg' => 'Gộp phiếu thành công!']);

    }

    /**
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    /**
     * @return \Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function exportExcelMenuCard()
    {
        $ids = explode(',',request('ids'));

        $menuEstCard = $this->menuEstimateCard::with('details', 'details.children')->findMany($ids);
        $customer = $menuEstCard->pluck('customer_id');

        if ($customer->unique()->count() > 1) {
            return redirect()->back()->with(['error' => 'Vui chọn cùng khách hàng để xuất excel!']);
        }

        $data = $this->getDataPrintPdfAndExportExcel($menuEstCard);

        $dataStudent = $data['student'];
        $dataTeacher = $data['teacher'];

        return Excel::download(new AdminExportMenuCard($ids, $dataStudent, $dataTeacher), 'Phiếu món ăn - ' . Carbon::now() . '.xlsx');
    }

    /**
     * @return false|string|string[]|void|null
     */
    public function previewPdfMenuCard()
    {
        $ids = explode(',',request('ids'));

        $menuEstCard = $this->menuEstimateCard::with('details', 'details.children')->findMany($ids);
        $customer = $menuEstCard->pluck('customer_id');

        if ($customer->unique()->count() > 1) {
            $html = view($this->templatePathAdmin . 'screen.error_template.product_not_found_for_print_davicook_error')
                ->with(['error' => 'Vui lòng chọn cùng khách hàng để in PDF!'])->render();
            $html = mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8');

            return $html;
        }

        $data = $this->getDataPrintPdfAndExportExcel($menuEstCard);

        $html = view($this->templatePathAdmin . 'screen.davicook_menu_card.print_pdf.pdf_template')
            ->with(['data' => $data])->render();
        $html = mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8');

        return $html;
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function updateImportPriceProduct($id)
    {
        $menuEstCard = $this->menuEstimateCard::with('details.children')->find($id);
        DB::beginTransaction();
        try {
            foreach ($menuEstCard->details as $detail) {
                foreach ($detail->children as $value) {
                    $import_price = $this->customer->getImportPriceToLatestPriceTable($menuEstCard->customer_id ?? '', $value->product_id ?? '', $value->date_for_dish);
                    $value->import_price = $import_price;
                    $value->amount_of_product_in_order = $import_price *  $value->total_bom;
                    $value->save();
                }
                $this->updateTotalCost($detail->id, null);
            }

        } catch (Throwable $e) {
            DB::rollBack();
            Log::error($e);
            return redirect()->back()->with(['error' => $e->getMessage()]);
        }
        DB::commit();
        return redirect()->back()->with(['success' => 'Cập nhập giá thành công!']);
    }

    /**
     * Xử lý lấy dữu liệu để xuất excel và pdf.
     * @param $menuEstCard
     * @return array
     */
    public function getDataPrintPdfAndExportExcel($menuEstCard)
    {
        $data = [];
        $dataStudent = [];
        $dataTeacher = [];
        $menuCardForStudent = $menuEstCard->where('type_object', 2);
        foreach ($menuCardForStudent as $index => $item) {
            $dataStudent[$index] = [
                'week_no' => $item->week_no,
                'card_name' => $item->card_name,
                'customer_name' => $item->customer_name,
            ];
            foreach ($item->details as $keyDetail => $detail) {
                $i = 1;
                $product_gift = '';
                foreach ($detail->children->where('product_gift', 1)->groupBy('dish_id') as $dishByProductGift) {
                    $product_gift .= $dishByProductGift->first()->dish_name . '<br>';
                }
                foreach ($detail->children->sortBy('created_at')->where('product_gift', 0)->groupBy('dish_id') as $dish) {
                    $count = $detail->children->where('product_gift', 0)->groupBy('dish_id')->count();
                    $comment = '';
                    $qty_raw_dish = '';
                    foreach ($dish as $product) {
                        if ($product->comment != '') {
                            $comment .= $product->comment . '; ';
                        }
                        if ($product->is_spice == 0) {
                            if ($product->product_unit == 'KG') {
                                $qty_raw_dish .= ($product->bom * 1000) . 'g ' . $product->product_name . ' + ';
                            } else {
                                $qty_raw_dish .= ((int)$product->bom) . $product->product_unit . ' ' . $product->product_name . ' + ';
                            }
                        }
                    }
                    if ($i == 1) {
                        $dataStudent[$index]['first_details'][] = [
                            'count' => $count,
                            'day' => $detail->th_day,
                            'date' => Carbon::make($detail->date)->format('d/m/Y'),
                            'dish_name' => $i . '. ' . $dish->first()->dish_name,
                            'qty_raw_dish' => $dish->first()->type_export_menu == 1 ? rtrim($qty_raw_dish, ' + ') : ' ',
                            'qty_cooked_dish' => $dish->first()->type_export_menu == 1 ? $dish->first()->qty_cooked_dish : ' ',
                            'product_gift_or_comment' => $dish->first()->product_gift != '' ? $product_gift : $comment,
                            'item' => [],
                        ];
                    }
                    if ($i > 1) {
                        $dataStudent[$index]['first_details'][$keyDetail]['item'][] = [
                            'dish_name' => $i . '. ' . $dish->first()->dish_name,
                            'qty_raw_dish' => $dish->first()->type_export_menu == 1 ? rtrim($qty_raw_dish, ' + ') : ' ',
                            'qty_cooked_dish' => $dish->first()->type_export_menu == 1 ? $dish->first()->qty_cooked_dish : ' ',
                        ];
                    }
                    $i++;
                }
            }
        }

        $menuCardForTeacher = $menuEstCard->where('type_object', 1);
        foreach ($menuCardForTeacher as $index => $item) {
            $dataTeacher[$index] = [
                'week_no' => $item->week_no,
                'card_name' => $item->card_name,
                'customer_name' => $item->customer_name,
            ];
            foreach ($item->details as $keyDetail => $detail) {
                $i = 1;
                foreach ($detail->children->groupBy('dish_id') as $dish) {
                    $count = $detail->children->groupBy('dish_id')->count();
                    if ($i == 1) {
                        $dataTeacher[$index]['first_details'][] = [
                            'count' => $count,
                            'day' => $detail->th_day,
                            'date' => Carbon::make($detail->date)->format('d/m/Y'),
                            'dish_name' => $i . '. ' . $dish->first()->dish_name,
                            'item' => [],
                        ];
                    }
                    if ($i > 1) {
                        $dataTeacher[$index]['first_details'][$keyDetail]['item'][] = [
                            'dish_name' => $i . '. ' . $dish->first()->dish_name,
                        ];
                    }
                    $i++;
                }
            }
        }

        $data['student'] = $dataStudent;
        $data['teacher'] = $dataTeacher;
        return $data;
    }

    /**
     * Update lại giá tiền cost.
     * @param $menu_card_id
     * @param $menu_est_card_id
     */
    public function updateTotalCost($menu_card_id, $menu_est_card_id)
    {
        if (!empty($menu_card_id)) {
            $menuCard = $this->menuCard::with('children')->find($menu_card_id);
            $menuCard->sub_total_cost = $menuCard->children->sum('amount_of_product_in_order');
            $menuCard->save();
        }

        $menuEstCard = $this->menuEstimateCard::with('details')->find($menuCard->menu_card_estimate_id ?? $menu_est_card_id);
        $menuEstCard->total_cost = $menuEstCard->details->sum('sub_total_cost');
        $menuEstCard->total_number_of_servings = $menuEstCard->details->sum('number_of_servings');
        $menuEstCard->save();
    }

}