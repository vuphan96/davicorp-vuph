<?php

namespace App\Admin\Controllers;

use App\Admin\Models\AdminCustomer;
use App\Admin\Models\AdminEInvoice;
use App\Admin\Models\AdminOrder;
use App\Admin\Models\AdminProduct;
use App\Exports\Einvoice\AdminDetermineVolumeExport;
use App\Exports\Einvoice\AdminEInvoiceReportAcceptanceExport;
use App\Exports\Einvoice\AdminEInvoiceReportPaymentOfferExport;
use App\Exports\Einvoice\AdminIntroDavicorpTemplateExport;
use App\Exports\Einvoice\AdminMultipleSheetSalesInvoiceDetailVirtualOrder;
use App\Exports\Einvoice\AdminMultipleSheetSalesInvoiceListVirtualOrder;
use App\Front\Models\ShopCustomer;
use App\Front\Models\ShopDepartment;
use App\Front\Models\ShopEInvoice;
use App\Front\Models\ShopEInvoiceDetail;
use App\Front\Models\ShopEInvoiceHistory;
use App\Front\Models\ShopGenId;
use App\Front\Models\ShopOrder;
use App\Front\Models\ShopProduct;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Maatwebsite\Excel\Facades\Excel;
use SCart\Core\Admin\Controllers\RootAdminController;
use Illuminate\Support\Facades\Log;
use Dompdf\Dompdf;
use Illuminate\Database\Eloquent\Collection;
use function Symfony\Component\DomCrawler\all;
use App\Imports\EinvoiceImport;
use function Symfony\Component\HttpFoundation\Session\Storage\Handler\commit;

class AdminEInvoiceController extends RootAdminController
{
    public $customer_kind;
    public function __construct()
    {
        parent::__construct();
        $this->customer_kind = ShopEInvoice::$INVOICE_CUSTOMER;
    }

    /**
     * Display list invoice.
     */
    public function index()
    {
        $data = [
            'title' => sc_language_render('admin.invoice.list'),
            'subTitle' => '',
            'icon' => 'fa fa-indent',
            'urlDeleteItem' => sc_route_admin('admin_order.delete'),
            'urlCombineOrder' => sc_route_admin('admin_order.merge'),
            'removeList' => 1, // 1 - Enable function delete list item
            'buttonRefresh' => 1, // 1 - Enable button refresh
            'buttonSort' => 1, // 1 - Enable button sort
            'css' => '',
            'js' => '',
            'is_orderlist' => 1
        ];

        //Process add content
        $data['menuRight'] = sc_config_group('menuRight', \Request::route()->getName());
        $data['menuLeft'] = sc_config_group('menuLeft', \Request::route()->getName());
        $data['topMenuRight'] = sc_config_group('topMenuRight', \Request::route()->getName());
        $data['topMenuLeft'] = sc_config_group('topMenuLeft', \Request::route()->getName());
        $data['blockBottom'] = sc_config_group('blockBottom', \Request::route()->getName());
        //List th
        //Customize collumn size and align
        $cssTd = [
            'id' => 'width: 110px',
            'customer_name' => 'width: auto; max-width: 240px',
            'object' => 'width: 128px',
            'created_at' => 'width: 168px',
            'delivery_time' => 'width: 168px',
            'total' => 'text-align: right; width: 200px',
            'status' => 'text-align: center; width: 150px',
            'edited' => 'text-align: center; width: 72px',
            'action' => 'text-align: center; width: 400px',
        ];
        $data['cssTd'] = $cssTd;

        //Sort input data
        $arrSort = [
            'created_at__desc' => 'Ngày đặt hàng giảm dần',
            'created_at__asc' => 'Ngày đặt hàng tăng dần',
            'invoice_date__desc' => 'Ngày giao hàng giảm dần',
            'invoice_date__asc' => 'Ngày giao hàng tăng dần',
            'total_amount__desc' => 'Tổng tiền giảm dần',
            'total_amount__asc' => 'Tổng tiền tăng dần',
        ];
        //Search
        $dataSearch = [
            'from_to' => sc_clean(request('from_to') ?? ''),
            'end_to' => sc_clean(request('end_to') ?? ''),
            'sort_order' => sc_clean(request('sort_order') ?? 'id_desc'),
            'arrSort' => $arrSort,
            'invoice_status' => sc_clean(request('invoice_status') ?? ''),
            'customer_kind' => sc_clean(request('customer_kind') ?? ''),
            'customer_name' => sc_clean(request('customer_name') ?? ''),
            'code' => sc_clean(request('code') ?? ''),
            'limit' => sc_clean(request('limit') ?? ''),
        ];

        $dataTmp = (new AdminEInvoice)->getListAllEInvoiceAdmin($dataSearch);
        session()->put('nameUrlEinvoiceIndex', URL::full());
        $invoiceStatus = ShopEInvoice::$INVOICE_STATUS;
        $data['status'] = $invoiceStatus;

        $data['dataInvoices'] = $dataTmp;

        $data['pagination'] = $dataTmp->appends(request()->except(['_token', '_pjax']))->links($this->templatePathAdmin . 'component.pagination');
        $optionPaginate = '';
        $arrayPaginate = [
            0 => 15,
            1 => 50,
            2 => 100,
            3 => 200,
        ];
        foreach ($arrayPaginate as $key => $value) {
            $optionPaginate .= '<option  ' . (($dataSearch['limit'] == $value) ? "selected" : "") . ' value="' . $value . '">' . $value . '</option>';
        }
        $data['resultItems'] = '
                            <div>
                                <div class="form-group" style="display: inline-flex">
                                    <label style="padding-right: 10px; font-weight: normal">Hiển thị</label>
                                    <select name="select_limit" style="width: 50px; margin-bottom: 8px" id="select_limit_paginate">
                                        ' . $optionPaginate . '
                                    </select>
                                    <div style="padding-left: 10px">Của '.$dataTmp->total().' kết quả </div>
                                </div>
                            </div>';
        //menuSort
        $optionSort = '';
        foreach ($arrSort as $key => $sort) {
            $optionSort .= '<option  ' . (($dataSearch['sort_order'] == $key) ? "selected" : "") . ' value="' . $key . '">' . $sort . '</option>';
        }
        $data['optionSort'] = $optionSort;
        $data['urlSort'] = sc_route_admin('admin_order.index', request()->except(['_token', '_pjax', 'sort_order']));
        //=menuSort

        //menuSearch
        $optionStatus = '';
        foreach ($invoiceStatus as $key => $status) {
            $optionStatus .= '<option  ' . (($dataSearch['invoice_status'] == $key) ? "selected" : "") . ' value="' . $key . '">' . $status . '</option>';
        }

        $customer_kind = $this->customer_kind;
        $data['customer_kind'] = $customer_kind;
        $optionCustomerKind = '';
        foreach ($customer_kind as $key => $kind) {
            $optionCustomerKind .= '<option  ' . (($dataSearch['customer_kind'] == $key) ? "selected" : "") . ' value="' . $key . '">' . $kind . '</option>';
        }
        $data['topMenuRight'][] = '
                <form action="' . sc_route_admin('admin.einvoice.index') . '" id="button_search" autocomplete="off">
                <input type="hidden" name="limit" value="'. ($dataSearch['limit'] ?? '') .'" id="limit_paginate">
                    <div class="input-group float-right">
                        <div class="col-lg-2 col-md-2 col-sm-12">
                            <div class="form-group">
                                <label>' . sc_language_render('action.from') . ':</label>
                                <div class="input-group">
                                <input style="text-align: center" type="text" name="from_to" id="from_to" class="form-control input-sm date_time rounded-0"  placeholder="Chọn ngày" value="'. $dataSearch["from_to"] .'"/> 
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-12">
                            <div class="form-group">
                                <label>' . sc_language_render('action.to') . ':</label>
                                <div class="input-group">
                                <input style="text-align: center" type="text" name="end_to" id="end_to" class="form-control input-sm date_time rounded-0"  placeholder="Chọn ngày" value="'. $dataSearch["end_to"] .'"/> 
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-12">
                            <div class="form-group">
                                <label>Loại khách hàng:</label>
                                <div class="input-group">
                                    <select class="form-control rounded-0" name="customer_kind">
                                    <option value="">Tất cả</option>
                                    ' . $optionCustomerKind . '
                                    </select>
                                </div>
                            </div>
                        </div>
                                
                        <div class="col-lg-2 col-md-2 col-sm-12">
                            <div class="form-group">
                                <label>' . sc_language_render('order.admin.status') . ':</label>
                                <div class="input-group">
                                <select class="form-control rounded-0" name="invoice_status">
                                <option value="">Tất cả trạng thái</option>
                                ' . $optionStatus . '
                                </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-12">
                            <div class="form-group">
                                <label>' . sc_language_render('order.admin.search_name') . ':</label>
                                <div class="input-group">
                                    <input type="text" name="customer_name" class="form-control rounded-0 float-right" placeholder="' . sc_language_render('order.admin.search_name') . '" value="' . $dataSearch['customer_name'] . '">
                                </div>
                            </div>
                        </div>  
                        <div class="col-lg-2 col-md-2 col-sm-12">
                            <div class="form-group">
                                <label>Mã đơn hàng</label>
                                <div class="input-group">
                                    <input type="text" name="code" class="form-control rounded-0 float-right" placeholder="Mã đơn hàng" value="' . $dataSearch['code'] . '">
                                    <div class="input-group-append">
                                        <button type="submit" class="btn btn-primary  btn-flat"><i class="fas fa-search"></i></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>';
        //=menuSearch
        return view($this->templatePathAdmin . 'screen.e_invoice.index')
            ->with($data);
    }

