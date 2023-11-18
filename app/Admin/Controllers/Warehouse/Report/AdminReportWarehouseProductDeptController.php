<?php

namespace App\Admin\Controllers\Warehouse\Report;


use App\Admin\Models\AdminExport;
use App\Admin\Models\AdminExportDetail;
use App\Admin\Models\AdminSystemChangeHistory;
use App\Admin\Models\AdminWarehouse;
use App\Admin\Models\ReportWarehouseProductDept;
use App\Admin\Models\ReportWarehouseProductDeptHistory;
use App\Exports\Warehouse\Report\ProductDept\ReportProductDeptExcel;
use App\Front\Models\ShopCategory;
use App\Front\Models\ShopCustomer;
use App\Front\Models\ShopDavicookCustomer;
use App\Front\Models\ShopDepartment;
use App\Front\Models\ShopGenId;
use App\Front\Models\ShopProduct;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use SCart\Core\Admin\Admin;
use SCart\Core\Admin\Controllers\RootAdminController;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class AdminReportWarehouseProductDeptController extends RootAdminController
{
    private $numPaginate;
    public function __construct()
    {
        $this->numPaginate = 30;
        parent::__construct();
    }

    /**
     * Report two targets.
     */
    public function index(){
        $data = [
            'title' => "Báo cáo nợ hàng của khách hàng",
            'icon' => 'fa fa-indent',
            'removeList' => 1,
        ];

        $listTh = [
            'export_date' => 'Ngày xuất kho',
            'product_code' => 'Mã SP',
            'product_name' => 'Tên SP',
            'export_code' => 'Phiếu xuất kho',
            'order_id_name' => 'Mã đơn hàng',
            'customer_name' => 'Tên khách hàng',
            'qty_dept' => 'Số lượng nợ KH',
            'qty_export' => 'Số lượng đã trả<br/>(số lượng xuất)',
            'qty_remain' => 'Số lượng còn phải trả',
        ];

        $dataSearch = [
            'date_start' => sc_clean(request('date_start') ?? ''),
            'date_end' => sc_clean(request('date_end') ?? ''),
            'category' => sc_clean(request('category') ?? ''),
            'product_kind' => sc_clean(request('product_kind') ?? ''),
            'customer' => sc_clean(request('customer') ?? ''),
            'keyword' => sc_clean(request('keyword') ?? ''),
            'key_search' => sc_clean(request('key_search') ?? ''),
        ];
        $dataTr = [];
        $cssTh = [
            'STT' => 'max-width:50px;',
            'bill_date' => 'min-width: 60px; max-width:90px;',
            'product_code' => 'text-align: center; min-width: 60px; max-width:60px;',
            'product_name' => 'min-width: 150px',
            'order_id_name' => 'text-align: center; min-width: 60px; max-width:100px; ',
            'explain' => 'text-align: center; min-width: 60px; max-width:100px;',
            'qty_import' => 'text-align: right; min-width: 60px; max-width:120px; ',
            'qty_export' => 'text-align: right; min-width: 60px; max-width:120px;',
            'qty_stock' => 'text-align: right; min-width: 60px; max-width:120px;',
            'object_name' => 'min-width: 150px',
        ];
        $cssTd = [
            'STT' => 'text-align: center;',
            'bill_date' => '',
            'product_code' => 'text-align: center',
            'product_name' => ' ',
            'order_id_name' => 'text-align: center',
            'explain' => 'text-align: center;',
            'qty_import' => 'text-align: right;',
            'qty_export' => 'text-align: right; ',
            'qty_stock' => 'text-align: right; ',
            'object_name' => 'text-align: left;',
        ];

        $data['cssTh'] = $cssTh;
        $data['cssTd'] = $cssTd;
        $dataTmp = $this->getReportByDataSearch($dataSearch)->paginate($this->numPaginate);
        foreach ($dataTmp as $key => $row) {
            $dataTr[$row->id] = [
                'id' => $row->id,
                'export_date' => formatDateVn($row->export_date),
                'product_code' => $row->product_code,
                'product_name' => '<a class="text-blue" title="" onclick="showModalHistory(\'' . $row->id . '\');">'.$row->product_name.'</a>',
                'export_code' => $row->export_code,
                'order_id_name' => $row->order_id_name,
                'customer_name' => $row->customer_name,
                'qty_dept' => $row->qty_dept,
                'qty_export' => $row->qty_export,
                'qty_remain' => $row->qty_dept - $row->qty_export,
            ];
        }
        if ($dataSearch['key_search'] == 'search') {
            $data['dataTr'] = $dataTr;
        } else {
            $data['dataTr'] = [];
        }
        $data['originalList'] = array_map(function ($item){
            return $item;
//            $temp = $item->toArray();
//            $temp['order'] = $temp['order_corp'] ?? $temp['order_cook'];
//            $temp['customer'] = $temp['customer_corp'] ?? $temp['customer_cook'];
//            unset($temp['order_cook'], $temp['order_corp'], $temp['customer_cook'], $temp['customer_corp']);
//            return $temp;
        }, $dataTmp->items());

        $data['listTh'] = $listTh;
        $optionWarehouse = '';
        $dataWarehouse = AdminWarehouse::get();
        foreach ($dataWarehouse as $key => $item) {
            $optionWarehouse .= '<option value="' . $item->id . '">' . $item->name . '</option>';
        }
        $data['optionWarehouse'] = $optionWarehouse;


        //Categories option
        $categories = ShopCategory::where('status', 1)->get()->keyBy('id');
        $optionCategories = '';
        foreach ($categories as $key => $value) {
            $optionCategories .= '<option  ' .(($dataSearch['category'] == $key) ? "selected" : "") . ' value="' . $key . '">' . ($value->name ?? '') . '</option>';
        }
        //Customer option
        $customerCorp = ShopCustomer::where('status', 1)->select('id', 'name', 'customer_code')->get()->keyBy('id');
        $customerCook = ShopDavicookCustomer::where('status', 1)->select('id', 'name', 'customer_code')->get()->keyBy('id');
        $customers = array_merge($customerCook->toArray(), $customerCorp->toArray());
        $optionCustomer = '';
        foreach ($customers as $key => $customer) {
            $optionCustomer .= '<option  ' . (is_array($dataSearch['customer']) ? (in_array(($customer['customer_code'] ?? []), $dataSearch['customer']) ? "selected" : "") : '' ) . ' value="' . ($customer['customer_code'] ?? '') . '">' . ($customer['name'] ?? '') . '</option>';
        }

        $currentDay = nowDateString();
        $from_day = $dataSearch['date_start'] ? $dataSearch['date_start'] : $currentDay ;
        $end_day = $dataSearch['date_end'] ? $dataSearch['date_end'] : $currentDay ;

        $data['pagination'] = $dataTmp->appends(request()->except(['_token', '_pjax']))->links($this->templatePathAdmin . 'component.pagination');
        $data['resultItems'] = sc_language_render('admin.result_item', ['item_from' => $dataTmp->firstItem(), 'item_to' => $dataTmp->lastItem(), 'total' => $dataTmp->total()]);
        //menuRight
        $data['menuRight'][] = '<button data-perm="warehouse_report_dept:createExport" class="btn btn-primary btn-flat" onclick="handleWarehouseExport()"><i class="fa fa-plus"></i> Tạo phiếu xuất</button>&nbsp;
                                <button data-perm="warehouse_report_dept:print_stamp" href="javascript:void(0)" class="btn btn-flat btn-warning text-white" onclick="printStampPdf()">In tem</button>&nbsp;
                                <a data-perm="warehouse_report_dept:export" class="btn btn-success btn-flat" title="" id="button_export">
                                <i class="fa fa-file-export" title="Xuất Excel"></i> Xuất Excel</a>&nbsp;
                                <a data-perm="warehouse_report_dept:print" href="javascript:void(0)" class="btn btn-flat btn-info" onclick="savePdf()"><i class="fas fa-file-pdf"></i>&nbsp;Xuất PDF</a>&nbsp;
                                 ';
        //=menuRight
        $data['topMenuRight'][] = '
                <form action="' . sc_route_admin('warehouse_report_product_dept.index') . '" id="button_search">
                <input type="hidden" name="key_search" value="search" id="key_search">
                <br>
                    <div class="">
                        <div class="row">
                            <div class="col-lg-2 col-md-2 col-sm-6">
                                <div class="form-group" >
                                    <label>Từ ngày</label>
                                    <div class="input-group " >
                                        <input type="text" name="date_start" id="date_start" class="form-control input-sm date_time rounded-0" style="text-align: center" placeholder="Chọn ngày" value="' . $from_day . '" />
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-2 col-md-2 col-sm-6">
                                <div class="form-group">
                                    <label>Đến ngày</label>
                                    <div class="input-group" >
                                        <input type="text" name="date_end" id="date_end" class="form-control input-sm date_time rounded-0" style="text-align: center" placeholder="Chọn ngày" value="' . $end_day . '"  />
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-2 col-md-2 col-sm-12">
                                <div class="form-group">
                                    <label>Danh mục mặt hàng</label>
                                    <div class="input-group">
                                        <select class="form-control select2" name="category" id="category" style="width: 100%">
                                            <option value="">Tất cả danh mục</option>
                                            '.$optionCategories.'
                                        </select>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-lg-2 col-md-2 col-sm-12">
                                <div class="form-group">
                                    <label>Loại mặt hàng</label>
                                    <div class="input-group">
                                        <select class="form-control select2" name="product_kind" id="product_kind" style="width: 100%">
                                            <option value="">Tất cả</option>
                                            <option value="0" '.($dataSearch['product_kind'] == 0 ? 'selected' : "").'>Hàng khô</option>
                                            <option value="1" '.($dataSearch['product_kind'] == 1 ? 'selected' : "").'>Hàng tươi</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-lg-2 col-md-2 col-sm-12">
                                    <div class="form-group">
                                        <label>Chọn khách hàng:</label>
                                        <div class="input-group">
                                            <select id="customer" style="width: 100%" class="form-control rounded-0" name="customer[]" multiple="multiple">
                                            ' . $optionCustomer . '
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                
                            <div class="col-lg-2 col-md-2 col-sm-12">
                                <div class="form-group">
                                    <label>Mã phiếu : </label>
                                    <div class="input-group">
                                        <input type="text" name="keyword" id="keyword" class="form-control rounded-0 float-right" placeholder="Mã tên phiếu, Mã tên khách hàng" value="'.$dataSearch['keyword'].'">
                                        <div class="input-group-append">
                                            <button type="submit" class="btn btn-primary  btn-flat"><i class="fas fa-search"></i></button>
                                        </div>
                                    </div>
                                </div>
                            </div> 
                        </div>
                    </div>
                </form>
                ';

        //=menuSearch
        return view($this->templatePathAdmin . 'screen.warehouse.report.warehouse_dept.index')
            ->with($data);
    }

    private function getReportByDataSearch($dataSearch , $ids = [])
    {
        $customerFilter = $dataSearch['customer'] ?? [];
        $fromFilter = $dataSearch['date_start'] ?? '';
        $endFilter = $dataSearch['date_end'] ?? '';
        $categoryFilter = $dataSearch['category'] ?? '';
        $productKindFiler = $dataSearch['product_kind'] ?? '';
        $keyword = $dataSearch['keyword'];
        $query = new ReportWarehouseProductDept();
        if(!empty($customerFilter)){
            $query = $query->whereIn('customer_code', $customerFilter);
        }
        if(!empty($categoryFilterr)){
            $query = $query->where('cateogry_id', $categoryFilter);
        }

        if(!empty($fromFilter)){
            $query = $query->where('export_date', '>=', \Carbon\Carbon::createFromFormat('d/m/Y', $fromFilter));
        }
        if(!empty($endFilter)){
            $query = $query->where('export_date', '<=', Carbon::createFromFormat('d/m/Y', $endFilter));
        }
        if(!empty($productKindFiler)){
            $query = $query->where('product_kind', $productKindFiler);
        }
        if(!empty($keyword)){
            $query = $query->where(function ($sql) use ($keyword) {
                $sql->where('product_name', 'like', '%' . $keyword . '%');
                $sql->orWhere('product_code', 'like', '%' . $keyword . '%');
            });
        }
        if(!empty($ids)){
            $query = $query->whereIn('id', $ids);
        }

        return $query;
    }

    /**
     * Xuất file excel đơn hàng nhập.
     * @return \Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function exportExcel()
    {
        $dataSearch = [
            'date_start' => sc_clean(request('date_start') ?? ''),
            'date_end' => sc_clean(request('date_end') ?? ''),
            'category' => sc_clean(request('category') ?? ''),
            'product_kind' => sc_clean(request('product_kind') ?? ''),
            'customer' => sc_clean(request('customer') ?? ''),
            'keyword' => sc_clean(request('keyword') ?? ''),
        ];
        $dataOrderMerge = $this->getReportByDataSearch($dataSearch)->get();
        if (!count($dataOrderMerge) > 0) {
            return redirect()->route('warehouse_report_product_dept.index')->with('error' , 'Không có dữ liệu!');
        }
        if (count($dataOrderMerge) > 20000) {
            return redirect()->route('warehouse_report_product_dept.index')->with('error' , 'Dữ liệu quá lớn!');
        }
        $from_to = str_replace("/","_",$dataSearch['date_start']);
        $end_to = str_replace("/","_",$dataSearch['date_end']);
        $fileName = 'BCNOHANG_'.$from_to.'-'.$end_to. '.xlsx';

        return Excel::download(new ReportProductDeptExcel($dataSearch, $dataOrderMerge), $fileName);
    }

    public function paginate($items, $perPage = 20, $page = null, $options = ['path' => ''])
    {
        $perPage = $this->numPaginate;
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
            'date_start' => sc_clean(request('date_start') ?? ''),
            'date_end' => sc_clean(request('date_end') ?? ''),
            'category' => sc_clean(request('category') ?? ''),
            'product_kind' => sc_clean(request('product_kind') ?? ''),
            'customer' => sc_clean(request('customer') ?? ''),
            'keyword' => sc_clean(request('keyword') ?? ''),
        ];
        $dataOrderMerge = $this->getReportByDataSearch($dataSearch)->get();
        if (!count($dataOrderMerge) > 0) {
            return redirect()->route('warehouse_report_product_dept.index')->with('error' , 'Không có dữ liệu!');
        }
        if (count($dataOrderMerge) > 20000) {
            return redirect()->route('warehouse_report_product_dept.index')->with('error' , 'Dữ liệu quá lớn!');
        }
        $html = view($this->templatePathAdmin . 'screen.warehouse.report.warehouse_dept.print_pdf')
            ->with(['data' => $dataOrderMerge, 'dataSearch' => $dataSearch])->render();

        $html = mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8');
        return $html;
    }

    public function createExportOrder(){
        $warehouse_id = request('warehouse');
        $data_export = request('data_export');
        $dataExportDetail = json_decode($data_export, true);
        $dataWarehouse = AdminWarehouse::find($warehouse_id);
        $user = Admin::user();
        DB::beginTransaction();
        try{
            $dataInsertOrderExport = [
                'id_name' => ShopGenId::genNextId('order_export'),
                'customer_id' =>  '',
                'customer_name' => 'Xuất kho từ báo cáo hàng ngày (BCNH)',
                'customer_code' =>  '',
                'customer_addr' => '',
                'phone' => '',
                'email' =>  '',
                'warehouse_id' => $warehouse_id,
                'warehouse_name' => $dataWarehouse->name,
                'warehouse_code' => $dataWarehouse->warehouse_code,
                'date_export' => Carbon::now()->format('Y-m-d'),
                'type_order' => 4,
                'status' => 1,
                'edit' => 0,
                'note' => '',
            ];
            $dataExport = AdminExport::create($dataInsertOrderExport);
            $dataDetailInsert = [];
            foreach ($dataExportDetail as $item) {
                $detail = ReportWarehouseProductDept::find($item['id']);
                $qty_reality = (float)str_replace(',', '', $item['qty_reality']);
                if ($detail) {
                    $dataDetailInsert[] = [
                        'id' => sc_uuid(),
                        'export_id' => $dataExport->id,
                        'export_code' => $dataExport->id_name,
                        'product_id' => $detail->product_id,
                        'product_name' => $detail->product_name,
                        'product_sku' => $detail->product_code,
                        'unit' =>  $detail->product_unit,
                        'product_kind' => $detail->product_kind,
                        'qty' => $detail->qty_dept,
                        'qty_reality' => $qty_reality ?? 0,
                        'order_detail_id' => $detail->id,
                        'order_id' => $detail->order_id,
                        'order_id_name' => $detail->order_id_name,
                        'order_explain' => $detail->order_explain,
                        'order_object_id' => $detail->order_object,
                        'order_delivery_date' => now(),
                        'category_id' =>  $detail->category_id,
                        'department_id' =>  $detail->department_id,
                        'customer_name' =>  $detail->customer_name,
                        'customer_code' =>  $detail->customer_code,
                        'zone_id' => $detail->zone_id,
                        'comment' => '',
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now()

                    ];
                    $dataInsertHistory = [
                        'id' => sc_uuid(),
                        'report_product_dept_id' => $detail->id,
                        'user_id' => $user->id ?? '',
                        'user_name' => $user->name ?? '',
                        'qty_dept_origin' => $detail->qty_dept - $detail->qty_export,
                        'qty_export_current' => $qty_reality,
                    ];
                    $detail->qty_export = $detail->qty_export + $qty_reality;
                    $detail->qty_export_final = $qty_reality;
                    $detail->save();
                    ReportWarehouseProductDeptHistory::create($dataInsertHistory);
                }
            }
            AdminExportDetail::insert($dataDetailInsert);
            DB::commit();
            return response()->json(['success' => true, 'message' => 'Dữ liệu xuất kho thành công']);
        } catch (\Throwable $e) {
            DB::rollback();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function getDataShowHistory()
    {
        $id = request('id');
        $report = ReportWarehouseProductDept::with('history')->find($id);

        if ($report) {
            return response()->json(['report' => $report, 'error' => 0]);
        }

        return response()->json(['report' => [], 'error' => 1]);
    }

    public function previewStampByHistory()
    {
        $ids = request('ids');
        ReportWarehouseProductDeptHistory::with('report')->whereIn('ids', $ids);
    }

    /**
     * Preview stamp trên trình duyệt.
     * @return false|string|string[]|void|null
     */
    public function previewStampPdf()
    {
        $ids = [];
        $data['listItem'] = [];
        $data['listQr'] = [];

        if (!empty(request('ids'))) {
            $ids = explode(',', request('ids'));
        }

        $dataSearch = [
            'from_to' => sc_clean(request('from_to') ?? ''),
            'end_to' => sc_clean(request('end_to') ?? ''),
            'category' => sc_clean(request('category') ?? ''),
            'product_kind' => sc_clean(request('product_kind') ?? ''),
            'customer' => sc_clean(request('customer')) ?  explode(',', sc_clean(request('customer'))) : [],
            'keyword' => sc_clean(request('keyword') ?? ''),
            'key_search' => sc_clean(request('key_search') ?? ''),
        ];


        $fileName = 'BaoCaoInTem-'. $dataSearch['from_to'] .'-'. $dataSearch['end_to']. '.pdf';
        $fileName = str_replace("/","_",$fileName);
        $attributes = [
            'name_admin' =>  Admin::user()->name,
            'time' =>  now(),
            'desc' =>  $fileName,
            'type' =>  'BC In Tem',
            'kind' =>  'In tem',
        ];
        AdminSystemChangeHistory::storeData($attributes);
        $arrDepartment = [];
        $arrDepartment = ShopDepartment::get()->pluck('short_name', 'id');
        $data = $this->getReportByDataSearch($dataSearch, $ids)->get();
        $arrListStampDetails = [];
        $qrList = new \Illuminate\Support\Collection([]);
        foreach ($data as $item) {
            $url = $item->product ? ($item->product->qr_code ?? "") : "";
            if($url){
                $qrSearch = $qrList->where("url", $url)->first();
                if(!$qrSearch){
                    $qrList->push(ShopProduct::generateQr($url));
                }
            }
            $arrListStampDetails[] = [
                'id' => $item->id_change,
                'order_id' => $item->order_id,
                'id_barcode' => $item->order_id_barcode ?? "",
                'order_name' => $item->order_id_name ?? "",
                'product_id' => $item->product_id ?? "",
                'delivery_time' => $item->export_date ?? "",
                'product_name' => $item->product_name ?? "",
                'name_unit' => $item->product_unit ?? "",
                'qty' => $item->qty_export_final ?? "",
                'customer_code' => $item->customer_code ?? "",
                'customer_name' => $item->customer_short_name ?? "",
                'customer_num' => $item->customer_num ?? "",
                'short_name' => $item->department_id ? $arrDepartment[$item->department_id] : "CTCP DAVICOOK HN",
                'qr_code' => $item->product ? ($item->product->qr_code ?? "") : "",
                'order_num' => $item->product ? ($item->product->order_num ?? "") : "",
                'category_id' => $item->category_id ?? "",
                'product_sku' => $item->product_code ?? "",
            ];
        }
        $data['listQr'] = $qrList->toArray();
        $data['listItem'] = $arrListStampDetails ?? [];

        $qrList = new Collection($data['listQr']);
        $dataItem = new Collection($data['listItem']);
        $dataItem = $dataItem->sortBy([
            'order_num',
            'qty'
        ]);

        $dataItem = $dataItem->values();
        $html = view($this->templatePathAdmin . 'print.stamp_report_print_template')
            ->with(['data' => $dataItem, 'qr' => $qrList, 'dataSearch' => $dataSearch])->render();
        $html = mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8');

        return $html;
    }
}
