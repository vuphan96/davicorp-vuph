<?php

namespace App\Admin\Controllers\Warehouse;
use App\Admin\Models\AdminCustomer;
use App\Admin\Models\AdminExport;
use App\Admin\Models\AdminExportDetail;
use App\Admin\Models\AdminExportHistory;
use App\Admin\Models\AdminImport;
use App\Admin\Models\AdminImportDetail;
use App\Admin\Models\AdminImportHistory;
use App\Admin\Models\AdminImportReturn;
use App\Admin\Models\AdminOrder;
use App\Admin\Models\AdminProduct;
use App\Admin\Models\AdminReportWarehouseProductStock;
use App\Admin\Models\AdminShopOrderChangeExtra;
use App\Admin\Models\AdminWarehouseProduct;
use App\Admin\Models\ReportWarehouseCard;
use App\Exceptions\ExportException;
use App\Exports\Warehouse\AdminExportWarehouseMultipleSheet;
use App\Front\Models\ShopCustomer;
use App\Front\Models\ShopImport;
use App\Traits\OrderTraits;
use App\Admin\Models\AdminUnit;
use App\Admin\Models\AdminWarehouse;
use App\Front\Models\ShopGenId;
use App\Front\Models\ShopImportPriceboard;
use App\Front\Models\ShopImportPriceboardDetail;
use App\Front\Models\ShopProduct;
use App\Front\Models\ShopSupplier;
use Dompdf\Dompdf;
use http\Env\Response;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Collection;
use SCart\Core\Admin\Admin;
use SCart\Core\Admin\Controllers\RootAdminController;
use SCart\Core\Front\Models\ShopOrder;
use function Symfony\Component\DomCrawler\all;
use Request;
use Validator;
use function Symfony\Component\HttpFoundation\Session\Storage\Handler\beginTransaction;
use function Symfony\Component\HttpKernel\Debug\format;


