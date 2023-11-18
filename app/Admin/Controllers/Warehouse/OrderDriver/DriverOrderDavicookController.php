<?php

namespace App\Admin\Controllers\Warehouse\OrderDriver;

use App\Admin\Models\AdminDavicookOrder;
use App\Admin\Models\AdminDriver;
use App\Admin\Models\AdminOrder;
use App\Front\Models\ShopCustomer;
use App\Front\Models\ShopDavicookCustomer;
use App\Front\Models\ShopDavicookOrder;
use App\Front\Models\ShopDavicookOrderDetail;
use App\Front\Models\ShopDavicookOrderHistory;
use App\Front\Models\ShopDepartment;
use App\Front\Models\ShopOrder;
use App\Front\Models\ShopOrderHistory;
use App\Front\Models\ShopOrderObject;
use App\Traits\OrderTraits;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use SCart\Core\Admin\Admin;
use SCart\Core\Admin\Controllers\RootAdminController;

class DriverOrderDavicookController extends RootAdminController
{
    public $styleStatus;
    public $drivers;
    public $deliveryStatus;

    use OrderTraits;
    public function __construct()
    {
        parent::__construct();
        $this->deliveryStatus = ShopDavicookOrder::$DELIVERY_STATUS;
        $this->drivers = AdminDriver::all();
        $this->styleStatus = ShopOrder::$STYLE_STATUS;
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
        ];

        //Process add content
        $data['menuRight'] = sc_config_group('menuRight', \Request::route()->getName());
        $data['menuLeft'] = sc_config_group('menuLeft', \Request::route()->getName());
        $data['topMenuRight'] = sc_config_group('topMenuRight', \Request::route()->getName());
        $data['topMenuLeft'] = sc_config_group('topMenuLeft', \Request::route()->getName());
        $data['blockBottom'] = sc_config_group('blockBottom', \Request::route()->getName());

        $listTh = [
            'id' => 'ID',
            'customer_name' => 'Tên khách hàng',
            'explain' => 'Diễn giải',
            'driver_name' => 'NV Giao hàng',
            'delivery_date' => 'Ngày giao hàng',
            'export_date' => 'Ngày xuất khô',
            'delivery_status' => 'Trạng thái',
            'type' => 'Loại đơn',
            'action' => sc_language_render('action.title'),
        ];
        $cssTh = [
            'id' => 'min-width: 90px;',
            'customer_name' => 'min-width: 200px; text-align: center;',
            'explain' => 'min-width: 100px; text-align: center;',
            'driver_name' => 'min-width: 200px; text-align: center;',
            'delivery_date' => 'min-width: 150px; text-align: center;',
            'export_date' => 'min-width: 150px; text-align: center;',
            'delivery_status' => 'min-width: 150px; text-align: center;',
            'type' => 'text-align: center; max-width: 60px; min-width:60px',
            'action' => 'min-width: 100px !important; max-width: 100px !important; width: 100px !important; text-align: center;',
        ];
        //Customize collumn size and align
        $cssTd = [
            'customer_name' => 'text-align: center;',
            'explain' => 'text-align: center;',
            'driver_name' => '',
            'export_date' => 'text-align: center;',
            'delivery_date' => 'text-align: center;',
            'delivery_status' => 'text-align: center;',
            'type' => 'text-align: center;',
            'action' => 'min-width: 100px !important; max-width: 100px !important; width: 100px !important; text-align: center;',
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
            'delivery_status' => sc_clean(request('delivery_status') ?? ''),
            'drive' => sc_clean(request('drive') ?? ''),
            'limit' => sc_clean(request('limit') ?? ''),
            'code' => sc_clean(request('code') ?? ''),
            'order_type' => sc_clean(request('order_type') ?? ''),
            'order_explain' => sc_clean(request('order_explain') ?? ''),
            'option_date' => sc_clean(request('option_date') ?? '')
        ];

