<?php

namespace App\Admin\Controllers\Warehouse;

use App\Admin\Controllers\AdminReportController;
use App\Admin\Models\AdminImport;
use App\Admin\Models\AdminImportDetail;
use App\Admin\Models\AdminImportHistory;
use App\Admin\Models\AdminImportReturn;
use App\Admin\Models\AdminProduct;
use App\Admin\Models\AdminReportWarehouseProductStock;
use App\Admin\Models\AdminWarehouseProduct;
use App\Admin\Models\ReportWarehouseCard;
use App\Exports\OrderImport\MultipleSheet;
use App\Front\Models\ShopCustomer;
use App\Front\Models\ShopDavicookCustomer;
use App\Front\Models\ShopDepartment;
use App\Front\Models\ShopImport;
use App\Traits\OrderTraits;
use App\Admin\Models\AdminWarehouse;
use App\Front\Models\ShopGenId;
use App\Front\Models\ShopImportPriceboard;
use App\Front\Models\ShopImportPriceboardDetail;
use App\Front\Models\ShopProduct;
use App\Front\Models\ShopSupplier;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use SCart\Core\Admin\Admin;
use Illuminate\Http\Request;
use SCart\Core\Admin\Controllers\RootAdminController;
class AdminOrderImportController extends RootAdminController
{
    public $customer_kind;
    use OrderTraits;
    public function __construct()
    {
        parent::__construct();
    }
    /**
     * Display list invoice.
     */
    public function index()
    {
        $data = [
            'title' => 'Danh sách đơn nhập hàng',
            'subTitle' => '',
            'icon' => 'fa fa-indent',
            'urlDeleteItem' => sc_route_admin('order_import.delete'),
            'removeList' => 1, // 1 - Enable function delete list item
            'buttonRefresh' => 1, // 1 - Enable button refresh
            'buttonSort' => 1, // 1 - Enable button sort
            'css' => '',
            'js' => '',
            'method' => 'delete',
            'url_action' => sc_route_admin('order_import.create'),
            'permGroup' => 'supplier'
        ];
        //Process add content
        $data['menuRight'] = sc_config_group('menuRight', \Request::route()->getName());
        $data['menuLeft'] = sc_config_group('menuLeft', \Request::route()->getName());
        $data['topMenuRight'] = sc_config_group('topMenuRight', \Request::route()->getName());
        $data['topMenuLeft'] = sc_config_group('topMenuLeft', \Request::route()->getName());
        $data['blockBottom'] = sc_config_group('blockBottom', \Request::route()->getName());
        $listTh = [
            'ID' => 'Mã ĐNH',
            'type_import' => 'Mẫu nhập hàng',
            'supplier_name' => 'Tên nhà cung cấp',
            'created_at' => 'Ngày đặt hàng',
            'delivery_date' => 'Ngày giao hàng',
            'reality_delivery_date' => 'Thời gian thực tế giao',
            'total' => 'Tổng tiền',
            'status' => 'Trạng thái',
            'action' => 'Thao tác',
        ];
        $cssTh = [
            'ID' => 'text-align: center; width: 10%',
            'type_import' => 'text-align: center; min-width: 95px !important;',
            'supplier_name' => 'text-align: center; min-width:250px !important;',
            'order_date' => 'text-align: center; min-width:100px',
            'delivery_date' => 'text-align: center; width: 12%',
            'reality_delivery_date' => 'text-align: center; min-width:125px;',
            'total' => 'text-align: center; width: 8%',
            'status' => 'text-align: center; min-width:150px;',
            'action' => 'text-align: center; width: 7%',
        ];
        $cssTd = [
            'ID' => 'text-align: center',
            'supplier_name' => '',
            'order_date' => '',
            'delivery_date' => '',
            'reality_date' => 'text-align: center',
            'total' => '',
            'status' => 'text-align: center',
            'action' => 'text-align: center',
        ];
        $data['cssTh'] = $cssTh;
        $data['cssTd'] = $cssTd;
        $dataTr = [];
        $status = AdminImport::$IMPORT_STATUS;
        $searchStatus = sc_clean(request('invoice_status') ?? 0);
        $code = sc_clean(request('keyword') ?? '');
        $arrSort = [
            'id__desc' => sc_language_render('filter_sort.id_desc'),
            'id__asc' => sc_language_render('filter_sort.id_asc'),
            'name__desc' => sc_language_render('filter_sort.name_desc'),
            'name__asc' => sc_language_render('filter_sort.name_asc'),
        ];
        $dataSearch = [
            'arrSort' => $arrSort,
            'status' => sc_clean(request('status') ?? ''),
            'keyword' => sc_clean(request('keyword') ?? ''),
            'from_to' => sc_clean(request('from_to') ?? ''),
            'end_to' => sc_clean(request('end_to') ?? ''),
            'department' => sc_clean(request('department') ?? ''),
            'type_date' => sc_clean(request('type_date') ?? ''),
        ];
        // function search
        $dataTmp = AdminImport::getImportListAdmin($dataSearch);
        $type = AdminImport::$TYPE;
        $departments = (new ShopDepartment)->pluck('name', 'id')->toArray();
        //end search
        foreach ($dataTmp as $key => $row) {
            $dataTr[$row->id] = [
                'ID' => $row->id_name,
                'type_import' => $type[$row->type_import],
                'supplier_name' => $row->supplier_name,
                'order_date' =>  Carbon::make($row->created_at ?? '')->format('d/m/Y H:i:s'),
                'delivery_date' => Carbon::make($row->delivery_date ?? '')->format('d/m/Y'),
                'reality_delivery_date' => $row->reality_date,
                'total' => number_format($row->total).'₫',
                'status' => $status[$row->status],
                'action' => '
                    <a data-perm="order_import:detail" href="' . sc_route_admin('order_import.edit', ['id' => $row->id ?  $row->id: 'not-found-id']) . '">
                    <span title="Chi tiết" type="button" class="btn btn-flat btn-sm btn-primary"><i class="fa fa-edit"></i></span></a>&nbsp;
                    <span data-perm="order_import:clone" onclick="cloneOrder(\'' . $row->id . '\');" title="' . sc_language_render('action.clone') . '" type="button" class="btn btn-flat btn-sm btn-secondary"><i class="fa fa-clipboard"></i></span>
                    <span data-perm="order_import:print" onclick="printPdf(\'' . $row->id . '\')"  title="' . sc_language_render('order.print.title') . '" type="button" class="btn btn-flat btn-sm btn-warning text-white"><i class="fas fa-print"></i></span>
                    <span data-perm="order_import:delete" onclick="deleteItem(\'' .  $row->id . '\');"  title="' . sc_language_render('action.delete') . '" class="btn btn-flat btn-sm btn-danger"><i class="fas fa-trash-alt"></i></span>'
                ,
            ];
        }
        $data['listTh'] = $listTh;
        $data['dataTr'] = $dataTr;
        $data['pagination'] = $dataTmp->appends(request()->except(['_token', '_pjax']))->links($this->templatePathAdmin . 'component.pagination');
        $data['resultItems'] = sc_language_render('admin.result_item', ['item_from' => $dataTmp->firstItem(), 'item_to' => $dataTmp->lastItem(), 'total' => $dataTmp->total()]);
        //menuSort
        $optionStatus = '';
        foreach ($status as $key => $item) {
            $optionStatus .= '<option  ' . (($searchStatus == $key) ? "selected" : "") . ' value="' . $key . '">' . $item . '</option>';
        }
        $optionDepartments = '';
        foreach ($departments as $key => $item) {
            $optionDepartments .= '<option  ' . (($dataSearch['department'] == $key) ? "selected" : "") . ' value="' . $key . '">' . $item . '</option>';
        }
        $data['urlSort'] = sc_route_admin('order_import.index', request()->except(['_token', '_pjax', 'sort_order']));
        //=menuSort
        //menuRight
        $data['menuRight'][] = '
         <button class="btn btn-success btn-flat" type="button" id="btn_export" title="Xuất excel"><i class="fa fa-file-export"></i>Xuất Excel</button>
         <a data-perm="order_import:print" href="#" class="btn btn-flat btn btn-warning text-white" title="In Pdf" onclick="savePdf()">
        <i class="fa fa-print"></i>Xuất Pdf</a>
            <a data-perm="order_import:create" href="' . sc_route_admin('order_import.create') . '" class="btn btn-success btn-flat" title="Tạo mới đơn nhập" id="button_create_new">
                <i class="fa fa-plus"></i>
            </a>
           ';
        //=menuRight
        //menuSearch
        $data['topMenuLeft'][] = '
                <form action="' . sc_route_admin('order_import.index') . '" id="button_search" autocomplete="off">
                <input type="hidden" name="limit" value="' . ($dataSearch['limit'] ?? '') . '" id="limit_paginate">
                    <div class="input-group">
                           <div class="col-lg-2 col-md-1 col-sm-6">
                            <div class="form-group">
                                <label>Chọn ngày</label>
                                <div class="input-group">
                                    <select class="form-control rounded-0" name="type_date">
                                    <option value="created_at" '.($dataSearch['type_date'] == "created_at" ? "selected" : "").'>Ngày đặt hàng</option>
                                    <option value="delivery_date" '.($dataSearch['type_date'] == "delivery_date" ? "selected" : "").'>Ngày giao hàng</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-2 col-md-1 col-sm-6">
                            <div class="form-group">
                                <label>Từ ngày</label>
                                <div class="input-group">
                                <input style="text-align: center" type="text" name="from_to" id="from_to" class="form-control input-sm date_time rounded-0"  placeholder="Chọn ngày" value="'.$dataSearch['from_to'].'"/>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-2 col-md-1 col-sm-6">
                            <div class="form-group">
                                <label>Đến ngày</label>
                                <div class="input-group">
                                <input style="text-align: center" type="text" name="end_to" id="end_to" class="form-control input-sm date_time rounded-0"  placeholder="Chọn ngày" value="'.$dataSearch['end_to'].'"/>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-2 col-md-1 col-sm-6">
                            <div class="form-group">
                                <label>Khách hàng thuộc</label>
                                <div class="input-group">
                                <select class="form-control rounded-0" name="department" id="department">
                                <option value="">Chọn tất cả</option>
                                ' . $optionDepartments . '
                                </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-2 col-md-1 col-sm-6">
                            <div class="form-group">
                                <label>Trạng thái</label></label>
                                <div class="input-group">
                                <select class="form-control rounded-0" name="status">
                                <option value="">Chọn tất cả</option>
                                ' . $optionStatus . '
                                </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-2 col-md-1 col-sm-6">
                            <div class="form-group">
                                <label>Tìm kiếm</label>
                                <div class="input-group">
                                    <input type="text" name="keyword" class="form-control rounded-0 float-right" placeholder="Mã đơn nhập" value="' . $dataSearch['keyword'] . '">
                                    <div class="input-group-append">
                                        <button type="submit" class="btn btn-primary  btn-flat"><i class="fas fa-search"></i></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
                ';
        //=menuSearch
        return view($this->templatePathAdmin . 'screen.warehouse.import.index')
            ->with($data);
    }

