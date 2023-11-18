<?php

namespace App\Admin\Controllers\Warehouse\OrderDriver;

use App\Admin\Models\AdminDriver;
use App\Admin\Models\AdminOrder;
use App\Front\Models\ShopCustomer;
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
use function Spatie\Ignition\ErrorPage\title;

class DriverOrderDavicorpController extends RootAdminController
{
    public $drivers;
    public $styleStatus;
    public $deliveryStatus;
    public $orderObjects;
    public $orderCustomer;
    public $statusShipping;

    use OrderTraits;
    public function __construct()
    {
        parent::__construct();
        $this->deliveryStatus = ShopOrder::$DELIVERY_STATUS;
        $this->styleStatus = ShopOrder::$STYLE_STATUS;
        $this->orderObjects = ShopOrderObject::getIdAll();
        $this->orderCustomer = ShopCustomer::getIdAll();
        $this->drivers = AdminDriver::all();
    }

    /**
     * Index interface.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function index()
    {
        $data = [
            'title' => 'Quản lý đơn hàng cho thủ kho/ nhân viên giao hàng',
            'subTitle' => '',
            'icon' => 'fa fa-indent',
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
            'driver_name' => 'NV Giao hàng',
            'bill_date' => 'Ngày trên đơn',
            'delivery_time' => 'Ngày giao hàng',
            'delivery_status' => 'Trạng thái',
            'action' => sc_language_render('action.title'),
        ];
        $cssTh = [
            'id' => 'min-width: 100px; text-align: center;',
            'customer_name' => 'min-width: 200px; text-align: center;',
            'object' => 'min-width: 100px; text-align: center;',
            'explain' => 'min-width: 100px; text-align: center;',
            'driver_name' => 'min-width: 200px; text-align: center;',
            'bill_date' => 'min-width: 150px; text-align: center;',
            'delivery_time' => 'min-width: 150px; text-align: center;',
            'delivery_status' => 'min-width: 150px; text-align: center;',
            'action' => 'min-width: 100px !important; max-width: 100px !important; width: 100px !important; text-align: center;',
        ];
        $data['cssTh'] = $cssTh;

        //Customize collumn size and align
        $cssTd = [
            'customer_name' => 'text-align: center;',
            'object' => 'text-align: center;',
            'explain' => 'text-align: center;',
            'driver_name' => '',
            'bill_date' => 'text-align: center;',
            'delivery_time' => 'text-align: center;',
            'delivery_status' => 'text-align: center;',
            'action' => 'min-width: 100px !important; max-width: 100px !important; width: 100px !important; text-align: center;',
        ];
        $data['cssTd'] = $cssTd;
        //Search
        $dataSearch = [
            'keyword' => sc_clean(request('keyword') ?? ''),
            'start_date' => sc_clean(request('start_date')),
            'end_date' => sc_clean(request('end_date')),
            'delivery_status' => sc_clean(request('delivery_status') ?? ''),
            'order_department' => sc_clean(request('order_department') ?? ''),
            'order_explain' => sc_clean(request('order_explain') ?? ''),
            'order_object' => sc_clean(request('order_object') ?? ''),
            'limit' => sc_clean(request('limit') ?? ''),
            'code' => sc_clean(request('code') ?? ''),
            'customer' => sc_clean(request('customer') ?? []),
            'drive' => sc_clean(request('drive') ?? ''),
            'option_date' => sc_clean(request('option_date') ?? '')
        ];

        $dataTmp = $this->getOrder($dataSearch);
        $nameUrl = URL::full();
        session()->put('nameUrl', $nameUrl);
        $styleStatus = $this->deliveryStatus;
        array_walk($styleStatus, function (&$v, $k) {
            $v = '<span style="width: 87px" class="badge badge-' . ($this->styleStatus[$k] ?? 'light') . '">' . $v . '</span>';
        });
        $dataTr = [];
        $id = 0;
        foreach ($dataTmp as $key => $row) {
            $id++;
            $dataMap = [
                'id' => $row->id_name,
                'customer_name' => $row->name ?? '',
                'object' => $row->object->name ?? '',
                'explain' => $row->explain ?? '',
                'driver_name' => $row->drive_name ?? '',
                'bill_date' => Carbon::make($row->bill_date ?? '')->format('d/m/Y'),
                'delivery_time' => Carbon::make($row->delivery_time ?? '')->format('d/m/Y'),
                'delivery_status' => $styleStatus[$row->delivery_status],
            ];

            $dataMap['action'] = '
                <a data-perm="order:detail" href="' . sc_route_admin('driver.order_davicorp_detail', ['id' => $row->id ? $row->id : 'not-found-id']) . '"><span title="' . sc_language_render('action.edit') . '" type="button" class="btn btn-flat btn-sm btn-primary"><i class="fa fa-edit"></i></span></a>
                ';
            $dataTr[$row->id] = $dataMap;
        }

        $data['listTh'] = $listTh;
        $data['dataTr'] = $dataTr;
        $data['pagination'] = $dataTmp->appends(request()->except(['_token', '_pjax']))->links($this->templatePathAdmin . 'component.pagination');
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

        $optionDepartment = '';
        foreach ($departments as $key => $department) {
            $optionDepartment .= '<option  ' . (($dataSearch['order_department'] == $key) ? "selected" : "") . ' value="' . $key . '">' . $department->name . '</option>';
        }

        $customers = ShopCustomer::where('status', 1)->get();
        $optionCustomer = '';
        foreach ($customers as $key => $customer) {
            $optionCustomer .= '<option  ' . (is_array($dataSearch['customer']) ? (in_array($customer->customer_code, $dataSearch['customer']) ? "selected" : "") : '' ) . ' value="' . $customer->customer_code . '">' . $customer->name . '</option>';
        }

        $optionDrive = '';
        foreach ($this->drivers as $key => $driver) {
            $optionDrive .= '<option  ' . ($dataSearch['drive'] == $driver->id ? "selected" : "") . ' value="' . $driver->id . '">' . $driver->full_name . '</option>';
        }

        $orderExplains = ShopOrder::$NOTE;
        $optionExplain = '';
        foreach ($orderExplains as $key => $explain) {
            $optionExplain .= '<option  ' . (($dataSearch['order_explain'] == $explain) ? "selected" : "") . ' value="' . $explain . '">' . $explain . '</option>';
        }
        $optionObject = '';
        foreach ($this->orderObjects as $key => $object) {
            $optionObject .= '<option  ' . (($dataSearch['order_object'] == $key) ? "selected" : "") . ' value="' . $key . '">' . $object . '</option>';
        }
        $data['topMenuRight'][] = '
                <form action="' . sc_route_admin('driver.list_drive_order_davicorp') . '" id="button_search" autocomplete="off">
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
                                        <input type="text" name="start_date" id="start_date" class="form-control input-sm date_time rounded-0"  placeholder="Chọn ngày" value="' . request('start_date') . '"/> 
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-2 col-md-1 col-sm-6">
                                    <div class="form-group">
                                        <label>' . sc_language_render('action.to') . ':</label>
                                        <div class="input-group">
                                        <input type="text" name="end_date" id="end_date" class="form-control input-sm date_time rounded-0"  placeholder="Chọn ngày" value="' . request('end_date') . '"/> 
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
                                        <label>Trạng thái giao hàng:</label>
                                        <div class="input-group">
                                            <select class="form-control rounded-0" name="delivery_status">
                                            <option value="">---</option>
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
                                        <label>Nhân viên giao hàng:</label>
                                        <div class="input-group">
                                            <select style="width: 100%" class="form-control rounded-0" name="drive" id="drive">
                                            <option value="">---</option>
                                            ' . $optionDrive . '
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
        return view($this->templatePathAdmin . 'screen.warehouse.drive_order.list_drive_davicorp')
            ->with($data);
    }

    /**
     * @param $dataSearch
     */
    private function getOrder($dataSearch)
    {
        $department = $dataSearch['order_department'] ?? '';
        $start_date = $dataSearch['start_date'] ?? '';
        $end_date = $dataSearch['end_date'] ?? '';
        $limit = $dataSearch['limit'] ?? '';
        $order_object = $dataSearch['order_object'];
        $order_explain = $dataSearch['order_explain'];
        $delivery_status = $dataSearch['delivery_status'];
        $code = $dataSearch['code'];
        $customer_id = $dataSearch['customer'];
        $drive = $dataSearch['drive'];
        $option_date = $dataSearch['option_date'] == 2 ? 'bill_date' : 'delivery_time';
        $orderList = (new ShopOrder);
        if ($code) {
            $orderList = $orderList->where(function ($sql) use ($code) {
                $sql->where('id_name', 'like', '%'.$code.'%')
                    ->orWhere('name', 'like', '%'.$code.'%')
                    ->orWhere('customer_code', 'like', '%'.$code.'%');
            });
        }

        if ($delivery_status != '') {
            $orderList = $orderList->where('delivery_status', (int)$delivery_status);
        }

        if ($customer_id) {
            $orderList = $orderList->whereIn('customer_code', $customer_id);
        }

        if ($drive) {
            $orderList = $orderList->where('drive_id', $drive);
        }

        if ($order_object != '') {
            $orderList = $orderList->where('object_id', (int)$order_object);
        }

        if ($order_explain != '') {
            $orderList = $orderList->where('explain', $order_explain);
        }

        if ($start_date) {
            $from_to = convertVnDateObject($start_date)->startOfDay()->toDateTimeString();
            $orderList = $orderList->whereDate($option_date, '>=', $from_to);
        }

        if ($end_date) {
            $end_date = convertVnDateObject($end_date)->endOfDay()->toDateTimeString();
            $orderList = $orderList->where($option_date, '<=', $end_date);
        }

        if ($department) {
            $orderList = $orderList->whereHas('customer', function ($query) use ($department) {
                $query->where('department_id', '=', $department);
            });
        }
        if ($limit) {
            return $data = $orderList->orderBy('created_at', 'desc')->paginate($limit);
        }
        return $data = $orderList->orderBy('created_at', 'desc')->paginate(config('pagination.admin.order'));
    }

    /**
     * Order detail
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    public function detail($id)
    {
        $order = ShopOrder::with('details', 'history', 'returnHistory')->find($id);

        if (!$order) {
            return redirect()->route('admin.data_not_found')->with(['url' => url()->full()]);
        }
        $drives = $this->drivers->pluck('full_name', 'id')->toArray();
        $drives[''] = "Chưa có NV giao hàng";
        $delivery_status = $this->deliveryStatus;
        return view($this->templatePathAdmin . 'screen.warehouse.drive_order.detail_davicorp')->with(
            [
                "title" => 'Chi tiết đơn hàng',
                "subTitle" => '',
                'icon' => 'fa fa-file-text-o',
                "order" => $order,
                "delivery_status" => $delivery_status,
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
            $orders = ShopOrder::whereIn('id', $ids)->get();
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
            'order_code' => $order->id_name,
            'title' => $title,
            'content' => $content,
            'admin_id' => $user->id,
            'user_name' => $user->name,
            'is_admin' => 1,
            'order_status_id' => 1,
        ];
        ShopOrderHistory::create($dataHistory);
    }
}