        $dataTmp = $this->getOrder($dataSearch);
        $styleStatus = $this->deliveryStatus;
        array_walk($styleStatus, function (&$v, $k) {
            $v = '<span style="width: 87px" class="badge badge-' . ($this->styleStatus[$k] ?? 'light') . '">' . $v . '</span>';
        });
        $dataTr = [];
        foreach ($dataTmp as $key => $row) {
            $dataMap = [
                'id' => $row->id_name,
                'customer_name' => $row->customer_name ?? '',
                'explain' => $row->explain,
                'drive_name' => $row->drive_name,
                'delivery_date' => isset($row->delivery_date) ? Carbon::make($row->delivery_date)->format('d/m/Y') : '',
                'export_date' => isset($row->export_date) ? Carbon::make($row->export_date)->format('d/m/Y') : '',
                'delivery_status' => ($styleStatus[$row['status']] ?? $row['status']),
                'type' => $row->type == 1 ? '<span class="badge badge-secondary">Nhu yếu phẩm</span>' : '<span class="badge badge-success">Món ăn</span>',
            ];
            $dataMap['action'] = '<a data-perm="davicook_order:detail" href="' . sc_route_admin('driver.order_davicook_detail', ['id' => $row['id'] ? $row['id'] : 'not-found-id']) . '"><span title="Chi tiết" type="button" class="btn btn-sm btn-primary"><i class="fa fa-edit"></i></span></a>';
            $dataTr[$row['id']] = $dataMap;
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
                               <a data-perm="order:change_drive" href="#" class="btn btn-flat btn btn-info" id="btn_chang_drive"><i class="fa fa-layer-group"></i> Chọn lại nhân viên giao hàng</a>
                            ';
        $data['drive'] = $this->drivers;
        $optionStatus = '';
        foreach ($styleStatus as $key => $status) {
            $optionStatus .= '<option  ' . (($dataSearch['delivery_status'] == $key) ? "selected" : "") . ' value="' . $key . '">' . $status . '</option>';
        }
        $orderType = [
            '0' => 'Món ăn',
            '1' => 'Nhu yếu phẩm',
        ];
        $optionOrderType = '';
        foreach ($orderType as $key => $status) {
            $optionOrderType .= '<option  '.(($dataSearch['order_type'] == $key) ? "selected" : "").' value="'.$key.'">'.$status.'</option>';
        }
        $optionExplain = '';
        $orderExplains = ShopDavicookOrder::$NOTE;
        foreach ($orderExplains as $key => $explain) {
            $optionExplain .= '<option  ' . (($dataSearch['order_explain'] == $explain) ? "selected" : "") . ' value="' . $explain . '">' . $explain . '</option>';
        }
        $optionDrive = '';
        foreach ($this->drivers as $key => $driver) {
            $optionDrive .= '<option  ' . ($dataSearch['drive'] == $driver->id ? "selected" : "") . ' value="' . $driver->id . '">' . $driver->full_name . '</option>';
        }
        $data['topMenuRight'][] = '
                <form action="'.sc_route_admin('driver.list_drive_order_davicook').'" id="button_search" autocomplete="off">
                <input type="hidden" name="limit" value="'. ($dataSearch['limit'] ?? '') .'" id="limit_paginate">
                <div class="row">
                <div class="input-group">
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
                                <label>Trạng thái giao hàng:</label>
                                <div class="input-group">
                                <select class="form-control rounded-0" name="delivery_status">
                                <option value="">---</option>
                                '.$optionStatus.'
                                </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-3 col-sm-12">
                            <div class="form-group">
                                <label>Nhân viên giao hàng:</label>
                                <div class="input-group">
                                    <select style="width: 100%" class="form-control rounded-0" name="drive" id="drive">
                                    <option value="">---</option>
                                    ' . $optionDrive . '
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-12">
                            <div class="form-group">
                                <label>Loại Đơn :</label>
                                <div class="input-group">
                                <select class="form-control rounded-0" name="order_type">
                                    <option value="">---</option>
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
                </div>
                </form>';
        //=menuSearch
        return view($this->templatePathAdmin.'screen.warehouse.drive_order.list_drive_davicook')
            ->with($data);
    }


