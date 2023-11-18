<?php

namespace App\Admin\Controllers;

use App\Admin\Models\AdminCategory;
use App\Admin\Models\AdminDavicookOrder;
use App\Admin\Models\AdminOrder;
use App\Admin\Models\AdminSystemChangeHistory;
use App\Exports\AdminReportNoteExport;
use App\Front\Models\ShopDepartment;
use Dompdf\Dompdf;
use Request;

use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use SCart\Core\Admin\Admin;
use SCart\Core\Admin\Controllers\RootAdminController;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class AdminReportNoteController extends RootAdminController{

    public function __construct()
    {
        parent::__construct();
    }


    public function index(){
        $data = [
            // ,
            'title' => sc_language_render('admin.note.report.title'),
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

        $listTh = [
            'STT' => sc_language_render('admin.report.stt'),
            'name'      => sc_language_render('admin.report.name'),
            'order_code' => sc_language_render('order.id'),
            'note_order'   => sc_language_render('admin.report.product.note'),
            'note_product'    => sc_language_render('order.order_note')

        ];
        $cssTh = [
            'STT' => 'text-align: center; width: 7%',
            'name'      => 'text-align: center; width: 25%',
            'order_code' => 'text-align: center; width: 18%',
            'note_order'   => 'text-align: center; width: 25%',
            'note_product'    => 'text-align: center; width: 25%',
        ];
        $data['cssTh'] = $cssTh;
        $dataSearch = [
            'from_to' => sc_clean(request('from_to') ?? ''),
            'end_to' => sc_clean(request('end_to') ?? ''),
            'name' => sc_clean(request('name') ?? ''),
            'department' => sc_clean(request('department') ?? ''),
        ];
        $data['menuLeft'] = sc_config_group('menuLeft', Request::route()->getName());
        $data['listTh'] = $listTh;
        $dataTmp = null;
        if (!empty($dataSearch['department'])) {
            if ($dataSearch['department'] == 2) {
                $getData = (new AdminDavicookOrder())->getNoteReportOrder($dataSearch)->get();
            } else {
                $getData = (new AdminOrder())->getNoteReportOrder($dataSearch)->get();
            }
        } else {
            $objOrderDavicorp = (new AdminOrder())->getNoteReportOrder($dataSearch)->get();
            $objOrderDavicook = (new AdminDavicookOrder())->getNoteReportOrder($dataSearch)->get();
            $getData = $objOrderDavicorp->mergeRecursive($objOrderDavicook);

        }
        $dataTmp = [];
        foreach ($getData as $key => $datum) {
            $j = 0;
            foreach ($datum->details as $keyItem => $value) {
                if(!empty($value->comment)) {
                    $j++;
                }
            }
            if(!empty($datum->comment) || $j > 0) {
                $dataTmp[] = $datum;
            }
        }
        $dataTmp = $this->paginate($dataTmp);
//        dd($dataTmp);
        $data['dataTr'] = $dataTmp;
        $data['pagination'] = $dataTmp->appends(request()->except(['_token', '_pjax']))->links($this->templatePathAdmin . 'component.pagination');
        $data['resultItems'] = sc_language_render('admin.result_item', ['item_from' => $dataTmp->firstItem(), 'item_to' => $dataTmp->lastItem(), 'total' => $dataTmp->total()]);
        $currentDay = nowDateString();
        $from_day = $dataSearch['from_to'] ? $dataSearch['from_to'] : $currentDay ;
        $name = $dataSearch['name'] ? $dataSearch['name'] : '' ;
        $end_day = $dataSearch['end_to'] ? $dataSearch['end_to'] : $currentDay ;
        $departments = [
            0 => 'Tổng hợp',
            1 => 'Mầm non',
            2 => 'Hàng khô suất ăn',
        ];
        $optionDepartment = '';
        foreach ($departments as $key => $value) {
            $optionDepartment .= '<option  ' . (($dataSearch['department'] == $key) ? "selected" : "") . ' value="' . $key . '">' . $value . '</option>';
        }
        $data['topMenuRight'][] = '
                <form action="' . sc_route_admin('admin_report_note.index') . '" id="button_search">
                <br>
                    <div class="input-group float-right">
                        <div class="row">
                            <div class="col-lg-2 col-md-2 col-sm-12">
                                <div class="form-group">
                                    <div class="input-group">
                                        <select class="form-control select2" name="department" id="department_id">
                                        ' . $optionDepartment . '
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-2 col-md-2 col-sm-12">
                                <div class="form-group" >
                                    <div class="input-group ">
                                        <div style="margin-top: 10px;margin-right: 10px;color: #93A7C1"">Từ</div>
                                        <input type="text" name="from_to" id="from_to" class="form-control input-sm date_time rounded-0" style="text-align: center"  placeholder="Chọn ngày" value="' . $from_day . '"/> 
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-2 col-md-2 col-sm-12">
                                <div class="form-group">
                                    <div class="input-group">
                                        <div style="margin-top: 10px;margin-right: 10px;color: #93A7C1"">Đến</div>
                                        <input type="text" name="end_to" id="end_to" class="form-control input-sm date_time rounded-0" style="text-align: center" dplaceholder="Chọn ngày" value="' . $end_day . '"/>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-3 col-sm-12">
                                <div class="form-group">
                                    <div class="input-group">
                                        <input type="text" name="name" id="name" class="form-control input-sm rounded-0" value="' . ($name ?? '') . '" placeholder="Tên Kh, Mã KH, Ghi chú"/>
                                        <div class="input-group-append">
                                            <button type="submit" class="btn btn-primary  btn-flat mr-1"><i class="fas fa-search"></i></button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-3 col-sm-12">
                                <div class="row">
                                    <div class="form-group">
                                        <div class="input-group">
                                            <a class="btn  btn-success  btn-flat" title="" id="button_export_filter">
                                                    <i class="fa fa-file-export" title="' . sc_language_render('category-export') . '"></i> ' . sc_language_render('category-export') .
                                            '</a>&nbsp;
                                            <a href="javascript:void(0)" class="btn btn-flat btn btn-info text-white" onclick="savePdf()"><i class="fas fa-file-pdf"> </i> ' . sc_language_render("admin.report.print_pdf") . '</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
                ';
        //=menuSearch
        
        return view($this->templatePathAdmin . 'screen.list_note_report')
            ->with($data);

    }

    public function paginate($items, $perPage = 20, $page = null, $options = ['path' => ''])
    {
        $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);
        $items = $items instanceof Collection ? $items : Collection::make($items);
        return new LengthAwarePaginator($items->forPage($page, $perPage), $items->count(), $perPage, $page, $options);
    }

    public function exportExcelNote()
    {
        $dataSearch = [
            'from_to' => sc_clean(request('from_to') ?? ''),
            'end_to' => sc_clean(request('end_to') ?? ''),
            'name' => sc_clean(request('name') ?? ''),
            'department' => sc_clean(request('department') ?? ''),
        ];

        $from_to = $dataSearch['from_to'] ?? \Carbon\Carbon::make($dataSearch['from_to'])->format("d/m/Y");
        $end_to = $dataSearch['end_to'] ?? \Carbon\Carbon::make($dataSearch['end_to'])->format("d/m/Y");
        $fileName = 'BaoCaoGhiChu-'. $from_to .'-'.$end_to. '.xlsx';
        $fileName = str_replace("/","_",$fileName);
        $attributes = [
            'name_admin' =>  Admin::user()->name,
            'time' =>  now(),
            'desc' =>  $fileName,
            'type' =>  'BC Ghi Chu',
            'kind' =>  'Xuất Excel',
        ];
        AdminSystemChangeHistory::storeData($attributes);

        return Excel::download(new AdminReportNoteExport($dataSearch), $fileName);
    }
    public function saveNotePdf()
    {
        $dataSearch = [
            'from_to' => sc_clean(request('from_to') ?? ''),
            'end_to' => sc_clean(request('end_to') ?? ''),
            'name' => sc_clean(request('name') ?? ''),
            'department' => sc_clean(request('department') ?? ''),
        ];

        if (!empty($dataSearch['department'])) {
            if ($dataSearch['department'] == 2) {
                $dataTmp = (new AdminDavicookOrder())->getNoteReportOrder($dataSearch)->get();
            } else {
                $dataTmp = (new AdminOrder())->getNoteReportOrder($dataSearch)->get();
            }
        } else {
            $objOrderDavicorp = (new AdminOrder())->getNoteReportOrder($dataSearch)->get();
            $objOrderDavicook = (new AdminDavicookOrder())->getNoteReportOrder($dataSearch)->get();
            $dataTmp = $objOrderDavicorp->mergeRecursive($objOrderDavicook);
        }

        $data = [];
        foreach ($dataTmp as $key => $datum) {
            $j = 0;
            foreach ($datum->details as $keyItem => $value) {
                if(!empty($value->comment)) {
                    $j++;
                }
            }
            if(!empty($datum->comment) || $j > 0) {
                $data[] = $datum;
            }
        }

        if (!count($data) > 0) {
            return redirect()->route('admin_report_note.index')->with('error' , 'Không có dữ liệu');
        }

        $html = view($this->templatePathAdmin . 'print.note_report_print_template')
            ->with(['data' => $data, 'dataSearch' => $dataSearch])->render();

        $html = mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8');

        $fileName = 'BC ghi chú-'. $dataSearch['from_to'] .'-'.$dataSearch['end_to']. '.pdf';
        $fileName = str_replace("/","_",$fileName);
        $attributes = [
            'name_admin' =>  Admin::user()->name,
            'time' =>  now(),
            'desc' =>  $fileName,
            'type' =>  'BC Ghi Chu',
            'kind' =>  'Xuất Pdf',
        ];
        AdminSystemChangeHistory::storeData($attributes);
        return $html;
//        $domPdf = new Dompdf();
//        $domPdf->getOptions()->setChroot(public_path());
//        $domPdf->loadHtml($html, 'UTF-8');
//        $domPdf->setPaper('A4', 'portrait');
//        $domPdf->render();
//        return $domPdf->stream('BaoCaoGhichu-' . Carbon::now() . '.pdf');
    }

}
