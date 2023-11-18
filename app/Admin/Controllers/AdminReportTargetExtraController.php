<?php

namespace App\Admin\Controllers;

use App\Admin\Models\AdminExport;
use App\Admin\Models\AdminExportDetail;
use App\Admin\Models\AdminExportHistory;
use App\Admin\Models\AdminShopOrderChangeExtra;
use App\Admin\Models\AdminSystemChangeHistory;
use App\Admin\Models\AdminWarehouse;
use App\Admin\Models\AdminWarehouseProduct;
use App\Admin\Models\ReportWarehouseCard;
use App\Exports\AdminReportTargetExport;
use App\Admin\Models\AdminDavicookOrder;
use App\Admin\Models\AdminOrder;
use App\Front\Models\ShopCategory;
use App\Front\Models\ShopDepartment;
use App\Front\Models\ShopOrderDetail;
use App\Front\Models\ShopOrderHistory;
use App\Front\Models\ShopProduct;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Request;
use Maatwebsite\Excel\Facades\Excel;
use mysql_xdevapi\Exception;
use SCart\Core\Admin\Admin;
use SCart\Core\Admin\Controllers\RootAdminController;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class AdminReportTargetExtraController extends RootAdminController{
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
    public function index(Request $request){
        $data = [
            // ,
            'title' => sc_language_render('admin.target.report.title'),
            'subTitle' => '',
            'icon' => 'fa fa-indent',
            'urlDeleteItem' => sc_route_admin('admin_price.delete'),
            'removeList' => 1, // 1 - Enable function delete list item
            'buttonRefresh' => '', // 1 - Enable button refresh
            'buttonSort' => '', // 1 - Enable button sort
            'css' => '',
            'js' => '',
            'url_action' => sc_route_admin('admin_price.create'),
        ];

        $listTh = [
            'STT' => sc_language_render('admin.report.stt'),
            'product_sku'      => sc_language_render('customer.admin.customer_code'),
            'product_name' => sc_language_render('admin.order.customer_name'),
            'send_time' => 'Thời gian gửi',
            'status' => 'Trạng thái',
            'content' => 'Nội dung',
            'qty'   => 'Số lượng',
            'note'    => sc_language_render('order.note')
        ];
        $dataSearch = [
            'keyword' => sc_clean(request('keyword') ?? ''),
            'key_export' => sc_clean(request('key_export') ?? []),
            'date_start' => sc_clean(request('date_start') ?? ''),
            'date_end' => sc_clean(request('date_end') ?? ''),
            'category' => sc_clean(request('category') ?? ''),
            'check_filter' => sc_clean(request('check_filter') ?? ''),
            'order_send_time_from' => sc_clean(request('order_send_time_from') ?? ''),
            'order_send_time_to' => sc_clean(request('order_send_time_to') ?? ''),
            'content' => sc_clean(request('content') ?? '')
        ];
        $data['menuLeft'] = sc_config_group('menuLeft', Request::route()->getName());
        $dataTr = [];
        $cssTh = [
            'product_id'=>'display:none',
            'detail_id'=>'display:none',
            'check_product_id'=>'display:none',
            'STT' => 'text-align: center; width: 5%',
            'product_sku' => 'text-align: center; width: 15%',
            'product_name' => 'text-align: center; width: 15%',
            'send_time' => 'text-align: center; width: 12%',
            'status' => 'text-align: center; width: 20%',
            'content' => 'text-align: center; width: 20%',
            'qty' => 'text-align: center; width: 7%',
            'note' => 'text-align: center; width: 25%'
        ];
        $cssTd = [
            'product_id'=>'display:none',
            'detail_id'=>'display:none',
            'check_product_id'=>'display:none',
            'STT' => 'text-align: center',
            'product_sku' => 'text-align: center',
            'product_name' => '',
            'send_time' => '',
            'status' => '',
            'content' => '',
            'qty' => '',
            'note' => ''
        ];

        $data['cssTh'] = $cssTh;
        $data['cssTd'] = $cssTd;
        if (!empty($dataSearch['check_filter'])) {
            $dataOrderMerge = $this->getDataByFilter($dataSearch['key_export'], $dataSearch);
        } else {
            $dataOrderMerge = new Collection();
        }
        $dataTmp = null;
        $dataGroupProductById = [];

        $countData = $dataOrderMerge->count();
        setlocale(LC_COLLATE, 'vi_VN.UTF-8', 'vi.UTF-8', 'vi_VN', 'vi');
        $sorted = $dataOrderMerge->sortByDesc('created_at')->sortBy(
            'product_name'
            , SORT_LOCALE_STRING);
        $arr = [];
        foreach ($sorted->groupBy('product_id') as $key => $value) {
            $dataGroupProductById[$key] = $value->sum('qty');
            foreach( $value as $k => $item) {
                $arr[$item['id_change']] = $k +1;
            }
        }
        $dataOrderMergePaginate = $this->paginate($sorted);
        $statusOrder = AdminShopOrderChangeExtra::STATUS;
        foreach ($dataOrderMergePaginate->groupBy('product_id') as $key => $row) {
            if($arr[$row->first()->id_change] == 1) {
                $dataTr[] = [
                    'STT' => '',
                    'customer_code' => '',
                    'product_name' => '<span style="font-weight: bold; text-transform: uppercase">' . ($row->first()->product_name).'</span>' ?? '',
                    'send_time' => '',
                    'status' => '',
                    'content' => '',
                    'qty'   => '<span style="font-weight: bold">' . number_format($dataGroupProductById[$key], 2)  .'</span>' ?? '',
                    'note'    => '',
                    'product_id'=>$row->first()->product_id,
                    'detail_id'=>'',
                    'check_product_id' => '',
                ];
            }

            foreach ($row as  $keyTmp => $item) {
                $dataTr[] = [
                    'STT' => $arr[$item->id_change],
                    'customer_code'      => $item->customer_code,
                    'product_name' => $item->customer_name ,
                    'send_time' => Carbon::make($item->created_at ?? '')->format('d/m/Y H:i:s'),
                    'status' => '<span class="status-'.($item->status==0 ? 1 : $item->status).'">'.$statusOrder[($item->status==0 ? 1 : $item->status)].'</span>',
                    'content' => $item->content,
                    'qty'   => number_format($item->qty, 2),
                    'note'    => $item->coment ?? '',
                    'product_id'=>'',
                    'detail_id'=>$item->id_change,
                    'check_product_id' => $row->first()->product_id

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
        $data['dataWarehouse'] = AdminWarehouse::all();

        //menuRight
        $data['menuRight'][] = '<a data-perm="report_target_extra:export" class="btn btn-success btn-flat" title="" id="button_export_filter">
                                <i class="fa fa-file-export" title="' . sc_language_render('category-export') . '"></i> ' . sc_language_render('category-export') . '</a> &nbsp;
                                <a data-perm="report_target_extra:print" href="javascript:void(0)" class="btn btn-flat btn-info" onclick="savePdf()"><i class="fas fa-file-pdf"></i>&nbsp;' . sc_language_render("admin.report.print_pdf") . '</a>&nbsp;&nbsp;
                                <a data-perm="report_target_extra:updateExport" href="javascript:void(0)" class="btn btn-flat btn-primary" onclick="updateExportWarehouse()"><i class="fa fa-edit"></i>&nbsp;Cập nhật xuất kho</a>
                                 ';

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
            $selected = ($value == $dataSearch['content']) ? 'selected' : '';
            $optionContentSearch .= '<option ' . $selected . ' value="' . $value . '">' . $value . '</option>';
        }

        $data['pagination'] = $dataOrderMergePaginate->appends(request()->except(['_token', '_pjax']))->links($this->templatePathAdmin . 'component.pagination');
        $data['resultItems'] = sc_language_render('admin.result_item', ['item_from' => $ofsetStart ? $ofsetStart : 0, 'item_to' =>(empty($check)) ? $ofsetEnd : count($dataOrderMerge), 'total' => count($dataOrderMerge)]);
        $currentDay = nowDateString();
        $date_start = $dataSearch['check_filter'] == 1 ? $dataSearch['date_start'] : $currentDay;
        $date_end = $dataSearch['check_filter'] == 1 ? $dataSearch['date_end'] : $currentDay;
        $data['topMenuRight'][] = '
                <form action="' . sc_route_admin('admin_report_2target_extra.index') . '" id="button_search">
                <input type="hidden" name="check_filter" id="check_filter" value=""/>
                <input type="hidden" name="data_count" value="' . $countData . '" id="data_count">
                <br>
                    <div class="input-group float-left" style="margin-left: 50px">
                        <div class="row">
                            <div style="width: 150px; margin: 0px 5px;">
                                <label>Mặt hàng:</label>
                                <div class="form-group">
                                    <div class="input-group">
                                        <select id="key_export" class="form-control d-none" name="key_export[]" multiple="multiple">
                                            ' . $optionTypeSearch . '
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div style="width: 200px; margin: 0px 5px;">
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
                                                <input type="text" name="order_send_time_from" id="order_send_time_from" class="form-control input-sm datepicker rounded-0" style="text-align: center" autocomplete="off" placeholder="" value="'. $dataSearch['order_send_time_from'] .'" /> 
                                            </div>
                                        </div>
                                    </div>
                                    <div class="input-group-addon" style="color: #93A7C1; margin-top: 32px">Đến</div>
                                    <div style="width: 160px; margin: 0px 5px; margin-top: 32px;">
                                        <div class="form-group">
                                            <div class="input-group">
                                                <input type="text" name="order_send_time_to" id="order_send_time_to" class="form-control input-sm datepicker rounded-0" style="text-align: center"  placeholder="" autocomplete="off" value="'. $dataSearch['order_send_time_to'] .'"  /> 
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div style="width: 200px; margin: 0px 5px;">
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
                                                <input type="text" name="date_start" id="date_start" class="form-control input-sm date_time rounded-0" style="text-align: center" autocomplete="off" placeholder="Chọn ngày" value="' . $date_start . '" /> 
                                            </div>
                                        </div>
                                    </div>
                                    <div class="input-group-addon" style="color: #93A7C1; margin-top: 32px">Đến</div>
                                    <div style="width: 130px; margin: 0px 5px; margin-top: 32px;">
                                        <div class="form-group">
                                            <div class="input-group">
                                                <input type="text" name="date_end" id="date_end" class="form-control input-sm date_time rounded-0" style="text-align: center" autocomplete="off" placeholder="Chọn ngày" value="' . $date_end . '"  /> 
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div style="width: 280px; margin-top: 32px; padding-right: 42px;">
                                <div class="form-group">
                                    <div class="input-group">
                                    <input type="text" name="keyword" id="keyword" class="form-control rounded-0 float-right" placeholder="Tên mã SP và KH, Mã đơn" value="' . $dataSearch['keyword'] . '">
                                        <div class="input-group-append">
                                            <button type="submit" class="btn btn-primary  btn-flat btn-search" id="submit_search"><i class="fas fa-search"></i></button>
                                        </div>
                                    </div> 
                                </div>
                            </div>
                        </div>
                    </div>
                </form>';

        //=menuSearch
        return view($this->templatePathAdmin . 'screen.report.target_extra.index')
            ->with($data);

    }

    public function export()
    {
        $dataSearch = [
            'from_to' => sc_clean(request('from_to') ?? ''),
            'end_to' => sc_clean(request('end_to') ?? ''),
            'keyword' => sc_clean(request('keyword') ?? ''),
            'category' => sc_clean(request('category') ?? ''),
            'keyExport' => sc_clean(request('key_export')) ?? [],
            'key_search' => sc_clean(request('key_search') ?? ''),
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
    public function printPdf()
    {
        $dataSearch = [
            'from_to' => sc_clean(request('from_to') ?? ''),
            'end_to' => sc_clean(request('end_to') ?? ''),
            'keyword' => sc_clean(request('keyword') ?? ''),
            'category' => sc_clean(request('category') ?? ''),
            'key_search' => sc_clean(request('key_search') ?? ''),
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

    /**
     * Handel search data davicorp + davicook.
     * @param $keyExport
     * @param $dataSearch
     * @return \Illuminate\Database\Eloquent\Collection|mixed
     */
    public function getDataByFilter($keyExport, $dataSearch, $ids = null)
    {
        if ((in_array(1,$keyExport)) && (count($keyExport) == 1)) {
            # Mặt hàng Davicorp.
            return $this->getData($dataSearch, null) ?? [];
        }

        if ((in_array(2,$keyExport)) && (count($keyExport) == 1)) {
            # Mặt hàng tươi Davicook.
            return $this->getData($dataSearch, 'dry') ?? [];
        }

        if ((in_array(3,$keyExport)) && (count($keyExport) == 1)) {
            # Mặt hàng khô Davicook.
            return $this->getData($dataSearch, 'fresh') ?? [];

        }

        if (empty(array_diff([1, 2], $keyExport)) && (count($keyExport) == 2)) {
            # Mặt hàng Daivcorp + Tươi Davicook.
            $getDataDavicorp = $this->getData($dataSearch, null);
            $getDetailDavicookByStatusProductFresh = $this->getData($dataSearch, 'fresh');
            return $getDataDavicorp->mergeRecursive($getDetailDavicookByStatusProductFresh);
        }

        if (empty(array_diff([1, 3], $keyExport)) && (count($keyExport) == 2)) {
            # Mặt hàng Daivcorp + Khô Davicook.
            $getDataDavicorp = $this->getData($dataSearch, null);
            $getDetailDavicookByStatusProductDry = $this->getData($dataSearch, 'dry');
            return $getDataDavicorp->mergeRecursive($getDetailDavicookByStatusProductDry);
        }

        if (empty(array_diff([2, 3], $keyExport)) && (count($keyExport) == 2)) {
            # Mặt hàng Tươi Daivcook + Khô Davicook.
            $getDetailDavicookByStatusProductDry = $this->getData($dataSearch, 'dry');
            $getDetailDavicookByStatusProductFresh = $this->getData($dataSearch, 'fresh');
            return $getDetailDavicookByStatusProductDry->mergeRecursive($getDetailDavicookByStatusProductFresh);
        }

        # Get data Corp + Tươi + Khô Cook
        $getDataDavicorp = $this->getData($dataSearch, null);

        $objDavicookOrderDetailFresh = $this->getData($dataSearch, 'fresh');
        $objDavicookOrderDetailDry = $this->getData($dataSearch, 'dry');
        $objDavicookOrderDetailMerge = $objDavicookOrderDetailFresh->mergeRecursive($objDavicookOrderDetailDry);

        return $getDataDavicorp->mergeRecursive($objDavicookOrderDetailMerge);
    }

    private function getData($dataSearch, $type)
    {
        $from_to = $dataSearch['date_start'] ?? '';
        $end_to = $dataSearch['date_end'] ?? '';
        $order_send_time_from = $dataSearch['order_send_time_from'] ?? '';
        $order_send_time_to = $dataSearch['order_send_time_to'] ?? '';
        $keyword = $dataSearch['keyword'] ?? '';
        $category = $dataSearch['category'] ?? '';
        $content = $dataSearch['content'] ?? '';
        
        $dataTmp = new AdminShopOrderChangeExtra();
        if ($from_to) {
            $from_to = convertVnDateObject($from_to)->startOfDay()->toDateString();
            $dataTmp = $dataTmp->whereDate("delivery_date_origin", ">=", $from_to);
        }

        if ($end_to) {
            $end_to = convertVnDateObject($end_to)->endOfDay()->toDateString();
            $dataTmp = $dataTmp->whereDate("delivery_date_origin", "<=", $end_to);
        }

        // Order send time
        if ($order_send_time_from) {
            $order_send_time_from = convertStandardDate($order_send_time_from)->toDateTimeString();
            $dataTmp = $dataTmp->whereDate("created_at", ">=", $order_send_time_from);
        }
        if ($order_send_time_to) {
            $order_send_time_to = convertStandardDate($order_send_time_to)->toDateTimeString();
            $dataTmp = $dataTmp->whereDate("created_at", "<=", $order_send_time_to);
        }

        if ($category) {
            $dataTmp = $dataTmp->where("category_id", $category);
        }

        if ($content) {
            $dataTmp = $dataTmp->where("content", $content);
        }

        if ($keyword) {
            $dataTmp = $dataTmp->where(function ($sql) use ($keyword) {
                $sql->where('product_name', 'like', '%' . $keyword . '%')
                    ->orWhere('order_name', 'like', '%' . $keyword . '%')
                    ->orWhere('customer_name', 'like', '%' . $keyword . '%')
                    ->orWhere('customer_code', 'like', '%' . $keyword . '%')
                    ->orWhere('product_code', 'like', '%' . $keyword . '%');
            });
        }

        if ($type == 'dry') {
            $dataTmp = $dataTmp->where('kind', 0);
        } else if ($type == 'fresh') {
            $dataTmp = $dataTmp->where('kind', 1);
        } else {
            $dataTmp = $dataTmp->where('kind', null);
        }

        $dataTmp = $dataTmp
            ->select(
                'id as id_change',
                'delivery_date_origin',
                'customer_code',
                'customer_name',
                'product_id',
                'qty',
                'content',
                'created_at',
                'product_code',
                'status',
                'product_name',
                'order_detail_id'
            )->get();

        return $dataTmp;
    }

    private function getDataDavicorp($dataSearch)
    {
        $from_to = $dataSearch['date_start'] ?? '';
        $end_to = $dataSearch['date_end'] ?? '';
        $order_send_time_from = $dataSearch['order_send_time_from'] ?? '';
        $order_send_time_to = $dataSearch['order_send_time_to'] ?? '';
        $keyword = $dataSearch['keyword'] ?? '';
        $category = $dataSearch['category'] ?? '';
        $content = $dataSearch['content'] ?? '';
        $nameTableOrderChangeExtra = SC_DB_PREFIX . 'shop_order_change_extra';

        $dataTmp = new AdminShopOrderChangeExtra();
        if ($from_to) {
            $from_to = convertVnDateObject($from_to)->startOfDay()->toDateTimeString();
            $dataTmp = $dataTmp->whereDate($nameTableOrderChangeExtra.".delivery_date_origin", ">=", $from_to);
        }

        if ($end_to) {
            $end_to = convertVnDateObject($end_to)->endOfDay()->toDateTimeString();
            $dataTmp = $dataTmp->whereDate($nameTableOrderChangeExtra.".delivery_date_origin", "<=", $end_to);
        }

        // Order send time
        if ($order_send_time_from) {
            $order_send_time_from = convertStandardDate($order_send_time_from)->toDateTimeString();
            $dataTmp = $dataTmp->whereDate("$nameTableOrderChangeExtra.created_at", ">=", $order_send_time_from);
        }
        if ($order_send_time_to) {
            $order_send_time_to = convertStandardDate($order_send_time_to)->toDateTimeString();
            $dataTmp = $dataTmp->whereDate("$nameTableOrderChangeExtra.created_at", "<=", $order_send_time_to);
        }

        if ($category) {
            $dataTmp = $dataTmp->where($nameTableOrderChangeExtra . ".category_id", $category);
        }

        if ($content) {
            $dataTmp = $dataTmp->where($nameTableOrderChangeExtra . ".content", $content);
        }

        if ($keyword) {
            $dataTmp = $dataTmp->where(function ($sql) use ($keyword) {
                $sql->where('product_name', 'like', '%' . $keyword . '%')
                    ->orWhere('so.id_name', 'like', '%' . $keyword . '%')
                    ->orWhere('customer_name', 'like', '%' . $keyword . '%')
                    ->orWhere('customer_code', 'like', '%' . $keyword . '%')
                    ->orWhere('product_code', 'like', '%' . $keyword . '%');
            });
        }

        $dataTmp = $dataTmp
            ->select(
                'id as id_change',
                'delivery_date_origin',
                'customer_code',
                'customer_name',
                'product_id',
                'qty',
                'content',
                'created_at',
                'product_code',
                'product_name'
            )->get();
        return $dataTmp;
    }

    private function getDataDavicookByProductDry($dataSearch)
    {
        $from_to = $dataSearch['date_start'] ?? '';
        $end_to = $dataSearch['date_end'] ?? '';
        $order_send_time_from = $dataSearch['order_send_time_from'] ?? '';
        $order_send_time_to = $dataSearch['order_send_time_to'] ?? '';
        $keyword = $dataSearch['keyword'] ?? '';
        $category = $dataSearch['category'] ?? '';
        $content = $dataSearch['content'] ?? '';
        $nameTableOrderChangeExtra = SC_DB_PREFIX . 'shop_order_change_extra';

        $dataTmp = AdminShopOrderChangeExtra::leftjoin(SC_DB_PREFIX . 'shop_davicook_order as so', function ($join) {
            $join->on(SC_DB_PREFIX . 'shop_order_change_extra.order_id', 'so.id');
        })
            ->leftjoin(SC_DB_PREFIX . 'shop_category as sc', function ($join) {
                $join->on('sc.id',SC_DB_PREFIX . 'shop_order_change_extra.category_id');
            });

        if ($from_to) {
            $from_to = convertVnDateObject($from_to)->startOfDay()->toDateTimeString();
            $dataTmp = $dataTmp->where($nameTableOrderChangeExtra.".delivery_date_origin", ">=", $from_to);
        }
        if ($end_to) {
            $end_to = convertVnDateObject($end_to)->endOfDay()->toDateTimeString();
            $dataTmp = $dataTmp->where($nameTableOrderChangeExtra.".delivery_date_origin", "<=", $end_to);
        }

        // Order send time
        if ($order_send_time_from) {
            $order_send_time_from = convertStandardDate($order_send_time_from)->toDateTimeString();
            $dataTmp = $dataTmp->where("$nameTableOrderChangeExtra.created_at", ">=", $order_send_time_from);
        }
        if ($order_send_time_to) {
            $order_send_time_to = convertStandardDate($order_send_time_to)->toDateTimeString();
            $dataTmp = $dataTmp->where("$nameTableOrderChangeExtra.created_at", "<=", $order_send_time_to);
        }

        if ($category) {
            $dataTmp = $dataTmp->where($nameTableOrderChangeExtra . ".category_id", $category);
        }
        if ($content) {
            $dataTmp = $dataTmp->where($nameTableOrderChangeExtra . ".content", $content);
        }
        if ($keyword) {
            $dataTmp = $dataTmp->where(function ($sql) use ($keyword, $nameTableOrderChangeExtra) {
                $sql->where($nameTableOrderChangeExtra. '.product_name', 'like', '%' . $keyword . '%')
                    ->orWhere('so.id_name', 'like', '%' . $keyword . '%')
                    ->orWhere('so.customer_name', 'like', '%' . $keyword . '%')
                    ->orWhere('so.customer_code', 'like', '%' . $keyword . '%')
                    ->orWhere($nameTableOrderChangeExtra. '.product_code', 'like', '%' . $keyword . '%');
            });
        }

        if (!empty($ids)) {
            $dataTmp = $dataTmp->whereIn(SC_DB_PREFIX . 'shop_order_change_extra.id', $ids);
        }

        $dataTmp = $dataTmp
            ->where($nameTableOrderChangeExtra . '.kind', 0)
            ->select(
                'id as id_change',
                'delivery_date_origin',
                'customer_code',
                'customer_name',
                'product_id',
                'qty',
                'content',
                'created_at',
                'product_code',
                'product_name'
            )->get();
        return $dataTmp;
    }

    private function getDataDavicookByProductFresh($dataSearch)
    {
        $from_to = $dataSearch['date_start'] ?? '';
        $end_to = $dataSearch['date_end'] ?? '';
        $order_send_time_from = $dataSearch['order_send_time_from'] ?? '';
        $order_send_time_to = $dataSearch['order_send_time_to'] ?? '';
        $keyword = $dataSearch['keyword'] ?? '';
        $category = $dataSearch['category'] ?? '';
        $content = $dataSearch['content'] ?? '';
        $nameTableOrderChangeExtra = SC_DB_PREFIX . 'shop_order_change_extra';

        $dataTmp = AdminShopOrderChangeExtra::leftjoin(SC_DB_PREFIX . 'shop_davicook_order as so', function ($join) {
            $join->on(SC_DB_PREFIX . 'shop_order_change_extra.order_id', 'so.id');
        })
            ->leftjoin(SC_DB_PREFIX . 'shop_category as sc', function ($join) {
                $join->on('sc.id',SC_DB_PREFIX . 'shop_order_change_extra.category_id');
            });
        if ($from_to) {
            $from_to = convertVnDateObject($from_to)->startOfDay()->toDateTimeString();
            $dataTmp = $dataTmp->where($nameTableOrderChangeExtra.".delivery_date_origin", ">=", $from_to);
        }

        if ($end_to) {
            $end_to = convertVnDateObject($end_to)->endOfDay()->toDateTimeString();
            $dataTmp = $dataTmp->where($nameTableOrderChangeExtra.".delivery_date_origin", "<=", $end_to);
        }
        // Order send time
        if ($order_send_time_from) {
            $order_send_time_from = convertStandardDate($order_send_time_from)->toDateTimeString();
            $dataTmp = $dataTmp->where("$nameTableOrderChangeExtra.created_at", ">=", $order_send_time_from);
        }
        if ($order_send_time_to) {
            $order_send_time_to = convertStandardDate($order_send_time_to)->toDateTimeString();
            $dataTmp = $dataTmp->where("$nameTableOrderChangeExtra.created_at", "<=", $order_send_time_to);
        }

        if ($category) {
            $dataTmp = $dataTmp->where($nameTableOrderChangeExtra . ".category_id", $category);
        }
        if ($content) {
            $dataTmp = $dataTmp->where($nameTableOrderChangeExtra . ".content", $content);
        }
        if ($keyword) {
            $dataTmp = $dataTmp->where(function ($sql) use ($keyword, $nameTableOrderChangeExtra) {
                $sql->where($nameTableOrderChangeExtra. '.product_name', 'like', '%' . $keyword . '%')
                    ->orWhere('so.id_name', 'like', '%' . $keyword . '%')
                    ->orWhere('so.customer_name', 'like', '%' . $keyword . '%')
                    ->orWhere('so.customer_code', 'like', '%' . $keyword . '%')
                    ->orWhere($nameTableOrderChangeExtra. '.product_code', 'like', '%' . $keyword . '%');
            });
        }
        if (!empty($ids)) {
            $dataTmp = $dataTmp->whereIn(SC_DB_PREFIX . 'shop_order_change_extra.id', $ids);
        }

        $dataTmp = $dataTmp->where($nameTableOrderChangeExtra . '.kind', 1)
            ->select(
                $nameTableOrderChangeExtra . '.id as id_change',
                $nameTableOrderChangeExtra . '.delivery_date_origin',
                $nameTableOrderChangeExtra . '.product_id',
                $nameTableOrderChangeExtra . '.customer_code',
                $nameTableOrderChangeExtra . '.customer_name',
                $nameTableOrderChangeExtra . '.qty',
                $nameTableOrderChangeExtra. '.content',
                $nameTableOrderChangeExtra . '.created_at',
                $nameTableOrderChangeExtra . '.content',
                $nameTableOrderChangeExtra . '.product_code',
                $nameTableOrderChangeExtra . '.product_name'
            )->get();
        return $dataTmp;
    }

    public function postUpdateDetail(){
        if (!request()->ajax()) {
            return response()->json(['error' => 1, 'msg' => sc_language_render('admin.method_not_allow')]);
        }
        $dataSearch = [
            'keyword' => sc_clean(request('keyword') ?? ''),
            'key_export' => sc_clean(request('key_export') ?? []),
            'date_start' => sc_clean(request('date_start') ?? ''),
            'date_end' => sc_clean(request('date_end') ?? ''),
            'category' => sc_clean(request('category') ?? ''),
            'check_filter' => sc_clean(request('check_filter') ?? ''),
            'order_send_time_from' => sc_clean(request('order_send_time_from') ?? ''),
            'order_send_time_to' => sc_clean(request('order_send_time_to') ?? ''),
            'content' => sc_clean(request('content') ?? '')
        ];
        $ids = request('ids');
        $arrId = [];
        DB::beginTransaction();
        try {
            if ($ids) {
                $arrId = explode(',', $ids);
            } else {
                $dataReport = $this->getDataByFilter($dataSearch['key_export'], $dataSearch);
                foreach ($dataReport as $item){
                    $arrId[] = $item->id_change;
                }
            }
            $details = AdminShopOrderChangeExtra::whereIn('id', $arrId)->get()->pluck('order_detail_id')->unique();
            foreach ($details as $id) {
                $this->updateExportWarehouseDetail($id);
            }
            DB::commit();
            return response()->json(['error'=>0, 'message'=>'cập nhật thành công']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error'=>1, 'message'=>'cập nhật không thành công']);
        }
    }
    public function updateExportWarehouseDetail($id){
        $orderChangeNew = AdminShopOrderChangeExtra::where('order_detail_id', $id)->orderBy('created_at', 'desc')->first();
        $dataExportDetail = AdminExportDetail::where('order_detail_id', $id)->orderBy('created_at', 'desc')->first();
        if ($dataExportDetail) {
            $oldQty = $dataExportDetail->qty_reality;
            $orderExport = AdminExport::find($dataExportDetail->export_id);
            if($orderExport->status != 2) {
                $dataExportUpdate = [
                    'qty_reality'=>$orderChangeNew->qty,
                ];
                $dataExportDetail->update($dataExportUpdate);
                if ($oldQty != $orderChangeNew->qty) {
                    $title = 'Thay đổi số lượng thực tế: '.$dataExportDetail->product_name;
                    $contentHistory = 'Thay đổi: '.$oldQty.' -> '.$orderChangeNew->qty;
                    $this->storeHistory($dataExportDetail->export_id, $title, $contentHistory);
                }
            }
        }

    }
    private function storeHistory($id, $titleHistory, $contentHistory)
    {
        //Add history
        $orderExport = AdminExport::find($id);
        $dataHistory = [
            'id' => sc_uuid(),
            'export_id' => $id,
            'export_code' => $orderExport->id_name,
            'title' => $titleHistory,
            'content' => $contentHistory,
            'user_id' => Admin::user()->id,
            'user_name' => Admin::user()->name,
        ];
        AdminExportHistory::create($dataHistory);
    }
}