    /**
     * View tạo đơn hàng nhập.
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function create(Request $request)
    {

        $data = [
            'title' => 'Tạo đơn nhập hàng',
            'subTitle' => '',
            'title_description' => 'Tạo mới đơn hàng',
            'icon' => 'fa fa-plus',
            'url_action' => sc_route_admin('order_import.create'),
            'warehouseList' => AdminWarehouse::all(),
            'supplierList' => ShopSupplier::all(),
        ];
        if ($request->has('warehouseId') && $request->has('deliveryDate')) {
            $warehouseId = $request->get('warehouseId');
            $data['warehouseId'] = $warehouseId;
            $deliveryDate = $request->get('deliveryDate');
            $data['deliveryDate'] = $deliveryDate;
            $productTableName = SC_DB_PREFIX . 'shop_product as p';
            $productWarehouseTableName = SC_DB_PREFIX . 'shop_product_warehouse as pw';
            $importPriceboardTableName = SC_DB_PREFIX . 'shop_import_priceboard as ip';
            $importPriceboardDetailTableName = SC_DB_PREFIX . 'shop_import_priceboard_detail as ipd';
            $supplierTable = SC_DB_PREFIX . 'shop_supplier as sl';
            $unitTable = SC_DB_PREFIX.'shop_unit as un';
            $productList = DB::table($productWarehouseTableName)
                ->join($productTableName, 'pw.product_id', '=', 'p.id')
                ->join($unitTable, 'p.unit_id', '=', 'un.id') // Thêm join với bảng $unitTable
                ->where('pw.warehouse_id', '=', $warehouseId)
                ->select('p.id', 'p.name', 'p.sku', 'pw.latest_import_qty', 'pw.qty', 'un.name as unit_name') // Lấy tên từ bảng unit và đặt alias
                ->get();
            foreach ($productList as $product) {
                if(isset($deliveryDate)){
                    $priceboard = DB::table($importPriceboardDetailTableName)
                        ->join($importPriceboardTableName, 'ipd.priceboard_id', '=', 'ip.id')
                        ->join($supplierTable, 'ip.supplier_id', '=', 'sl.id') // Thêm join với bảng supplier
                        ->where('ipd.product_id', '=', $product->id)
                        ->where( 'ip.start_date', '<=', $deliveryDate )
                        ->where( 'ip.end_date', '>=', $deliveryDate)
                        ->select('ip.supplier_id', 'ipd.price', 'sl.name','sl.address', 'sl.email', 'sl.phone') // Chọn cột name từ bảng supplier
                        ->get();
                    $product->priceboard = $priceboard;
                }
            }

            $data['productList'] = $productList;

        }

        $listTh = [
            'ID' => 'STT',
            'product_code' => 'Mã sản phẩm',
            'product_name' => 'Tên sản phẩm *',
            'supplier_name' => 'Nhà cung cấp',
            'qty' => 'Số lượng *',
            'unit' => 'Đơn vị tính',
            'price' => 'Giá',
            'total' => 'Tổng tiền',
            'note' => 'Ghi chú',
            'action' => 'Thao tác',
        ];
        $cssTh = [
            'STT' => 'text-align: center; width: 8%',
            'product_code' => 'text-align: center; width: 8%',
            'product_name' => 'text-align: center; width: 15%',
            'supplier_name' => 'text-align: center; width: 15%',
            'qty' => 'text-align: center; width: 8%',
            'unit' => 'text-align: center; width: 8%',
            'price' => 'text-align: center; width: 8%',
            'total' => 'text-align: center; width: 13%',
            'note' => 'text-align: center; width: 12%',
            'action' => 'text-align: center; width: 5%',
        ];
        $cssTd = [
            'STT' => '',
            'product_code' => '',
            'product_name' => '',
            'supplier_name' => '',
            'qty' => 'text-align: center',
            'unit' => 'text-align: center',
            'price' => 'text-align: center',
            'total' => '',
            'note' => 'text-align: center',
            'action' => 'text-align: center',
        ];
        $data['cssTh'] = $cssTh;
        $data['cssTd'] = $cssTd;
        $dataTr = [];
        $data['listTh'] = $listTh;
        $data['dataTr'] = $dataTr;
        return view($this->templatePathAdmin . 'screen.warehouse.import.create')
            ->with($data);
    }

    /**
     * Lưu tạo mới đơn hàng nhập thủ công.
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $data = $request->all();
        try {
            $wareHouse = AdminWarehouse::where('id', $data['data_to_save'][0]['warehouse'])->first();
            $newData = new Collection($data['data_to_save'] ?? []);
            $newData = $newData->groupBy('supplierID');
            foreach ($newData as $keySupplierId => $item) {
                $supplier = ShopSupplier::where('id', $keySupplierId)->first();
                $dataInsert = [
                    'id' => sc_uuid(),
                    'id_name' => ShopGenId::genNextId('order_import'),
                    'supplier_id' => $supplier->supplier_id,
                    'supplier_name' => $supplier->name,
                    'warehouse_id' => $wareHouse->id,
                    'warehouse_code' => $wareHouse->warehouse_code,
                    'warehouse_name' => $wareHouse->name,
                    'address' => $supplier->address,
                    'email' => $supplier->email,
                    'phone' => $supplier->phone,
                    'delivery_date' => Carbon::createFromFormat('Y-m-d', $item[0]['deliveryDate']),
                    'reality_delivery_date' => null,
                    'total' => 0,
                    'total_reality' => 0,
                    'status' => 1,
                    'edit' => 0,
                    'note' => null,
                    'type_import' => 0,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
                $newImport = AdminImport::create($dataInsert);
                $dataInsertDetail = [];
                $total = 0;
                foreach ($item as $detail) {
                    $total += (float)$detail['price'] * (float)$detail['quantity'];
                    $product = ShopProduct::with('unit')->where('sku', $detail['productSku'])->first();
                    $dataInsertDetail[] = array(
                        'id' => sc_uuid(),
                        'import_id' => $newImport->id,
                        'import_id_name' => $newImport->id_name,
                        'product_id' => $product->id,
                        'product_code' => $product->sku,
                        'product_name' => $product->name,
                        'category_id' => $product->category_id,
                        'product_kind' => $product->kind,
                        'qty_order' => $detail['quantity'],
                        'qty_reality' => $detail['quantity'],
                        'customer_id' => null,
                        'customer_code' => null,
                        'customer_name' => null,
                        'unit_id' => $product->unit->id ?? '',
                        'unit_name' => $product->unit->name ?? '',
                        'product_price' => $detail['price'],
                        'amount' => (float)$detail['price'] * (float)$detail['quantity'],
                        'amount_reality' => (float)$detail['price'] * (float)$detail['quantity'],
                        'comment' => $detail['note'],
                    );
                }
                $newImport->total = $total;
                $newImport->total_reality = $total;
                $newImport->save();

                AdminImportDetail::insert($dataInsertDetail);
            }
            return response()->json(['success' => true, 'message' => 'Dữ liệu đã được lưu thành công']);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * View chi tiết đơn hàng nhập.
     * @param $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     */
    public function edit($id)
    {
        $orderImport = AdminImport::with('details', 'return', 'history')->where('id',$id)->first();
        if (!$orderImport) {
            return redirect()->route('admin.data_not_found')->with(['url' => url()->full()]);
        }
        $status = AdminImport::$IMPORT_STATUS;
        $suppliers = ShopSupplier::select('id', 'name')->get();
        $products = ShopProduct::with('unit')->where('status', 1)->get();
        $editable = $this->checkOrderEditable($orderImport);
        $nameView = $orderImport->type_import == 1 ? 'edit_combine_product' : 'edit';
        return view($this->templatePathAdmin . 'screen.warehouse.import.'.$nameView)->with(
            [
                "title" => 'Chi tiết đơn hàng nhập',
                "subTitle" => '',
                'icon' => 'fa fa-file-text-o',
                "orderImport" => $orderImport,
                "status" => $status,
                'suppliers' => $suppliers,
                'editable' => $editable,
                'products' => $products,
            ]
        );
    }

