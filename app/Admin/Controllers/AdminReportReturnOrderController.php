<?php

namespace App\Admin\Controllers;

use App\Admin\Models\AdminDavicookOrder;
use App\Admin\Models\AdminOrder;
use App\Admin\Models\AdminSystemChangeHistory;
use App\Exports\ReturnOrder\AdminReportReturnOrder;
use App\Front\Models\ShopOrder;
use Dompdf\Dompdf;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use SCart\Core\Admin\Admin;
use SCart\Core\Admin\Controllers\RootAdminController;
use Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class AdminReportReturnOrderController extends RootAdminController{

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Report two targets.
     */
    public function index(){
        $data = [
            // ,
            'title' => sc_language_render('admin.report_return_order.title'),
            'subTitle' => '',
            'icon' => 'fa fa-indent',
            'removeList' => '', // 1 - Enable function delete list item
            'buttonRefresh' => '', // 1 - Enable button refresh
            'buttonSort' => '', // 1 - Enable button sort
            'css' => '',
            'js' => '',
        ];

        $listTh = [
            'date' => 'Ngày',
            'customer_code' => 'Mã khách hàng',
            'customer_name' => 'Tên khách hàng',
            'id_name' => 'Mã đơn hàng',
            'explain' => 'Diễn giải',
            'product_code' => 'Mã sản phẩm',
            'product_name' => 'Tên mặt hàng',
            'product_unit' => 'Đvt',
            'qty' => 'Số lượng',
            'price' => 'Đơn giá',
            'total_price' => 'Thành tiền',
        ];
        $keyword = sc_clean(request('keyword') ?? '');
        $dataSearch = [
            'keyword' => $keyword,
            'from_to' => sc_clean(request('from_to') ?? ''),
            'end_to' => sc_clean(request('end_to') ?? ''),
            'explain' => sc_clean(request('explain') ?? ''),
            'key_export' => sc_clean(request('key_export') ?? 0),
            'key_search' => sc_clean(request('key_search') ?? ''),
        ];
        $data['menuLeft'] = sc_config_group('menuLeft', Request::route()->getName());
        $dataTr = [];
        $cssTh = [
            'date' => 'text-align: center; min-width: 100px',
            'customer_code' => 'text-align: center; min-width: 90px; white-space:normal',
            'customer_name' => 'text-align: center; width: 25%',
            'id_name' => 'text-align: center; min-width: 95px; white-space:normal',
            'explain' => 'text-align: center; width: 9%',
            'product_code' => 'text-align: center; min-width: 70px; white-space:normal',
            'product_name' => 'text-align: center; width: 15%',
            'product_unit' => 'text-align: center; min-width: 55px',
            'qty' => 'text-align: center; width: 7%',
            'price' => 'text-align: center; min-width: 92px',
            'total_price' => 'text-align: center; max-width: 92px; min-width: 0',

        ];
        $cssTd = [
            'STT' => 'text-align: center',
            'product_sku' => 'text-align: center',
            'product_name' => '',
            'price' => 'text-align: center',
            'total_price' => 'text-align: center',
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
        $countData = $dataOrderMerge->count();
        $sorted = $dataOrderMerge->sortBy(['delivery_date', 'customer_code', 'product_name']);
        $dataOrderMergePaginate = $this->paginate($sorted);
        foreach ($dataOrderMergePaginate as $key => $row) {
            $dataTr[] = [
                'date' => Carbon::make($row['delivery_date'])->format('d/m/Y'),
                'customer_code' => $row['customer_code'],
                'customer_name' => $row['customer_name'],
                'id_name' => $row['id_name'],
                'explain' => $row['explain'],
                'product_code' => $row['product_code'],
                'product_name' => $row['product_name'],
                'product_unit' => $row['product_unit'],
                'qty' => (isset($row['created_at']) ? '-' : '') . number_format($row['qty'], 2),
                'price' => number_format($row->price),
                'total_price' => (isset($row['created_at']) ? '-' : '') . number_format($row['qty'] * $row['price']),
            ];
        }

        $page = request('page') ?? 1;
        $data['dataTr'] = $dataTr;

        $ofsetStart = ($page - 1) * config('pagination.search.default');
        $ofsetEnd = ($page - 1) * config('pagination.search.default') + count($dataOrderMergePaginate);
        $data['ofsetEnd'] = $ofsetEnd;
        $data['ofsetStart'] = $ofsetStart;
        $data['listTh'] = $listTh;

        $orderExplains = ShopOrder::$NOTE;
        $optionExplain = '';
        foreach ($orderExplains as $key => $explain) {
            $optionExplain .= '<option  ' . (($dataSearch['explain'] == $explain) ? "selected" : "") . ' value="' . $explain . '">' . $explain . '</option>';
        }
        $typeSearch = [
            0 => 'Tổng hợp',
            1 => 'Mặt hàng Davicorp',
            2 => 'Mặt hàng Davicook',
        ];
        $optionTypeSearch = '';
        foreach ($typeSearch as $key => $value) {
            $optionTypeSearch .= '<option  ' . (($dataSearch['key_export'] == $key) ? "selected" : "") . ' value="' . $key . '">' . $value . '</option>';
        }

        $data['pagination'] = $dataOrderMergePaginate->appends(request()->except(['_token', '_pjax']))->links($this->templatePathAdmin . 'component.pagination');
        $data['resultItems'] = sc_language_render('admin.result_item', ['item_from' => $ofsetStart ? $ofsetStart : 0, 'item_to' =>(empty($check)) ? $ofsetEnd : count($dataOrderMerge), 'total' => count($dataOrderMerge)]);
        $currentDay = nowDateString();
        $from_day = $dataSearch['from_to'] ? $dataSearch['from_to'] : $currentDay ;
        $end_day = $dataSearch['end_to'] ? $dataSearch['end_to'] : $currentDay ;
        $data['topMenuRight'][] = '
                <form action="' . sc_route_admin('admin_report_return_order.index') . '" id="button_search">
                <input type="hidden" name="key_search" value="search" id="key_search">
                <input type="hidden" name="data_count" value="' . $countData . '" id="data_count">
                <br>
                    <div class="input-group float-right">
                        <div class="row">
                            <div class="col-lg-7 col-md-7 col-sm-12">
                                <div class="row">
                                    <div class="col-lg-3 col-md-3 col-sm-12">
                                        <div class="form-group">
                                            <div class="input-group">
                                                <select class="form-control select2" name="key_export" id="key_export">
                                                    ' . $optionTypeSearch . '
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-3 col-md-3 col-sm-12">
                                        <div class="form-group">
                                            <div class="input-group">
                                                <select class="form-control select2" name="explain" id="explain">
                                                    <option value="">Tất cả</option>
                                                    ' . $optionExplain . '
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-3 col-md-3 col-sm-12">
                                        <div class="form-group" >
                                            <div class="input-group ">
                                                <input type="text" name="from_to" id="from_to" class="form-control input-sm date_time rounded-0" style="text-align: center" placeholder="Chọn ngày" value="' . $from_day . '" />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-3 col-md-3 col-sm-12">
                                        <div class="form-group">
                                            <div class="input-group">
                                                <div style="margin-top: 10px;margin-right: 6px;color: #93A7C1"">Đến</div>
                                                <input type="text" name="end_to" id="end_to" class="form-control input-sm date_time rounded-0" style="text-align: center" placeholder="Chọn ngày" value="' . $end_day . '"  />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-3 col-sm-12">
                                <div class="form-group">
                                    <div class="input-group">
                                        <input type="text" name="keyword" id="keyword" class="form-control rounded-0 float-right" placeholder="' . sc_language_render('admin.report_return_order.search_placeholder') . '" value="' . $keyword . '">
                                        <div class="input-group-append">
                                            <button type="submit" class="btn btn-primary  btn-flat" id="submit_report"><i class="fas fa-search"></i></button>
                                        </div>
                                    </div>
                                </div>
                            </div>                     
                            <div class="col-lg-2 col-md-2 col-sm-12" style="max-width: 115px">
                                <div class="row" >
                                    <div class="form-group">
                                        <div class="input-group">
                                            <a class="btn btn-success btn-flat" title="" id="button_export_filter">
                                                <i class="fa fa-file-export" title="' . sc_language_render('category-export') . '"></i> ' . sc_language_render('category-export') . '</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
                ';

        //=menuSearch
        return view($this->templatePathAdmin . 'screen.list_report_return_order')
            ->with($data);

    }

    /**
     * Xuất excel phiếu trả hàng.
     * @param  int  $keyExport
     * @return \Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function exportExcelReturnOrder()
    {
        $dataSearch = [
            'from_to' => sc_clean(request('from_to') ?? ''),
            'end_to' => sc_clean(request('end_to') ?? ''),
            'keyword' => sc_clean(request('keyword') ?? ''),
            'category' => sc_clean(request('category') ?? ''),
            'explain' => sc_clean(request('explain') ?? ''),
            'keyExport' => sc_clean(request('key_export')) ?? 0,
            'key_search' => sc_clean(request('key_search') ?? ''),
        ];

        $dataOrderMerge = $this->getDataSearch($dataSearch['keyExport'], $dataSearch);

        if (!count($dataOrderMerge) > 0) {
            return redirect()->route('admin_report_return_order.index')->with('error' , 'Không có dữ liệu');
        }

        $sorted = $dataOrderMerge->sortBy(['delivery_date', 'customer_code', 'product_name']);
        $date['start'] = $dataSearch['from_to'] ?? \Carbon\Carbon::make($dataSearch['from_to'])->format("d/m/Y");
        $date['end'] = $dataSearch['end_to'] ?? \Carbon\Carbon::make($dataSearch['end_to'])->format("d/m/Y");
        $fileName = 'PhieuTraHang-'. $date['start'] .'-'.$date['end']. '.xlsx';
        $fileName = str_replace("/","_",$fileName);

        $attributes = [
            'name_admin' =>  Admin::user()->name,
            'time' =>  now(),
            'desc' =>  $fileName,
            'type' =>  'BC Tra Hàng',
            'kind' =>  'Xuất Excel',
        ];
        AdminSystemChangeHistory::storeData($attributes);

        return Excel::download(new AdminReportReturnOrder($sorted, $date), $fileName);
    }

    /**
     * Handel search data davicorp + davicook.
     * @param $keyExport
     * @param $dataSearch
     * @return \Illuminate\Database\Eloquent\Collection|mixed
     */
    public function getDataSearch($keyExport, $dataSearch) {
        if ($keyExport == 2) {
            // Lấy trả hàng davicook.
            $dataOrderMerge = (new AdminDavicookOrder())->getOrderReturnHistoryDavicook($dataSearch);
        } elseif ($keyExport == 1) {
            // Lấy hàng tươi và khô Davicorp.
            $dataOrderMerge = (new AdminOrder())->getOrderReturnHistoryDavicorp($dataSearch);
        } else {
            // Lấy tổng hợp Khô và tươi davicorp + davicook.
            $dataReturnOrderDavicook = (new AdminDavicookOrder())->getOrderReturnHistoryDavicook($dataSearch);
            $dataReturnOrderDavicorp = (new AdminOrder())->getOrderReturnHistoryDavicorp($dataSearch);
            $dataOrderMerge = $dataReturnOrderDavicorp->mergeRecursive($dataReturnOrderDavicook);
        }

        return $dataOrderMerge;
    }

    public function paginate($items, $perPage = 20, $page = null, $options = ['path' => ''])
    {
        $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);
        $items = $items instanceof Collection ? $items : Collection::make($items);
        return new LengthAwarePaginator($items->forPage($page, $perPage), $items->count(), $perPage, $page, $options);

    }
}