    /**
     * Xử lý code màn hình hiển thị chi tiết hóa đơn điện tử.
     * @param $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     */
    public function detailEInvoice($id)
    {
        $einvoice = AdminEInvoice::getEinvoices($id);

        if (!$einvoice) {
            return redirect()->route('admin.einvoice.index')->with(['error' => 'Không tìm thấy hóa đơn này!']);
        }

        $customer_id = AdminCustomer::where('customer_code', $einvoice->customer_code)->get()->pluck('id')->first();
        $histories = ShopEInvoiceHistory::where('einv_id', $id)->orderBy('created_at', 'desc')->get();
        $products = (new AdminOrder)->getProductByCustomerPriceBoard(null, $customer_id, null);

        return view($this->templatePathAdmin . 'screen.e_invoice.detail')->with(
            [
                "title" => sc_language_render('einvoice.detail'),
                "subTitle" => '',
                'icon' => 'fa fa-file-text-o',
                "einvoice" => $einvoice,
                "products" => $products,
                'histories' => $histories,
                'details' => $einvoice->details,
                'status' => ShopEInvoice::$INVOICE_STATUS,
                'customer' => ShopEInvoice::$INVOICE_CUSTOMER,
                'historyStatus' => ShopEInvoiceHistory::$INVOICE_HISTORY_STATUS,
            ]
        );

    }

    /**
     * Update chi tiết hóa đơn điện tử.
     * Update ở đơn cha không lam thay đổi ở đơn con.
     * Hóa đơn là đơn con không thể update.
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateEinvoice() {
        $data = request()->all();
        $arrTax = [0, 5, 8, 10];
        DB::beginTransaction();
        try {
            $einvoiceDetail = ShopEInvoiceDetail::find($data['pk']);
            $e_invoice = AdminEInvoice::find($einvoiceDetail->einv_id);
            if (!$einvoiceDetail) {
                return response()->json(['error' => 1, 'msg' => sc_language_render('admin.data_not_found_detail', ['msg' => 'detail#' . $data['pk']]), 'detail' => '']);
            }

            if ($e_invoice->del_flag == 1) {
                return response()->json(['error' => 1, 'msg' => "Hóa đơn đã bị gộp. Không thể chỉnh sửa!"]);
            }

            if ($e_invoice->process_status == 4 ) {
                return response()->json(['error' => 1, 'msg' => 'Hóa đơn đã xuất, không thể chỉnh sửa!']);
            }

            if ($e_invoice->process_status == 1) {
                return response()->json(['error' => 1, 'msg' => 'Hóa đơn chờ xuất, không thể chỉnh sửa. Vui lòng hủy đồng bộ trước khi thực hiện!']);
            }

            if ($e_invoice->process_status == 2) {
                return response()->json(['error' => 1, 'msg' => 'Hóa đơn đang xuất, không thể xóa chỉnh sửa!']);
            }

            if (!$e_invoice) {
                return response()->json(['error' => 1, 'msg' => sc_language_render('admin.data_not_found_detail', ['msg' => 'einvoice#' . $einv_id]), 'detail' => '']);
            }

            if ($data['name'] == 'qty') {
                if ($data['value'] <= 0 || !is_numeric($data['value'])) {
                    return response()->json(['error' => 1, 'msg' => "Số lượng không hợp lệ!"]);
                }
                $einvoiceDetail->qty = $data['value'];
                $einvoiceDetail->tax_amount = ($einvoiceDetail->price * $data['value']) - ($einvoiceDetail->pretax_price * $data['value']);
                if (!$einvoiceDetail->save()) {
                    throw new \Exception('Lỗi cập nhập chi tiết hóa đơn, vui lòng kiểm tra lại!');
                }
            }
            if ($data['name'] == 'price') {
                if ($data['value'] < 0 || !is_numeric($data['value'])) {
                    return response()->json(['error' => 1, 'msg' => "Giá tiền không hợp lệ!"]);
                }
                $pretax_price = ($data['value']) / ( 1 + $einvoiceDetail->tax_no/100);
                $einvoiceDetail->price = $data['value'];
                $einvoiceDetail->pretax_price = $pretax_price;
                $einvoiceDetail->tax_amount = ($einvoiceDetail->qty * $data['value']) - ($pretax_price * $einvoiceDetail->qty);
                if (!$einvoiceDetail->save()) {
                    throw new \Exception('Lỗi cập nhập chi tiết hóa đơn, vui lòng kiểm tra lại!');
                }
            }
            if ($data['name'] == 'tax_no') {
                if ($data['value'] < 0 || !is_numeric($data['value'])) {
                    return response()->json(['error' => 1, 'msg' => "Thuế suất không hợp lệ!"]);
                }
                if (!in_array($data['value'], $arrTax)) {
                    return response()->json(['error' => 1, 'msg' => "Thuế suất qui định là: 0 5 8 10!"]);
                }
                $pretax_price = ($einvoiceDetail->price) / ( 1 + $data['value']/100);
                $einvoiceDetail->tax_no = $data['value'];
                $einvoiceDetail->pretax_price = $pretax_price;
                $einvoiceDetail->tax_amount = ($einvoiceDetail->qty * $einvoiceDetail->price) - ($pretax_price * $einvoiceDetail->qty);
                if (!$einvoiceDetail->save()) {
                    throw new \Exception('Lỗi cập nhập chi tiết hóa đơn, vui lòng kiểm tra lại!');
                }
            }
            AdminEInvoice::updateTotalAmount($einvoiceDetail->einv_id);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error($e);
            return response()->json([
                'error' => 1,
                'msg' => sc_language_render('action.update_fail')
            ]);
        }
        DB::commit();

        return response()->json([
            'error' => 0,
            'msg' => sc_language_render('action.update_success')
        ]);
    }

    /**
     * Thêm mới item.
     * Thêm mới ở đơn cha cập nhập lại giá tiền và không làm thay đổi ở chi tiết đơn con.
     * Hóa đơn là đơn con không thể thêm mới.
     * @return \Illuminate\Http\JsonResponse
     */
    public function createItemEinvoice() {
        $addProductIds = explode(',', request('products_id'));
        $add_price = request('add_price');
        $add_qty = request('add_qty');
        $tax_no = request('add_tax');
        $einv_id = request('einv_id');
        $arrTax = [0, 5, 8, 10];
        $e_invoice = AdminEInvoice::find($einv_id);

        if ($e_invoice->del_flag == 1) {
            return response()->json(['error' => 1, 'msg' => "Hóa đơn đã bị gộp. Không thể chỉnh sửa!"]);
        }

        if ($e_invoice->process_status == 4 ) {
            return response()->json(['error' => 1, 'msg' => 'Hóa đơn đã xuất, không thể chỉnh sửa!']);
        }

        if ($e_invoice->process_status == 1) {
            return response()->json(['error' => 1, 'msg' => 'Hóa đơn chờ xuất, không thể chỉnh sửa. Vui lòng hủy đồng bộ trước khi thực hiện!']);
        }

        if ($e_invoice->process_status == 2) {
            return response()->json(['error' => 1, 'msg' => 'Hóa đơn đang xuất, không thể xóa chỉnh sửa!']);
        }
        foreach ($addProductIds as $key => $id) {
            if ($id && $add_qty[$key]) {
                $product = AdminProduct::getProductAdmin($id);
                if (!$product) {
                    return response()->json(['error' => 1, 'msg' => sc_language_render('admin.data_not_found_detail', ['msg' => '#' . $id]), 'detail' => '']);
                }
                if (!in_array($tax_no[$key], $arrTax)) {
                    return response()->json(['error' => 1, 'msg' => "Thuế suất qui định là: 0 5 8 10!"]);
                }
                $items = array(
                    'einv_id' => $einv_id,
                    'product_code' => $product->sku,
                    'product_name' => $product->bill_name,
                    'unit' => $product->unit->name ?? '',
                    'qty' => $add_qty[$key],
                    'price' => $add_price[$key],
                    'tax_no' => $tax_no[$key],
                    'pretax_price' => $add_price[$key]/(1 + $tax_no[$key]/100),
                    'tax_amount' => ($add_price[$key] * $add_qty[$key]) - ($add_price[$key]/(1 + $tax_no[$key]/100) * $add_qty[$key]),
                    'created_at' => sc_time_now(),
                );
                try {
                    ShopEInvoiceDetail::insert($items);
                    AdminEInvoice::updateTotalAmount($einv_id);
                } catch (\Throwable $e) {
                    return response()->json(['error' => 1, 'msg' => 'Error: ' . $e->getMessage()]);
                }
            }
        }

        return response()->json(['error' => 0, 'msg' => sc_language_render('action.update_success')]);
    }