    /**
     * Update lại các chi tiết Đơn hàng nhập
     * @return \Illuminate\Http\JsonResponse
     */
    public function update()
    {
        $id = request('pk');
        $code = request('name');
        $value = request('value');
        try {
            $status = AdminImport::$IMPORT_STATUS;
            if ($code == 'note') {
                $orderImport = AdminImport::find($id);
                $title = 'Sửa ghi chú đơn nhập hàng';
                $contentHistory = 'Thay đổi: '.$orderImport->comment.' -> '.$value;
                $this->storeHistory($id, $title, $contentHistory);
                $orderImport->note = $value;
                $orderImport->edit = 1;
                $orderImport->save();

            }

            if ($code == 'status') {
                $orderImport = AdminImport::with('details')->find($id);
                $statusOrigin =  $orderImport->status;
                $title = 'Sửa trạng thái đơn nhập';
                $contentHistory = 'Thay đổi: '.$status[$orderImport->status].' -> '.$status[$value];
                $this->storeHistory($id, $title, $contentHistory);
                $orderImport->status = $value;
                $orderImport->edit = 1;
                $orderImport->save();
                if ($value == 3) {
                    foreach ($orderImport->details as $item) {
                        $qtyStock = $this->checkQtyStock($item->product_id, $orderImport->warehouse_id , $item->qty, 'add');
                        $this->addArrReportProductSock($orderImport, $item, $qtyStock, $item->qty_reality, 'add');
                    }
                    ReportWarehouseCard::where('order_id', $orderImport->id)->delete();
                }

                if ($statusOrigin == 3 && $value != 3) {
                    $dataWarehouseCardInsert = [];
                    foreach ($orderImport->details as $item) {
                        $qtyStock = $this->checkQtyStock($item->product_id, $orderImport->warehouse_id , $item->qty, 'sub');;
                        $dataWarehouseCardInsert[] = $this->addArrWarehouseCard($orderImport, $item, $qtyStock, $item->id);
                        $this->addArrReportProductSock($orderImport, $item, $qtyStock, $item->qty_reality, 'sub');
                    }
                    ReportWarehouseCard::insert($dataWarehouseCardInsert);
                }
            }

            if ($code == 'supplier_id') {
                $orderImport = AdminImport::find($id);
                $supplier = ShopSupplier::find($value);
                $oldSupplier = $orderImport->supplier_name;
                $orderImport->supplier_id = $value;
                $orderImport->supplier_code = $supplier->supplier_code;
                $orderImport->supplier_name = $supplier->name;
                $orderImport->edit = 1;
                $orderImport->save();
                $title = 'Thay đổi nhà cung cấp';
                $contentHistory = 'Thay đổi: '.$oldSupplier.' -> '.$supplier->name;
                $this->storeHistory($id, $title, $contentHistory);
            }

            if ($code == 'qty_order') {
                $detail = AdminImportDetail::find($id);
                $oldQty = $detail->qty_order;
                $detail->qty_order = $value;
                $detail->qty_reality = $value;
                $detail->amount = $value * $detail->product_price;
                $detail->amount_reality = $value * $detail->product_price;
                $detail->save();
                $title = 'Thay đổi số lượng: '.$detail->product_name;
                $contentHistory = 'Thay đổi: '.$oldQty.' -> '.$value;
                $this->storeHistory($detail->import_id, $title, $contentHistory);
                $this->updateTotalImport($detail->import_id);
            }

            if ($code == 'qty_reality') {
                $detail = AdminImportDetail::find($id);
                $oldQty = $detail->qty_reality;
                $detail->qty_reality = $value;
                $detail->amount_reality = $value * $detail->product_price;
                $detail->save();
                $title = 'Thay đổi số lượng thực tế: '.$detail->product_name;
                $contentHistory = 'Thay đổi: '.$oldQty.' -> '.$value;
                $this->storeHistory($detail->import_id, $title, $contentHistory);
                $this->updateTotalImport($detail->import_id);
            }

            if ($code == 'product_price') {
                $detail = AdminImportDetail::find($id);
                $oldPrice = $detail->product_price;
                $detail->product_price = $value;
                $detail->amount = $value * $detail->qty_order;
                $detail->amount_reality = $value * $detail->qty_reality;
                $detail->save();
                $title = 'Thay đổi giá: '.$detail->product_name;
                $contentHistory = 'Thay đổi: '.$oldPrice.' -> '.$value;
                $this->storeHistory($detail->import_id, $title, $contentHistory);
                $this->updateTotalImport($detail->import_id);
            }

            if ($code == 'comment') {
                $detail = AdminImportDetail::find($id);
                $oldComment = $detail->comment;
                $detail->comment = $value;
                $detail->save();
                $title = 'Thay đổi ghi chú chi tiết: '.$detail->product_name;
                $contentHistory = 'Thay đổi: '.($oldComment != '' ? $oldComment : 'Trống').' -> '.$value;
                $this->storeHistory($detail->import_id, $title, $contentHistory);
            }
            return response()->json([
                'error' => 0,
                'msg' => sc_language_render('action.update_success')
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'error' => 1,
                'msg' => $e->getMessage()
            ]);
        }
    }

