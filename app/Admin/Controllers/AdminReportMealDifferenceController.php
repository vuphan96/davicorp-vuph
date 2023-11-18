<?php

namespace App\Admin\Controllers;

use App\Admin\Models\AdminSystemChangeHistory;
use App\Exports\AdminReportMealDifferenceDetailExport;
use App\Exports\AdminReportMealDifferenceExport;

use App\Admin\Models\AdminDavicookOrder;
use App\Admin\Models\AdminOrder;
use App\Front\Models\ShopDavicookOrderDetail;
use Dompdf\Dompdf;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\URL;
use Maatwebsite\Excel\Facades\Excel;
use SCart\Core\Admin\Admin;
use SCart\Core\Admin\Controllers\RootAdminController;
use Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use PDF;
use function Symfony\Component\Mime\toString;

class AdminReportMealDifferenceController extends RootAdminController{

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Report Meal Difference.
     */
    public function reportMealDifference(){
        $data = [
            // ,
            'title' => sc_language_render('admin.meal.difference.report.title'),
            'subTitle' => '',
            'icon' => 'fa fa-indent',
            'urlDeleteItem' => sc_route_admin('admin_price.delete'),
            'removeList' => '', // 1 - Enable function delete list item
            'buttonRefresh' => '', // 1 - Enable button refresh
            'buttonSort' => '', // 1 - Enable button sort
            'css' => '',
            'js' => '',
            'url_action' => sc_route_admin('admin_price.create'),
        ];

        $keyword = sc_clean(request('keyword') ?? '');
        $dataSearch = [
            'keyword' => $keyword,
            'from_to' => sc_clean(request('from_to') ?? ''),
            'end_to' => sc_clean(request('end_to') ?? ''),
            'key_search' => sc_clean(request('key_search') ?? ''),
        ];
        $data['menuLeft'] = sc_config_group('menuLeft', Request::route()->getName());
        $nameUrl = URL::full();
        session()->put('nameUrlReportDifference', $nameUrl);
        if (!empty($dataSearch['key_search'])) {
            $dataAllOrderReportreportMealDifference = (new AdminDavicookOrder())->getAllOrderReportMealDifference($dataSearch);
        } else {
            $dataAllOrderReportreportMealDifference['details'] = new Collection();
        }
        $data['number_of_servings'] = $dataAllOrderReportreportMealDifference['number_of_servings'] ?? 0;
        $data['number_of_servings_fact'] = $dataAllOrderReportreportMealDifference['number_of_servings_fact'] ?? 0;
        $dataAllOrderReportreportMealDifference = $this->paginate($dataAllOrderReportreportMealDifference['details']);
        $data['result'] = $dataAllOrderReportreportMealDifference;
        $data['countPaginate'] = ceil(count($dataAllOrderReportreportMealDifference)/config('pagination.admin.medium'));
        $data['pagination'] = $dataAllOrderReportreportMealDifference->appends(request()->except(['_token', '_pjax']))->links($this->templatePathAdmin . 'component.pagination');
        $data['resultItems'] = sc_language_render('admin.result_item', ['item_from' => $dataAllOrderReportreportMealDifference->firstItem(), 'item_to' => $dataAllOrderReportreportMealDifference->lastItem(), 'total' => $dataAllOrderReportreportMealDifference->total()]);
        $currentDay = nowDateString();
        $from_day = $dataSearch['from_to'] ? $dataSearch['from_to'] : $currentDay ;
        $end_day = $dataSearch['end_to'] ? $dataSearch['end_to'] : $currentDay ;
        $data['topMenuRight'][] = '
                <form action="' . sc_route_admin('admin_report_quantity_diference.index') . '" id="button_search">
                <input type="hidden" name="key_search" value="search" id="key_search">
                <br>
                    <div class="input-group float-right">
                        <div class="row">
                            <div class="col-lg-5 col-md-5 col-sm-12">
                                <div class="row">
                                    <div class="col-lg-6 col-md-6 col-sm-12">
                                        <div class="form-group" >
                                            <div class="input-group ">
                                                <div style="margin-top: 10px;margin-right: 7px;color: #93A7C1"">Từ</div>
                                                <input type="text" name="from_to" id="from_to" class="form-control input-sm date_time rounded-0" style="text-align: center" data-date-format="yyyy-mm-dd" placeholder="yyyy-mm-dd" value="' . $from_day . '"/> 
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-md-6 col-sm-12">
                                        <div class="form-group">
                                            <div class="input-group">
                                                <div style="margin-top: 10px;margin-right: 6px;color: #93A7C1"">Đến</div>
                                                <input type="text" name="end_to" id="end_to" class="form-control input-sm date_time rounded-0" style="text-align: center" data-date-format="yyyy-mm-dd" placeholder="yyyy-mm-dd" value="' . $end_day . '"/> 
                                            </div>
                                        </div>
                                    </div>
                                </div> 
                            </div>
                            <div class="col-lg-4 col-md-4 col-sm-12">
                                <div class="form-group">
                                    <div class="input-group">
                                    <input type="text" name="keyword" id="keyword" class="form-control rounded-0 float-right" placeholder="' . sc_language_render('admin.name_meal_difference.search_placeholder') . '" value="' . $keyword . '">
                                        <div class="input-group-append">
                                            <button type="submit" class="btn btn-primary  btn-flat" id="submit_meal_difference"><i class="fas fa-search"></i></button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-3 col-sm-12">
                                <div class="form-group" style="width: 250px">
                                    <div class="input-group">
                                        <a class="btn  btn-success  btn-flat" title="" id="button_export_filter">
                                                <i class="fa fa-file-export" title="' . sc_language_render('category-export') . '"></i> ' . sc_language_render('category-export') .
                                        '</a>&nbsp;
                                        <a href="javascript:void(0)" class="btn btn-flat btn btn-info text-white" onclick="savePdf()"><i class="fas fa-file-pdf"></i>&nbsp;' . sc_language_render("admin.report.print_pdf") . '</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                </form>
                ';

        //=menuSearch
        return view($this->templatePathAdmin . 'screen.report.meal_difference.index')
            ->with($data);

    }

