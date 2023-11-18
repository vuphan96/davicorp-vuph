<?php

namespace App\Admin\Controllers\Warehouse\Report;

use App\Admin\Models\AdminExport;
use App\Admin\Models\AdminExportDetail;
use App\Admin\Models\AdminImport;
use App\Admin\Models\AdminImportDetail;
use App\Admin\Models\AdminSystemChangeHistory;
use App\Admin\Models\AdminWarehouse;
use App\Admin\Models\AdminWarehouseProduct;
use App\Exports\AdminReportTargetExport;
use App\Admin\Models\AdminCategory;
use App\Admin\Models\AdminDavicookOrder;
use App\Admin\Models\AdminOrder;
use App\Exports\Warehouse\Report\OrderImport\ExportOrderImport;
use App\Exports\Warehouse\Report\ProductReturn\ExportReportProductReturn;
use App\Front\Models\ShopCategory;
use App\Front\Models\ShopCustomer;
use App\Front\Models\ShopDavicookCustomer;
use App\Front\Models\ShopDavicookOrderReturnHistory;
use App\Front\Models\ShopDepartment;
use App\Front\Models\ShopGenId;
use App\Front\Models\ShopOrderReturnHistory;
use App\Front\Models\ShopProduct;
use App\Front\Models\ShopSupplier;
use App\Front\Models\ShopZone;
use Dompdf\Dompdf;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use SCart\Core\Admin\Admin;
use SCart\Core\Admin\Controllers\RootAdminController;
use Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use PDF;
use ZipArchive;
use function PHPUnit\Framework\never;