    /**
     * Lấy thông tin sản phẩm.
     * @return \Illuminate\Http\JsonResponse
     */
    public function getInfoProduct()
    {
        $id = request('id');
        $customer_kind = request('customer_kind');

        $product = AdminProduct::getProductAdmin($id);
        if (!$product) {
            return response()->json(['error' => 1, 'msg' => sc_language_render('admin.data_not_found_detail', ['msg' => '#product:' . $id]), 'detail' => '']);
        }
        $arrayReturn['tax_no'] = $customer_kind == 1 ? $product->tax_company : ($customer_kind == 2 ? $product->tax_school : $product->tax_default);
        $arrayReturn['sku'] = $product->sku;
        $arrayReturn['unit'] = $product->unit->name ?? '';
        $arrayReturn['unit_type'] = $product->unit->type ?? 0;
        $arrayReturn['minimum_qty_norm'] = $product->minimum_qty_norm ?? 0;
        return response()->json($arrayReturn);
    }

    /**
     * Xóa chi tiết trong hóa đơn.
     * Xóa chi tiết ở đơn cha cập nhập lại giá tiền đơn cha.
     * Xóa chi tiết ở đơn cha => không làm thay đổi đơn con.
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteItemDetail()
    {
        try {
            $data = request()->all();
            $pId = $data['pId'] ?? "";
            $itemDetail = ShopEInvoiceDetail::find($pId);
            if (!$itemDetail) {
                return response()->json(['error' => 1, 'msg' => sc_language_render('admin.data_not_found_detail', ['msg' => 'detail#' . $pId]), 'detail' => '']);
            }
            $einv_id = $itemDetail->einv_id;
            $e_invoice = AdminEInvoice::find($einv_id);

            if ($e_invoice->del_flag == 1) {
                return response()->json(['error' => 1, 'msg' => "Hóa đơn đã bị gộp. Không thể xóa!"]);
            }

            if ($e_invoice->process_status == 4 ) {
                return response()->json(['error' => 1, 'msg' => 'Hóa đơn đã xuất, không thể xóa!']);
            }

            if ($e_invoice->process_status == 1) {
                return response()->json(['error' => 1, 'msg' => 'Hóa đơn chờ xuất, không thể xóa. Vui lòng hủy đồng bộ trước khi thực hiện!']);
            }

            if ($e_invoice->process_status == 2) {
                return response()->json(['error' => 1, 'msg' => 'Hóa đơn đang xuất, không thể xóa xóa!']);
            }

            if (!$e_invoice) {
                return response()->json(['error' => 1, 'msg' => sc_language_render('admin.data_not_found_detail', ['msg' => 'einvoice#' . $einv_id]), 'detail' => '']);
            }

            $e_invoice->total_amount = $e_invoice->total_amount - $itemDetail->qty * $itemDetail->price;
            $e_invoice->save();
            $itemDetail->delete(); //Remove item from shop order detail

            return response()->json(['error' => 0, 'msg' => sc_language_render('action.update_success')]);
        } catch (\Throwable $e) {

            return response()->json(['error' => 1, 'msg' => 'Error: ' . $e->getMessage()]);
        }
    }

    /**
     * Xử lý chức năng gộp các hóa đơn điện tử.
     */
    public function combineEinvoice()
    {
        $ids = explode(',', request('ids'));

        $flagDelete = ShopEInvoice::whereIn('id', $ids)->where('del_flag', 1)->first();

        if ($flagDelete) {
            return response()->json(['error' => 1, 'msg' => sc_language_render('einvoice.combine.fail')]);
        }

        $einvoices = ShopEInvoice::whereIn('id', $ids)->orderBy('id', 'DESC')->get();
        /**
         * Tạo ra đơn mới với chi tiết là tổng đơn con.
         * Đơn hàng có order_id = null đơn gộp -> delete.
         * Đơn hàng có order_id != null đơn chi tiết sẽ giữ nguyên đưa vào đơn con.
         */
        DB::beginTransaction();
        try {
            $firstEinvoice = $einvoices->first();
            $id = ShopEInvoice::insertGetId(
                [
                    'id_name' => ShopGenId::genNextId('einvoice'),
                    'customer_code' => $firstEinvoice->customer_code ?? '',
                    'customer_name' => $firstEinvoice->customer_name,
                    'invoice_date' => now(),
                    'process_status' => 0,
                    'customer_address' => $firstEinvoice->customer_address ?? '',
                    'customer_kind' => $firstEinvoice->customer_kind,
                    'tax_code' => $firstEinvoice->tax_code,
                    'sync_system' => $firstEinvoice->sync_system,
                    'total_amount' => $einvoices->sum('total_amount'),
                ]
            );
            $details = ShopEInvoiceDetail::WhereIn('einv_id', $ids)->get();
            foreach ($details->groupBy(['product_code', 'price']) as $product_code => $product) {
                foreach ($product as $value) {
                    $insert = [
                        'einv_id' => $id,
                        'product_code' => $product_code,
                        'product_name' => $value->first()->product_name,
                        'unit' => $value->first()->unit,
                        'qty' => $value->sum('qty'),
                        'price' => $value->first()->price,
                        'tax_no' => $value->first()->tax_no,
                        'pretax_price' => $value->first()->pretax_price,
                        'tax_amount' => $value->sum('tax_amount'),
                    ];
                    ShopEInvoiceDetail::insert($insert);
                }
            }
            ShopEInvoice::whereIn('parent_id', $ids)->update(['parent_id' => $id]);
            foreach ($einvoices as $key => $item) {
                if ($item->order_id != '') {
                    $item->parent_id = $id;
                    $item->del_flag = 1;
                    $item->save();
                }
                if ($item->order_id == '') {
                    $item->delete();
                }
            }

            DB::commit();
            return response()->json(['error' => 0, 'msg' => sc_language_render('einvoice.combined')]);
        } catch (\Exception $e) {
            Log::error($e);
            DB::rollback();
            return response()->json(['error' => 1, 'msg' => sc_language_render('einvoice.combine.fail')]);
        }
    }