    /**
     * Export danh sách báo cáo.
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function exportExcelReportMealDifference() {
        $dataSearch = [
            'from_to' => sc_clean(request('from_to') ?? ''),
            'end_to' => sc_clean(request('end_to') ?? ''),
            'keyword' => sc_clean(request('keyword') ?? ''),
        ];

        $from_to = $dataSearch['from_to'] ?? \Carbon\Carbon::make($dataSearch['from_to'])->format("d/m/Y");
        $end_to = $dataSearch['end_to'] ?? \Carbon\Carbon::make($dataSearch['end_to'])->format("d/m/Y");
        $fileName = 'Báo Cáo Chênh Lệch Hàng Xuất Bếp Ăn-'. $from_to .'-'.$end_to. '.xlsx';
        $fileName = str_replace("/","_",$fileName);
        $attributes = [
            'name_admin' =>  Admin::user()->name,
            'time' =>  now(),
            'desc' =>  $fileName,
            'type' =>  'BC Bep An - Chi Tiet',
            'kind' =>  'Xuất Excel',
        ];
        AdminSystemChangeHistory::storeData($attributes);

        return Excel::download(new AdminReportMealDifferenceExport($dataSearch), $fileName);
    }

    /**
     * PDF danh sách báo cáo
     * @return \Illuminate\Http\RedirectResponse|void
     */
    public function exportPDFReportMealDifference() {
        $dataSearch = [
            'from_to' => sc_clean(request('from_to') ?? ''),
            'end_to' => sc_clean(request('end_to') ?? ''),
            'keyword' => sc_clean(request('keyword') ?? ''),
        ];

        $dataAllOrderReportreportMealDifference = (new AdminDavicookOrder())->getAllOrderReportMealDifference($dataSearch);

        if (!count($dataAllOrderReportreportMealDifference) > 0) {
            return redirect()->route('admin_report_quantity_diference.index')->with('error' , 'Không có dữ liệu');
        }

        $html = view($this->templatePathAdmin . 'screen.report.meal_difference.print_pdf_template')
            ->with(['datas' => $dataAllOrderReportreportMealDifference, 'dataSearch' => $dataSearch])->render();
        $fileName = 'Báo Cáo Chênh Lệch Hàng Xuất Bếp Ăn Chi Tiết-'. $dataSearch['from_to'] .'-'.$dataSearch['end_to']. '.pdf';
        $fileName = str_replace("/","_",$fileName);
        $attributes = [
            'name_admin' =>  Admin::user()->name,
            'time' =>  now(),
            'desc' =>  $fileName,
            'type' =>  'BC Bep An - Chi Tiet',
            'kind' =>  'Xuất Pdf',
        ];
        AdminSystemChangeHistory::storeData($attributes);
        $html = mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8');
        return $html;

        return $domPdf->stream('Báo Cáo Chênh Lệch Hàng Xuất Bếp Ăn - ' . Carbon::now() . '.pdf');
    }

