<?php

namespace App\Admin\Controllers;

use App\Admin\Models\AdminPoint;
use App\Admin\Models\AdminRating;
use App\Exports\CustomerExport;
use App\Exports\PointExport;
use App\Exports\RatingExport;
use App\Front\Models\ShopCustomer;
use App\Front\Models\ShopOrder;
use App\Front\Models\ShopPoint;
use App\Front\Models\ShopPointHistory;
use App\Front\Models\ShopRating;
use App\Front\Models\ShopRewardPrinciple;
use Carbon\CarbonPeriod;
use Dompdf\Dompdf;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Maatwebsite\Excel\Facades\Excel;
use SCart\Core\Admin\Controllers\RootAdminController;
use SCart\Core\Front\Models\ShopLanguage;

class AdminRatingController extends RootAdminController
{
    public $languages;
    public $statusOrder = [];


    public function __construct()
    {
        parent::__construct();
        $this->languages = ShopLanguage::getListActive();
    }

    public function index()
    {
        $data = [
            'title' => "Lịch sử đánh giá dịch vụ",
            'subTitle' => '',
            'icon' => 'fa fa-indent',
            'removeList' => 0, // 1 - Enable function delete list item
            'buttonRefresh' => 1, // 1 - Enable button refresh
            'buttonSort' => 1, // 1 - Enable button sort
            'css' => '',
            'js' => '',
            'is_reward' => 1,
        ];

        //Process add content
        $data['menuRight'] = sc_config_group('menuRight', \Request::route()->getName());
        $data['menuLeft'] = sc_config_group('menuLeft', \Request::route()->getName());
        $data['topMenuRight'] = sc_config_group('topMenuRight', \Request::route()->getName());
        $data['topMenuLeft'] = sc_config_group('topMenuLeft', \Request::route()->getName());
        $data['blockBottom'] = sc_config_group('blockBottom', \Request::route()->getName());
        //List th
        $listTh = [
            'index' => "STT",
            'name' => "Tên khách hàng",
            'code' => "Mã khách hàng",
            'point' => "Mức độ hài lòng",
            'content' => "Nội dung phản hồi",
            'action' => "Chi tiết",

        ];
        //Customize collumn size and align
        $cssTd = [
            'index' => 'text-align: center',
            'customer_name' => 'width: auto; min-width: 320px',
            'action' => 'text-align: center; width: 120px',
            'content' => 'text-align: left; text-overflow: ellipsis; width: 30%;'
        ];
        $data['cssTd'] = $cssTd;

        //Sort input data
        $arrSort = [
            'id__desc' => sc_language_render('filter_sort.id_desc'),
            'id__asc' => sc_language_render('filter_sort.id_asc'),
            'point__desc' => sc_language_render('filter_sort.point_desc'),
            'point__asc' => sc_language_render('filter_sort.point_asc'),
        ];
        //Search
        $dataSearch = [
            'keyword' => sc_clean(request('keyword') ?? ''),
            'filter_month' => sc_clean(request('filter_month') ?? ''),
            'sort_order' => sc_clean(request('sort_order') ?? 'id_desc'),
            'arrSort' => $arrSort
        ];

        $dataTmp = (new AdminRating())->getRatingData($dataSearch);
        $dataTr = [];
        $index = 0;

        foreach ($dataTmp as $row) {
            $index++;
            $dataMap = [
                'index' => $index,
                'name' => $row->customer->name ?? '',
                'code' => $row->customer->customer_code ?? '',
                'point' => ($row->point ?? 0) . "/5 <i class='fas fa-star text-warning'></i>",
                'content' => $row->content ?? "",
            ];
            $dataMap['action'] = '<span onclick="showRating(\'' . $row->id . '\');"  title="' . sc_language_render('	action.detail') . '" class="btn btn-sm btn-sm btn-primary"><i class="fas fa-list-ul text-white"></i></span>';
            $dataTr[$row->id] = $dataMap;
        }


        $data['listTh'] = $listTh;
        $data['dataTr'] = $dataTr;
        $data['pagination'] = $dataTmp->appends(request()->except(['_token', '_pjax']))->links($this->templatePathAdmin . 'component.pagination');
        $data['resultItems'] = sc_language_render('admin.result_item', ['item_from' => $dataTmp->firstItem(), 'item_to' => $dataTmp->lastItem(), 'total' => $dataTmp->total()]);
        //menuRight
        $data['menuRight'][] = '
        <button onclick="exportRatingExcel()" class="btn btn-flat btn btn-success"><i class="fa fa-file-excel"></i>&nbsp; Xuất excel</button>
        <button onclick="exportRatingPdf()" class="btn btn-flat btn btn-info"><i class="fa fa-file-pdf"></i>&nbsp; Xuất PDF' .
            '</button>';
        //=menuRight

        //menuSort
        $optionSort = '';
        foreach ($arrSort as $key => $sort) {
            $optionSort .= '<option ' . (($dataSearch['sort_order'] == $key) ? "selected" : "") . ' value="' . $key . '">' . $sort . '</option>';
        }
        $data['optionSort'] = $optionSort;
        $data['urlSort'] = route('admin.rating.index', request()->except(['_token', '_pjax', 'sort_order']));
        //=menuSort

        //menuSearch
        $optionMonth = '';
        $start = ShopPoint::min('year');
        $end = ShopPoint::max('year');
        foreach ($this->getRange() as $key => $month) {
            $optionMonth .= '<option  ' . ((request('filter_month') == $month) ? 'selected' : '') . ' ' . ((empty(request('filter_month')) && (Carbon::now()->format('m/Y') == $month)) ? 'selected' : '')  . ' value="' . $month . '"> Tháng ' . $month . '</option>';
        }

        $data['topMenuRight'][] = '
                <form action="' . sc_route_admin('admin.rating.index') . '" id="button_search">
                    <div class="input-group float-left">
                    <div class="col-md-2"></div>
                        <div class="col-md-5">
                            <div class="form-group">
                                <label>' . sc_language_render('reward.filter_month') . ':</label>
                                <div class="input-group">
                                <select class="form-control rounded-0" name="filter_month">
                                <option value="">' . sc_language_render('reward.filter_month') . '</option>
                                ' . $optionMonth . '
                                </select>
                                </div>
                            </div>
                        </div>
                     
                        <div class="col-md-5">
                            <div class="form-group">
                                <label>' . sc_language_render('order.admin.search_name') . ':</label>
                                <div class="input-group">
                                    <input type="text" name="keyword" class="form-control rounded-0 float-right" placeholder="' . sc_language_render('order.admin.search_name') . '" value="' . $dataSearch['keyword'] . '">
                                    <div class="input-group-append">
                                        <button type="submit" class="btn btn-primary  btn-flat"><i class="fas fa-search"></i></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>';

        //=menuSearch
        return view($this->templatePathAdmin . 'screen.list_rating')
            ->with($data);
    }