    /**
     * View trả hàng.
     * @param $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     */
    public function return($id)
    {
        $orderImport = AdminImport::with('details', 'return')->find($id);

        if (!$orderImport) {
            return redirect()->route('admin.data_not_found')->with(['url' => url()->full()]);
        }
        $editable = $this->checkOrderEditable($orderImport);
        return view($this->templatePathAdmin . 'screen.warehouse.import.return')->with(
            [
                "title" => sc_language_render('admin_order.return_title'),
                "subTitle" => '',
                'icon' => 'fa fa-file-text-o',
                "orderImport" => $orderImport,
                'editable' => $editable,
            ]
        );
    }

    /**
     * Lưu thông tin trả hàng.
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storeReturn($id)
    {
        $data = request()->all();
        $listProductName = [];
        DB::beginTransaction();
        try {
            $order = AdminImport::with('details')->find($id);

            if (!$order) {
                throw new \Exception('Không tìm thấy đơn hàng ' . $data['order_id']);
            }
            if (!$this->checkOrderEditable($order)) {
                return redirect()->back()->with("error", "Bạn không có quyền thao tác với đơn hàng này");
            }

            $returned = 0;
            foreach ($data['qty_reality'] as $idDetail => $qty) {
                $detail = $order->details->find($idDetail);
                $typeUnit = $detail->product->unit->type ?? 0;

                if ($qty <= 0) {
                    continue;
                }

                if ($qty > $detail->qty_order) {
                    throw new \Exception('Số lượng trả hàng lớn hơn số lượng trên hóa đơn. Vui lòng kiểm tra lại!');
                }
                if ($qty > $detail->qty_reality) {
                    throw new \Exception('Số lượng trả hàng lớn hơn số lượng trên hóa đơn. Vui lòng kiểm tra lại!');
                }

                if ($typeUnit == 1) {
                    if (is_float($qty/1)) {
                        throw new \Exception('Sản phẩm -'. $detail->product_name .'- bắt buộc nhập số nguyên. Vui lòng kiểm tra lại!');
                    }

                }
                $detail->qty_order = $detail->qty_order - $qty;
                $detail->qty_reality = $detail->qty_reality - $qty;
                $detail->amount = $detail->product_price * $detail->qty;
                $detail->amount_reality = $detail->product_price * $detail->qty_reality;
                $returned += $qty * $detail->product_price;
                $listProductName[] = $detail->product_name;
                // Return History
                $returnHistory = AdminImportReturn::create([
                    'import_id' => $id,
                    'import_detail_id' => $detail->id,
                    'product_id' => $detail->product_id,
                    'product_name' => $detail->product_name,
                    'product_code' => $detail->product_code,
                    'product_unit' => $detail->unit_name,
                    'product_price' => $detail->product_price,
                    'customer_name' => $detail->customer_name,
                    'customer_id' => $detail->customer_id,
                    'customer_code' => $detail->customer_code,
                    'qty_original' => $detail->qty_reality,
                    'qty_return' => $qty,
                    'admin_id' => Admin::user()->id ?? '',
                    'return_amount' => $qty * $detail->product_price
                ]);

                if (!$detail->save()) {
                    throw new \Exception('Có lỗi xảy ra khi cập nhật chi tiết đơn hàng. Vui lòng kiểm tra lại');
                }
            }

            $title = 'Trả hàng';
            $contentHistory = $contentHistory = implode("<br>", $listProductName);
            $this->storeHistory($id, $title, $contentHistory);
            $this->updateTotalImport($id);
        } catch (\Throwable $e) {
            DB::rollBack();
            return redirect()->back()->with(['error' => $e->getMessage()]);
        }
        DB::commit();
        return redirect()->back()->with(['success' => 'Trả hàng thành công']);
    }

    /**
     * Hoàn tác trả hàng.
     * @return \Illuminate\Http\JsonResponse
     */
    public function undoImportDetail()
    {
        $detail_id = request('detail_id');
        $return_id = request('return_id');
        $detail = AdminImportDetail::find($detail_id);

        $return = AdminImportReturn::find($return_id);
        try {
            $qtyOrigin = $detail->qty_order;
            $qtyReaOrigin = $detail->qty_reality;
            if ($detail) {
                $detail->qty_order = $qtyOrigin + $return->qty_return;
                $detail->amount = ($qtyOrigin + $return->qty_return) * $detail->product_pice;
                $detail->qty_reality = $qtyReaOrigin + $return->qty_return;
                $detail->amount_reality = ($qtyReaOrigin + $return->qty_return) * $detail->product_pice;
                $detail->save();
            } else {
                $product = AdminProduct::getProductAdmin($return->product_id);
                if (!$product) {
                    throw new \Exception('Không tìm thấy thông tin sản phẩm '.$return->product_name);
                }
                $insertData = [
                    'id' => sc_uuid(),
                    'import_id' => $return->import_id,
                    'product_id' => $return->product_id,
                    'product_code' => $product->sku,
                    'product_name' => $product->name,
                    'category_id' => $product->category_id,
                    'product_kind' => $product->kind,
                    'unit_name' => $product->unit->name,
                    'unit_id' => $product->unit->id,
                    'qty_order' => $return->qty_return,
                    'qty_reality' => $return->qty_return,
                    'customer_id' => $return->customer_id,
                    'customer_code' => $return->customer_code,
                    'customer_name' => $return->customer_name,
                    'product_price' => $return->product_price,
                    'amount' => $return->return_amount,
                    'amount_reality' => $return->return_amount,
                    'comment' => '',
                    'created_at' => now(),
                ];
                $detail = AdminImportDetail::create($insertData);
            }
            $importOrder = ShopImport::find($return->import_id);

            if ($importOrder->status == 3) {
                $this->checkProductStock($importOrder, $detail, $return->qty_return, 'add');
            }

            $this->updateTotalImport($return->import_id);
            $title = 'Hoàn tác trả hàng';
            $contentHistory = "Hoàn tác trả hàng sản phẩm - {$return->product_name} -> Số lượng: " . $return->qty_return;
            $this->storeHistory($return->import_id, $title, $contentHistory);
            $return->delete();
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['error' => 1, 'msg' => $e->getMessage()]);
        }
        DB::commit();
        return response()->json(['error' => 0, 'msg' => 'Hoàn tác thành công']);
    }


    public function cloneImportOrder($id){

    }

    /**
     * Xuất excel và pdf đơn hàng nhập.
     * @return false|\Illuminate\Http\RedirectResponse|string|string[]|null
     */
    public function printImportOrder()
    {
        $ids = explode(',', request('ids'));

        $orderData = AdminImport::with('details')
            ->orderBy('supplier_name')->orderBy('delivery_date', 'DESC')->findMany($ids);
        if (!count($orderData) > 0) {
            return redirect()->route('admin.data_not_found')->with(['url' => url()->full()]);
        }
        if (count($orderData) > 200) {
            return redirect()->back()->with(['error' => 'Dữ liệu quá tải vui lòng chọn lại trường lọc!']);
        }

        $html = view($this->templatePathAdmin . 'screen.warehouse.import.pdf.printf_pdf')
            ->with(['data' => $orderData])->render();
        $html = mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8');

        return $html;

    }

    /**
     * Xuất excel và pdf đơn hàng nhập.
     * @return false|\Illuminate\Http\RedirectResponse|string|string[]|null
     */
    public function exportExcelImportOrder()
    {
        $ids = explode(',', request('ids'));
        $typePrint = request('type_print') ?? 1;
        $orderData = AdminImport::with('details')
            ->orderBy('supplier_name')->orderBy('delivery_date', 'DESC')->findMany($ids);
        if (!count($orderData) > 0) {
            return redirect()->route('admin.data_not_found')->with(['url' => url()->full()]);
        }
        if (count($orderData) > 200) {
            return redirect()->back()->with(['error' => 'Dữ liệu quá tải vui lòng chọn lại trường lọc!']);
        }

        return Excel::download(new MultipleSheet($orderData), 'DonNhapHang-' . Carbon::now() . '.xlsx');
    }

    /**
     * Xóa chi tiết từng sản phẩm
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function deleteImportOrderDetail(){
        $detail_id = request('detail_id');

        try {
            DB::beginTransaction();
            $detail = AdminImportDetail::find($detail_id);

            if ($detail) {
                $dataImport = ShopImport::find($detail->import_id);
                if ($dataImport->status == 2) {
                    $this->checkProductStock($dataImport, $detail, $detail->qty_reality, 'subtract');
                }
                $title = 'Xóa sản phẩm';
                $contentHistory = 'Xóa SP: '. $detail->product_name;
                $this->storeHistory($detail->import_id, $title, $contentHistory);
                $detail->delete();
                $this->updateTotalImport($detail->import_id);
            }
            DB::commit();
            return response([
                'error' => 0,
                'msg' => 'Xóa thành công',
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error($e);
            return response([
                'error' => 1,
                'msg' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Xóa nhiều đơn nhập hàng.
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteImportOrder()
    {
        $ids = request('ids');
        $arrID = explode(',', $ids);
        try {
            DB::beginTransaction();
            AdminImport::destroy($arrID);
            DB::commit();
            return response()->json(['error' => 0, 'msg' => sc_language_render('action.update_success')]);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error($e);
            return response()->json(['error' => 1, 'msg' => 'Xóa đơn nhập hàng lỗi!']);
        }
    }

    /**
     * Get sản phẩm theo NCC và giá nhập.
     * @return \Illuminate\Http\JsonResponse
     */
    public function getDataChangeProduct()
    {
        $detail = AdminImportDetail::where('id', request('detail_id'))->first();
        $import = AdminImport::find($detail->import_id);
        $product_id = $detail->product_id;
        $tablePriceName = (new ShopImportPriceboard())->table;
        $tablePriceDetail = (new ShopImportPriceboardDetail())->table;
        $tableSupplier= (new ShopSupplier())->table;
        $dataPrice = ShopImportPriceboard::join($tablePriceDetail . ' as ipd', function ($join) use ($tablePriceName, $product_id) {
            $join->on("ipd.priceboard_id", $tablePriceName . ".id");
            $join->where("ipd.product_id", $product_id);
        })->join($tableSupplier . ' as sp', function ($join) use ($tablePriceName) {
            $join->on("sp.id", $tablePriceName . ".supplier_id");
        })->where($tablePriceName.'.supplier_id', '!=', $import->supplier_id)
            ->whereDate($tablePriceName.'.start_date', '<=', $import->delivery_date)
            ->whereDate($tablePriceName.'.end_date','>=', $import->delivery_date)
            ->select(
                'ipd.id as price_detail_id',
                'ipd.price as import_price',
                'sp.name as supplier_name'
            )->get();
        if ($dataPrice->isEmpty()) {
            return response()->json([
                'error' => 1,
                'msg' => 'Không tìm thấy các bảng giá nhập phù hợp cho sản phẩm này!'
            ]);
        }
        $data = [
            'detail_id' => $detail->id,
            'product_name' => $detail->product_name,
            'supplier_name' => $import->supplier_name,
            'product_price' => sc_currency_render($detail->product_price, 'VND'),
            'priceForSupplier' => $dataPrice,
        ];

        return response()->json($data);
    }

    /**
     * Thay đổi sản phẩm theo nhà cung cấp -> tạo đơn nhập hàng mới với sản phẩm cũ và NCC mới.
     * Xóa sản phẩm cũ trong đơn nhập cũ.
     */
    public function storeChangeProduct(): \Illuminate\Http\RedirectResponse
    {
        $detailOldImport = AdminImportDetail::where('id', request('detail_id'))->first();
        $orderOldImport = AdminImport::where('id', $detailOldImport->import_id)->first();
        $titleHistory = 'Thay đổi NCC của Sản phẩm: '.$detailOldImport->produc_name;
        $detailPriceProduct = ShopImportPriceboardDetail::where('id', request('price_detail_id'))->first();
        $priceProduct = ShopImportPriceboard::where('id', $detailPriceProduct->priceboard_id)->first();
        try {
            DB::beginTransaction();
            $supplier = ShopSupplier::find($priceProduct->supplier_id);
            $dataInsert = [
                'id' => sc_uuid(),
                'id_name' => ShopGenId::genNextId('order_import'),
                'supplier_id' => $supplier->id,
                'supplier_name' => $supplier->name,
                'warehouse_code' => $orderOldImport->warehouse_code,
                'warehouse_name' => $orderOldImport->warehouse_name,
                'address' => $supplier->address,
                'email' => $supplier->email,
                'phone' => $supplier->phone,
                'delivery_date' => $orderOldImport->delivery_date,
                'reality_delivery_date' => null,
                'total' => $detailOldImport->qty_order * $detailPriceProduct->price,
                'total_reality' => $detailOldImport->qty_order * $detailPriceProduct->price,
                'status' => 1,
                'edit' => 0,
                'note' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            $newOrderImport = AdminImport::create($dataInsert);

            $dataInsertDetail = [
                'id' => sc_uuid(),
                'import_id' => $newOrderImport->id,
                'import_id_name' => $newOrderImport->id_name,
                'product_id' => $detailOldImport->product_id,
                'product_code' => $detailOldImport->product_code,
                'product_name' => $detailOldImport->product_name,
                'category_id' => $detailOldImport->category_id,
                'product_kind' => $detailOldImport->product_kind,
                'qty_order' => $detailOldImport->qty_order,
                'qty_reality' => $detailOldImport->qty_order,
                'customer_code' => $detailOldImport->customer_code,
                'customer_name' => $detailOldImport->customer_name,
                'customer_id' => $detailOldImport->customer_id,
                'unit_id' => $detailOldImport->unit_id,
                'unit_name' => $detailOldImport->unit_name,
                'product_price' => $detailPriceProduct->price,
                'amount' => $detailPriceProduct->price * $detailOldImport->qty_order,
                'amount_reality' => $detailPriceProduct->price * $detailOldImport->qty_order,
                'comment' => null,
            ];
            AdminImportDetail::create($dataInsertDetail);
            $detailOldImport->delete();
            $contentHistory = 'Thay đổi NCC'.$orderOldImport->supplier_name.'-> '.$supplier->name;

            $this->storeHistory($orderOldImport->id, $titleHistory, $contentHistory);
            $this->updateTotalImport($orderOldImport->id);
            DB::commit();
            return redirect()->route('order_import.index', $orderOldImport->id)->with(['success' => 'Thay đổi sản phẩm thành công!']);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error($e);
            return redirect()->route('order_import.edit', $orderOldImport->id)->with(['error' => $e->getMessage()]);
        }
    }

    /**
     * Thêm sản phẩm mới vào đơn nhập hàng.
     * @return \Illuminate\Http\JsonResponse
     */
    public function addItemProductDetail()
    {
        $productIds = request('product_id') ?? [];
        $productPrice = request('product_price') ?? [];
        $qtyOrder = request('qty_order') ?? [];
        $comment = request('comment') ?? [];
        $orderImportId = request('import_id');
        $orderImport = AdminImport::find($orderImportId);
        $productList = [];
        if ($orderImport->status == 3) {
            return response()->json(['error' => 1, 'msg' => 'Đơn đã nhập không thể sửa!']);
        }
        try {
            DB::beginTransaction();
            foreach ($productIds as $key => $product_id) {
                $product = ShopProduct::with('unit')->where('id', $product_id)->first();
                if (!$product) {
                    return response()->json(['error' => 1, 'msg' => sc_language_render('admin.data_not_found_detail', ['msg' => '#' . $orderImportId]), 'detail' => '']);
                }
                $productList[] = '- '.$product->name;
                $item = array(
                    'id' => sc_uuid(),
                    'import_id' => $orderImport->id,
                    'product_id' => $product_id,
                    'product_code' => $product->sku,
                    'product_name' => $product->name,
                    'category_id' => $product->category_id,
                    'product_kind' => $product->kind,
                    'unit_name' => $product->unit->name,
                    'unit_id' => $product->unit->id,
                    'qty_order' => round($qtyOrder[$key], 2),
                    'qty_reality' => round($qtyOrder[$key], 2),
                    'product_price' => $productPrice[$key],
                    'amount' => $productPrice[$key] * round($qtyOrder[$key], 2),
                    'amount_reality' => $productPrice[$key] * round($qtyOrder[$key], 2),
                    'comment' => $comment[$key],
                    'created_at' => now()->addSeconds($key * 2),
                );
                $detail = AdminImportDetail::create($item);
            }

            $title = "Thêm sản phẩm mới";
            $contentHistory = implode("<br>", $productList);
            $this->storeHistory($orderImportId, $title, $contentHistory);
            $this->updateTotalImport($orderImportId);
            DB::commit();
            return response()->json(['error' => 0, 'msg' => sc_language_render('action.update_success')]);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error($e);
            return response()->json(['error' => 1, 'msg' => $e->getMessage()]);
        }
    }

    /**
     * Tạo đơn hàng từ báo cáo nhập hàng.
     * @return \Illuminate\Http\JsonResponse
     */
    public function createImportByReport()
    {
        $dataSearch = [
            'search_supplier' => sc_clean(request('search_supplier') ?? ''),
            'keyword' => sc_clean(request('keyword') ?? ''),
            'from_to' => sc_clean(request('from_to') ?? ''),
            'end_to' => sc_clean(request('end_to') ?? ''),
            'customer_kind' => sc_clean(request('customer_kind') ?? ''),
            'key_export' => sc_clean(request('key_export') ?? []),
            'department' => sc_clean(request('department') ?? ''),
        ];
        $import_delivery_date = request('import_delivery_date');
        $type_import = request('type_import');
        $warehouseId = request('warehouse_id');
        $resultReport = (new AdminReportController())->getDataPriceImport($dataSearch['key_export'],$dataSearch);
        $wareHouse = AdminWarehouse::where('id', $warehouseId)->first();
        if($resultReport->isEmpty()){
            return response()->json(
                [
                    'error' => 1,
                    'msg' => 'Dữ liệu trống. Vui lòng chọn lại ngày lọc!',
                ]
            );
        }
        $products = ShopProduct::select('id', 'category_id', 'kind', 'sku', 'name')->get();
        $customerDavicorp = ShopCustomer::all();
        $customerDavicook = ShopDavicookCustomer::all();
        try {
            DB::beginTransaction();
            if ($type_import == 1) {
                $resultReport = $resultReport->groupBy(['supplier_code']);
                foreach ($resultReport as $codeSupplier => $item) {
                    $supplier = ShopSupplier::where('supplier_code', $codeSupplier)->first();
                    $dataInsert = [
                        'id_name' => ShopGenId::genNextId('order_import'),
                        'supplier_id' => $supplier->id,
                        'supplier_name' => $supplier->name,
                        'warehouse_id' => $wareHouse->id,
                        'warehouse_code' => $wareHouse->warehouse_code,
                        'warehouse_name' => $wareHouse->name,
                        'address' => $supplier->address,
                        'email' => $supplier->email,
                        'phone' => $supplier->phone,
                        'delivery_date' => Carbon::createFromFormat('d/m/Y', $import_delivery_date),
                        'reality_delivery_date' => null,
                        'total' => 0,
                        'total_reality' => 0,
                        'status' => 1,
                        'edit' => 0,
                        'note' => null,
                        'type_import' => 1,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                    $newOrderImport = AdminImport::create($dataInsert);
                    $dataInsertDetail = [];
                    $total = 0;
                    foreach ($item as $detail) {
                        $total += $detail['price'] * (float)$detail['qtyProduct'];
                        $product = $products->find($detail['product_id']);
                        $customerCorp = $customerDavicorp->where('customer_code', $detail['customer_code'])->first();
                        $customerCook = $customerDavicook->where('customer_code', $detail['customer_code'])->first();
                        $dataInsertDetail[] = array(
                            'id' => sc_uuid(),
                            'import_id' => $newOrderImport->id,
                            'import_id_name' => $newOrderImport->id_name,
                            'product_id' => $detail['product_id'],
                            'product_code' => $detail['product_code'],
                            'product_name' => $detail['product_name'],
                            'product_kind' => $product->kind ?? '',
                            'category_id' => $product->category_id ?? '',
                            'qty_order' => $detail['qtyProduct'],
                            'qty_reality' => $detail['qtyProduct'],
                            'customer_id' => $detail['customer_id'],
                            'customer_code' => $detail['customer_code'],
                            'customer_name' => $detail['customer_name'],
                            'department_id' => $customerCorp->department_id ?? '',
                            'zone_id' => !empty($customerCorp) ? $customerCorp->zone_id : ($customerCook->zone_id ?? ''),
                            'unit_id' => '',
                            'unit_name' => $detail['product_unit'],
                            'product_price' => $detail['price'] ,
                            'amount' =>$detail['price'] * $detail['qtyProduct'],
                            'amount_reality' => $detail['price']  * $detail['qtyProduct'],
                            'comment' => $detail['comment'],
                        );
                    }
                    AdminImportDetail::insert($dataInsertDetail);

                    $newOrderImport->total = $total;
                    $newOrderImport->total_reality = $total;
                    $newOrderImport->save();
                }
            }

            if ($type_import == 2) {
                $resultReport = $resultReport->groupBy(['supplier_code', 'product_id']);
                foreach ($resultReport as $codeSupplier => $item) {
                    $supplier = ShopSupplier::where('supplier_code', $codeSupplier)->first();
                    $dataInsert = [
                        'id' => sc_uuid(),
                        'id_name' => ShopGenId::genNextId('order_import'),
                        'supplier_id' => $supplier->id,
                        'supplier_name' => $supplier->name,
                        'warehouse_id' => $wareHouse->id,
                        'warehouse_code' => $wareHouse->warehouse_code,
                        'warehouse_name' => $wareHouse->name,
                        'address' => $supplier->address,
                        'email' => $supplier->email,
                        'phone' => $supplier->phone,
                        'delivery_date' => Carbon::createFromFormat('d/m/Y', $import_delivery_date),
                        'reality_delivery_date' => null,
                        'total' => 0,
                        'total_reality' => 0,
                        'status' => 1,
                        'edit' => 0,
                        'note' => null,
                        'type_import' => 2,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                    $import = AdminImport::create($dataInsert);
                    $dataInsertDetail = [];
                    $total = 0;
                    foreach ($item as $detail) {
                        $total += $detail->first()['price'] * (float)$detail->sum('qtyProduct');
                        $product = $products->find($detail->first()['product_id']);
                        $customerCorp = $customerDavicorp->where('customer_code', $detail->first()['customer_code'])->first();
                        $customerCook = $customerDavicook->where('customer_code', $detail->first()['customer_code'])->first();
                        $comment = '';
                        foreach ($detail as $row) {
                            if ($row['comment'] != '') {
                                $comment .= $row['customer_name'] . ' : ('. $row['qtyProduct'].') ' . $row['comment']. '; ';
                            }
                        }
                        $dataInsertDetail[] = array(
                            'id' => sc_uuid(),
                            'import_id' => $import->id,
                            'import_id_name' => $import->id_name,
                            'product_id' => $detail->first()['product_id'],
                            'product_code' => $detail->first()['product_code'],
                            'product_name' => $detail->first()['product_name'],
                            'product_kind' => $product->kind ?? '',
                            'category_id' => $product->category_id ?? '',
                            'qty_order' => $detail->sum('qtyProduct'),
                            'qty_reality' => $detail->sum('qtyProduct'),
                            'customer_id' => $detail->first()['customer_id'],
                            'customer_code' => $detail->first()['customer_code'],
                            'customer_name' => $detail->first()['customer_name'],
                            'department_id' => $customerCorp->department_id ?? '',
                            'zone_id' => !empty($customerCorp) ? $customerCorp->zone_id : ($customerCook->zone_id ?? ''),
                            'unit_id' => '',
                            'unit_name' => $detail->first()['product_unit'],
                            'product_price' => $detail->first()['price'],
                            'amount' => $detail->first()['price'] * $detail->sum('qtyProduct'),
                            'amount_reality' => $detail->first()['price'] * $detail->sum('qtyProduct'),
                            'comment' => $comment,
                        );
                    }
                    AdminImportDetail::insert($dataInsertDetail);
                    $import->total = $total;
                    $import->total_reality = $total;
                    $import->save();
                }
            }
            DB::commit();
            return response()->json(
                [
                    'error' => 0,
                    'msg' => 'Tạo đơn nhập thành công!'
                ]
            );
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error($e);
            return response()->json(
                [
                    'error' => 1,
                    'msg' => $e->getMessage(),
                ]
            );
        }

    }

    /**
     * Cập nhập giá chi tiết từng sản phẩm
     */
    public function updatePriceProduct($id)
    {
        $importOrder = AdminImport::with('details')->find($id);
        if (!$importOrder) {
            return redirect()->back()->with(['error' => 'Không tìm thấy đơn nhập']);
        }
        try {
            DB::beginTransaction();
            foreach ($importOrder->details as $key => $item) {
                $price = $this->getImportPriceProductDetail($item->product_id, $importOrder->supplier_id, $importOrder->delivery_date);
                $item->product_price = $price;
                $item->amount = $item->qty_order * $price;
                $item->amount_reality = $item->amount_reality * $price;
                $item->save();
            }
            $title = 'Cập nhập giá sản phẩm';
            $this->storeHistory($importOrder->id, $title, $title);
            $this->updateTotalImport($importOrder->id);
            DB::commit();
            return redirect()->back()->with(['success' => 'Cập nhập giá thành công!']);
        } catch (\Throwable $e) {
            DB::rollBack();
            return redirect()->back()->with(['error' => 'Cập nhập giá lỗi!']);
        }

    }

    /**
     * Lấy giá sản phẩm
     * @return \Illuminate\Http\JsonResponse
     */
    public function getProductInfoAndImportPrice()
    {
        $product_id = request('product_id');
        $import_id = request('import_id');
        $product = ShopProduct::with('unit')->where('id', $product_id)->first();
        $import = AdminImport::find($import_id);

        if (!$product) {
            return response()->json(['error' => 1, 'msg' => sc_language_render('admin.data_not_found_detail', ['msg' => '#product:' . $product_id]), 'detail' => '']);
        }

        if ($product->status == 0) {
            return response()->json([
                'error' => 1,
                'msg' => $product->name . ' '. $product->sku . ' - Hết hàng, vui lòng chọn sản phẩm thay thế!',
            ]);
        }


        $price = $this->getImportPriceProductDetail($product_id, $import->supplier, $import->delivery_date);

        $data = [
            'sku' => $product->sku ?? '',
            'unit_name' => $product->unit->name ?? '',
            'unit_type' => $product->unit->type ?? '',
            'price' => $price ,
        ];

        return response()->json($data);
    }

    /**
     * Lấy giá nhập mới nhất theo sản phẩm trong từng đơn hàng nhập.
     * @param $product_id
     * @param $supplier_id
     * @param $delivery_date
     * @return int
     */
    private function getImportPriceProductDetail($product_id, $supplier_id, $delivery_date)
    {
        $price = 0;
        $priceTable = ShopImportPriceboard::where('supplier_id', $supplier_id)->whereDate('start_date', '<=', $delivery_date)
            ->whereDate('end_date', '>=', $delivery_date)->first();
        if (!$priceTable) {
            return $price;
        }
        $priceTableDetail = ShopImportPriceboardDetail::where('priceboard_id', $priceTable->id)->where('product_id', $product_id)->first();
        if ($priceTableDetail) {
            $price = $priceTableDetail->price;
        }

        return $price;
    }

    /**
     * Xử lý lưu lịch sử thay thao tác Đơn hàng nhập
     * @param $id
     * @param $titleHistory
     * @param $contentHistory
     */
    private function storeHistory($id, $titleHistory, $contentHistory)
    {
        //Add history
        $dataHistory = [
            'id' => sc_uuid(),
            'import_id' => $id,
            'title' => $titleHistory,
            'content' => $contentHistory,
            'admin_id' => Admin::user()->id,
            'user_name' => Admin::user()->name,
            'import_id_name' => $id
        ];
        AdminImportHistory::create($dataHistory);
    }

    /**
     * Update lại tổng tiền Đơn hàng nhập.
     * @param $id
     */
    private function updateTotalImport($id)
    {
        $orderImport = AdminImport::with('details')->where('id', $id)->first();
        $total = 0;
        $totalReality = 0;
        foreach ($orderImport->details as $item) {
            $total += $item->product_price * $item->qty_order;
            $totalReality += $item->product_price * $item->qty_reality;
        }
        $orderImport->total = $total;
        $orderImport->total_reality = $totalReality;
        $orderImport->edit = 1;
        $orderImport->save();
    }

    /**
     * @param $data
     * @param $detail
     * @param $qty
     * @param string $type
     * @return bool
     */
    private function checkProductStock($data, $detail, $qty, $type)
    {
        $now = now();
        DB::beginTransaction();
        try {
            $productWarehouseImport = AdminWarehouseProduct::where('warehouse_id', $data->warehouse_id)
                ->where('product_id', $detail->product_id)->first();
            if ($productWarehouseImport) {
                if ($type == 'add') {
                    $qtyStockImport = $productWarehouseImport->qty + $qty;
                    $productWarehouseImport->qty = $productWarehouseImport->qty + $qty;
                    $productWarehouseImport->latest_import_qty = $qty;
                } else {
                    $qtyStockImport = $productWarehouseImport->qty - $qty;
                    $productWarehouseImport->qty = $productWarehouseImport->qty - $qty;
                }

                $productWarehouseImport->save();
            } else {
                if ($type == 'add') {
                    $qtyStockImport = $qty;
                } else {
                    $qtyStockImport = 0 - $qty;
                }
                AdminWarehouseProduct::create([
                    'warehouse_id' => $data->warehouse_id,
                    'product_id' => $detail->product_id,
                    'qty' => $detail->qty,
                    'latest_import_qty' => 0,
                ]);
            }
            $reportProductStockImport = AdminReportWarehouseProductStock::whereDate('date_action', $now)
                ->where('warehouse_id', $data->warehouse_id)
                ->where('product_id', $detail->product_id)->first();
            if ($reportProductStockImport) {
                if ($type == 'add') {
                    $reportProductStockImport->qty_import = $reportProductStockImport->qty_import + $qty;
                } else {
                    $reportProductStockImport->qty_import = $reportProductStockImport->qty_import - $qty;
                }
                $reportProductStockImport->qty_stock = $qtyStockImport;
                $reportProductStockImport->save();
            } else {
                $qty_import = $type == 'add' ? $qty : (0 - $qty);
                $dataInsert = [
                    'id' => sc_uuid(),
                    'warehouse_id' =>  $data->warehouse_id,
                    'warehouse_name' =>  $data->warehouse_name,
                    'product_id' =>  $detail->product_id,
                    'product_code' =>  $detail->product_code,
                    'product_name' =>  $detail->product_name,
                    'product_kind' =>  $detail->product_kind ?? 0,
                    'qty_import' =>  $qty_import,
                    'qty_export' =>  0,
                    'qty_stock' =>  $qtyStockImport,
                    'date_action' => $now,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
                AdminReportWarehouseProductStock::create($dataInsert);
            }
            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
        }
        return true;
    }


    /**
     * Check số lượng tồn trong kho
     * @param $product_id
     * @param $warehouse_id
     * @param $qty
     * @param $typeCalculate
     * @return int
     */
    private function checkQtyStock($product_id, $warehouse_id, $qty, $typeCalculate)
    {
        $dataProductWarehouse = AdminWarehouseProduct::where('product_id', $product_id)->where('warehouse_id', $warehouse_id)->first();
        if ($dataProductWarehouse) {
            if ($typeCalculate == 'add') {
                $dataProductWarehouse->qty = $dataProductWarehouse->qty + $qty;
                $dataProductWarehouse->latest_import_qty = $qty;
            } else {
                $dataProductWarehouse->qty = $dataProductWarehouse->qty - $qty;
            }
            $dataProductWarehouse->save();
            $qtyStock = $dataProductWarehouse->qty;
        } else {
            if ($typeCalculate == 'add') {
                $qtyStock = $qty;
                $latest_import_qty = $qty;
            } else {
                $qtyStock = 0 - $qty;
                $latest_import_qty = 0;
            }
            AdminWarehouseProduct::create([
                'product_id' => $product_id,
                'warehouse_id' => $warehouse_id,
                'qty' => $qtyStock,
                'latest_import_qty' => $latest_import_qty,
            ]);
        }
        return $qtyStock;
    }

    /**
     * @param $export
     * @param $detail
     * @param $qtyStock
     * @param $detail_id
     * @return array
     */
    private function addArrWarehouseCard($import, $detail, $qtyStock, $detail_id)
    {
        $now = Carbon::now();
        return $data = [
            'id' => sc_uuid(),
            'order_id' => $import->id ?? '',
            'order_id_name' => $import->id_name ?? '',
            'product_id' => $detail->product_id ?? '',
            'product_name' =>  $detail->product_name?? '',
            'product_code' => $detail->product_sku ?? '',
            'explain' => 'Phiếu nhập hàng',
            'qty_export' => 0,
            'qty_import' => $detail->qty_reality,
            'qty_stock' =>$qtyStock ?? '',
            'object_name' => $import->customer_name ?? '',
            'customer_code' => $import->customer_code ?? '',
            'bill_date' => $now,
            'warehouse_id' => $import->warehouse_id,
            'warehouse_name' => $import->warehouse_name,
            'type_order' => 2,
            'detail_id' => $detail_id,
            'created_at' => $now,
            'updated_at' => $now
        ];
    }

    /**
     * @param $export
     * @param $detail
     * @param $qtyStock
     * @param $detail_id
     * @return array
     */
    private function addArrReportProductSock($import, $detail, $qtyStock, $qtyImport, $type)
    {
        $now = Carbon::now();
        $reportProductStockImport = AdminReportWarehouseProductStock::whereDate('date_action', $now)
            ->where('warehouse_id', $import->warehouse_id)
            ->where('product_id', $detail->product_id)->first();
        if ($reportProductStockImport) {
            if ($type == 'add') {
                $reportProductStockImport->qty_import = $reportProductStockImport->qty_import + $qtyImport;
            } else {
                $reportProductStockImport->qty_import = $reportProductStockImport->qty_import - $qtyImport;
            }
            $reportProductStockImport->qty_stock = $qtyStock;
            $reportProductStockImport->save();
        } else {
            $qty_import = $type == 'add' ? $qtyImport : (0 - $qtyImport);
            $dataInsert = [
                'id' => sc_uuid(),
                'warehouse_id' =>  $import->warehouse_id,
                'warehouse_name' =>  $import->warehouse_name,
                'product_id' =>  $detail->product_id,
                'product_code' =>  $detail->product_code,
                'product_name' =>  $detail->product_name,
                'product_kind' =>  $detail->product_kind ?? 0,
                'qty_import' =>  $qty_import,
                'qty_export' =>  0,
                'qty_stock' =>  $qtyStock,
                'date_action' => $now,
                'created_at' => $now,
                'updated_at' => $now,
            ];
            AdminReportWarehouseProductStock::create($dataInsert);
        }
    }
}