    /**
     * @param $dataSearch
     */
    private function getOrder($dataSearch)
    {
        $keyword = $dataSearch['keyword'] ?? '';
        $from_to = $dataSearch['from_to'] ?? '';
        $limit = $dataSearch['limit'] ?? '';
        $end_to = $dataSearch['end_to'] ?? '';
        $delivery_status = $dataSearch['delivery_status'];
        $drive = $dataSearch['drive'];
        $code = $dataSearch['code'];
        $type = $dataSearch['order_type'];
        $explain = $dataSearch['order_explain'] ?? '';
        $option_date = $dataSearch['option_date'] == 2 ? 'export_date' : 'delivery_date';
        $orderList = new ShopDavicookOrder;

        if($delivery_status != ''){
            $orderList = $orderList->where('delivery_status', $delivery_status);
        }

        if($drive != ''){
            $orderList = $orderList->where('drive', $drive);
        }

        if ($keyword) {
            $orderList = $orderList->where(function ($sql) use ($keyword) {
                $sql->where('customer_name', 'like', '%'.$keyword.'%')
                    ->orWhere('customer_code', 'like', '%'.$keyword.'%');
            });
        }

        if ($code) {
            $orderList = $orderList->where('id_name', 'like', "%$code%");
        }

        if ($explain) {
            $orderList = $orderList->where('explain', $explain);
        }

        if ($type != '') {
            $orderList = $orderList->where('type', $type);
        }

        if ($from_to) {
            $from_to = Carbon::createFromFormat('d/m/Y', $from_to)->startOfDay()->toDateTimeString();
            $orderList = $orderList->whereDate($option_date, '>=', $from_to);
        }

        if ($end_to) {
            $end_to = Carbon::createFromFormat('d/m/Y', $end_to)->endOfDay()->toDateTimeString();
            $orderList = $orderList->whereDate($option_date, '<=', $end_to);
        }

        if ($limit) {
            return $orderList->paginate($limit);
        }

        return $orderList->paginate(config('pagination.admin.order'));
    }

    /**
     * Order detail
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    public function detail($id)
    {
        $order = ShopDavicookOrder::with('details', 'history', 'returnHistory')->find($id);
        if (!$order) {
            return redirect()->route('admin.data_not_found')->with(['url' => url()->full()]);
        }

        $drives = $this->drivers->pluck('full_name', 'id')->toArray();
        $dish_extra_order = ShopDavicookOrderDetail::select('dish_id','dish_name')->where('order_id',$id)->where('type',1)->distinct()->get();
        $drives[''] = "Chưa có NV giao hàng";

        $nameFile = $order->type == 1 ? 'detail_essential_davicook' : 'detail_davicook';
        $dish_order = ShopDavicookOrderDetail::select('dish_id','dish_name')->where('order_id',$id)->where('type',0)->distinct()->get();
        return view($this->templatePathAdmin . 'screen.warehouse.drive_order.' . $nameFile)->with(
            [
                "title" => 'Chi tiết đơn hàng',
                "subTitle" => '',
                'icon' => 'fa fa-file-text-o',
                "order" => $order,
                'dish_extra_order' => $dish_extra_order,
                'dish_order' => $dish_order,
                "delivery_status" => $this->deliveryStatus,
                'drivers' => $drives,
            ]
        );
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function changeDrive()
    {

        DB::beginTransaction();
        try {
            if (!request('pk')) {
                $ids = explode(',', request('ids'));
            } else {
                $ids = explode(',', request('pk'));
            }
            $driveId = request('drive_id') ?? request('value');
            $drive = AdminDriver::find($driveId);
            $user = Admin::user();
            $orders = ShopDavicookOrder::whereIn('id', $ids)->get();
            foreach ($orders as $order) {
                if ($order->drive_id == '') {
                    $content = "Thêm mới Nv giao hàng: " . $drive->full_name;
                } else {
                    $content = "Thay đổi NV giao hàng: " .$order->drive_name . ' -> '. $drive->full_name;
                }
                $order->drive_id = $drive->id;
                $order->drive_code = $drive->id_name;
                $order->drive_address = $drive->address;
                $order->drive_name = $drive->full_name;
                $order->drive_phone = $drive->phone;
                $order->save();
                $title = 'Thay đổi NV giao hàng';
                $this->storeHistoryOrder($user, $order, $title, $content);
            }
            DB::commit();
            return response()->json(['error' => 0, 'msg' => "Thay đổi thành công!"]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['error' => 1, 'msg' => $e->getMessage()]);
        }
    }

    public function storeHistoryOrder($user, $order, $title, $content)
    {
        $dataHistory = [
            'order_id' => $order->id,
            'title' => $title,
            'content' => $content,
            'admin_id' => $user->id,
            'order_status_id' => 1,
            'add_date' => now()
        ];

        ShopDavicookOrderHistory::create($dataHistory);
    }

}