class AdminReportReturnImportOrderController extends RootAdminController
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
            'title' => "Báo cáo nhập hàng đối với hàng trả",
            'icon' => 'fa fa-indent',
            'removeList' => 1, // 1 - Enable function delete list item
            'buttonRefresh' => 1, // 1 - Enable button refresh
            'buttonSort' => 1, // 1 - Enable button sort
            'css' => '',
            'js' => '',
            'method' => 'delete',
            'url_action' => sc_route_admin('order_import.create'),
        ];
        //Process add content
        $data['menuRight'] = sc_config_group('menuRight', \Request::route()->getName());
        $data['menuLeft'] = sc_config_group('menuLeft', \Request::route()->getName());
        $data['topMenuRight'] = sc_config_group('topMenuRight', \Request::route()->getName());
        $data['topMenuLeft'] = sc_config_group('topMenuLeft', \Request::route()->getName());
        $data['blockBottom'] = sc_config_group('blockBottom', \Request::route()->getName());
        $listTh = [
            'date' => 'Ngày trả hàng',
            'product_code' => 'Mã sản phẩm',
            'product_name' => 'Tên sản phẩm',
            'code_order' => 'Mã đơn hàng',
            'name_customer' => 'Tên khách hàng',
            'qty_return' => 'SL trả hàng',
            'qty_entered' => 'SL đã nhập',
            'qty_yet' => 'SL chưa nhập'
        ];
        $dataSearch = [
            'keyword' => sc_clean(request('keyword') ?? ''),
            'date_start' => sc_clean(request('date_start') ?? nowDateString()),
            'date_end' => sc_clean(request('date_end') ?? nowDateString()),
            'category' => sc_clean(request('category') ?? ''),
            'customer' => sc_clean(request('customer') ?? []),
            'product_kind' => sc_clean(request('product_kind') ?? ''),
            'key_search' => sc_clean(request('key_search') ?? ''),
        ];
        $dataTr = [];
        $cssTh = [
            'date' => 'text-align: center; width:12%',
            'product_code' => 'text-align: center; width:10%',
            'product_name' => 'text-align: center; with:25%',
            'code_order' => 'text-align: center; width:10%',
            'name_customer' => 'text-align: center; with:2%',
            'qty_return' => 'text-align: center; width:10%',
            'qty_entered' => 'text-align: center; width:10%',
            'qty_yet' => 'text-align: center; width:10%'
        ];
        $cssTd = [
            'date' => 'text-align: center',
            'qty_return' => 'text-align: center',
            'qty_entered' => 'text-align: center',
            'qty_yet' => 'text-align: center',
            'product_code' => 'text-align: center',
            'code_order' => 'text-align: center',
        ];

        $data['cssTh'] = $cssTh;
        $data['cssTd'] = $cssTd;
        if (!empty($dataSearch['key_search'])) {
            $dataOrderMerge = $this->getDataImportOrder($dataSearch['product_kind'],$dataSearch);
        } else {
            $dataOrderMerge = new Collection();
        }
        $sorted = $dataOrderMerge->sortBy(['created_at']);
        $dataOrderMergePaginate = $this->paginate($sorted);
        foreach ($dataOrderMergePaginate as $key => $row) {
            $dataTr[$row['id']] = [
                'date_return' => Carbon::parse($row->created_at)->format('d/m/Y'),
                'product_code' => $row['product_code'] ?? '',
                'product_name' => $row['product_name'] ?? '',
                'code_order' => $row['order_id_name'] ?? '',
                'name_customer' => $row['customer_name'] ?? '',
                'qty_return' => number_format($row['return_qty'], 2),
                'qty_entered' => $row['qty_import'] ?? '',
                'qty_yet' => $row['return_qty'] - $row['qty_import'],
                'type_order' => $row['type_order'],
            ];
        }
        $dataOrderMergePaginate = $this->paginate($dataOrderMerge);
        $dataWarehouse = AdminWarehouse::get();
        $page = request('page') ?? 1;
        $data['dataTr'] = $dataTr;

        $ofsetStart = ($page - 1) * ($this->numPaginate);
        $ofsetEnd = ($page - 1) * ($this->numPaginate) + count($dataOrderMergePaginate);
        $data['ofsetEnd'] = $ofsetEnd;
        $data['ofsetStart'] = $ofsetStart;
        $data['listTh'] = $listTh;
        $data['dataWarehouse'] = $dataWarehouse;

        $categories = ShopCategory::all();
        $optionCategories = '';
        foreach ($categories as $key => $category) {
            $optionCategories .= '<option  ' . (($dataSearch['category'] == $category->id) ? "selected" : "") . ' value="' . $category->id . '">' . $category->name . '</option>';
        }

        $typeSearch = [
            0 => 'Tất cả mặt hàng',
            1 => 'Hàng khô Davicorp',
            2 => 'Hàng tươi Davicorp',
            3 => 'Hàng khô Davicook',
            4 => 'Hàng tươi Davicook',
        ];
        $optionTypeSearch = '';
        foreach ($typeSearch as $key => $value) {
            $optionTypeSearch .= '<option  ' . (($dataSearch['product_kind'] == $key) ? "selected" : "") . ' value="' . $key . '">' . $value . '</option>';
        }

        $customerDavicorp = ShopCustomer::all();
        $customerDavicook = ShopDavicookCustomer::all();
        $customers = array_merge($customerDavicorp->toArray(), $customerDavicook->toArray());
        $optionCustomers = '';
        foreach ($customers as $key => $customer) {
            $optionCustomers .= '<option  ' . (in_array($customer['customer_code'], $dataSearch['customer']) ? "selected" : "") . ' value="' . $customer['customer_code'] . '">' . $customer['name'] . '</option>';
        }
        $data['pagination'] = $dataOrderMergePaginate->appends(request()->except(['_token', '_pjax']))->links($this->templatePathAdmin . 'component.pagination');
        $data['resultItems'] = sc_language_render('admin.result_item', ['item_from' => $ofsetStart ? $ofsetStart : 0, 'item_to' =>(empty($check)) ? $ofsetEnd : count($dataOrderMerge), 'total' => count($dataOrderMerge)]);
        //menuRight
        $data['menuRight'][] = '
                                <a href="javascript:void(0)" class="btn btn-flat btn-info" onclick="openPopup()"><i class="fas fa-file-pdf"></i>&nbsp;Tạo phiếu nhập</a>&nbsp;&nbsp;
                                <a class="btn btn-success btn-flat" title="" id="btn_export">
                                <i class="fa fa-file-export" title="Xuất Excel"></i> Xuất Excel</a> &nbsp;
                                <a href="javascript:void(0)" class="btn btn-flat btn-info" onclick="savePdf()"><i class="fas fa-file-pdf"></i>&nbsp;Xuất PDF</a>&nbsp;&nbsp;
                                 ';
        //=menuRight
        $data['topMenuRight'][] = '
                <form action="' . sc_route_admin('warehouse_report_return_import.index') . '" id="button_search">
                <input type="hidden" name="key_search" value="search" id="key_search">
                <br>
                    <div class="">
                    <div class="container">
                        <div class="row">
                            <div class="col-lg-2 col-md-2 col-sm-6">
                                <div class="form-group" >
                                    <label>Từ ngày</label>
                                    <div class="input-group " >
                                        <input type="text" name="date_start" id="date_start" class="form-control input-sm date_time rounded-0" style="text-align: center" placeholder="Chọn ngày" value="' . $dataSearch['date_start'] . '" />
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-2 col-md-2 col-sm-6">
                                <div class="form-group">
                                    <label>Đến ngày</label>
                                    <div class="input-group" >
                                        <input type="text" name="date_end" id="date_end" class="form-control input-sm date_time rounded-0" style="text-align: center" placeholder="Chọn ngày" value="' . $dataSearch['date_end'] . '"  />
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-2 col-md-2 col-sm-12">
                            <div class="form-group">
                                <label>Khách hàng</label>
                                <div class="input-group">
                                    <select class="form-control select-custom" name="customer[]" id="customer" style="width: 100%" multiple="multiple">
                                        ' . $optionCustomers . '
                                    </select>
                                </div>
                            </div>
                             </div>
                            <div class="col-lg-2 col-md-2 col-sm-12">
                                <div class="form-group">
                                    <label>Danh mục mặt hàng</label>
                                    <div class="input-group">
                                        <select class="form-control" name="category" id="category" style="width: 100%">
                                            <option value="">Tất cả danh mục</option>
                                            ' . $optionCategories . '
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-2 col-md-2 col-sm-12">
                                <div class="form-group">
                                    <label>Loại mặt hàng</label>
                                    <div class="input-group">
                                        <select class="form-control" name="product_kind" id="product_kind" style="width: 100%">
                                            ' . $optionTypeSearch . '
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-2 col-md-2 col-sm-12" style="padding-left: 0px;">
                                <div class="form-group">
                                    <label>Tìm kiếm</label>
                                    <div class="input-group">
                                        <input type="text" name="keyword" id="keyword" class="form-control rounded-0 float-right" placeholder="' . sc_language_render('admin.name_target.search_placeholder') . '" value="' . $dataSearch['keyword'] . '">
                                        <div class="input-group-append">
                                            <button type="submit" class="btn btn-primary  btn-flat" id="submit_report_target"><i class="fas fa-search"></i></button>
                                        </div>
                                    </div>
                                </div>
                            </div>                     
                            
                        </div>
                    </div>
                </div>
            </form>
                ';

        //=menuSearch
        return view($this->templatePathAdmin . 'screen.warehouse.report.return_import.index')
            ->with($data);
    }

    /**
     * Xuất file excel đơn hàng nhập.
     * @return \Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function exportExcel()
    {
        $dataSearch = [
            'keyword' => sc_clean(request('keyword') ?? ''),
            'date_start' => sc_clean(request('date_start') ?? nowDateString()),
            'date_end' => sc_clean(request('date_end') ?? nowDateString()),
            'category' => sc_clean(request('category') ?? ''),
            'customer' => request('customer') != '' ? explode(',', request('customer')) : [],
            'product_kind' => sc_clean(request('product_kind') ?? ''),
        ];
        $dataOrderMerge = $this->getDataImportOrder($dataSearch['product_kind'],$dataSearch);
        $data = $dataOrderMerge;
        if (!count($data) > 0) {
            return redirect()->route('warehouse_report_return_import.index')->with('error' , 'Không có dữ liệu!');
        }
        if (count($data) > 20000) {
            return redirect()->route('warehouse_report_return_import.index')->with('error' , 'Dữ liệu quá lớn!');
        }

        $from_to = str_replace("/","_",$dataSearch['date_start']);
        $end_to = str_replace("/","_",$dataSearch['date_end']);
        $fileName = 'BCNHAPHANG_'.$from_to.'-'.$end_to. '.xlsx';

        return Excel::download(new ExportReportProductReturn($dataSearch, $data), $fileName);
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
            'keyword' => sc_clean(request('keyword') ?? ''),
            'date_start' => sc_clean(request('date_start') ?? nowDateString()),
            'date_end' => sc_clean(request('date_end') ?? nowDateString()),
            'category' => sc_clean(request('category') ?? ''),
            'customer' => request('customer') != '' ? explode(',', request('customer')) : [],
            'product_kind' => sc_clean(request('product_kind') ?? ''),
            'key_search' => sc_clean(request('key_search') ?? ''),
         ];
         $dataOrderMerge = $this->getDataImportOrder($dataSearch['product_kind'],$dataSearch);
         if (!count($dataOrderMerge) > 0) {
             return redirect()->route('warehouse_report_return_import.index')->with('error' , 'Không có dữ liệu!');
         }

         if (count($dataOrderMerge) > 20000) {
             return redirect()->route('warehouse_report_return_import.index')->with('error' , 'Dữ liệu quá lớn!');
         }

         $html = view($this->templatePathAdmin . 'screen.warehouse.report.return_import.print_pdf_template')
             ->with(['data' => $dataOrderMerge, 'dataSearch' => $dataSearch])->render();

         $html = mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8');
         return $html;
    }

    private function getDataImportOrder($product_kind,$dataSearch)
    {
        if ($product_kind == 4 || $product_kind == 3) {
            // Lấy trả hàng khô hoặc tươi Darvicook
            $dataOrderMerge = $this->getOrderReportReturnHistoryDavicook($dataSearch);
        } elseif ($product_kind == 1 || $product_kind == 2) {
         // Lấy trả hàng khô hoặc tươi Darvicorp
            $dataOrderMerge = $this->getOrderReportReturnHistoryDavicorp($dataSearch);
        } else {
            // Lấy tổng hợp Khô và tươi davicorp + davicook.
            $dataReturnOrderDavicook = $this->getOrderReportReturnHistoryDavicook($dataSearch);
            $dataReturnOrderDavicorp = $this->getOrderReportReturnHistoryDavicorp($dataSearch);
            $dataOrderMerge = $dataReturnOrderDavicorp->mergeRecursive($dataReturnOrderDavicook);
        }
        return $dataOrderMerge;
    }

    /**
     * Lấy data trả hàng davicook.
     * @param array $dataSearch
     * @return mixed
     */
    public function getOrderReportReturnHistoryDavicook(array $dataSearch)
    {
        $keyword = $dataSearch['keyword'] ?? '';
        $product_kind = $dataSearch['product_kind'] ?? '';
        $category = $dataSearch['category'] ?? '';
        $customer = $dataSearch['customer'] ?? [];
        # Lấy đơn hàng trả davicook.
        $dataReturn = new ShopDavicookOrderReturnHistory();
        if($dataSearch['date_start']){
            $dataReturn = $dataReturn->whereDate("created_at", ">=", convertVnDateObject($dataSearch['date_start'])->toDateString());
        }
        if($dataSearch['date_end']){
            $dataReturn = $dataReturn->whereDate("created_at", "<=", convertVnDateObject($dataSearch['date_end'])->toDateString());
        }
        if ($keyword) {
            $dataReturn = $dataReturn->where(function ($sql) use ($keyword) {
                $sql->where("product_code", "like", "%" . $keyword . "%")
                ->orWhere("product_name", "like", "%" . $keyword . "%")
                ->orWhere("order_id_name", "like", "%" . $keyword . "%")
                ->orWhere("customer_name", "like", "%" . $keyword . "%")
            ;
            });
        }
        if($product_kind =='3'){
            $dataReturn = $dataReturn->where('product_kind', "=", '0');
        }
        if($product_kind =='4'){
            $dataReturn = $dataReturn->where('product_kind', "=", '1');
        }
        if($category){
            $dataReturn = $dataReturn->where('category_id', "=", $category);
        }
        if($customer){
            $dataReturn = $dataReturn->whereIn('customer_code', $customer);
        }
        $dataReturn = $dataReturn->orderBy("created_at", 'desc')->orderBy('customer_name')->get();
        return $dataReturn;
    }

    /**
     * Lấy data trả hàng davicorp.
     * @param array $dataSearch
     * @return mixed
     */
    public function getOrderReportReturnHistoryDavicorp(array $dataSearch)
    {
        $keyword = $dataSearch['keyword'] ?? '';
        $product_kind = $dataSearch['product_kind'] ?? '';
        $category = $dataSearch['category'] ?? '';
        $customer = $dataSearch['customer'] ?? [];
        # Lấy đơn hàng trả davicook.
        $dataReturn = new ShopOrderReturnHistory();
        if($dataSearch['date_start']){
            $dataReturn = $dataReturn->whereDate("created_at", ">=", convertVnDateObject($dataSearch['date_start'])->toDateString());
        }
        if($dataSearch['date_end']){
            $dataReturn = $dataReturn->whereDate("created_at", "<=", convertVnDateObject($dataSearch['date_end'])->toDateString());
        }
        if($product_kind =='1'){
            $dataReturn = $dataReturn->where('product_kind', "=", '0');
        }
        if($product_kind =='2'){
            $dataReturn = $dataReturn->where('product_kind', "=", '1');
        }
        if($category){
            $dataReturn = $dataReturn->where('category_id', "=", $category);
        }
        if($customer){
            $dataReturn = $dataReturn->whereIn('customer_code', $customer);
        }
        if ($keyword) {
            $dataReturn = $dataReturn->where(function ($sql) use ($keyword) {
                $sql->where("product_code", "like", "%" . $keyword . "%")
                    ->orWhere("product_name", "like", "%" . $keyword . "%")
                    ->orWhere("order_id_name", "like", "%" . $keyword . "%")
                    ->orWhere("customer_name", "like", "%" . $keyword . "%")
                ;
            });
        }

        $dataReturn = $dataReturn->orderBy("created_at", 'desc')->orderBy('customer_name')->get();
        return $dataReturn;
    }

    /**
     * Lấy data show popup
     * @return \Illuminate\Http\JsonResponse
     */
    public function getDataShowPopup()
    {
        if (!request()->ajax()) {
            return response()->json(['error' => 1, 'msg' => sc_language_render('admin.method_not_allow')]);
        }
        $arrID = explode(',', request('id'));
        $dataDavicook = ShopDavicookOrderReturnHistory::whereIn('id', $arrID)->get();
        $dataDavicorp = ShopOrderReturnHistory::whereIn('id', $arrID)->get();
        $datas = $dataDavicook->merge($dataDavicorp);
        $data = [];
        foreach ($datas as $row) {
            $data[] = [
                'id' => $row->id,
                'date_return' => isset($row->created_at) ? Carbon::parse($row->created_at)->format('d/m/Y') : '',
                'product_code' => $row->product_code ?? '',
                'product_name' => $row->product_name ?? '',
                'code_order' => $row->order_id_name ?? '',
                'name_customer' => $row->customer_name ?? '',
                'qty' => ($row->return_qty - $row->qty_import) ?? '',
                'qty_entered' => 0,
                'type' => $row->source ??''
            ];
        }

        return response()->json(['detail' => $data]);
    }

    /**
     * Tạo phiếu nhâp từ Bc Trả hàng.
     * @return \Illuminate\Http\JsonResponse
     */
    public function createImportOrderByReportReturn()
    {
        if (!request()->ajax()) {
            return response()->json(['error' => 1, 'msg' => sc_language_render('admin.method_not_allow')]);
        }
        $data = request('data');
        $dataInsertDetail = [];
        $warehouse = request('warehouse');
        try {
            $wareHouse = AdminWarehouse::where('id', $warehouse)->first();
            $dataInsert = [
                'id' => sc_uuid(),
                'id_name' => ShopGenId::genNextId('order_import'),
                'supplier_name' => 'Nhập hàng từ đơn trả',
                'warehouse_id' => $wareHouse->id,
                'warehouse_code' => $wareHouse->warehouse_code,
                'warehouse_name' => $wareHouse->name,
                'delivery_date' => now(),
                'reality_delivery_date' => null,
                'total' => 0,
                'total_reality' => 0,
                'status' => 1,
                'edit' => 0,
                'note' => null,
                'type_import' => 4,
                'created_at' => now(),
                'updated_at' => now(),
            ];
            $newImport = AdminImport::create($dataInsert);
            foreach($data as $arr) {
                if($arr[0]){
                    $currentQtyImport = ShopDavicookOrderReturnHistory::find($arr[0]) ?? ShopOrderReturnHistory::find($arr[0]);;
                    if($currentQtyImport){
                        $currentQtyImport->qty_import = $currentQtyImport->qty_import + $arr[1];
                        $currentQtyImport->qty_not_import = $currentQtyImport->return_qty - $currentQtyImport->qty_import;
                        $currentQtyImport->save();
                    }
                }
                $dataInsertDetail[] = array(
                    'id' => sc_uuid(),
                    'import_id' => $newImport->id,
                    'import_id_name' => $newImport->id_name,
                    'product_id' => $currentQtyImport->product_id,
                    'product_code' => $currentQtyImport->product_code,
                    'product_name' => $currentQtyImport->product_name,
                    'category_id' => $currentQtyImport->category_id,
                    'product_kind' => $currentQtyImport->product_kind,
                    'qty_order' => $arr[1] ?? 0,
                    'qty_reality' => $arr[1] ?? 0,
                    'customer_id' => $currentQtyImport->customer_id,
                    'customer_code' => $currentQtyImport->customer_code,
                    'customer_name' => $currentQtyImport->customer_name,
                    'unit_id' => '',
                    'unit_name' => $currentQtyImport->product_unit,
                    'product_price' => 0,
                    'amount' => 0,
                    'amount_reality' => 0,
                    'comment' => '',
                    'created_at' => now(),
                    'updated_at' => now(),
                );
            }
            AdminImportDetail::insert($dataInsertDetail);
            return response()->json(['success' => true, 'message' => 'Dữ liệu đã được lưu thành công']);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