class AdminOrderExportController extends RootAdminController
{
    public $languages;
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
            'title' =>'Danh sách đơn hàng xuất',
            'subTitle' => '',
            'icon' => 'fa fa-indent',
            'urlDeleteItem' => sc_route_admin('order_export.delete'),
            'removeList' => 1, // 1 - Enable function delete list item
            'buttonRefresh' => 1, // 1 - Enable button refresh
            'buttonSort' => 1, // 1 - Enable button sort
            'css' => '',
            'js' => '',
            'method' => 'delete',
            'url_action' => sc_route_admin('admin_warehouse_export.create'),
            'url_export' => sc_route_admin('admin_warehouse_export.index'),
            'permGroup' => 'supplier'
        ];
        //Process add content
        $data['menuRight'] = sc_config_group('menuRight', Request::route()->getName());
        $data['menuLeft'] = sc_config_group('menuLeft', Request::route()->getName());
        $data['topMenuRight'] = sc_config_group('topMenuRight', Request::route()->getName());
        $data['topMenuLeft'] = sc_config_group('topMenuLeft', Request::route()->getName());
        $data['blockBottom'] = sc_config_group('blockBottom', Request::route()->getName());

        $listTh = [
            'ID' => 'ID',
            'customer_name' => 'Tên đơn vị xuất hàng',
            'created_at' => 'Ngày đặt đơn',
            'date_export' => 'Ngày xuất kho',
            'note_export' => 'Ghi chú đơn xuất',
            'type' => 'Loại đơn',
            'status' => 'Trạng thái',
            'action' => 'Thao tác',
        ];

        $searchStatus = sc_clean(request('status') ?? '');
        $searchType = sc_clean(request('type') ?? '');
        $dateType = sc_clean(request('date_type') ?? '');
        $fromDate = sc_clean(request('from_date') ?? '');
        $endDate = sc_clean(request('end_date') ?? '');
        $keyword = sc_clean(request('keyword') ?? '');

        $dataSearch = [
            'keyword' => $keyword,
            'from_date' => $fromDate,
            'end_date' => $endDate,
            'status' => $searchStatus,
            'type' => $searchType,
            'date_type' => $dateType,
        ];
        $cssTh = [
            'ID' => 'text-align: center; width: 10%',
            'customer_name' => 'text-align: center; min-width: 170px !important;',
            'created_at' => 'text-align: center; min-width:100px !important;',
            'date_export' => 'text-align: center; min-width:100px',
            'note_export' => 'text-align: center; min-width: 130px',
            'type' => 'text-align: center; min-width:135px;',
            'status' => 'text-align: center; min-width:150px;',
            'action' => 'text-align: center; width: 7%',
        ];
        $cssTd = [
            'ID' => 'text-align: center',
            'customer_name' => '',
            'created_at' => '',
            'date_export' =>'',
            'note_export' => '',
            'type' => 'text-align: center',
            'status' => 'text-align: center',
            'action' => 'text-align: center',
        ];
        $data['cssTh'] = $cssTh;
        $data['cssTd'] = $cssTd;

        $dataTmp = (new AdminExport())->getExportListAdmin($dataSearch);
        $dataTr = [];
        $status = AdminExport::$EXPORT_STATUS;
        $type = AdminExport::$TYPE;
        foreach ($dataTmp as $key => $row) {
            $dataTr[$row->id] = [

                'ID' => $row->id_name ?? '',
                'customer_name' => $row->customer_name ?? '',
                'created_at' => $row->created_at ? Carbon::make($row->created_at)->format('d/m/Y H:i:s') : '',
                'date_export' => $row->date_export != null ?  Carbon::make($row->date_export)->format('d/m/Y') : '',
                'note_export' => $row->note ?? '',
                'type' => $row->type_order == 1  ? '<span style="border: 1px solid #94B9D6; border-radius: 5px; color: #94B9D6; background-color: #F2F9FF; padding: 3px">Đơn thủ công</span>' : '<span style="border: 1px solid #9CB880; border-radius: 5px; color: #9CB880; background-color: #F2F9FF; padding: 3px">Đơn từ báo cáo</span>',
                'status' => $status[$row->status] ? '<span class="status-'. $row->status .'">' . $status[$row->status] . '</span><input type="hidden" id="check_status-'. $row->id .'" value="'. $row->status .'">': '',
                'action' => '<a data-perm="order_export:detail" href="' . sc_route_admin('admin_warehouse_export.edit', ['id' => $row->id]) . '"><span title="Sửa" type="button" class="btn btn-flat btn-sm btn-primary"><i class="fa fa-edit"></i></span></a>
                            <span data-perm="order_export:clone" onclick="cloneOrder(\'' . $row->id . '\');" title="' . sc_language_render('action.clone') . '" type="button" class="btn btn-flat btn-sm btn-secondary"><i class="fa fa-clipboard"></i></span>
                            <span data-perm="order_export:print" onclick="printPdf(\'' . $row->id . '\')"  title="' . sc_language_render('order.print.title') . '" type="button" class="btn btn-flat btn-sm btn-warning text-white"><i class="fas fa-print"></i></span>
                            <span data-perm="order_export:delete" onclick="deleteExportItem(\'' . $row->id . '\');"  title="Xóa" class="btn btn-flat btn-sm btn-danger"><i class="fas fa-trash-alt"></i></span>
                            '
            ];
        }
        $data['listTh'] = $listTh;
        $data['dataTr'] = $dataTr;
        $data['pagination'] = $dataTmp->appends(request()->except(['_token', '_pjax']))->links($this->templatePathAdmin . 'component.pagination');
        $data['resultItems'] = sc_language_render('admin.result_item', ['item_from' => $dataTmp->firstItem(), 'item_to' => $dataTmp->lastItem(), 'total' => $dataTmp->total()]);

        //menuRight
        $data['menuRight'][] = '
        <a data-perm="order_export:export" class="btn btn-success btn-flat" title="" id="button_export_filter">
        <i class="fa fa-file-export" title="' . sc_language_render('category-export') . '"></i> ' . sc_language_render('category-export') . '</a> &nbsp;
        <a data-perm="order_export:print" href="javascript:void(0)" class="btn btn-flat btn-info" onclick="savePdf()"><i class="fas fa-file-pdf"></i>&nbsp;' . sc_language_render("admin.report.print_pdf") . '</a>&nbsp;&nbsp;
        <a data-perm="order_export:create" href="' . sc_route_admin('admin_warehouse_export.create') . '" class="btn  btn-success  btn-flat" title="New" id="button_create_new">
            <i class="fa fa-plus" title="Thêm"></i>
        </a>
        ';
        // option status order
        $optionStatus = '';
        foreach ($status as $key => $item) {
            $optionStatus .= '<option  ' . (($searchStatus == $key) ? "selected" : "") . ' value="' . $key . '">' . $item . '</option>';
        }
        // option type order
        $optionType = '';
        foreach ($type as $key => $item) {
            $optionType .= '<option  ' . (($searchType == $key) ? "selected" : "") . ' value="' . $key . '">' . $item . '</option>';
        }
        $data['urlSort'] = sc_route_admin('order_export.index', request()->except(['_token', '_pjax', 'sort_order']));

        //menuSearch
        $data['topMenuRight'][] = '
                <form action="' . sc_route_admin('admin_warehouse_export.index') . '" id="button_search" autocomplete="off">
                <input type="hidden" name="limit" value="'. ($dataSearch['limit'] ?? '') .'" id="limit_paginate">
                    <div class="input-group">
                        <div class="col-lg-2 col-md-2 col-sm-12">
                            <div class="form-group">
                                <label>Chọn ngày</label>
                                <div class="input-group">
                                    <select class="form-control rounded-0" name="date_type">
                                    <option value="">Chọn ngày</option>
                                    <option value="1">Ngày đặt đơn</option>
                                    <option value="2">Ngày xuất kho</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-12">
                            <div class="form-group">
                                <label>Từ ngày</label>
                                <div class="input-group">
                                <input style="text-align: center" type="text" name="from_date" id="from_date" class="form-control input-sm date_time rounded-0"  placeholder="Chọn ngày" value=""/>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-12">
                            <div class="form-group">
                                <label>Đến ngày</label>
                                <div class="input-group">
                                <input style="text-align: center" type="text" name="end_date" id="end_date" class="form-control input-sm date_time rounded-0"  placeholder="Chọn ngày" value=""/>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-12">
                            <div class="form-group">
                                <label>Trạng thái</label></label>
                                <div class="input-group">
                                <select class="form-control rounded-0" name="status">
                                <option  value="">Tất cả trạng thái</option>
                                '.$optionStatus.'
                                </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-12">
                            <div class="form-group">
                                <label>Loại Đơn</label></label>
                                <div class="input-group">
                                <select class="form-control rounded-0" name="type">
                                '.$optionType.'
                                </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-12">
                            <div class="form-group">
                                <label>Tìm kiếm</label>
                                <div class="input-group">
                                    <input type="text" name="keyword" class="form-control rounded-0 float-right" placeholder="Mã đơn hàng" value="">
                                    <div class="input-group-append">
                                        <button type="submit" class="btn btn-primary  btn-flat"><i class="fas fa-search"></i></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>';
        //=menuSearch

        return view($this->templatePathAdmin . 'screen.warehouse.export.index')
            ->with($data);
    }

    public function create()
    {
        $dataWarehouse = AdminWarehouse::where('status', 1)->get();
        $dataCustomer = AdminCustomer::where('status', 1)->get();
        $productWarehouse = AdminWarehouseProduct::get();
        $products = (new AdminExport())->getProductList();
        $data = [
            'title' => 'Tạo đơn xuất kho',
            'subTitle' => '',
            'title_description' => '',
            'icon' => 'fa fa-plus',
            'languages' => $this->languages,
            'list_warehouse' => $dataWarehouse,
            'customers' => $dataCustomer,
            'dataProduct' => $products,
            'dataProductWarehouse' => $productWarehouse,
            'url_action' => sc_route_admin('admin_warehouse_export.create'),
        ];


        $listTh = [
            'ID' => 'STT',
            'product_code' => 'Mã sản phẩm',
            'product_name' => 'Tên sản phẩm',
            'qty' => 'Số lượng',
            'unit' => 'Đơn vị tính',
            'note' => 'Ghi chú',
            'action' => 'Thao tác',
        ];
        $data['listTh'] = $listTh;
        $data['dataTr'] = [];


        return view($this->templatePathAdmin . 'screen.warehouse.export.create')
            ->with($data);
    }

    public function postCreate(Request $request)
    {
        $data = [
            'data_save' =>request('data_save'),
            'warehouse' =>request('warehouse'),
            'customer_id' =>request('customer_id'),
            'address' =>request('address'),
            'date_export' =>request('date_export'),
            'note' =>request('note'),
        ];
        DB::beginTransaction();
        try{
            $customer = AdminCustomer::find($data['customer_id']);
            $warehouse = AdminWarehouse::find($data['warehouse']);
            $dataInsert = [
                'id_name' => ShopGenId::genNextId('order_export'),
                'customer_id' => $data['customer_id'] ?? '',
                'customer_name' => $customer->name ?? 'Lý do khác',
                'customer_code' =>  $customer->customer_code ?? '',
                'customer_addr' =>  $customer->address ?? '',
                'phone' =>  $customer->phone ?? '',
                'email' =>  $customer->email ?? '',
                'warehouse_id' => $data['warehouse'],
                'warehouse_name' => $warehouse->name,
                'warehouse_code' => $warehouse->warehouse_code,
                'date_export' => $data['date_export'] ?? Carbon::now()->format('Y-m-d'),
                'type_order' => 1,
                'status' => 1,
                'edit' => 0,
                'note' => $data['note'],
            ];
            $dataExport = AdminExport::create($dataInsert);
            foreach ($data['data_save'] as $key => $row) {
                $dataDetailInsert = [
                    'id' => sc_uuid(),
                    'export_id' => $dataExport->id,
                    'export_code' => $dataExport->id_name,
                    'product_id' => $row['productId'],
                    'product_name' => $row['productName'],
                    'product_kind' => $row['productKind'],
                    'category_id' => $row['categoryId'],
                    'product_sku' => $row['productSku'],
                    'qty' => $row['qty'],
                    'qty_reality' => $row['qty'],
                    'unit' => $row['unit'],
                    'comment' => $row['note'],
                    'department_id' => $customer->department_id ?? '',
                    'customer_name' => $customer->name ?? '',
                    'customer_code' => $customer->customer_code ?? '',
                    'zone_id'       => $customer->zone_id ?? '',

                ];
                AdminExportDetail::create($dataDetailInsert);
            }
            DB::commit();
            return response()->json(['success' => true, 'message' => 'Dữ liệu đã được lưu thành công']);
        } catch (\Throwable $e) {
            DB::rollback();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }


    }

    public function getListProduct() {
        $customer_id = request('customer_id');
        $bill_date = request('bill_date');

        if ($customer_id && $bill_date) {
            $products= (new AdminOrder())->getProductByCustomerPriceBoard($bill_date, $customer_id, null);
        }
        else {
            $products = (new AdminExport())->getProductList()->toArray();
        }
        if(!$products) {
            return response()->json(['error' => 1, 'msg' => 'Lỗi hệ thống']);
        }
        return response()->json($products);
    }

    public function edit($id)
    {
        $productWarehouse = AdminWarehouseProduct::get();
        $orderExport = AdminExport::with('details', 'history')->where('id',$id)->first();
        if (!$orderExport) {
            return redirect()->route('admin.data_not_found')->with(['url' => url()->full()]);
        }
        $dataCustomer = AdminCustomer::where('status', 1)->get();
        $status = AdminExport::$EXPORT_STATUS;
        $products = (new AdminExport())->getProductList();
        $editable = $this->checkOrderEditable($orderExport);
        return view($this->templatePathAdmin . 'screen.warehouse.export.edit')->with(
            [
                "title" => 'Chi tiết đơn hàng xuất',
                "subTitle" => '',
                'icon' => 'fa fa-file-text-o',
                "orderExport" => $orderExport,
                "status" => $status,
                'editable' => $editable,
                'products' => $products,
                'customers' => $dataCustomer,
                'dataProductWarehouse' => $productWarehouse,
            ]
        );
    }
    public function updateCustomer(){
        $dataCustomer = request('dataCustomer');
        $orderId = request('orderId');
        DB::beginTransaction();
        try{
            $dataExport = AdminExport::find($orderId);
            $dataCustomerUpdate = [
                'customer_id' => $dataCustomer['customer_id'] ?? '',
                'customer_name' => $dataCustomer['customer_name'] ?? 'Lý do khác',
                'customer_code' => $dataCustomer['customer_code'] ?? '',
                'customer_addr' => $dataCustomer['customer_addr'],
                'email' => $dataCustomer['customer_email'],
                'phone' => $dataCustomer['customer_phone'],
            ];
            $dataCustomerDetailUpdate = [
                'customer_name' => $dataCustomer['customer_name'] ?? '',
                'customer_code' => $dataCustomer['customer_code'] ?? '',
            ];
            if ($dataExport->status == 2) {
                return response()->json(['error' => true, 'message' => 'Đơn đã xuất không thể chỉnh sửa!']);
            }
            $title = 'Thay đổi thông tin khách hàng';
            $contentHistory =  $dataExport->customer_name .'->'.($dataCustomer['customer_name'] ?? 'Lý do khác' );
            $this->storeHistory($dataExport->id, $title, $contentHistory);
            $dataExport->update($dataCustomerUpdate);
            AdminExportDetail::where('export_id',$orderId)->update($dataCustomerDetailUpdate);
            DB::commit();
            return response()->json(['success' => true, 'message' => 'Dữ liệu đã được lưu thành công']);
        } catch (\Throwable $e) {
            DB::rollback();
            return response()->json(['error' => true, 'message' => $e->getMessage()]);
        }
    }

    /**
     * Thêm chi tiết sản phẩm mới
     * @return \Illuminate\Http\JsonResponse
     */
    public function postEdit() {
        $data = [
            'data_save' =>request('data_save'),
            'order_id' =>request('order_id'),
            'customer_id' =>request('customer_id')
        ];
        $product_name = [];
        DB::beginTransaction();
        try{
            $dataExport = AdminExport::find($data['order_id']);
            if ($dataExport->status == 2) {
                return response()->json(['success' => false, 'message' => 'Đơn đã xuất không thể chỉnh sửa!'], 500);
            }
            foreach ($data['data_save'] as $key => $row) {
                $id_detail = sc_uuid();
                $product_name[] = $row['productName'];
                $dataDetailInsert = [
                    'id' => $id_detail,
                    'export_id' => $dataExport->id,
                    'export_code' => $dataExport->id_name,
                    'product_id' => $row['productId'],
                    'product_name' => $row['productName'],
                    'product_sku' => $row['productSku'],
                    'qty' => $row['qty'],
                    'qty_reality' => $row['qty'],
                    'unit' => $row['unit'],
                    'comment' => $row['note'],
                ];
                AdminExportDetail::create($dataDetailInsert);
            }
            $title = 'Thêm sản phẩm mới';
            $contentHistory = implode("<br>", $product_name);
            $this->storeHistory($dataExport->id, $title, $contentHistory);
            DB::commit();
            return response()->json(['success' => true, 'message' => 'Dữ liệu đã được lưu thành công']);
        } catch (\Throwable $e) {
            DB::rollback();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
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
        DB::beginTransaction();
        try {
            $status = AdminExport::$EXPORT_STATUS;
            if ($code == 'note') {
                $orderExport = AdminExport::find($id);
                $title = 'Sửa ghi chú đơn xuất hàng';
                $contentHistory = 'Thay đổi: '.$orderExport->comment.' -> '.$value;
                $this->storeHistory($id, $title, $contentHistory);
                $orderExport->note = $value;
                $orderExport->edit = 1;
                $orderExport->save();
            }
            $arrIdDetail = [];
            if ($code == 'status') {
                $orderExport = AdminExport::with('details')->find($id);
                if($orderExport->status == 2 && $value != 2) {
                    foreach ($orderExport->details as $item) {
                        $arrIdDetail[]= $item->order_detail_id;
                        $qtyStock = $this->checkQtyStock($item->product_id, $orderExport->warehouse_id , $item->qty, 'add');
                        $this->addArrReportProductSock($orderExport, $item,$qtyStock, $item->qty, 'sub');
                    }
                    ReportWarehouseCard::where('order_id', $orderExport->id)->delete();
                } else {
                    if($value==2) {
                        $dataWarehouseCardInsert = [];
                        foreach ($orderExport->details as $item) {
                            $arrIdDetail[]= $item->order_detail_id;
                            $qtyStock = $this->checkQtyStock($item->product_id, $orderExport->warehouse_id , $item->qty, 'sub');;
                            $dataWarehouseCardInsert[] = $this->addArrWarehouseCard($orderExport, $item, $qtyStock, $item->id);
                            $this->addArrReportProductSock($orderExport, $item,$qtyStock, $item->qty, 'add');
                        }
                        ReportWarehouseCard::insert($dataWarehouseCardInsert);
                    } else {
                        foreach ($orderExport->details as $item) {
                            $arrIdDetail[]= $item->order_detail_id;
                        }
                    }
                }
                AdminShopOrderChangeExtra::whereIn('order_detail_id', $arrIdDetail)->update(['status'=>$value]);

                $title = 'Sửa trạng thái đơn xuất';
                $contentHistory = 'Thay đổi: '.$status[$orderExport->status].' -> '.$status[$value];
                $this->storeHistory($id, $title, $contentHistory);
                $orderExport->status = $value;
                $orderExport->edit = 1;
                $orderExport->date_export = now();
                $orderExport->save();
            }

            if ($code == 'qty') {
                $detail = AdminExportDetail::find($id);
                $orderExport = AdminExport::find($detail->export_id);
                $oldQty = $detail->qty;
                $detail->qty = $value;
                $detail->qty_reality = $value;
                $detail->amount = $value * $detail->price;
                $detail->amount_reality = $value * $detail->price;
                $detail->save();
                $title = 'Thay đổi số lượng: '.$detail->product_name;
                $contentHistory = 'Thay đổi: '.$oldQty.' -> '.$value;
                $this->storeHistory($detail->export_id, $title, $contentHistory);
                $this->updateTotalExport($detail->export_id);
            }

            if ($code == 'qty_reality') {
                $detail = AdminExportDetail::find($id);
                $orderExport = AdminExport::find($detail->export_id);
                $oldQty = $detail->qty_reality;
                $detail->qty_reality = $value;
                $detail->amount_reality = $value * $detail->price;
                $detail->save();
                $title = 'Thay đổi số lượng thực tế: '.$detail->product_name;
                $contentHistory = 'Thay đổi: '.$oldQty.' -> '.$value;
                $this->storeHistory($detail->export_id, $title, $contentHistory);
                $this->updateTotalExport($detail->export_id);
            }

            if ($code == 'price') {
                $detail = AdminExportDetail::find($id);
                $oldPrice = $detail->price;
                $detail->price = $value;
                $detail->amount = $value * $detail->qty;
                $detail->amount_reality = $value * $detail->qty_reality;
                $detail->save();
                $title = 'Thay đổi giá: '.$detail->product_name;
                $contentHistory = 'Thay đổi: '.$oldPrice.' -> '.$value;
                $this->storeHistory($detail->export_id, $title, $contentHistory);
                $this->updateTotalExport($detail->export_id);
            }

            if ($code == 'comment') {
                $detail = AdminExportDetail::find($id);
                $oldComment = $detail->comment;
                $detail->comment = $value;
                $detail->save();
                $title = 'Thay đổi ghi chú chi tiết: '.$detail->product_name;
                $contentHistory = 'Thay đổi: '.($oldComment != '' ? $oldComment : 'Trống').' -> '.$value;
                $this->storeHistory($detail->export_id, $title, $contentHistory);
            }
            DB::commit();
            return response()->json([
                'error' => 0,
                'msg' => sc_language_render('action.update_success')
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error($e);
            return response()->json([
                'error' => 1,
                'msg' => $e->getMessage()
            ]);
        }
    }

    public function cloneExportOrder()
    {
        $id = request('id') ?? '';
        $dataExport = AdminExport::findOrFail($id);
        $dataExportDetail = AdminExportDetail::where('export_id', $id)->get();
        $customer = new ShopCustomer();
        if (!$dataExport) {
            throw new ExportException('Không tìm thấy đơn hàng');
        }
        if ($dataExport->customer_code){
            $customer = $customer->Where('customer_code', $dataExport->customer_code)->first();
        }
        DB::beginTransaction();
        try{
            $dataExportClone = [
                'id_name' => ShopGenId::genNextId('order_export'),
                'customer_id' => $dataExport->customer_code ?  $customer->id : '',
                'customer_name' => $dataExport->customer_code ? $customer->name : 'Lý do khác',
                'customer_code' => $dataExport->customer_code ? $customer->customer_code : '',
                'customer_addr' => $dataExport->customer_code ?  ($customer->address ?? '') : '',
                'phone' => $dataExport->customer_code ?  ($customer->phone ?? '') : '',
                'email' => $dataExport->customer_code ?  ($customer->email ?? '') : '',
                'warehouse_id' => $dataExport->warehouse_id ??'',
                'warehouse_name' => $dataExport->warehouse_name ??'',
                'warehouse_code' => $dataExport->warehouse_code ??'',
                'date_export' => $dataExport->date_export ?? Carbon::now()->format('Y-m-d'),
                'type_order' => $dataExport->type_order ??'',
                'total' => $dataExport->total ?? 0,
                'total_reality' => $dataExport->total_reality ?? 0,
                'status' => 1,
                'edit' => 0,
                'note' => $dataExport->note ?? '',
            ];
            $dataExport = AdminExport::create($dataExportClone);
            foreach ($dataExportDetail as $key => $row) {
                $dataDetailInsert = [
                    'id' => sc_uuid(),
                    'export_id' => $dataExport->id,
                    'export_code' => $dataExport->id_name,
                    'product_id' => $row->product_id ?? '',
                    'product_name' => $row->product_name ?? '',
                    'product_sku' => $row->product_sku ?? '',
                    'qty' => $row->qty ?? 0,
                    'qty_reality' => $row->qty_reality ?? '',
                    'unit' => $row->unit ?? '',
                    'amount' => $row->amount ?? 0,
                    'amount_reality' => $row->amount_reality ?? 0,
                    'comment' => $row->comment ?? '',
                    'product_kind'  => $row->product_kind ?? '',
                    'order_id'      =>$row->order_id ?? '',
                    'order_id_name' => $row->order_id_name ?? '',
                    'order_explain' => $row->order_explain ?? '',
                    'order_object_id' => $row->order_object_id ?? '',
                    'order_delivery_date' => $row->order_delivery_date ?? '',
                    'category_id' => $row->category_id ?? '',
                    'department_id' => $row->department_id ?? '',
                    'customer_name' => $row->customer_name ?? '',
                    'customer_code' => $row->customer_code ?? '',
                    'zone_id' => $row->zone_id ?? '',

                ];
                AdminExportDetail::create($dataDetailInsert);
            }
            DB::commit();
            return response()->json(['success' => true, 'message' => 'Nhân bản thành công']);
        } catch (\Throwable $e) {
            DB::rollback();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Xóa chi tiết từng sản phẩm
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function deleteExportOrderDetail(){
        $detail_id = request('pId');
        try {
            DB::beginTransaction();
            $detail = AdminExportDetail::find($detail_id);
            if ($detail) {
                $title = 'Xóa sản phẩm';
                $contentHistory = 'Xóa SP: '. $detail->product_name;
                $this->storeHistory($detail->export_id, $title, $contentHistory);
                $detail->delete();
                $this->updateTotalExport($detail->export_id);
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
    public function deleteExportOrder()
    {
        $ids = request('ids');
        $arrID = explode(',', $ids);
        try {
            DB::beginTransaction();
            foreach ($arrID as $item) {
                $result = AdminExport::where('id', $item)->where('status', '!=', 2)->first();
                if ($result) {
                    $result->delete();
                }
            }
            DB::commit();
            return response()->json(['error' => 0, 'msg' => sc_language_render('action.update_success')]);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error($e);
            return response()->json(['error' => 1, 'msg' => 'Xóa đơn nhập hàng lỗi!']);
        }
    }

    ///todo
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

    /**
     * Update lại tổng tiền Đơn hàng nhập.
     * @param $id
     */
    private function updateTotalExport($id)
    {
        $orderExport = AdminExport::with('details')->where('id', $id)->first();
        $total = 0;
        $totalReality = 0;
        foreach ($orderExport->details as $item) {
            $total += $item->price * $item->qty;
            $totalReality += $item->price * $item->qty_reality;
        }
        $orderExport->total = $total;
        $orderExport->total_reality = $totalReality;
        $orderExport->edit = 1;
        $orderExport->save();
    }

    public function printPdf(){

        $ids = explode(',', request('ids'));
        $dataExport = AdminExport::with('details')->orderBy('created_at', 'DESC')->findMany($ids);
        if (!count($dataExport) > 0) {
            return redirect()->route('admin.data_not_found')->with(['url' => url()->full()]);
        }
        if (count($dataExport) > 200) {
            return redirect()->back()->with(['error' => 'Dữ liệu quá tải vui lòng chọn lại trường lọc!']);
        }

        $html = view($this->templatePathAdmin . 'screen.warehouse.export.print.template_print_pdf')
            ->with(['data' => $dataExport])->render();
        $html = mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8');

        return $html;
    }
    public function exportExcel(){

        $ids = explode(',', request('ids'));
        $dataExport = AdminExport::with('details')->orderBy('created_at', 'DESC')->findMany($ids);
        if (!count($dataExport) > 0) {
            return redirect()->route('admin.data_not_found')->with(['url' => url()->full()]);
        }
        if (count($dataExport) > 200) {
            return redirect()->back()->with(['error' => 'Dữ liệu quá tải vui lòng chọn lại trường lọc!']);
        }
        return Excel::download(new AdminExportWarehouseMultipleSheet($dataExport), 'Đơn hàng xuất - ' . Carbon::now() . '.xlsx');
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
            if ($typeCalculate == 'sub') {
                $dataProductWarehouse->qty = $dataProductWarehouse->qty - $qty;
            } else {
                $dataProductWarehouse->qty = $dataProductWarehouse->qty + $qty;
            }
            $dataProductWarehouse->save();
            $qtyStock = $dataProductWarehouse->qty;
        } else {
            if ($typeCalculate == 'sub') {
                $qtyStock = 0 - $qty;
            } else {
                $qtyStock = $qty;
            }
            AdminWarehouseProduct::create([
                'product_id' => $product_id,
                'warehouse_id' => $warehouse_id,
                'qty' => $qtyStock,
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
    private function addArrWarehouseCard($export, $detail, $qtyStock, $detail_id)
    {
         $now = Carbon::now();
         return $data = [
            'id' => sc_uuid(),
            'order_id' => $detail->export_id ?? '',
            'order_id_name' => $detail->export_code ?? '',
            'product_id' => $detail->product_id ?? '',
            'product_name' =>  $detail->product_name?? '',
            'product_code' => $detail->product_sku ?? '',
            'explain' => $detail->order_explain ?? '',
            'qty_export' =>$detail->qty_reality ?? '',
            'qty_stock' =>$qtyStock ?? '',
            'object_name' => $export->customer_name ?? '',
            'customer_code' => $export->customer_code ?? '',
            'bill_date' => $now,
            'warehouse_id' => $export->warehouse_id,
            'warehouse_name' => $export->warehouse_name,
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
    private function addArrReportProductSock($export, $detail, $qtyStock, $qtyExport, $type)
    {
        $now = Carbon::now();
        $reportProductStockImport = AdminReportWarehouseProductStock::whereDate('date_action', $now)
            ->where('warehouse_id', $export->warehouse_id)
            ->where('product_id', $detail->product_id)->first();
        if ($reportProductStockImport) {
            if ($type == 'add') {
                $reportProductStockImport->qty_export = $reportProductStockImport->qty_export + $qtyExport;
            } else {
                $reportProductStockImport->qty_export = $reportProductStockImport->qty_export - $qtyExport;
            }
            $reportProductStockImport->qty_stock = $qtyStock;
            $reportProductStockImport->save();
        } else {
            $qty_export = $type == 'add' ? $qtyExport : (0 - $qtyExport);
            $dataInsert = [
                'id' => sc_uuid(),
                'warehouse_id' =>  $export->warehouse_id,
                'warehouse_name' =>  $export->warehouse_name,
                'product_id' =>  $detail->product_id,
                'product_code' =>  $detail->product_sku,
                'product_name' =>  $detail->product_name,
                'product_kind' =>  $detail->product_kind ?? 0,
                'qty_import' =>  0,
                'qty_export' =>  $qty_export,
                'qty_stock' =>  $qtyStock,
                'date_action' => $now,
                'created_at' => $now,
                'updated_at' => $now,
            ];
            AdminReportWarehouseProductStock::create($dataInsert);
        }
    }

}