    /**
     * Xử lý gởi robot
     */
    public function sendRobot()
    {
        $ids = explode(',', request('ids'));
        $type_send = request('type_send');
        $hour_start = request('hour_start');
        $minute_start = request('minute_start');
        $date_start = request('date_start');
        $dateStart = Carbon::createFromFormat('d/m/Y H:i', $date_start . ' ' . $hour_start . ":" . $minute_start);
        $now = now();

        DB::beginTransaction();
        try {
            foreach ($ids as $id) {
                $einvoice = ShopEInvoice::find($id);
                if ($type_send == 0) {
                    $einvoice->mode_run = 0;
                    $einvoice->process_status = 1;
                    $einvoice->priority = 0;
                    $einvoice->plan_start_date = $now;
                    $einvoice->delivery_date = now();
                    $einvoice->save();
                } else if ($type_send == 1) {
                    $einvoice->mode_run = 0;
                    $einvoice->process_status = 1;
                    $einvoice->priority = 0;
                    $einvoice->plan_start_date = $now;
                    $einvoice->delivery_date = $now;
                    $einvoice->plan_sign_date = $now;
                    $einvoice->save();
                } else if ($type_send == 2) {
                    $einvoice->mode_run = 1;
                    $einvoice->process_status = 1;
                    $einvoice->priority = 0;
                    $einvoice->plan_start_date = $now;
                    $einvoice->delivery_date = $now;
                    $einvoice->plan_sign_date = $dateStart;
                    $einvoice->save();
                }

            }
            DB::commit();
            return redirect()->back();
        } catch (\Exception $e) {
            Log::error($e);
            DB::rollback();
            return response()->json(['error' => 1, 'msg' => 'Gửi robot thất bại. Vui lòng thử lại!']);
        }
    }

    /**
     * Xử lý hủy đồng bộ.
     * @return \Illuminate\Http\JsonResponse
     */
    public function cancelSync()
    {
        $ids = explode(',', request('ids'));
        foreach ($ids as $id) {
            $einvoice = ShopEInvoice::find($id);
            $einvoice->process_status = 0;
            $einvoice->sign_status = 0;
            $einvoice->plan_start_date = null;
            $einvoice->plan_sign_date = null;
            $einvoice->save();
        }

        return response()->json(['error' => 0, 'msg' => sc_language_render('einvoice.cancel_sync_success')]);
    }

    /**
     * Preview pdf báo cáo bảng kê phần ảo (danh sách hóa đơn điện tử).
     */
    public function printSalesInvoiceListVirtualOrder()
    {
        $id_orders = explode(',', request('ids'));
        $order_num = count(array_unique($id_orders));
        
        $dataArr = AdminEInvoice::getVirtualOrderCombineData($id_orders);
        $customer_code = data_get($dataArr, '0.customer_code');
        $customer = AdminCustomer::with('department')->where('customer_code', $customer_code)->first();
        if(!$customer) {
            return redirect()->back()->with('error', 'Khách hàng không tồn tại');
        }
        $department_name = $customer->department->name;
        $department_address =$customer->department->address;
        $dataOrderDetails[] = [
            'data' => $dataArr,
            'order_num' => $order_num,
            'department_name'=>$department_name,
            'department_address'=>$department_address,
        ];

        $html = view($this->templatePathAdmin . 'screen.e_invoice.print_pdf.einvoice_list_virtual_report')
        ->with(['datas' => $dataOrderDetails])->render();
        $html = mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8');
        $domPdf = new Dompdf();
        $domPdf->getOptions()->setChroot(public_path());
        $domPdf->loadHtml($html, 'UTF-8');
        $domPdf->setPaper('A4', 'portrait');
        $domPdf->render();

        return $domPdf->stream('bangkehoadonbanhangphanao-' . now() . '.pdf', ["Attachment" => false]);
    }

    /**
     * Xử lý xuất báo cáo bảng kê đơn phần ảo (danh sách hóa đơn điện tử).
     */
    public function exportSalesInvoiceListVirtualOrder()
    {
        $dataSearch = [
            'from_to' => sc_clean(request('from_to_time') ?? ''),
            'end_to' => sc_clean(request('end_to_time') ?? ''),
        ];

        $id_orders = explode(',', request('ids'));
        $order_num = count(array_unique($id_orders));
        $dataArr = AdminEInvoice::getVirtualOrderCombineData($id_orders)->groupBy('customer_code');
        $customer_code = $dataArr->keys()->first();
        $customer = AdminCustomer::where('customer_code', $customer_code)->first();
        if(!$customer) {
            return redirect()->back()->with('error', 'Khách hàng không tồn tại');
        }
        $department_id = $customer->department_id;
        $department = ShopDepartment::find($department_id);
        return Excel::download(new AdminMultipleSheetSalesInvoiceListVirtualOrder($dataSearch, $dataArr, $order_num, $department), 'BẢNG KÊ HÓA ĐƠN BÁN HÀNG - BÊN ẢO ' . Carbon::now() . '.xlsx');
    }

    /**
     * Preview pdf trước khi in báo cáo bảng kê chi tiết đơn phần ảo.
     */
    public function printSalesInvoiceDetailVirtualOrder()
    {
        $order_id = explode(',',request('order_id'));
        $orderData = (new AdminEInvoice())->getVirtualOrderCombineData($order_id);
        $customer_code = data_get($orderData, '0.customer_code');
        $customer = AdminCustomer::with('department')->where('customer_code', $customer_code)->first();
        $department_name = '';
        $department_address = '';
        if(!$customer) {
            return redirect()->back()->with('error', 'Khách hàng không tồn tại');
        }
        $department_name = $customer->department->name;
        $department_address =$customer->department->address;
        $dataOrderDetails[] = [
            'data' => $orderData,
            'department_name'=>$department_name,
            'department_address'=>$department_address,
        ];
        $html = view($this->templatePathAdmin . 'screen.e_invoice.print_pdf.einvoice_virtual_detail_report')
        ->with(['datas' => $dataOrderDetails])->render();
        $html = mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8');
        $domPdf = new Dompdf();
        $domPdf->getOptions()->setChroot(public_path());
        $domPdf->loadHtml($html, 'UTF-8');
        $domPdf->setPaper('A4', 'portrait');
        $domPdf->render();

        return $domPdf->stream('bangkehoadonbanhangphanao-' . now() . '.pdf', ["Attachment" => false]);
    }