    /**
     * Report the difference in meal rates by product.
     * @param $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function reportMealDifferenceDetail($id)
    {
        $dataSearch = [
            'from_to' => sc_clean(request('from_to') ?? ''),
            'end_to' => sc_clean(request('end_to') ?? ''),
            'keyword' => sc_clean(request('keyword') ?? ''),
        ];

        $data = [
            // ,
            'title' => sc_language_render('admin.meal.difference.report.title.detail'),
            'subTitle' => '',
            'icon' => 'fa fa-indent',
            'urlDeleteItem' => sc_route_admin('admin_price.delete'),
            'removeList' => '', // 1 - Enable function delete list item
            'buttonRefresh' => '', // 1 - Enable button refresh
            'buttonSort' => '', // 1 - Enable button sort
            'css' => '',
            'js' => '',
            'url_action' => sc_route_admin('admin_price.create'),
        ];

        $keyword = sc_clean(request('keyword') ?? '');
        $data['menuLeft'] = sc_config_group('menuLeft', Request::route()->getName());

        $dataAllOrderReportreportMealDifference = (new AdminDavicookOrder())->getAllOrderReportMealDifferenceDetail($dataSearch, $id)->get();
        $dataAllOrderReportreportMealDifference = $this->paginate($dataAllOrderReportreportMealDifference);
        $data['result'] = $dataAllOrderReportreportMealDifference;
        $data['countPaginate'] = ceil(count($dataAllOrderReportreportMealDifference)/config('pagination.admin.medium'));

        $data['pagination'] = $dataAllOrderReportreportMealDifference->appends(request()->except(['_token', '_pjax']))->links($this->templatePathAdmin . 'component.pagination');
        $data['resultItems'] = sc_language_render('admin.result_item', ['item_from' => $dataAllOrderReportreportMealDifference->firstItem(), 'item_to' => $dataAllOrderReportreportMealDifference->lastItem(), 'total' => $dataAllOrderReportreportMealDifference->total()]);
        $currentDay = Carbon::today()->toDateString();
        $from_day = $dataSearch['from_to'] ? $dataSearch['from_to'] : $currentDay ;
        $end_day = $dataSearch['end_to'] ? $dataSearch['end_to'] : $currentDay ;
        $data['topMenuRight'][] = '
                <form action="' . sc_route_admin('admin_report_quantity_diference.detail', $id ) . '" id="button_search">
                <br>
                    <div class="input-group float-right">
                        <div class="row">
                            <div class="col-lg-5 col-md-5 col-sm-12">
                                <div class="row">
                                    <div class="col-lg-6 col-md-6 col-sm-12" >
                                        <div class="form-group" >
                                            <div class="input-group ">
                                                <div style="margin-top: 10px;margin-right: 7px;color: #93A7C1"">Từ</div>
                                                <input type="text" name="from_to" id="from_to" class="form-control input-sm date_time rounded-0" style="text-align: center" data-date-format="yyyy-mm-dd" placeholder="yyyy-mm-dd" value="' . $from_day . '"/> 
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-md-6 col-sm-12">
                                        <div class="form-group">
                                            <div class="input-group">
                                                <div style="margin-top: 10px;margin-right: 6px;color: #93A7C1"">Đến</div>
                                                <input type="text" name="end_to" id="end_to" class="form-control input-sm date_time rounded-0" style="text-align: center" data-date-format="yyyy-mm-dd" placeholder="yyyy-mm-dd" value="' . $end_day . '"/> 
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-3 col-sm-12">
                                <div class="form-group">
                                    <div class="input-group">
                                        <input type="text" name="keyword" id="keyword" class="form-control rounded-0 float-right" placeholder="' . sc_language_render('admin.customer_meal_difference.search_placeholder') . '" value="' . $keyword . '">
                                        <div class="input-group-append">
                                            <button type="submit" class="btn btn-primary  btn-flat"><i class="fas fa-search"></i></button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-4 col-sm-12>
                                <div class="form-group">
                                    <div class="input-group">
                                        <a class="btn  btn-success  btn-flat" title="" id="button_export_filter">
                                            <i class="fa fa-file-export" title="' . sc_language_render('category-export') . '"></i> ' . sc_language_render('category-export') .
                                        '</a> &nbsp;&nbsp;
                                        <a href="javascript:void(0)" class="btn btn-flat btn btn-info text-white" onclick="savePdf()"><i class="fas fa-file-pdf"></i>&nbsp;' . sc_language_render("admin.report.print_pdf") . '</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                </form>
                ';

        return view($this->templatePathAdmin . 'screen.report.meal_difference.detail')
            ->with($data);
    }

    /**
     * Export the excel file with the difference in meal rates by product.
     * @param $id
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function exportExcelReportMealDifferenceDetail($id) {
        $dataSearch = [
            'from_to' => sc_clean(request('from_to') ?? ''),
            'end_to' => sc_clean(request('end_to') ?? ''),
            'keyword' => sc_clean(request('keyword') ?? ''),
        ];

        $from_to = $dataSearch['from_to'] ? date("d-m-Y", strtotime($dataSearch['from_to'])) : '-';
        $end_to = $dataSearch['end_to'] ? date("d-m-Y", strtotime($dataSearch['end_to'])) : '-';
        $fileName = 'Báo Cáo Chênh Lệch Hàng Xuất Bếp Ăn Chi Tiết-'. $from_to .'-'.$end_to. '.xlsx';

        $attributes = [
            'name_admin' =>  Admin::user()->name,
            'time' =>  now(),
            'desc' =>  $fileName,
            'type' =>  'BC Bep An - Chi Tiet',
            'kind' =>  'Xuất Excel',
        ];
        AdminSystemChangeHistory::storeData($attributes);

        return Excel::download(new AdminReportMealDifferenceDetailExport($dataSearch, $id), $fileName);
    }

    /**
     * Export the Pdf file with the difference in meal rates by product.
     * @return \Illuminate\Http\RedirectResponse|void
     */
    public function exportPDFReportMealDifferenceDetail($id) {
        $dataSearch = [
            'from_to' => sc_clean(request('from_to') ?? ''),
            'end_to' => sc_clean(request('end_to') ?? ''),
            'keyword' => sc_clean(request('keyword') ?? ''),
        ];
        $dataAllOrderReportreportMealDifference = (new AdminDavicookOrder())->getAllOrderReportMealDifference($dataSearch, $id)['details'];
        $dataAllOrderReportreportMealDifferenceDetail = (new AdminDavicookOrder())->getAllOrderReportMealDifferenceDetail($dataSearch, $id)->get();
        if (!count($dataAllOrderReportreportMealDifference) > 0) {
            return redirect()->back()->with('error' , 'Không có dữ liệu');
        }

        $html = view($this->templatePathAdmin . 'screen.report.meal_difference.print_detail_pdf_template')
            ->with(['data' => $dataAllOrderReportreportMealDifference, 'dataDetails' => $dataAllOrderReportreportMealDifferenceDetail, 'dataSearch' => $dataSearch])->render();

        $html = mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8');

        $domPdf = new Dompdf();
        $domPdf->getOptions()->setChroot(public_path());
        $domPdf->loadHtml($html, 'UTF-8');
        $domPdf->setPaper('A4', 'portrait');
        $domPdf->render();
        $from_to = $dataSearch['from_to'] ?? \Carbon\Carbon::make($dataSearch['from_to'])->format("d/m/Y");
        $end_to = $dataSearch['end_to'] ?? \Carbon\Carbon::make($dataSearch['end_to'])->format("d/m/Y");
        $fileName = 'Báo Cáo Chênh Lệch Hàng Xuất Bếp Ăn Chi Tiết-'. $from_to .'-'.$end_to. '.pdf';
        $fileName = str_replace("/","_",$fileName);
        $attributes = [
            'name_admin' =>  Admin::user()->name,
            'time' =>  now(),
            'desc' =>  $fileName,
            'type' =>  'BC Bep An - Chi Tiet',
            'kind' =>  'Xuất Pdf',
        ];
        AdminSystemChangeHistory::storeData($attributes);
        return $domPdf->stream($fileName);
    }

    /**
     * @param $items
     * @param int $perPage
     * @param null $page
     * @param string[] $options
     * @return LengthAwarePaginator
     */
    public function paginate($items, $perPage = 20, $page = null, $options = ['path' => ''])
    {
        $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);
        $items = $items instanceof Collection ? $items : Collection::make($items);
        return new LengthAwarePaginator($items->forPage($page, $perPage), $items->count(), $perPage, $page, $options);

    }

}
