<?php

namespace App\Admin\Controllers;

use App\Admin\Models\AdminCustomer;
use App\Front\Models\ShopCustomer;
use App\Front\Models\ShopDavicookCustomer;
use App\Front\Models\ShopDavicookOrder;
use App\Front\Models\ShopOrder;
use Illuminate\Support\Carbon;
use SCart\Core\Admin\Controllers\RootAdminController;
use App\Admin\Models\AdminOrder;

class DashboardController extends RootAdminController
{
    public function __construct()
    {
        parent::__construct();
    }
    
    public function index()
    {
        //Check user allow view dasdboard
        if (!\Admin::user()->checkUrlAllowAccess(route('admin.home'))) {
            $data['title'] = sc_language_render('admin.dashboard');
            return view($this->templatePathAdmin.'default', $data);
        }
        $dataSearch = [
            'customer_status' => sc_clean(request('customer_status') ?? ''),
            'keyword' => sc_clean(request('keyword') ?? ''),
            'delivery_date' => sc_clean(request('delivery_date') ?? '')
        ];
        $currentDay = request('delivery_date') != '' ? convertVnDateObject(request('delivery_date'))->startOfDay()->toDateTimeString() :  Carbon::today()->toDateString();

//        $startToDay = Carbon::createFromFormat('Y-m-d', carbon::today()->toDateString())->startOfDay()->toDateTimeString();
//        $endToDay = Carbon::createFromFormat('Y-m-d', carbon::today()->toDateString())->endOfDay()->toDateTimeString();
        $totalCustomerDavicorpId = ShopCustomer::where('status',1)->get()->pluck('id');
        $totalCustomerDavicorp = ShopCustomer::where('status',1)->count();
        $totalCustomerDavicookId = ShopDavicookCustomer::where('status',1)->get()->pluck('id');
        $totalCustomerDavicook = ShopDavicookCustomer::where('status',1)->count();
        $totalCustomerDavicorpInDay = ShopOrder::whereDate('delivery_time', $currentDay)->whereIn('status', [1,2])
            ->whereIn('customer_id', $totalCustomerDavicorpId)
            ->groupBy('customer_id')->get()->count();
//        $totalCustomerDavicookInDay = ShopDavicookOrder::whereBetween('delivery_date', [$startToDay, $endToDay])->whereIn('status', [0,1,2])
        $totalCustomerDavicookInDay = ShopDavicookOrder::whereDate('delivery_date', $currentDay)->whereIn('status', [0,1,2])
            ->whereIn('customer_id', $totalCustomerDavicookId)
            ->groupBy('customer_id')->get()->count();
        $customerList = (new AdminCustomer)->getCustomerOrderInToday($dataSearch);
        $data['delivery_date'] = request('delivery_date') != '' ?   str_replace('/', '%2F', $dataSearch['delivery_date']) : str_replace('/', '%2F',nowDateString());
        $data['currentDay'] = $currentDay;
        $data['totalCustomerDavicorp'] = $totalCustomerDavicorp;
        $data['totalCustomerDavicook'] = $totalCustomerDavicook;
        $data['totalCustomerDavicorpInDay'] = $totalCustomerDavicorpInDay;
        $data['totalCustomerDavicookInDay'] = $totalCustomerDavicookInDay;
        //Order in 30 days
        $totalsInMonth = AdminOrder::getSumOrderTotalInMonth()->keyBy('md')->toArray();
        $rangDays = new \DatePeriod(
            new \DateTime('-1 month'),
            new \DateInterval('P1D'),
            new \DateTime('+1 day')
        );
        $orderInMonth  = [];
        $amountInMonth  = [];
        foreach ($rangDays as $day) {
            $date = $day->format('m-d');
            $orderInMonth[$date] = $totalsInMonth[$date]['total_order'] ?? '';
            $amountInMonth[$date] = (int)($totalsInMonth[$date]['total_amount'] ?? 0);
        }
        $data['orderInMonth'] = $orderInMonth;
        $data['amountInMonth'] = $amountInMonth;
        $currentDayFormat = nowDateString();

        $from_day = $dataSearch['delivery_date'] ? $dataSearch['delivery_date'] : $currentDayFormat ;
        $customerStatus = [
            1 => 'Chưa đặt hàng',
            2 => 'Đã đặt hàng',
            3 => 'Tất cả trạng thái',
        ];
        $optionCutomer = '';
        foreach ($customerStatus as $key => $department) {
            $optionCutomer .= '<option  ' . (($dataSearch['customer_status'] == $key) ? "selected" : "") . ' value="' . $key . '">' . $department . '</option>';
        }
        $data['pagination'] = $customerList->appends(request()->except(['_token', '_pjax']))->links($this->templatePathAdmin . 'component.pagination');
        $data['dataCustomers'] = $customerList;
        $data['resultItems'] = sc_language_render('admin.result_item', ['item_from' => $customerList->firstItem(), 'item_to' => $customerList->lastItem(), 'total' => $customerList->total()]);
        $data['topMenuRight'][] = '
                <form action="' . sc_route_admin('admin.home') . '" id="button_search" autocomplete="off">
                    <div class="input-group float-left">
                        <div class="col-md-6">
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Ngày giao hàng</label>
                                <div class="input-group">
                                    <input type="text" name="delivery_date" id="from_to" class="form-control input-sm date_time rounded-0" style="text-align: center" placeholder="Chọn ngày" value="' . $from_day . '" />
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Trạng thái</label>
                                <div class="input-group">
                                    <select class="form-control rounded-0" name="customer_status">
                                        ' . $optionCutomer . '
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Tên KH, Mã Đơn</label>
                                    <div class="input-group">
                                        <input type="text" name="keyword" class="form-control rounded-0 float-right" placeholder="Tên khách hàng" value="' . $dataSearch['keyword'] . '">
                                        <div class="input-group-append">
                                            <button id="btn-submit-search" type="submit" class="btn btn-primary btn-flat"><i class="fas fa-search"></i></button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>';

        //End order in 30 days
       
        return view($this->templatePathAdmin.'dashboard', $data);
    }


    /**
     * Page not found
     *
     * @return  [type]  [return description]
     */
    public function dataNotFound()
    {
        $data = [
            'title' => sc_language_render('admin.data_not_found'),
            'icon' => '',
            'url' => session('url'),
        ];
        return view($this->templatePathAdmin.'data_not_found', $data);
    }


    /**
     * Page deny
     *
     * @return  [type]  [return description]
     */
    public function deny()
    {
        $data = [
            'title' => sc_language_render('admin.deny'),
            'icon' => '',
            'method' => session('method'),
            'url' => session('url'),
        ];
        return view($this->templatePathAdmin.'deny', $data);
    }

    /**
     * [denySingle description]
     *
     * @return  [type]  [return description]
     */
    public function denySingle()
    {
        $data = [
            'method' => session('method'),
            'url' => session('url'),
        ];
        return view($this->templatePathAdmin.'deny_single', $data);
    }
}