    /**
     * Xử lý xuất báo cáo bảng kê chi tiết đơn phần ảo.
     */
    public function exportSalesInvoiceDetailVirtualOrder()
    {
        $dataSearch = [
            'from_to' => sc_clean(request('from_to_time') ?? ''),
            'end_to' => sc_clean(request('end_to_time') ?? ''),
        ];

        $order_id = explode(',',request('order_id'));
        $data = (new AdminEInvoice())->getVirtualOrderCombineData($order_id)->groupBy('customer_code');
        $customer_code = $data->keys()->first();
        $customer = AdminCustomer::where('customer_code', $customer_code)->first();
        if(!$customer) {
            return redirect()->back()->with('error', 'Khách hàng không tồn tại');
        }
        $department_id = $customer->department_id;
        $department = ShopDepartment::find($department_id);

        return Excel::download(new AdminMultipleSheetSalesInvoiceDetailVirtualOrder($dataSearch, $data, $department), 'BẢNG KÊ HÓA ĐƠN BÁN HÀNG - BÊN ẢO ' . Carbon::now() . '.xlsx');
    }

    /**
     * Handel delete E-Invoice .
     * @return mixed
     */
    public function multipleDeleteInvoice()
    {
        if (!request()->ajax()) {
            return response()->json(['error' => 1, 'msg' => sc_language_render('admin.method_not_allow')]);
        }

        $arrID = explode(',', request('ids'));
        ShopEInvoice::destroy($arrID);

        return response()->json(['error' => 0, 'msg' => sc_language_render('action.update_success')]);
    }

    /**
     * Lấy dữ liệu show Mẫu báo cáo biên bản nghiệm thu
     * @return mixed
     */
    public function acceptanceReport()
    {
        if (!request()->ajax()) {
            return response()->json(['error' => 1, 'msg' => sc_language_render('admin.method_not_allow')]);
        }

        $arrID = explode(',', request('id'));
        $objInvoices = AdminEInvoice::whereIn('id', $arrID)->get();
        $customer = ShopCustomer::with('department')->where('customer_code', $objInvoices->first()->customer_code)->first();
        if (!$customer) {
            return response()->json(['error' => 1, 'messages' => 'Dữ liệu khách hàng bị lỗi']);
        }
        $objInvoiceIds = $objInvoices->pluck('id')->toArray();
        $objInvoiceDetail = (new ShopEInvoiceDetail())->getEinvoiceDetail($objInvoiceIds);

        $data = [
            'customer' => $customer,
            'department' => ucwords(mb_strtolower($customer->department->name, 'utf-8')),
            'date_month' => date('m', strtotime($objInvoices->first()->invoice_date)),
            'date_year' => date('Y', strtotime($objInvoices->first()->invoice_date)),
            'einvoice' => $objInvoiceDetail,
        ];
        return response()->json($data);
    }

    /**
     * Xuất pdf hoặc excel Mẫu báo cáo biên bản nghiệm thu
     * @return \Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\BinaryFileResponse|void
     */
    public function acceptanceReportPrint()
    {
        $arrID = explode(',', request('id_invoice'));
        $attributes = request()->except('id_invoice', '_token');
        $keyPrint = request('key_print_acceptance');
        $objInvoices = AdminEInvoice::whereIn('id', $arrID)->get();
        $customer = ShopCustomer::where('customer_code', $objInvoices->first()->customer_code)->first();
        $objInvoiceIds = $objInvoices->pluck('id')->toArray();
        $objInvoiceDetail = (new ShopEInvoiceDetail())->getEinvoiceDetail($objInvoiceIds);

        $data = [
            'customer' => $customer,
            'einvoice' => $objInvoiceDetail,
        ];

        if (!count($objInvoiceDetail) > 0) {
            return redirect()->back()->with('error' , 'Không có dữ liệu');
        }

        // dd/mm/yyyy -> yyyy-mm-dd
        $attributes['einvoice_date'] = request('einvoice_date') ? Carbon::createFromFormat('d/m/Y', request('date'))->toDateString() : '';
        $attributes['date'] = request('date') ? Carbon::createFromFormat('d/m/Y', request('date'))->toDateString() : '';
        $attributes['date_contract'] = request('date_contract') ? Carbon::createFromFormat('d/m/Y', request('date_contract'))->toDateString() : '';
        $attributes['start_date_effective_contract'] = request('start_date_effective_contract') ? Carbon::createFromFormat('d/m/Y', request('start_date_effective_contract'))->toDateString() : '';
        $attributes['end_date_effective_contract'] = request('end_date_effective_contract') ? Carbon::createFromFormat('d/m/Y', request('end_date_effective_contract'))->toDateString() : '';

        if ($keyPrint == 0) {
            $html = view($this->templatePathAdmin . 'screen.e_invoice.print_pdf.acceptance_report')
                ->with(['data' => $data, 'attributes' => $attributes])->render();

            $html = mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8');

            $domPdf = new Dompdf();
            $domPdf->getOptions()->setChroot(public_path());
            $domPdf->loadHtml($html, 'UTF-8');
            $domPdf->setPaper('A4', 'portrait');
            $domPdf->render();

            return $domPdf->stream('Biên bản nghiêm thu - ' . Carbon::now() . '.pdf', array("Attachment" => false));
        }

        return Excel::download(new AdminEInvoiceReportAcceptanceExport($data, $attributes), 'Biên bản nghiêm thu - ' . Carbon::now() . '.xlsx');
    }

    /**
     * Mẫu đề nghị thanh toán
     * @return mixed
     */
    public function paymentOfferReport()
    {
        if (!request()->ajax()) {
            return response()->json(['error' => 1, 'msg' => sc_language_render('admin.method_not_allow')]);
        }

        $arrID = explode(',', request('id'));
        $objInvoices = AdminEInvoice::whereIn('id', $arrID)->get();
        $customer = ShopCustomer::with('department')->where('customer_code', $objInvoices->first()->customer_code)->first();
        if (!$customer) {
            return response()->json(['error' => 1, 'messages' => 'Dữ liệu khách hàng bị lỗi']);
        }

        if ($customer->department_id == 1) {
            $codeCitad = '01201023';
            $departmentName = 'Công ty cổ phần Davicook Hà Nội';
        } elseif ($customer->department_id == 2) {
            $codeCitad = '01201023';
            $departmentName = 'Công ty cổ phần Davicorp Việt Nam';
        } elseif ($customer->department_id == 5) {
            $codeCitad = '01302003';
            $departmentName = 'Cửa hàng thực phẩm Davicorp';
        } else {
            $codeCitad = '01311007';
            $departmentName = 'Cửa hàng thực phẩm sạch Davicorp';
        }

        $data = [
            'customer' => $customer,
            'customer_name' => $objInvoices->first()->customer_name,
            'address' => $customer->address,
            'department_id' => $customer->department_id,
            'department_name' => $departmentName,
            'code_citad' => $codeCitad,
            'number_total_amount' => $objInvoices->sum('total_amount'),
            'text_total_amount' => convert_number_to_words($objInvoices->sum('total_amount')),
        ];

        return response()->json($data);
    }