    public function detail()
    {
        $data = ShopRating::find(request("id"));
        $response = null;
        if(!$data){
            $response = [
                "error" => 1,
                "msg" => "Không tìm thấy đánh giá",
                "data" => null
            ];
        } else {
            $response = [
                "error" => 0,
                "msg" => "Success",
                "data" => [
                    "name" => $data->customer->name,
                    "point" => $data->point,
                    "content" => $data->content,
                    "month" => $data->month,
                    "year" => $data->year,
                ]
            ];
        }

        return response()->json($response);
    }
    public function exportExcel(){
        //Search
        $dataSearch = [
            'keyword' => sc_clean(request('keyword') ?? ''),
            'filter_month' => sc_clean(request('filter_month') ?? ''),
            'from_to' => sc_clean(request('from_to') ?? ''),
            'end_to' => sc_clean(request('end_to') ?? ''),
            'sort_order' => sc_clean(request('sort_order') ?? 'id_desc'),
            'arrSort' => []
        ];
        $inputMonth = empty($dataSearch['filter_month']) ? now()->format("m/Y") : $dataSearch['filter_month'];
        $dataTmp = (new AdminRating())->getRatingData($dataSearch, true);
        return Excel::download(new RatingExport($dataTmp, $inputMonth), 'LichSuDanhGia-' . Carbon::now() . '.xlsx');
    }
    public function exportPDF(){
        //Search
        $dataSearch = [
            'keyword' => sc_clean(request('keyword') ?? ''),
            'filter_month' => sc_clean(request('filter_month') ?? ''),
            'from_to' => sc_clean(request('from_to') ?? ''),
            'end_to' => sc_clean(request('end_to') ?? ''),
            'sort_order' => sc_clean(request('sort_order') ?? 'id_desc'),
            'arrSort' => []
        ];
        // Prepare data for passing
        $now = now();
        $dataTmp = (new AdminRating())->getRatingData($dataSearch, true);
        $inputMonth = explode("/", $dataSearch['filter_month']);
        $month = $inputMonth[0] ?? $now->format("m");
        $year = $inputMonth[1] ?? $now->format("Y");
        // Print
        $html = view((new \SCart\Core\Admin\Controllers\RootAdminController())->templatePathAdmin .
            'print.rating_export_pdf_templete')->with(['data' => $dataTmp, 'month' => $month, 'year' => $year]);
        $html = mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8');
        $domPdf = new Dompdf();
        $domPdf->getOptions()->setChroot(public_path());
        $domPdf->loadHtml($html, 'UTF-8');
        $domPdf->setPaper('A4', 'portrait');
        $domPdf->render();
        return $domPdf->stream('LichSuDanhGia-' . now() . '.pdf', ["Attachment" => false]);
    }
    public static function getRange()
    {
        $start = ShopRating::min("year");
        $end = ShopRating::max("year");
        $result = CarbonPeriod::create("$start-1-1", '1 month', "$end-12-31");
        $output = [];
        foreach ($result as $dt) {
            $output[] = $dt->format("m/Y");
        }
        return $output;
    }
}
