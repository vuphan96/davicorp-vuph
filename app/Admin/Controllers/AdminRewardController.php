<?php

namespace App\Admin\Controllers;

use App\Admin\Models\AdminPoint;
use App\Exports\Point\AdminExportPoint;
use App\Exports\Point\AdminExportPointHitoryDetail;
use App\Front\Models\ShopPoint;
use App\Front\Models\ShopPointHistory;
use Dompdf\Dompdf;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use SCart\Core\Admin\Controllers\RootAdminController;
use SCart\Core\Front\Models\ShopLanguage;
use App\Console\Commands\AddPointCustomer;

class AdminRewardController extends RootAdminController
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
            'title' => sc_language_render('reward.title'),
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
            'index' => sc_language_render('reward.no'),
            'name' => sc_language_render('reward.customer_name'),
            'code' => sc_language_render('customer.code'),
            'tier' => sc_language_render('reward.tier'),
            'point' => sc_language_render('reward.point'),
            'exchange' => sc_language_render('reward.exchange'),
            'action' => sc_language_render('action.title'),

        ];
        //Customize collumn size and align
        $cssTd = [
            'index' => 'text-align: center',
            'customer_name' => 'width: auto; min-width: 320px',
            'action' => 'text-align: center; width: 120px',
            'index' => 'text-align: center'
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
            'from_to' => sc_clean(request('from_to') ?? ''),
            'end_to' => sc_clean(request('end_to') ?? ''),
            'sort_order' => sc_clean(request('sort_order') ?? 'id_desc'),
            'arrSort' => $arrSort
        ];

        $dataTmp = (new AdminPoint)->getRewardData($dataSearch);
        $dataTr = [];
        $index = 0;

        foreach ($dataTmp as $row) {
            $index++;
            $dataMap = [
                'index' => $index,
                'name' => $row->name ?? '',
                'code' => $row->customer_code ?? '',
                'tier' => $row->tier_name ?? sc_language_render('reward.no_tier'),
                'point' => $row->point ?? 0,
                'exchange' => sc_currency_render(($row->point ?? 0) * ($row->rate ?? 0), 'VND'),
            ];

            $dataMap['action'] = '<span onclick="showHistory(\'' . $row->id . '\');"  title="' . sc_language_render('	action.detail') . '" class="btn btn-sm btn-sm btn-warning"><i class="fas fa-history text-white"></i></span>';
            $dataTr[$row->id] = $dataMap;
        }

        $data['listTh'] = $listTh;
        $data['dataTr'] = $dataTr;
        $data['pagination'] = $dataTmp->appends(request()->except(['_token', '_pjax']))->links($this->templatePathAdmin . 'component.pagination');
        $data['resultItems'] = sc_language_render('admin.result_item', ['item_from' => $dataTmp->firstItem(), 'item_to' => $dataTmp->lastItem(), 'total' => $dataTmp->total()]);
        //menuRight
        $data['menuRight'][] = '
        <!-- <a href="' . sc_route_admin('admin_point_setting.principle.index.test') . '" class="btn btn-flat btn btn-info">&nbsp; Test Point' . '</a> -->
        <a href="' . sc_route_admin('admin_point_setting.principle.index') . '" class="btn btn-flat btn btn-primary"><i class="fa fa-layer-group"></i>&nbsp;' . sc_language_render("reward.principle_title") . '</a>
        <a href="' . sc_route_admin('admin_point_setting.tier.index') . '" class="btn btn-flat btn btn-primary"><i class="fa fa-exchange-alt"></i>&nbsp;' . sc_language_render("reward.exchange_title") . '</a>
        <button onclick="exportPointExcel()" class="btn btn-flat btn btn-success"><i class="fa fa-file-excel"></i>&nbsp; Xuất excel</button>
        <button onclick="exportPointPdf()" class="btn btn-flat btn btn-info"><i class="fa fa-file-pdf"></i>&nbsp; Xuất PDF' .
            '</button>';
        //=menuRight

        //menuSort
        $optionSort = '';
        foreach ($arrSort as $key => $sort) {
            $optionSort .= '<option ' . (($dataSearch['sort_order'] == $key) ? "selected" : "") . ' value="' . $key . '">' . $sort . '</option>';
        }
        $data['optionSort'] = $optionSort;
        $data['urlSort'] = sc_route_admin('admin_point_view.index', request()->except(['_token', '_pjax', 'sort_order']));
        //=menuSort

        //menuSearch
        $optionMonth = '';
        $start = ShopPoint::min('year');
        $end = ShopPoint::max('year');
        foreach (AdminPoint::getRange($start, $end) as $key => $month) {
            $optionMonth .= '<option  ' . ((request('filter_month') == $month) ? 'selected' : '') . ' ' . ((empty(request('filter_month')) && (Carbon::now()->format('m/Y') == $month)) ? 'selected' : '')  . ' value="' . $month . '"> Tháng ' . $month . '</option>';
        }

        $data['topMenuRight'][] = '
                <form action="' . sc_route_admin('admin_point_view.index') . '" id="button_search">
                    <div class="input-group float-left">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>' . sc_language_render('reward.filter_month') . ':</label>
                                <div class="input-group">
                                <select class="form-control rounded-0" id="filter_month" name="filter_month">
                                <option value="">' . sc_language_render('reward.filter_month') . '</option>
                                ' . $optionMonth . '
                                </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>' . sc_language_render('action.from') . ':</label>
                                <div class="input-group">
                                <input type="number" min="0" autocomplete="off" name="from_to" id="from_to" class="form-control input-sm rounded-0" placeholder="' . sc_language_render('reward.point') . '" value="'. $dataSearch['from_to'] . '" /> 
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>' . sc_language_render('action.to') . ':</label>
                                <div class="input-group">
                                <input type="number" min="0" autocomplete="off" name="end_to" id="end_to" class="form-control input-sm rounded-0" placeholder="' . sc_language_render('reward.point') . '" value="'. $dataSearch['end_to'] . '"/> 
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
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
        return view($this->templatePathAdmin . 'screen.points.index')
            ->with($data);
    }

    /**
     * Show chi tiết lịch sử.
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function getHistory()
    {
        $month = request('filter_month') ?? '';
        $year_month = explode('/', $month);
        $data = ShopPoint::with(['history' => function ($query) {
                $query->where('action', 1);
            }])->where('customer_id', request('id'))
            ->where('year', count($year_month) > 1 ? $year_month[1] : \Carbon\Carbon::now()->format('Y'))
            ->where('month', count($year_month) > 1 ? $year_month[0] : \Carbon\Carbon::now()->format('m'))
            ->orderBy('created_at', 'DESC')
            ->get();
        return view($this->templatePathAdmin . 'screen.points.history_detail')->with(['data' => $data]);
    }

    public function exportPointHistory()
    {
        $filter = request('filter_month') ?? '';
        $year_month = explode('/', $filter);
        $data = ShopPoint::with(['history' => function ($query) {
                $query->where('action', 1);
            }])->where('customer_id', request('id'))
            ->where('year', count($year_month) > 1 ? $year_month[1] : \Carbon\Carbon::now()->format('Y'))
            ->where('month', count($year_month) > 1 ? $year_month[0] : \Carbon\Carbon::now()->format('m'))
            ->orderBy('created_at', 'DESC')
            ->get();
        return Excel::download(new AdminExportPointHitoryDetail($data, $filter), 'LichSuDiemThuong-' . Carbon::now() . '.xlsx');
    }

    /**
     * Update điểm thưởng thực tế.
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateActualPoint()
    {
        $value = request('value');
        $name = request('name');
        $id = request('pk');

        $item = ShopPointHistory::find($id);
        DB::beginTransaction();
        try {
            if (!$item) {
                throw new \Exception('Lỗi update điểm thực tế!');
            }
            $item->{$name} = $value;
            $item->save();
            $point = ShopPointHistory::where('point_id', $item->point_id)->sum('actual_point');
            ShopPoint::where('id', $item->point_id)->update([
                'point' => $point,
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'error' => 1,
                'msg' => $e->getMessage(),
            ]);
        }

        DB::commit();
        return response()->json([
            'error' => 0,
            'msg' => sc_language_render('action.update_success'),
            'point' => $point
        ]);
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
        $dataTmp = (new AdminPoint)->getRewardData($dataSearch, true);
        return Excel::download(new AdminExportPoint($dataTmp, $dataSearch["filter_month"]), 'TongHopDiemThuong-' . Carbon::now() . '.xlsx');
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
        $dataTmp = (new AdminPoint)->getRewardData($dataSearch, true);

        // Print
        $html = view((new \SCart\Core\Admin\Controllers\RootAdminController())->templatePathAdmin .
            'print.point_export_pdf_templete')->with(['data' => $dataTmp, "time" => $dataSearch["filter_month"]]);
        $html = mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8');
        $domPdf = new Dompdf();
        $domPdf->getOptions()->setChroot(public_path());
        $domPdf->loadHtml($html, 'UTF-8');
        $domPdf->setPaper('A4', 'portrait');
        $domPdf->render();
        return $domPdf->stream('TongHopDiemThuong-' . now() . '.pdf', ["Attachment" => false]);
    }

    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    public function testPoint()
    {
        try {
            $test = (new AddPointCustomer())->handle();
            Log::info('TEST-POINT');
            return redirect()->back();
        } catch (\Throwable $e) {
            dd($e);
        }
    }

}