    /**
     * Xuất pdf hoặc excel Mẫu báo cáo đề nghị thanh toán
     * @return \Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\BinaryFileResponse|void
     */
    public function paymentOfferReportPrint()
    {
        $arrID = explode(',', request('id_invoice'));
        $attributes = request()->except('id_invoice', '_token');
        $keyPrintPaymentOfferByCash = request('key_print_payment_by_cash');
        $keyPrintPaymentOfferByTransfer = request('key_print_payment_by_transfer');
        $objInvoices = AdminEInvoice::whereIn('id', $arrID)->get();
        $customer = ShopCustomer::where('customer_code', $objInvoices->first()->customer_code)->first();
        $keyDepartment = $customer->department_id;

        $data = [
            'customer' => $customer,
            'number_total_amount' => $objInvoices->sum('total_amount'),
            'text_total_amount' => convert_number_to_words($objInvoices->sum('total_amount')),
            'keyDepartment' => $keyDepartment
        ];

        if (!count($objInvoices) > 0) {
            return redirect()->back()->with('error' , 'Không có dữ liệu');
        }

        // dd/mm/yyyy -> yyyy-mm-dd
        $attributes['identification_date'] = request('identification_date') ? (Carbon::createFromFormat('d/m/Y', request('identification_date'))->toDateString()) : '';
        $attributes['date_create'] = request('date_create') ? Carbon::createFromFormat('d/m/Y', request('date_create'))->toDateString() : '';

        if ($keyPrintPaymentOfferByCash == 1 || $keyPrintPaymentOfferByTransfer == 1) {

            $pathViews = isset($keyPrintPaymentOfferByCash) ? 'payment_offer_by_cash_report' : 'payment_offer_by_transfer_report' ;
            $html = view($this->templatePathAdmin . 'screen.e_invoice.print_pdf.' . $pathViews)
                ->with(['data' => $data, 'attributes' => $attributes])->render();

            $html = mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8');

            $domPdf = new Dompdf();
            $domPdf->getOptions()->setChroot(public_path());
            $domPdf->loadHtml($html, 'UTF-8');
            $domPdf->setPaper('A4', 'portrait');
            $domPdf->render();

            return $domPdf->stream('Đề Nghị Thanh Toán - ' . Carbon::now() . '.pdf', ["Attachment" => false]);
        }

        return Excel::download(new AdminEInvoiceReportPaymentOfferExport($data, $attributes, $keyPrintPaymentOfferByCash, $keyPrintPaymentOfferByTransfer), 'Đề Nghị Thanh Toán - ' . Carbon::now() . '.xlsx');
    }

    /**
     * Mẫu báo cáo xác định khối lượng
     * @return \Illuminate\Http\JsonResponse
     */
    public function postDetermineVolume()
    {
        if (!request()->ajax()) {
            return response()->json(['error' => 1, 'msg' => sc_language_render('admin.method_not_allow')]);
        }
        $arrID = explode(',', request('id'));

        $objInvoices = AdminEInvoice::whereIn('id', $arrID)->get();
        $customer = ShopCustomer::with('department')->where('customer_code', $objInvoices->first()->customer_code)->first();
        if(!$customer) {
            return response()->json(['error' => 1]);
        }

        $checkCustomer = [];
        foreach ($objInvoices as $item) {
            $checkCustomer[] = data_get($item, 'customer_code');
        }

        $customerUnique = array_unique($checkCustomer);
        if(count($customerUnique)>1) {
            return response()->json(['error' => 1]);
        }

        $einvoiceIds = $objInvoices->pluck('id')->toArray();
        $objInvoiceDetail = (new ShopEInvoiceDetail())->getEinvoiceDetail($einvoiceIds);
        $data['customer'] = [
            'name' => $objInvoices->first()->customer_name,
            'department' => ucwords(mb_strtolower($customer->department->name, 'utf-8')),
            'date' => date('d/m/Y', strtotime($objInvoices->first()->invoice_date)),
        ];
        $data['detail'] = $objInvoiceDetail;

        return response()->json($data);
    }

    /**
     * Xuất excel mẫu giấy xác định khối lượng.
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function exportExcelDetermineVolume()
    {
        $dataImport = request()->except('_token');
        $dataImport['date'] = request('date') ? Carbon::createFromFormat('d/m/Y', request('date'))->toDateString() : '';
        $dataImport['date_acceptance'] = request('date_acceptance') ? Carbon::createFromFormat('d/m/Y', request('date_acceptance'))->toDateString() : '';
        $dataImport['report_acceptance'] = request('report_acceptance') ? Carbon::createFromFormat('d/m/Y', request('report_acceptance'))->toDateString() : '';

        return Excel::download(new AdminDetermineVolumeExport($dataImport), 'BaoCaoKhoiLuongHoanThanh' . Carbon::now() . '.xlsx');

    }

    /**
     * Xuất pdf mẫu giấy xác định khối lượng.
     */
    public function exportPdfDetermineVolume()
    {
        $dataImport = request()->except('_token');
        $arrID = explode(',', $dataImport['id_invoice']);
        $objInvoices = AdminEInvoice::whereIn('id', $arrID)->get();
        $einvoiceIds = $objInvoices->pluck('id')->toArray();

        $objInvoiceDetails = (new ShopEInvoiceDetail())->getEinvoiceDetail($einvoiceIds);

        // dd/mm/yyyy -> yyyy-mm-dd
        $dataImport['date'] = request('date') ? Carbon::createFromFormat('d/m/Y', request('date'))->toDateString() : '';
        $dataImport['date_acceptance'] = request('date_acceptance') ? Carbon::createFromFormat('d/m/Y', request('date_acceptance'))->toDateString() : '';
        $dataImport['report_acceptance'] = request('report_acceptance') ? Carbon::createFromFormat('d/m/Y', request('report_acceptance'))->toDateString() : '';
        
        $html = view($this->templatePathAdmin . 'screen.e_invoice.print_pdf.determine_volume_report')
            ->with(['data'=>$dataImport, 'objInvoiceDetails'=>$objInvoiceDetails])->render();
        $html = mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8');
        $domPdf = new Dompdf();
        $domPdf->getOptions()->setChroot(public_path());
        $domPdf->loadHtml($html, 'UTF-8');
        $domPdf->setPaper('A4', 'portrait');
        $domPdf->render();
        return $domPdf->stream('BaoCaoKhoiLuongHoanThanh-' . Carbon::now() . '.pdf',array("Attachment" => false));

    }

