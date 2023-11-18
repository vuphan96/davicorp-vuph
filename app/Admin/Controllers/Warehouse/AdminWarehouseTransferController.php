<?php

namespace App\Admin\Controllers\Warehouse;


use App\Admin\Models\AdminImport;
use App\Admin\Models\AdminImportDetail;
use App\Admin\Models\AdminReportWarehouseProductStock;
use App\Admin\Models\AdminWarehouseProduct;
use App\Admin\Models\AdminWarehouseTransfer;
use App\Admin\Models\AdminWarehouseTransferDetail;
use App\Admin\Models\AdminWarehouseTransferHistory;
use App\Admin\Models\AdminWarehouseTransferWithImport;
use App\Admin\Models\ReportWarehouseCard;
use App\Traits\OrderTraits;
use App\Admin\Models\AdminWarehouse;
use App\Front\Models\ShopGenId;
use App\Front\Models\ShopProduct;
use App\Front\Models\ShopSupplier;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use SCart\Core\Admin\Admin;
use Illuminate\Http\Request;
use SCart\Core\Admin\Controllers\RootAdminController;
use SCart\Core\Front\Models\ShopOrder;
use Validator;
use Illuminate\Database\Eloquent\Collection;



class AdminWarehouseTransferController extends RootAdminController
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
            'title' => 'Danh sách điều chuyển hàng hóa',
            'subTitle' => '',
            'icon' => 'fa fa-indent',
            'removeList' => 1, // 1 - Enable function delete list item
            'buttonRefresh' => 1, // 1 - Enable button refresh
            'buttonSort' => 1, // 1 - Enable button sort
            'css' => '',
            'js' => '',
        ];
        //Process add content
        $data['menuRight'] = sc_config_group('menuRight', \Request::route()->getName());
        $data['menuLeft'] = sc_config_group('menuLeft', \Request::route()->getName());
        $data['topMenuRight'] = sc_config_group('topMenuRight', \Request::route()->getName());
        $data['topMenuLeft'] = sc_config_group('topMenuLeft', \Request::route()->getName());
        $data['blockBottom'] = sc_config_group('blockBottom', \Request::route()->getName());

        $listTh = [
            'ID' => 'Mã CH',
            'title' => 'Tên đơn chuyển hàng',
            'created_at' => 'Ngày đặt đơn',
            'export_date' => 'Ngày chuyển hàng thành công',
            'reason' => 'Lý do chuyển hàng',
            'status' => 'Trạng thái',
            'action' => 'Thao tác',
        ];

        $dataSearch = [
            'keyword' => sc_clean(request('keyword') ?? ''),
            'type_date' => sc_clean(request('type_date') ?? ''),
            'date_start' => sc_clean(request('date_start') ?? ''),
            'date_end' => sc_clean(request('date_end') ?? ''),
            'status' => sc_clean(request('status') ?? ''),
        ];

        $cssTh = [
            'ID' => 'text-align: center; width: 10%',
            'title' => 'text-align: center; min-width: 200px !important;',
            'created_at' => 'text-align: center; min-width:110px !important;',
            'export_date' => 'text-align: center; min-width:100px',
            'reason' => 'text-align: center; min-width:200px;',
            'status' => 'text-align: center; min-width:150px;',
            'action' => 'text-align: center; width: 7%',
        ];
        $cssTd = [
            'ID' => 'text-align: center',
            'title' => '',
            'created_at' => '',
            'export_date' => '',
            'reason' => 'text-align: center',
            'status' => 'text-align: center',
            'action' => 'text-align: center',
        ];
        $data['cssTh'] = $cssTh;
        $data['cssTd'] = $cssTd;
        $dataTmp = AdminWarehouseTransfer::getLitAll($dataSearch);
        $dataTr = [];
        $status = AdminWarehouseTransfer::STATUS;
        foreach ($dataTmp as $key => $row) {
            $dataTr[$row->id] = [
                'ID' => $row->id_name,
                'title' => $row->title,
                'created_at' => Carbon::make($row->created_at ?? '')->format('d/m/Y H:i:s'),
                'export_date' => $row->date_export != '' ? Carbon::make($row->date_export)->format('d/m/Y') : '',
                'reason' => $row->reason,
                'status' => '<span class="badge '. ( $row->status == 1 ? 'badge-secondary' : ($row->status == 2 ? 'badge-warning' : ($row->status == 3 ? 'badge-info' : ( $row->status == 4 ? 'badge-danger': 'badge-success' )))) .'">'.$status[$row->status].'</span>',
                'action' => '
                    <a data-perm="warehouse_transfer:detail" href="' . sc_route_admin('warehouse_transfer.edit', ['id' => $row->id ? $row->id : 'not-found-id']) . '"><span title="' . sc_language_render('action.edit') . '" type="button" class="btn btn-flat btn-sm btn-primary"><i class="fa fa-edit"></i></span></a>&nbsp;
                     <span data-perm="warehouse_transfer:clone" onclick="cloneOrder(\'' . $row->id . '\');" title="' . sc_language_render('action.clone') . '" type="button" class="btn btn-flat btn-sm btn-secondary"><i class="fa fa-clipboard"></i></span>
                    <span data-perm="warehouse_transfer:print" onclick="printModal(\'' . $row->id . '\')"  title="' . sc_language_render('order.print.title') . '" type="button" class="btn btn-flat btn-sm btn-warning text-white"><i class="fas fa-print"></i></span>
                    <span data-perm="warehouse_transfer:delete" onclick="deleteItem(\'' . $row->id . '\');"  title="' . sc_language_render('action.delete') . '" class="btn btn-flat btn-sm btn-danger"><i class="fas fa-trash-alt"></i></span>'
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
            $optionStatus .= '<option  ' . (($dataSearch['status'] == $key) ? "selected" : "") . ' value="' . $key . '">' . $item . '</option>';
        }
        $data['urlSort'] = sc_route_admin('warehouse_transfer.index', request()->except(['_token', '_pjax', 'sort_order']));
        //=menuSort
        //menuRight
        $data['menuRight'][] = '
            <a data-perm="product_exchange:create" href="' . sc_route_admin('warehouse_transfer.create') . '" class="btn btn-success btn-flat" title="Tạo mới đơn điều chuyển" id="button_create_new">
                <i class="fa fa-plus"></i>
            </a>
           ';
        //=menuRight
        //menuSearch
        $data['topMenuRight'][] = '
                <form action="' . sc_route_admin('warehouse_transfer.index') . '" id="button_search" autocomplete="off">
                <input type="hidden" name="limit" value="' . ($dataSearch['limit'] ?? '') . '" id="limit_paginate">
                    <div class="input-group float-right">
                           <div class="col-lg-2 col-md-2 col-sm-12">
                            <div class="form-group">
                                <label>Chọn ngày</label>
                                <div class="input-group">
                                    <select class="form-control rounded-0" name="type_date">
                                    <option value="export_date" ' . ($dataSearch['type_date'] == 'export_date' ? 'selected' : "") . '>Ngày chuyển hàng hoá</option>
                                    <option value="created_at" ' . ($dataSearch['type_date'] == 'created_at' ? 'selected' : "") . '>Ngày tạo đơn</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-12">
                            <div class="form-group">
                                <label>Từ ngày</label>
                                <div class="input-group">
                                <input style="text-align: center" type="text" name="date_start" id="date_start" class="form-control input-sm date_time rounded-0"  placeholder="Chọn ngày" value="' . $dataSearch['date_start'] . '"/>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-12">
                            <div class="form-group">
                                <label>Đến ngày</label>
                                <div class="input-group">
                                <input style="text-align: center" type="text" name="date_end" id="date_end" class="form-control input-sm date_time rounded-0"  placeholder="Chọn ngày" value="' . $dataSearch['date_start'] . '"/>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-12">
                            <div class="form-group">
                                <label>Trạng thái</label></label>
                                <div class="input-group">
                                <select class="form-control rounded-0" name="status">
                                <option value="">Tất cả trạng thái</option>
                                ' . $optionStatus . '
                                </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-4 col-sm-12">
                            <div class="form-group">
                                <label>Tìm kiếm</label>
                                <div class="input-group">
                                    <input type="text" name="keyword" class="form-control rounded-0 float-right" placeholder="Mã, Tên đơn chuyển" value="' . $dataSearch['keyword'] . '">
                                    <div class="input-group-append">
                                        <button type="submit" class="btn btn-primary  btn-flat"><i class="fas fa-search"></i></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>';
        //=menuSearch

        return view($this->templatePathAdmin . 'screen.warehouse.warehouse_transfer.index')
            ->with($data);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function create()
    {

        $products = ShopProduct::with('unit')->where('status', 1)->get();
        $unit = [];
        foreach ($products as $product) {
            $unit[] = [
                'product_id' => $product->id,
                'sku' => $product->sku,
                'unit_name' => $product->unit->name ?? '',
                'type_unit' => $product->unit->type ?? 1,
            ];
        }
        $wareHouse = AdminWarehouse::where('status', 1)->get();
        $data = [
            'title' => 'Tạo đơn điều chuyển hàng hóa',
            'subTitle' => '',
            'unit' => $unit,
            'products' => $products,
            'wareHouse' => $wareHouse,
            'title_description' => 'Tạo đơn điều chuyển hàng hóa',
            'icon' => 'fa fa-plus',
        ];

        return view($this->templatePathAdmin . 'screen.warehouse.warehouse_transfer.create')
            ->with($data);
    }

    /**
     * Lưu đơn chuyển hàng.
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $data = $request->all();
        DB::beginTransaction();
        try {
            $wareHouseTo = AdminWarehouse::where('id', $data['warehouse_id_to'])->first();
            $wareHouseFrom = AdminWarehouse::where('id', $data['warehouse_id_from'])->first();
            $dataInsert = [
                'title' => $data['title'] ?? '',
                'id_name' => ShopGenId::genNextId('warehouse_transfer'),
                'reason' => $data['reason'] ?? '',
                'warehouse_id_to' => $wareHouseTo->id,
                'warehouse_name_to' => $wareHouseTo->name,
                'warehouse_code_to' => $wareHouseTo->warehouse_code,
                'warehouse_id_from' => $wareHouseFrom->id,
                'warehouse_name_from' => $wareHouseFrom->name,
                'warehouse_code_from' => $wareHouseFrom->warehouse_code,
                'date_export' => null,
                'edit' => 0,
                'status' => 1,
                'note' => '',
            ];

            $warehouseTransfer = AdminWarehouseTransfer::create($dataInsert);
            $dataInsertDetail = [];
            foreach ($data['product_id'] as $key => $productId) {
                $product = ShopProduct::with('unit')->where('id', $productId)->first();
                $dataInsertDetail[] = [
                    'id' => sc_uuid(),
                    'warehouse_transfer_id' => $warehouseTransfer->id,
                    'product_id' => $product->id,
                    'product_code' => $product->sku,
                    'product_name' => $product->name,
                    'product_kind' => $product->kind,
                    'qty' => $data['qty'][$key] ?? 0,
                    'unit_name' => $product->unit->name ?? '',
                    'comment' => $data['comment'][$key] ?? '',
                ];
            }
            if (isset($data['id_order_import'])) {
                foreach ($data['id_order_import'] as $item) {
                    $orderImport = AdminImport::where('id', $item)->first();
                    AdminWarehouseTransferWithImport::create([
                        'warehouse_transfer_id' => $warehouseTransfer->id,
                        'import_id' => $orderImport->id,
                        'import_id_name' => $orderImport->id_name,
                    ]);
                }
            }
            AdminWarehouseTransferDetail::insert($dataInsertDetail);
            DB::commit();
            return redirect()->route('warehouse_transfer.index')->with(['success' => 'Tạo mới thành công']);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error($e);
            return redirect()->back()->with(['error' => $e->getMessage()]);
        }
    }

    /**
     * @param $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     */
    public function edit($id)
    {
        $data = AdminWarehouseTransfer::with('details', 'history', 'imports')->where('id', $id)->first();
        if (!$data) {
            return redirect()->route('admin.data_not_found')->with(['url' => url()->full()]);
        }

        $status = AdminWarehouseTransfer::STATUS;
        $products = ShopProduct::with('unit')->where('status', 1)->get();
        $unit = [];
        foreach ($products as $product) {
            $unit[] = [
                'product_id' => $product->id,
                'sku' => $product->sku,
                'unit_name' => $product->unit->name ?? '',
                'type_unit' => $product->unit->type ?? 1,
            ];
        }
        $editable = $this->checkOrderEditable($data);
        return view($this->templatePathAdmin . 'screen.warehouse.warehouse_transfer.edit')->with(
            [
                "title" => 'Chi tiết đơn chuyển hàng',
                "subTitle" => '',
                'icon' => 'fa fa-file-text-o',
                'unit' => $unit,
                "warehouseTransfer" => $data,
                "status" => $status,
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
        DB::beginTransaction();
        try {
            if ($code == 'note') {
                $data = AdminWarehouseTransfer::find($id);
                $title = 'Sửa ghi chú đơn chuyển hàng';
                $contentHistory = 'Thay đổi: ' . $data->note . ' -> ' . $value;
                $this->storeHistory($id, $title, $contentHistory);
                $data->note = $value;
                $data->edit = 1;
                $data->save();
            }

            if ($code == 'status') {
                $status = AdminWarehouseTransfer::STATUS;
                $data = AdminWarehouseTransfer::with('details')->find($id);
                $statusOrigin = $data->status;
                $title = 'Sửa trạng thái đơn chuyển hàng';
                $contentHistory = 'Thay đổi: ' . $status[$data->status] . ' -> ' . $status[$value];
                $this->storeHistory($id, $title, $contentHistory);
                if ($value == 3) {
                    foreach ($data->details as $detail) {
                        # xử lý kho nhận.
                        $qtyStockForm = $this->checkQtyStock($detail->product_id ?? '' ,$data->warehouse_id_from ?? '', $detail->qty,'add', 'import');
                        $this->checkReportProductStock($data->warehouse_id_from, $data->warehouse_name_from, $detail, $detail->qty, $qtyStockForm,'add' ,'import');
                        $this->addArrWarehouseCard($data, $detail, $qtyStockForm, 'import');
                        # Xử khí kho xuất hàng.
                        $qtyStockTo = $this->checkQtyStock($detail->product_id, $data->warehouse_id_to, $detail->qty,'sub', 'export');
                        $this->checkReportProductStock($data->warehouse_id_to, $data->warehouse_name_to, $detail, $detail->qty, $qtyStockTo,'sub' ,'export');
                        $this->addArrWarehouseCard($data, $detail, $qtyStockForm, 'export');
                    }
                }

                if ($statusOrigin == 3 && $value != 3) {
                    foreach ($data->details as $detail) {
                        # xử lý kho nhâp hàng.
                        $qtyStockForm = $this->checkQtyStock($detail->product_id ?? '' ,$data->warehouse_id_form ?? '',
                            $detail->qty,'sub', 'import');
                        $this->checkReportProductStock($data->warehouse_id_form, $data->warehouse_name_form, $detail,
                            $detail->qty, $qtyStockForm,'sub' ,'import');
                        # Xử khí kho xuất hàng.
                        $qtyStockTo= $this->checkQtyStock($detail->product_id ?? '' , $data->warehouse_id_to ?? '',
                            $detail->qty,'add', 'export');
                        $this->checkReportProductStock($data->warehouse_id_to ?? '', $data->warehouse_name_to ?? '', $detail,
                            $detail->qty, $qtyStockTo,'add' ,'export');
                    }
                    ReportWarehouseCard::where('order_id', $data->id)->delete();
                }

                $data->status = $value;
                $data->edit = 1;
                $data->save();
            }

            if ($code == 'title') {
                $data = AdminWarehouseTransfer::find($id);
                $title = 'Sửa tên đơn chuyển hàng';
                $contentHistory = 'Thay đổi: ' . $data->title . ' -> ' . $value;
                $this->storeHistory($id, $title, $contentHistory);
                $data->title = $value;
                $data->edit = 1;
                $data->save();
            }

            if ($code == 'reason') {
                $data = AdminWarehouseTransfer::find($id);
                $title = 'Sửa lý do chuyển hàng';
                $contentHistory = 'Thay đổi: ' . $data->reason . ' -> ' . $value;
                $this->storeHistory($id, $title, $contentHistory);
                $data->reason = $value;
                $data->edit = 1;
                $data->save();
            }

            if ($code == 'qty') {
                $detail = AdminWarehouseTransferDetail::find($id);
                $title = 'Thay đổi số lượng SP: ' . $detail->product_name;
                $contentHistory = 'Thay đổi: ' . $detail->qty . ' -> ' . $value;
                $this->storeHistory($detail->warehouse_transfer_id, $title, $contentHistory);
                $detail->qty = $value;
                $detail->save();
            }

            if ($code == 'comment') {
                $detail = AdminWarehouseTransferDetail::find($id);
                $title = 'Thay đổi ghi chú SP: ' . $detail->product_name;
                $contentHistory = 'Thay đổi: ' . ($detail->comment != '' ? $detail->comment : 'Trống') . ' -> ' . $value;
                $this->storeHistory($detail->warehouse_transfer_id, $title, $contentHistory);
                $detail->comment = $value;
                $detail->save();
            }
            DB::commit();
            return response()->json([
                'error' => 0,
                'msg' => sc_language_render('action.update_success')
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            dd($e);
            return response()->json([
                'error' => 1,
                'msg' => $e->getMessage()
            ]);
        }
    }

    /**
     * Xóa chi tiết từng sản phẩm
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function deleteDetail()
    {
        $detail_id = request('detail_id');
        try {
            DB::beginTransaction();
            $detail = AdminWarehouseTransferDetail::find($detail_id);
            if ($detail) {
                $title = 'Xóa sản phẩm';
                $contentHistory = 'Xóa SP: ' . $detail->product_name;
                $this->storeHistory($detail->warehouse_transfer_id, $title, $contentHistory);
                $detail->delete();
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
    public function delete()
    {
        $ids = request('ids');
        $arrID = explode(',', $ids);
        try {
            DB::beginTransaction();
            AdminWarehouseTransfer::destroy($arrID);
            DB::commit();
            return response()->json(['error' => 0, 'msg' => sc_language_render('action.update_success')]);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error($e);
            return response()->json(['error' => 1, 'msg' => 'Xóa đơn nhập hàng lỗi!']);
        }
    }

    /**
     * Thêm sản phẩm mới vào đơn nhập hàng.
     * @return \Illuminate\Http\JsonResponse
     */
    public function addItemProductDetail()
    {
        $productIds = request('product_id') ?? [];
        $qty = request('qty') ?? [];
        $comment = request('comment') ?? [];
        $id = request('warehouse_transfer_id');
        $items = [];
        $productList = [];
        try {
            DB::beginTransaction();
            foreach ($productIds as $key => $product_id) {
                $product = ShopProduct::with('unit')->where('id', $product_id)->first();
                if (!$product) {
                    return response()->json(['error' => 1, 'msg' => sc_language_render('admin.data_not_found_detail', ['msg' => '#' . $id]), 'detail' => '']);
                }
                $productList[] = '- ' . $product->name;
                $qtyItem = $product->unit->type == 1 ? ceil($qty[$key]) : $qty[$key];
                $items[] = array(
                    'id' => sc_uuid(),
                    'warehouse_transfer_id' => $id,
                    'product_id' => $product->id,
                    'product_code' => $product->sku,
                    'product_name' => $product->name,
                    'product_kind' => $product->kind,
                    'qty' => $qtyItem,
                    'unit_name' => $product->unit->name ?? '',
                    'comment' => $comment[$key] ?? '',
                );
            }
            AdminWarehouseTransferDetail::insert($items);
            $title = 'Thêm sản phẩm mới';
            $contentHistory = implode("<br>", $productList);
            $this->storeHistory($id, $title, $contentHistory);
            DB::commit();
            return response()->json(['error' => 0, 'msg' => sc_language_render('action.update_success')]);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error($e);
            return response()->json(['error' => 1, 'msg' => $e->getMessage()]);
        }
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function getDataImportOrder()
    {
        $keyword = \request('keyword');
        $data = AdminImport::where(function ($q) use ($keyword) {
            $q->where('id_name', 'like', '%' . $keyword . '%');
            $q->orWhere('supplier_name', 'like', '%' . $keyword . '%');
        })->select('id', 'id_name', 'supplier_name', 'total')->limit(10)->get();
        if ($data->isEmpty()){
            return response()->json(['error' => 1, 'msg' => 'Dữ liệu trống']);
        }
        return response()->json([
            'error' => 0,
            'orderImport' => $data,
        ]);
    }

    public function getDataImportOrderDetail()
    {
        $import_id = \request('import_id');
        if ($import_id != '') {
            $import_id = explode(',', $import_id);
        }
        $details = AdminImportDetail::whereIn('import_id', $import_id)->get();

        $arrImportId = $details->map->only(['import_id', 'import_id_name'])->values()->unique();

        if ($details->isEmpty()) {
            return response()->json([
                'error' => 1,
                'msg' => 'Lỗi ko lấy được sản phẩm chi tiết đơn hàng nhập!',
            ]);
        }

        return response()->json([
            'error' => 0,
            'importId' => $arrImportId,
            'orderImportDetail' => $details,
        ]);

    }

    /**
     * Lưu lịch sử chỉnh sửa.
     * @param $id
     * @param $title
     * @param $content
     */
    private function storeHistory($id, $title, $content)
    {
        AdminWarehouseTransferHistory::create([
            'warehouse_transfer_id' => $id,
            'title' => $title,
            'admin_id' => Admin::user()->id,
            'user_name' => Admin::user()->name,
            'content' => $content,
        ]);

    }
    # Lưu thông tin Report nhập xuất tồn!
    private function storeReportProductStock($data)
    {
        $dataInsert = [];
        $now = now();
        DB::beginTransaction();
        try {
            foreach ($data->details as $detail) {
                # Sản phẩm nhập vào
                $productWarehouseImport = AdminWarehouseProduct::where('warehouse_id', $data->warehouse_id_from)->where('product_id', $detail->product_id)->first();
                if ($productWarehouseImport) {
                    $qtyStockImport = $productWarehouseImport->qty + $detail->qty;
                    $productWarehouseImport->qty = $productWarehouseImport->qty + $detail->qty;
                    $productWarehouseImport->latest_import_qty = $detail->qty;
                    $productWarehouseImport->save();
                } else {
                    $qtyStockImport = $detail->qty;
                    AdminWarehouseProduct::create([
                        'warehouse_id' => $data->warehouse_id_from,
                        'product_id' => $detail->product_id,
                        'qty' => $detail->qty,
                        'latest_import_qty' => $detail->qty,
                    ]);
                }
                $reportProductStockImport = AdminReportWarehouseProductStock::whereDate('date_action', $now)->where('warehouse_id', $data->warehouse_id_from)->where('product_id', $detail->product_id)->first();
                if ($reportProductStockImport) {
                    $reportProductStockImport->qty_import = $reportProductStockImport->qty_import + $detail->qty;
                    $reportProductStockImport->qty_stock = $qtyStockImport;
                    $reportProductStockImport->save();
                } else {
                    $dataInsert[] = [
                        'warehouse_id' =>  $data->warehouse_id_from,
                        'warehouse_name' =>  $data->warehouse_name_from,
                        'product_id' =>  $detail->product_id,
                        'product_code' =>  $detail->product_code,
                        'product_name' =>  $detail->product_name,
                        'product_kind' =>  $detail->product_kind ?? 0,
                        'qty_import' =>  $qtyStockImport,
                        'qty_export' =>  0,
                        'qty_stock' =>  $qtyStockImport,
                        'date_action' => $now,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }

                # Sản phẩm xuất đi
                $productWarehouseExport = AdminWarehouseProduct::where('warehouse_id', $data->warehouse_id_to)->where('product_id', $detail->product_id)->first();
                if ($productWarehouseExport) {
                    $qtyStockExport = $productWarehouseImport->qty - $detail->qty;
                    $productWarehouseExport->qty = $productWarehouseImport->qty - $detail->qty;
                    $productWarehouseExport->latest_import_qty = $detail->qty;
                    $productWarehouseExport->save();
                } else {
                    $qtyStockExport = 0 - $detail->qty;
                    AdminWarehouseProduct::create([
                        'warehouse_id' => $data->warehouse_id_from,
                        'product_id' => $detail->product_id,
                        'qty' => $qtyStockExport,
                        'latest_import_qty' => 0,
                    ]);
                }
                $reportProductStockExport = AdminReportWarehouseProductStock::whereDate('date_action', now())->where('warehouse_id', $data->warehouse_id_to)->where('product_id', $detail->product_id)->first();
                if ($reportProductStockExport) {
                    $reportProductStockImport->qty_export = $reportProductStockImport->qty_export - $detail->qty;
                    $reportProductStockImport->qty_stock = $qtyStockExport;
                    $reportProductStockImport->save();
                } else {
                    $dataInsert[] = [
                        'warehouse_id' =>  $data->warehouse_id_from,
                        'warehouse_name' =>  $data->warehouse_name_from,
                        'product_id' =>  $detail->product_id,
                        'product_code' =>  $detail->product_code,
                        'product_name' =>  $detail->product_name,
                        'product_kind' =>  $detail->product_kind ?? 0,
                        'qty_import' =>  0,
                        'qty_export' =>  $qtyStockExport,
                        'qty_stock' =>  $qtyStockExport,
                        'date_action' => $now,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }
            }

            if (!empty($dataInsert)) {
                AdminReportWarehouseProductStock::insert($dataInsert);
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
    private function checkQtyStock($product_id, $warehouse_id, $qty, $typeCalculate, $type)
    {
        $dataProductWarehouse = AdminWarehouseProduct::where('product_id', $product_id)->where('warehouse_id', $warehouse_id)->first();
        if ($dataProductWarehouse) {
            if ($typeCalculate == 'add') {
                $dataProductWarehouse->qty = $dataProductWarehouse->qty + $qty;
            } else {
                $dataProductWarehouse->qty = $dataProductWarehouse->qty - $qty;
            }
            if ($type == 'import') {
                $dataProductWarehouse->latest_import_qty = $qty;
            }
            $dataProductWarehouse->save();
            $qtyStock = $dataProductWarehouse->qty;
        } else {
            if ($typeCalculate == 'add') {
                $qtyStock = $qty;
            } else {
                $qtyStock = 0 - $qty;
            }

            AdminWarehouseProduct::create([
                'product_id' => $product_id,
                'warehouse_id' => $warehouse_id,
                'qty' => $qtyStock,
                'latest_import_qty' => $type == 'import' ? $qty : 0,
            ]);
        }
        return $qtyStock;
    }

    /**
     * @param $warehouse_id
     * @param $warehouse_name
     * @param $detail
     * @param $qty
     * @param $qtyStock
     * @param $typeCalculate
     * @param $typeOrder
     * @return bool
     */
    private function checkReportProductStock($warehouse_id, $warehouse_name, $detail, $qty, $qtyStock, $typeCalculate, $typeOrder)
    {
        $now = now();
        $reportProductStockImport = AdminReportWarehouseProductStock::whereDate('date_action', $now)
            ->where('warehouse_id', $warehouse_id)
            ->where('product_id', $detail->product_id)->first();
        if ($reportProductStockImport) {
            if ($typeCalculate == 'add') {
                if ($typeOrder == 'import') {
                    $reportProductStockImport->qty_import = $reportProductStockImport->qty_import + $qty;
                } else {
                    $reportProductStockImport->qty_export = $reportProductStockImport->qty_export + $qty;
                }
            } else {
                if ($typeOrder == 'import') {
                    $reportProductStockImport->qty_import = $reportProductStockImport->qty_import - $qty;
                } else {
                    $reportProductStockImport->qty_export = $reportProductStockImport->qty_export - $qty;
                }
            }
            $reportProductStockImport->qty_stock = $qtyStock;
            $reportProductStockImport->save();
        } else {
            $qtyImport = $typeOrder == 'import' ? $qty : 0;
            $qtyExport = $typeOrder == 'import' ? 0 : $qty;
            $dataInsert = [
                'id' => sc_uuid(),
                'warehouse_id' =>  $warehouse_id,
                'warehouse_name' =>  $warehouse_name,
                'product_id' =>  $detail->product_id,
                'product_code' =>  $detail->product_code,
                'product_name' =>  $detail->product_name,
                'product_kind' =>  $detail->product_kind ?? 0,
                'qty_import' =>  $qtyImport,
                'qty_export' =>  $qtyExport,
                'qty_stock' =>  $qtyStock,
                'date_action' => $now,
                'created_at' => $now,
                'updated_at' => $now,
            ];
            AdminReportWarehouseProductStock::create($dataInsert);
        }

        return true;
    }

    /**
     * @param $transfer
     * @param $detail
     * @param $qtyStock
     * @param $detail_id
     * @param $type
     * @return array
     */
    private function addArrWarehouseCard($transfer, $detail, $qtyStock, $type)
    {
        $now = Carbon::now();
        return $data = [
            'id' => sc_uuid(),
            'order_id' => $transfer->id ?? '',
            'order_id_name' => $transfer->id_name ?? '',
            'product_id' => $detail->product_id ?? '',
            'product_name' =>  $detail->product_name?? '',
            'product_code' => $detail->product_code ?? '',
            'explain' => 'Phiếu chuyển hàng',
            'qty_export' => $type == 'import' ? $detail->qty : 0,
            'qty_import' => $type == 'import' ? 0 : $detail->qty,
            'qty_stock' => $qtyStock ?? 0,
            'object_name' => $transfer->customer_name ?? '',
            'customer_code' => $transfer->customer_code ?? '',
            'bill_date' => $now,
            'warehouse_id' => $type == 'import' ? $transfer->warehouse_id_to : $transfer->warehouse_id_form,
            'warehouse_name' => $type == 'import' ? $transfer->warehouse_name_to : $transfer->warehouse_name_form,
            'type_order' => 2,
            'detail_id' => $detail->id,
            'created_at' => $now,
            'updated_at' => $now
        ];
    }
}