    /**
     * Show view import.
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function import()
    {
        return view($this->templatePathAdmin . "screen.e_invoice.import_excel")->with(
            [
                'title' => 'Nhập hoá đơn điện tử',
            ]
        );
    }

    /**
     * Handle import hóa đơn điện tử.
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function importPost()
    {
        try {
            DB::beginTransaction();
            $errorDupticated = [];
            $arrayOrderIds = [];
            //Get file and check extension
            $file = request()->file('excel_file');
            if (!$file || !is_file($file) || !in_array($file->extension(), ['xls', 'xlsx'])) {
                return redirect()->back()->with("error", 'Cảnh báo. Định dạng file không hợp lệ!');
            }
            //Get data and clean
            $rawExcel = Excel::toArray(new EinvoiceImport(), $file);
            $rawExcel = !empty($rawExcel) ? cleanExcelFile($rawExcel) : [];
            if(count($rawExcel) > 8) {
                return redirect()->back()->with("error", 'Cảnh báo. Dữ liệu quá tải vui lòng giảm bớt sheet!');
            }
            //Pre-processing data
            $preData = $this->prepareImportData($rawExcel);
            if($preData["error"]){
                return redirect()->back()->with("error_validate", $preData);
            }
            $listCodeCustomer = data_get($preData, "data.*.master.customer_code");
            $listSkuProduct = data_get($preData, "data.*.details.*.product_code");
            $listOrderIdName = array_unique(data_get($preData, "data.*.details.*.id_name"));
            // Get customer information
            $listCustomer = ShopCustomer::whereIn("customer_code", $listCodeCustomer)->get();
            $listProduct = ShopProduct::whereIn("sku", $listSkuProduct)->get();
            $listOrder = ShopOrder::whereIn("id_name", $listOrderIdName)->get();
            // Loop throught array
            $sheets = $preData["data"];
            $errorDatabinding = [];
            $finalDataInsert = [];
            foreach ($sheets as $sheetIndex => &$sheet){
                $currentCustomer = $listCustomer->where("customer_code", $sheet["master"]["customer_code"])->first();
                if(!$currentCustomer){
                    $errorDatabinding[$sheetIndex][] = "Không tìm thấy thông tin khách hàng " . $sheet["master"]["customer_code"];
                    continue;
                }
                if($currentCustomer->kind == 3){
                    $errorDatabinding[$sheetIndex][] = "Khách hàng thuộc loại 'Khác' . Không thể nhập lên hóa đơn điện tử. Mã KH " . $sheet["master"]["customer_code"];
                    continue;
                }

                $sheet["master"]["customer_name"] = $currentCustomer->name;
                $sheet["master"]["customer_address"] = $currentCustomer->address;
                $sheet["master"]["tax_code"] = $currentCustomer->taxt_code;
                $sheet["master"]["customer_kind"] = $currentCustomer->kind;
                $sheet["master"]["sync_system"] = $currentCustomer->kind == 0 ? 'einv' : 'fast';
                foreach ($sheet["details"] as $key => &$detail){
                    $currentProduct = $listProduct->where("sku", $detail['product_code'])->first();
                    if (!$currentProduct) {
                        $errorDupticated[$sheetIndex][] = "Mã sản phẩm không hơp lệ dòng số " .( $key )." tên mã : '" . $detail['product_code'] . "'" ;
                        continue;
                    }
                    $taxRate = 0;
                    switch ($currentCustomer->kind){
                        case 0:
                            $taxRate = $currentProduct->tax_default ?? 0;
                            break;
                        case 1:
                            $taxRate = $currentProduct->tax_company ?? 0;
                            break;
                        case 2:
                            $taxRate = $currentProduct->tax_school ?? 0;
                            break;
                    }
                    $detail["tax_no"] = $taxRate;
                    $detail["pretax_price"] = ($detail['price']) / ( 1 + $taxRate/100);
                    $detail["tax_amount"] = ($detail['price'] * $detail['qty']) - ($detail["pretax_price"] * $detail['qty']);
                    $detail["product_name"] = $currentProduct->bill_name ?? $detail['product_name'];
                }
                $detailCollect = collect($sheet["details"]);
                $detailByGroup = $detailCollect->groupBy("id_name");
                $listOrderExcel = array_keys($detailByGroup->toArray());
                $tempOrderList = ShopOrder::whereIn("id_name", $listOrderExcel)->get();
                $checkOrderInInvoiceDB = ShopEInvoice::with("order")->whereIn("order_id", data_get($tempOrderList, "*.id_name"))->get();
                if($checkOrderInInvoiceDB && (count($checkOrderInInvoiceDB) > 0)){
                    $errorDupticated[$sheetIndex][] = "Đơn hàng sau đây đã có trong hoá đơn điện tử: " . implode(", ", array_unique(data_get($checkOrderInInvoiceDB, "*.order_id")));
                    continue;
                }
                foreach ($detailByGroup as $keyIdName => $detailByGroupItems){
                    $arrayOrderIds[$sheetIndex][] = $keyIdName;
                    $tempOrder = $listOrder->where("id_name", $keyIdName)->first();
                    if(!$tempOrder){
                        $errorDatabinding[$sheetIndex][] = "Không tìm thấy đơn hàng $keyIdName";
                        continue;
                    }
                    $item = $sheet["master"];
                    $item["order_id"] = $tempOrder->id_name;
                    $item["id_name"] = ShopGenId::genNextId('einvoice');
                    $item["delivery_date"] = $listOrder->where("id_name", $keyIdName)->first()->delivery_time;
                    $item["invoice_date"] = now();
                    $item["created_at"] = now();
                    $item["updated_at"] = now();
                    $item["details"] = $detailByGroupItems;
                    $item["total_amount"] = array_sum(data_get($detailByGroupItems, "*.temp_total"));
                    $finalDataInsert[] = $item;
                }
            }
            if($errorDupticated){
                DB::rollBack();
                if($errorDupticated){
                    return redirect()->back()->with("error_dupticate", $errorDupticated);
                }
            }

            if($errorDatabinding){
                DB::rollBack();
                return redirect()->back()->with("error_data", $errorDatabinding);
            }
            foreach ($finalDataInsert as $insertItem){
                $einvoiceId = ShopEInvoice::insertGetId(array_except($insertItem, "details"));
                if(!$einvoiceId){
                    DB::rollBack();
                    return redirect(route("admin.einvoice.index"))->with("error", "Lỗi không xác định! Vui lòng liên hệ bộ phận kĩ thuật");
                }
                $details = $insertItem["details"]->toArray();
                $details = data_set($details, "*.einv_id", $einvoiceId);
                foreach ($details as &$detail){
                    unset($detail["id_name"]);
                    unset($detail["temp_total"]);
                }
                $checkInsert = ShopEInvoiceDetail::insert($details);
                if(!$checkInsert){
                    DB::rollBack();
                    return redirect(route("admin.einvoice.index"))->with("error", "Lỗi không xác định! Vui lòng liên hệ bộ phận kĩ thuật");
                }
            }
            $this->combineEinvoiceWhenImport($arrayOrderIds);
            DB::commit();
            return redirect(route("admin.einvoice.index"))->with("success", "Nhập hoá đơn điện tử thành công!");
        } catch (\Throwable $e) {
            Log::error($e);
            return redirect()->back()->with("error", "Lỗi file! Vui lòng liên hệ bộ phận kĩ thuật để kiểm tra");
        }
    }

    /**
     * Handel check lỗi file excel import.
     * @param $sheets
     * @return array
     */
    public function prepareImportData($sheets){
        $detailStartRow = 9;
        $output = [];
        $error = 0;
        foreach ($sheets as $sheetIndex => $sheet){
            $realSheet = $sheetIndex + 1;
            $errorDupticate = [];
            $errorSheet = [];
            $errorMaster = [];

            // Template data
            $customerCode = $sheet[4][1] ?? "";
            $totalAmount = $sheet[array_key_last($sheet)][8] ?? "";

            if(!$customerCode){
                $errorMaster[5] = "Mã đơn vị trống";
            }
            if(!is_numeric($totalAmount)){
                $errorMaster[array_key_last($sheet) + 1] = "Tổng tiền trống";
            }

            $master = [
                "customer_code" => $customerCode,
                "sync_system" => "einv"
            ];
            $details = [];
            $array = array_keys($sheet);
            $countItem = array_pop($array);
            foreach ($sheet as $index => $item){
                if ($index < $detailStartRow  ) {
                    continue;
                }

                if ($index >= $countItem-1) {
                    continue;
                }
                $itemTemp = $item;
                unset($itemTemp[8]);
                if(empty(implode("", $itemTemp)) && $item[8] != '' ) {
                    continue;
                };
                $productCode = $item[3];
                $productName = $item[4];
                $idName = $item[1];
                $unit = $item[5];
                $qty = $item[6] ?? 0;
                $price = $item[7] ?? 0;
                if(!$idName){
                    $errorSheet[$index + 1][] = "Số HĐ trống";
                }
                if(!$productName){
                    $errorSheet[$index + 1][] = "Tên sản phẩm trống";
                }
                if(!$productCode){
                    $errorSheet[$index + 1][] = "Mã sản phẩm trống";
                }
                if(!$unit){
                    $errorSheet[$index + 1][] = "Đơn vị trống";
                }
                if(!is_numeric($price)){
                    $errorSheet[$index + 1][] = "Giá phải là số";
                } else {
                    if($price <= 0){
                        $errorSheet[$index + 1][] = "Giá bán phải lớn hơn 0";
                    }
                }
                if(!$qty){

                    $errorSheet[$index + 1][] = "Số lượng trống hoặc số lượng = 0";
                } else {
                    if(!is_numeric($qty)){
                        $errorSheet[$index + 1][] = "Số lượng phải là số";
                    }
                    if($qty <= 0){
                        $errorSheet[$index + 1][] = "Số lượng phải lớn hơn 0";
                    }
                }
                if(empty($errorSheet[$index + 1])){
                    $details[$index + 1] = [
                        "product_code" => $productCode,
                        "product_name" => $productName,
                        "unit" => $unit,
                        "qty" => $qty,
                        "price" => $price,
                        "id_name" => $idName,
                        "einv_id" => 0,
                        "temp_total" => $qty * $price
                    ];
                } else {
                    $error = 1;
                }
            }

            $output["data"][$realSheet] = [
                "master" => $master,
                "details" => $details,
                "error_msg" => ($errorSheet || $errorDupticate || $errorMaster) ? ["master" => $errorMaster, "detail" => $errorSheet, "dupticate" => $errorDupticate] : []
            ];
        }
        $output["error"] = $error;
        return $output;
    }

    /**
     * Gộp hóa đơn điện tử khi import dữ liệu.
     * @param $arrayOrderIds
     */
    public function combineEinvoiceWhenImport($arrayOrderIds)
    {
        foreach ($arrayOrderIds as $value) {
            $einvoices = ShopEInvoice::whereIn('order_id', $value)->get();
            $dataGroupByCustomer = $einvoices->groupBy('customer_code');
            foreach ($dataGroupByCustomer as $item) {
                if (count($item) >= 2) {
                    $firstEinvoice = $item->first();
                    $idEinvoiceDetail = $item->pluck('id')->toArray();
                    $id = ShopEInvoice::insertGetId(
                        [
                            'id_name' => ShopGenId::genNextId('einvoice'),
                            'customer_code' => $firstEinvoice->customer_code ?? '',
                            'customer_name' => $firstEinvoice->customer_name,
                            'invoice_date' => now(),
                            'process_status' => 0,
                            'customer_kind' => $firstEinvoice->customer_kind,
                            'tax_code' => $firstEinvoice->tax_code,
                            'sync_system' => $firstEinvoice->sync_system,
                            'total_amount' => $einvoices->sum('total_amount'),
                        ]
                    );
                    $details = ShopEInvoiceDetail::WhereIn('einv_id', $idEinvoiceDetail)->get();
                    foreach ($details->groupBy(['product_code', 'price']) as $product_code => $product) {
                        foreach ($product as $value) {
                            $insert = [
                                'einv_id' => $id,
                                'product_code' => $product_code,
                                'product_name' => $value->first()->product_name,
                                'unit' => $value->first()->unit,
                                'qty' => $value->sum('qty'),
                                'price' => $value->first()->price,
                                'tax_no' => $value->first()->tax_no,
                                'pretax_price' => $value->first()->pretax_price,
                                'tax_amount' => $value->sum('tax_amount'),
                            ];
                            ShopEInvoiceDetail::insert($insert);
                        }
                    }
                    ShopEInvoice::whereIn('id', $idEinvoiceDetail)->update([
                        'del_flag' => 1,
                        'parent_id' => $id,
                    ]);
                } else {
                    $idEinvoice = $item->first()->id;
                    $details = ShopEInvoiceDetail::Where('einv_id', $idEinvoice)->get();
                    $idsDetail = $details->pluck('id');
                    foreach ($details->groupBy(['product_code', 'price']) as $product_code => $product) {
                        foreach ($product as $value) {
                            $insert = [
                                'einv_id' => $idEinvoice,
                                'product_code' => $product_code,
                                'product_name' => $value->first()->product_name,
                                'unit' => $value->first()->unit,
                                'qty' => $value->sum('qty'),
                                'price' => $value->first()->price,
                                'tax_no' => $value->first()->tax_no,
                                'pretax_price' => $value->first()->pretax_price,
                                'tax_amount' => $value->sum('tax_amount'),
                            ];
                            ShopEInvoiceDetail::insert($insert);
                        }
                    }
                    ShopEInvoiceDetail::whereIn('id', $idsDetail)->delete();
                }
            }
        }

    }

    /**
     * Form thông tin mẫu giấy giới thiệu.
     * @return \Illuminate\Http\JsonResponse
     */
    public function postIntrodavicorpForm()
    {
        $ids = request('id');
        $arrId = explode(',', $ids);

        if(count($arrId) > 1) {
            return response()->json(['error' => 1]);
        }
        $objEinvoice = ShopEInvoice::findOrFail($ids);
        $customer_code = $objEinvoice->customer_code;
        $objCustomer = ShopCustomer::where('customer_code', $customer_code)->first();
        if (!$objCustomer) {
                return response()->json(['error' => 1, 'messages' => 'Dữ liệu khách hàng bị lỗi']);
        }
        $department = $objCustomer->department_id;
        if($department == 1 || $department == 2) {
            $object = 'GIÁM ĐỐC CÔNG TY';
            $objectHeader = 'CÔNG TY CỔ PHẦN DAVICORP VIỆT NAM';
        } else {
            $object = 'CHỦ CỬA HÀNG';
            $objectHeader = 'CỬA HÀNG THỰC PHẨM SẠCH DAVICORP';
        }
        $date = \Carbon\Carbon::parse($objEinvoice->invoice_date);
        $dataEinvoice = [
            'invoice_date' => $date->format('d/m/Y'),
            'customer_name' => $objCustomer->name,
            'object_represent' => $object,
            'object_header' => $objectHeader
        ];

        return response()->json($dataEinvoice);
    }

    /**
     * Xuất excel mẫu giấy giới thiệu.
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function exportExcelIntrodavicorp()
    {
        $data = request()->except('_token');
        // dd/mm/yyyy -> yyyy-mm-dd
        $data['invoice_date'] = request('invoice_date') ? Carbon::createFromFormat('d/m/Y', request('invoice_date'))->toDateString() : '';
        $data['date_supply'] = request('date_supply') ? Carbon::createFromFormat('d/m/Y', request('date_supply'))->toDateString() : '';
        $data['date_effect'] = request('date_effect') ? Carbon::createFromFormat('d/m/Y', request('date_effect'))->toDateString() : '';
        
        return Excel::download(new AdminIntroDavicorpTemplateExport($data), 'MauGiayGioiThieu' . Carbon::now() . '.xlsx');
    }

    /**
     * Xuất file pdf mẫu giấy giới thiệu.
     */
    public function exportPdfIntrodavicorp()
    {
        $data = request()->except('_token');
        // dd/mm/yyyy -> yyyy-mm-dd
        $data['invoice_date'] = request('invoice_date') ? Carbon::createFromFormat('d/m/Y', request('invoice_date'))->toDateString() : '';
        $data['date_supply'] = request('date_supply') ? Carbon::createFromFormat('d/m/Y', request('date_supply'))->toDateString() : '';
        $data['date_effect'] = request('date_effect') ? Carbon::createFromFormat('d/m/Y', request('date_effect'))->toDateString() : '';

        $html = view($this->templatePathAdmin . 'screen.e_invoice.print_pdf.intro_report')
            ->with(['data'=>$data])->render();
        $html = mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8');
        $domPdf = new Dompdf();
        $domPdf->getOptions()->setChroot(public_path());
        $domPdf->loadHtml($html, 'UTF-8');
        $domPdf->setPaper('A4', 'portrait');
        $domPdf->render();
        return $domPdf->stream('MauGiayGioiThieu-' . Carbon::now() . '.pdf',array("Attachment" => false));
    }

}