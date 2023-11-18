<?php

namespace App\Admin\Controllers;

use App\Admin\Models\AdminCategory;
use App\Admin\Models\AdminProduct;
use App\Admin\Models\AdminUnit;
use App\Admin\Models\AdminWarehouse;
use App\Admin\Models\AdminWarehouseProduct;
use App\Exceptions\ImportException;
use App\Exports\ProductExport;
use App\Front\Models\ShopCategory;
use App\Front\Models\ShopProduct;
use App\Front\Models\ShopProductDescription;
use App\Front\Models\ShopDavicookMenuDetail;
use App\Http\Requests\Admin\AdminProductRequest;
use App\Imports\ProductImport;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use SCart\Core\Admin\Admin;
use SCart\Core\Admin\Controllers\RootAdminController;
use SCart\Core\Admin\Models\AdminUser;
use SCart\Core\Front\Models\ShopAttributeGroup;
use SCart\Core\Front\Models\ShopLanguage;
use SCart\Core\Front\Models\ShopLength;
use SCart\Core\Front\Models\ShopProductProperty;
use SCart\Core\Front\Models\ShopWeight;

use Illuminate\Support\Benchmark;

class AdminProductController extends RootAdminController
{
    public $languages;
    public $properties;
    public $attributeGroup;
    public $listWeight;
    public $listLength;

    public function __construct()
    {
        parent::__construct();
        $this->languages = ShopLanguage::getListActive();
        $this->listWeight = ShopWeight::getListAll();
        $this->listLength = ShopLength::getListAll();
        $this->attributeGroup = ShopAttributeGroup::getListAll();
        $this->properties = (new ShopProductProperty)->pluck('name', 'code')->toArray();
    }

    public function index()
    {
        $data = [
            'title' => sc_language_render('product.admin.list'),
            'subTitle' => '',
            'icon' => 'fa fa-indent',
            'urlDeleteItem' => sc_route_admin('admin_product.delete'),
            'removeList' => 1, // Enable function delete list item
            'buttonRefresh' => 1, // 1 - Enable button refresh
            'buttonSort' => 1, // 1 - Enable button sort
            'css' => '',
            'js' => '',
            'urlExport' => sc_route_admin('admin_product.export'),
            'permGroup' => 'product'
        ];
        //Process add content
        $data['menuRight'] = sc_config_group('menuRight', \Request::route()->getName());
        $data['menuLeft'] = sc_config_group('menuLeft', \Request::route()->getName());
        $data['topMenuRight'] = sc_config_group('topMenuRight', \Request::route()->getName());
        $data['topMenuLeft'] = sc_config_group('topMenuLeft', \Request::route()->getName());
        $data['blockBottom'] = sc_config_group('blockBottom', \Request::route()->getName());

        $listTh = [
            'sku' => sc_language_render('product.sku'),
            'name' => sc_language_render('product.name'),
            'category' => sc_language_render('product.category'),
            'status' => sc_language_render('product.status'),
            'kind' => sc_language_render('product.kind'),
            'action' => sc_language_render('action.title'),
        ];

        $cssTd = [
            'sku' => 'max-width: 120px'
        ];
        $data['cssTd'] = $cssTd;

        $keyword = sc_clean(request('keyword') ?? '');
        $category_id = sc_clean(request('category_id') ?? '');
        $sort_order = sc_clean(request('sort_order') ?? 'id_desc');

        $arrSort = [
            'id__desc' => sc_language_render('filter_sort.id_desc'),
            'id__asc' => sc_language_render('filter_sort.id_asc'),
            'name__desc' => sc_language_render('filter_sort.name_desc'),
            'name__asc' => sc_language_render('filter_sort.name_asc'),
        ];
        $dataSearch = [
            'keyword' => $keyword,
            'category_id' => $category_id,
            'sort_order' => $sort_order,
            'arrSort' => $arrSort,
        ];

        $dataTmp = (new AdminProduct)->getProductListAdmin($dataSearch);
        $arrProductId = $dataTmp->pluck('id')->toArray();

        $dataTr = [];
        foreach ($dataTmp as $row) {
            $dataMap = [
                'sku' => $row->sku ?? '',
                'name' => $row->name ?? '',
                'category' => $row->category ? $row->category->name : "",
            ];
            $dataMap['status'] = $row['status'] ? '<span class="badge badge-success">ON</span>' : '<span class="badge badge-danger">OFF</span>';
            $dataMap['kind'] = $row['kind'] == 0 ? '<span class="badge badge-success">Hàng khô</span>' : '<span class="badge badge-danger">Hàng tươi sống</span>';

            $htmlAction = '
            <a data-perm="product:detail" href="' . sc_route_admin('admin_product.edit', ['id' => $row['id'] ? $row['id'] : 'not-found-id']) . '">
                <span title="' . sc_language_render('product.admin.edit') . '" type="button" class="btn btn-flat btn-sm btn-primary">
                    <i class="fa fa-edit"></i>
                </span>
            </a>
            <span data-perm="product:create" onclick="cloneProduct(\'' . $row['id'] . '\');" title="' . sc_language_render('product.admin.clone') . '" type="button" class="btn btn-flat btn-sm btn-secondary">
                <i class="fa fa-clipboard"></i>
            </span>
            <span data-perm="product:delete" onclick="deleteItem(\'' . $row['id'] . '\');"  title="' . sc_language_render('action.delete') . '" class="btn btn-flat btn-sm btn-danger">
                <i class="fas fa-trash-alt"></i>
            </span>';

            $dataMap['action'] = $htmlAction;
            $dataTr[$row['id']] = $dataMap;
        }

        $data['listTh'] = $listTh;
        $data['dataTr'] = $dataTr;
        $data['pagination'] = $dataTmp->appends(request()->except(['_token', '_pjax']))->links($this->templatePathAdmin . 'component.pagination');
        $data['resultItems'] = sc_language_render('admin.result_item', ['item_from' => $dataTmp->firstItem(), 'item_to' => $dataTmp->lastItem(), 'total' => $dataTmp->total()]);

        //menuRight
        $data['menuRight'][] = '
            <a data-perm="product:create" href="' . sc_route_admin('admin_product.create') . '" class="btn btn-success btn-flat" title="' . sc_language_render('product.admin.add_new_title') . '" id="button_create_new">
                <i class="fa fa-plus"></i>
            </a>
            <a data-perm="product:import" href="' . sc_route_admin('admin_product.import') . '" class="btn  btn-success  btn-flat" title="New" id="button_import">
                <i class="fa fa-file-import" title="' . sc_language_render('category-import') . '"></i> ' . sc_language_render('category-import').
            '</a>
            <a data-perm="product:export" href="#"  id="btn_export" class="btn  btn-success  btn-flat" title="New" id="button_export">
                <i class="fa fa-file-export" title="' . sc_language_render('category-export') . '"></i> ' . sc_language_render('category-export').
            '</a>';
        //=menuRight

        //menuSort
        $optionSort = '';
        foreach ($arrSort as $key => $sort) {
            $optionSort .= '<option  ' . (($sort_order == $key) ? "selected" : "") . ' value="' . $key . '">' . $sort . '</option>';
        }
        $data['optionSort'] = $optionSort;
        $data['urlSort'] = sc_route_admin('admin_product.index', request()->except(['_token', '_pjax', 'sort_order']));
        //=menuSort

        //Search with category
        $optionCategory = '';
        $categories = (new AdminCategory)->getCategories();
        if ($categories) {
            foreach ($categories as $k => $v) {
                $optionCategory .= "<option value='{$k}' " . (($category_id == $k) ? 'selected' : '') . ">{$v}</option>";
            }
        }

        //topMenuRight
        $data['topMenuRight'][] = '
                <form action="' . sc_route_admin('admin_product.index') . '" id="button_search">
                <div class="input-group input-group float-left">
                    <select class="form-control rounded-0 select2" name="category_id" id="category_id">
                    <option value="">' . sc_language_render('product.admin.select_category') . '</option>
                    ' . $optionCategory . '
                    </select> &nbsp;
                    <input type="text" name="keyword" class="form-control rounded-0 float-right" placeholder="' . sc_language_render('product.admin.search_place') . '" value="' . $keyword . '">
                    <div class="input-group-append">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i></button>
                    </div>
                </div>
                </form>';
        //=topMenuRight

        // JS


        return view($this->templatePathAdmin . 'screen.product.index')
            ->with($data);
    }

    /**
     * Form create new item in admin
     * @return [type] [description]
     */
    public function create()
    {
        $categories = (new AdminCategory)->getCategories();
        $wareHouse = AdminWarehouse::where('status', 1)->get();
        // html add more images
        $htmlMoreImage = '<div class="input-group"><input type="text" id="id_sub_image" name="sub_image[]" value="image_value" class="form-control rounded-0 input-sm sub_image" placeholder=""  /><span class="input-group-btn"><a data-input="id_sub_image" data-preview="preview_sub_image" data-type="product" class="btn btn-primary lfm"><i class="fa fa-picture-o"></i> Choose</a></span></div><div id="preview_sub_image" class="img_holder"></div>';
        //end add more images

        // html select attribute
        $htmlProductAtrribute = '<tr><td><br><input type="text" name="attribute[attribute_group][name][]" value="attribute_value" class="form-control rounded-0 input-sm" placeholder="' . sc_language_render('product.admin.add_attribute_place') . '" /></td><td><br><input type="number" step="0.01" name="attribute[attribute_group][add_price][]" value="add_price_value" class="form-control rounded-0 input-sm" placeholder="' . sc_language_render('product.admin.add_price_place') . '"></td><td><br><span title="Remove" class="btn btn-flat btn-sm btn-danger removeAttribute"><i class="fa fa-times"></i></span></td></tr>';
        //end select attribute

        $data = [
            'title' => sc_language_render('product.admin.add_new_title'),
            'subTitle' => '',
            'title_description' => sc_language_render('product.admin.add_new_des'),
            'icon' => 'fa fa-plus',
            'languages' => $this->languages,
            'categories' => $categories,
            'wareHouse' => $wareHouse,
            'properties' => $this->properties,
            'htmlMoreImage' => $htmlMoreImage,
            'units' => AdminUnit::all()->keyBy('id')
        ];

        return view($this->templatePathAdmin . 'screen.product.add')
            ->with($data);
    }

    /**
     * Post create new item in admin
     * @return [type] [description]
     */

    public function postCreate(AdminProductRequest $request)
    {
        $data = $request->validated();
        $descriptions = $data['descriptions'] ?? '';
        try {
            DB::beginTransaction();
            $dataInsert = [
                'sku' => $data['sku'] ?? '',
                'cost' => $data['cost'] ?? 0,
                'unit_id' => $data['unit_id'] ?? 0,
                'stock' => $data['stock'] ?? 0,
                'order_num' => $data['order_num'] ?? 0,
                'image' => $data['image'] ?? '',
                'category_id' => $data['category_id'] ?? '',
                'minimum_qty_norm' => $data['minimum_qty_norm'] ?? 0,
                'qr_code' => $data['qr_code'] ?? '',
                'status' => (!empty($data['status']) ? 1 : 0),
                'kind' => $data['kind'] ?? 0,
                'purchase_priority_level' => $data['priority'] ?? 0,
                'tax_default' => $data['default'] ?? 0,
                'tax_school' => $data['school'] ?? 0,
                'tax_company' => $data['company'] ?? 0,
                'qty_limit' => $data['qty_limit'] ?? 0,
                'name' => $descriptions['vi']['name'],
                'short_name' => $descriptions['vi']['short_name'],
                'bill_name' => $descriptions['vi']['bill_name'],
            ];
            $dataInsert = sc_clean($dataInsert, [], true);
            $product = (new AdminProduct($dataInsert));

            if($product->save()){
                sc_clear_cache('cache_product');
            }
            if (!empty($data['warehouse_id'])) {
                $this->createProductWarehouse($product->id ?? '', $data['warehouse_id'],  $data['qty_warehouse'] ?? [], $data['unit_id'] ?? '');
            }
            DB::commit();
            return redirect()->route('admin_product.index')->with('success', sc_language_render('product.admin.create_success'));
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::debug($e);
            return redirect()->back()->withInput($data)->with('error', "Tạo mới sản phẩm thất bại");
        }
    }

    /*
    * Form edit
    */
    public function edit($id)
    {
        $product = (new AdminProduct)->getProductAdmin($id);
        $wareHouse = AdminWarehouse::where('status', 1)->get();
        $productWareHouse = AdminWarehouseProduct::where('product_id', $product->id)->get();
        if ($product === null) {
            return redirect()->route('admin.data_not_found')->with(['url' => url()->full()]);
        }

        $categories = (new AdminCategory)->getCategories();


        $data = [
            'title' => sc_language_render('product.admin.edit'),
            'subTitle' => '',
            'title_description' => '',
            'icon' => 'fa fa-edit',
            'languages' => $this->languages,
            'wareHouse' => $wareHouse,
            'productWareHouse' => $productWareHouse,
            'product' => $product,
            'categories' => $categories,
            'units' => AdminUnit::all()->keyBy('id')
        ];

        return view($this->templatePathAdmin . 'screen.product.edit')
            ->with($data);
    }


    public function postEdit(AdminProductRequest $request, $id)
    {
        $product = (new AdminProduct)->getProductAdmin($id);
        if (empty($product)) {
            return redirect()->route('admin.data_not_found')->with(['url' => url()->full()]);
        }
        $data = $request->validated();
        DB::beginTransaction();
        try {
            //Edit
            $category = $data['category'] ?? [];
            $subImages = $data['sub_image'] ?? [];;
            $dataUpdate = [
                'image' => $data['image'] ?? '',
                'unit_id' => $data['unit_id'] ?? 0,
                'order_num' => $data['order_num'] ?? 0,
                'sku' => $data['sku'],
                'category_id' => $data['category_id'] ?? '',
                'status' => (!empty($data['status']) ? 1 : 0),
                'kind' => $data['kind'] ?? 0,
                'minimum_qty_norm' => $data['minimum_qty_norm'] ?? 0,
                'qr_code' => $data['qr_code'] ?? 0,
                'purchase_priority_level' => $data['priority'] ?? 0,
                'tax_default' => $data['default'] ?? 0,
                'tax_school' => $data['school'] ?? 0,
                'tax_company' => $data['company'] ?? 0,
                'qty_limit' => $data['qty_limit'] ?? 0,
                'name' => $data['descriptions']['vi']['name'],
                'short_name' => $data['descriptions']['vi']['short_name'],
                'bill_name' => $data['descriptions']['vi']['bill_name'],
            ];
            $dataUpdate = sc_clean($dataUpdate, [], true);

            if (!$product->update($dataUpdate)) {
                throw new \Exception('Lỗi cập nhật sản phẩm, vui lòng kiểm tra lại!');
            }
            AdminWarehouseProduct::where('product_id', $id)->delete();
            if (!empty($data['warehouse_id'])) {
                $this->createProductWarehouse($id, $data['warehouse_id'],  $data['qty_warehouse'] ?? [], $data['unit_id'] ?? '');
            }
        } catch (\Throwable $e) {
            DB::rollBack();
            return redirect()->back()->withInput($data)->with('error', $e->getMessage());
        }
        DB::commit();
        sc_clear_cache('cache_product');
        return redirect()->route('admin_product.index')->with('success', sc_language_render('product.admin.edit_success'));
    }

    /**
     * @param $product_id
     * @param $data
     * @param $qty
     * @param $unit_id
     */
    private function createProductWarehouse($product_id, $data, $qty, $unit_id)
    {
        $unit = AdminUnit::where('id', $unit_id)->first();
        foreach ($data as $key => $idWarehouse) {
            $dataInsertProductWarehouse = [
                'product_id' => $product_id,
                'qty' =>  $unit->type == 1 ? (int)$qty[$key] : $qty[$key],
                'warehouse_id' => $idWarehouse,
                'latest_import_qty' => $unit->type == 1 ? (int)$qty[$key] : $qty[$key],
                'created_at' => now(),
                'updated_at' => now(),
            ];
            AdminWarehouseProduct::create($dataInsertProductWarehouse);
        }
    }

    /*
        Delete list Item
        Need mothod destroy to boot deleting in model
    */
    public function deleteList()
    {
        if (!request()->ajax()) {
            return response()->json(['error' => 1, 'msg' => sc_language_render('admin.method_not_allow')]);
        }
        $ids = request('ids');
        $arrID = explode(',', $ids);
        $arrCantDelete = [];
        $arrDontPermission = [];
        foreach ($arrID as $key => $id) {
            if (!$this->checkPermisisonItem($id)) {
                $arrDontPermission[] = $id;
            }
            // Check product assigned to the dish davicook  
            if (!$this->checkProductOnDishDavicook($id)) {
                return response()->json(['error' => 1, 'msg' => 'Sản phẩm đã được gán cho một món ăn không thể xóa!']);
            }
        }

        if (count($arrDontPermission)) {
            return response()->json(['error' => 1, 'msg' => sc_language_render('admin.remove_dont_permisison') . ': ' . json_encode($arrDontPermission)]);
        }
        if (count($arrCantDelete)) {
            return response()->json(['error' => 1, 'msg' => sc_language_render('product.admin.cant_remove_child') . ': ' . json_encode($arrCantDelete)]);
        }

        if ($arrID) {
            $deleteFlag = AdminProduct::destroy($arrID);
            sc_clear_cache('cache_product');
        }
        
        return response()->json(['error' => 0, 'msg' => sc_language_render('action.delete_success')]);
    }

    /**
     * Validate attribute product
     */
    public function validateAttribute(array $arrValidation)
    {
        if (sc_config_admin('product_supplier')) {
            $arrValidation['supplier_id'] = 'nullable';
        }

        if (sc_config_admin('product_cost')) {
            $arrValidation['cost'] = 'nullable|numeric|min:0';
        }

        if (sc_config_admin('product_stock')) {
            $arrValidation['stock'] = 'nullable|numeric';
        }

        return $arrValidation;
    }

    /**
     * Check permisison item
     */
    public function checkPermisisonItem($id)
    {
        return (new AdminProduct)->getProductAdmin($id);
    }

    /**
     * Clone product
     * Only clone single product
     * @return  [type]  [return description]
     */
    public function cloneProduct()
    {
        if (!request()->ajax()) {
            return response()->json(['error' => 1, 'msg' => sc_language_render('admin.method_not_allow')]);
        }
        $pId = request('pId');
        $product = AdminProduct::find($pId);
        if (!$product) {
            return response()->json(['error' => 1, 'msg' => 'Không tìm thấy sản phẩm']);
        }
        try {
            DB::connection(SC_CONNECTION)->beginTransaction();
            //Product info
            $dataProduct = \Illuminate\Support\Arr::except($product->toArray(), ['id', 'created_at', 'updated_at']);
            $dataProduct['sku'] = $dataProduct['sku'] . '-CLONE-' . time();
            AdminProduct::create($dataProduct);

            DB::connection(SC_CONNECTION)->commit();
            return response()->json(['error' => 0, 'msg' => sc_language_render('product.admin.clone_success')]);
        } catch (\Throwable $e) {
            DB::connection(SC_CONNECTION)->rollBack();
            return response()->json(['error' => 1, 'msg' => $e->getMessage()]);
        }
    }

    public function export()
    {
        $filter = json_decode(json_decode(request('filter')), true, 2);
        $ids = explode(',', request('ids'));
        $option = request('option') ?? 0;
        return Excel::download(new ProductExport($filter, $ids, $option), 'DanhSachSanPham-' . Carbon::now() . '.xlsx');
    }

    public function import()
    {
        return view($this->templatePathAdmin . 'screen.product.view_import',
            [
                'title' => 'Nhập danh sách sản phẩm'
            ]);
    }
    /**
     * @throws ImportException
     */
    public function importProduct(){
        $messageError = [];
        $arrCodeWarehouse = [];
        $productCode = '';
        $file = request()->file('excel_file');
        $products = ShopProduct::all();
        $categoryProducts = ShopCategory::all();
        $unitProducts = AdminUnit::all();
        $codeWarehouses = AdminWarehouse::all();
        DB::beginTransaction();
        try {
            $startRow = (new ProductImport())->headingRow() + 1;
            $raw_excel_array_product = Excel::toArray(new ProductImport(), $file);
            $excel_product = $raw_excel_array_product ? cleanExcelFile($raw_excel_array_product)[0] : [];
            if (!$excel_product) {
                throw new ImportException('File không hợp lệ! Đảm bảo file được định dạng theo mẫu cho sẵn');
            }
            if (count($excel_product) > 5000) {
                throw new ImportException('Kích thước dữ liệu khi import ghi đè quá tải. Vui lòng tách file để tránh quá tải');
            }
            foreach ($excel_product as $keyProduct => $itemProduct) {
                if ($itemProduct['ma_san_pham'] == '' && $keyProduct == 0) {
                    $messageError[] = 'Mã sản phẩm không được trống - Dòng ' . ($keyProduct + 2);
                }
                if ($itemProduct['ten_san_pham'] == '' && $keyProduct == 0) {
                    $messageError[] = 'Tên sản phẩm không được trống - Dòng ' . ($keyProduct + 2);
                }
                if ($itemProduct['ma_danh_muc'] == '' && $keyProduct == 0) {
                    $messageError[] = 'Mã danh mục không được trống - Dòng ' . ($keyProduct + 2);
                }
                if ($itemProduct['ten_hien_thi_tren_tem'] == '' && $keyProduct == 0) {
                    $messageError[] = 'Tên hiển thị trên tem không được trống - Dòng ' . ($keyProduct + 2);
                }
                if ($itemProduct['stt_tren_tem'] == '' && $keyProduct == 0) {
                    $messageError[] = 'STT trên tem không được trống - Dòng ' . ($keyProduct + 2);
                }
                if ($itemProduct['loai_mat_hang'] == '' && $keyProduct == 0) {
                    $messageError[] = 'Loại mặt hàng không được trống - Dòng ' . ($keyProduct + 2);
                }
                if ($itemProduct['muc_do_uu_tien'] == '' && $keyProduct == 0) {
                    $messageError[] = 'Mức độ ưu tiên không được trống - Dòng ' . ($keyProduct + 2);
                }
                if ($itemProduct['thue_suat_ap_dung_cho_kh_lay_hoa_don_cua_hang'] == '' && $keyProduct == 0) {
                    $messageError[] = 'Thuế suất áp dụng cho khách hàng lấy hóa đơn cửa hàng không được trống - Dòng ' . ($keyProduct + 2);
                }
                if ($itemProduct['thue_suat_cho_kh_la_cong_ty_xuat_hoa_don_tu_cong_ty'] == '' && $keyProduct == 0) {
                    $messageError[] = 'Thuế suất cho khách hàng là công ty xuất đơn từ công ty không được trống - Dòng ' . ($keyProduct + 2);
                }
                if ($itemProduct['thue_suat_ap_dung_cho_kh_la_truong_hoc_lay_hoa_don_cong_ty'] == '' && $keyProduct == 0) {
                    $messageError[] = 'Thuế suất cho khách hàng là trường học lấy hóa đơn công ty không được trống - Dòng ' . ($keyProduct + 2);
                }
                if ($itemProduct['trang_thai'] == '' && $keyProduct == 0) {
                    $messageError[] = 'Trạng thái không được để trống! - Dòng ' . ($keyProduct + 2);
                }
                if (in_array($itemProduct['ma_kho'], $arrCodeWarehouse)) {
                    $messageError[] = 'Mã kho đã trùng trong excel - Dòng ' . ($keyProduct + 2);
                }
                if ($itemProduct['ma_san_pham'] != '') {
                    $product = $products->where('sku','=',$itemProduct['ma_san_pham'])->first();
                }
                if ($itemProduct['ma_danh_muc'] != '') {
                    $categoryProduct = $categoryProducts->where('sku','=',$itemProduct['ma_danh_muc'])->first();
                    if ($categoryProduct){
                        $categoryCode = $categoryProduct->id ?? '';
                    }else{
                        $messageError[] = 'Mã danh mục không tồn tại trong hệ thống! - Dòng ' . ($keyProduct + 2);
                    }
                }
                if ($itemProduct['don_vi_tinh'] != '') {
                    $unitProduct = $unitProducts->where('name','=',$itemProduct['don_vi_tinh'])->first();
                    if ($unitProduct){
                        $idUnit = $unitProduct->id ?? '';
                    }else{
                        $messageError[] = 'Tên đơn vị tính này không tồn tại trong hệ thống! - Dòng ' . ($keyProduct + 2);
                    }
                }
                if ($itemProduct['ma_kho'] != '') {
                    $codeWarehouse = $codeWarehouses->where('warehouse_code','=',$itemProduct['ma_kho'])->first();
                    if ($codeWarehouse){
                        $idWarehouse = $codeWarehouse->id ?? '';
                    }else{
                        $messageError[] = 'Mã kho này không tồn tại trong hệ thống! - Dòng ' . ($keyProduct + 2);
                    }
                }
                if ($product){
                    if ($itemProduct['ma_san_pham'] != '' && $itemProduct['ten_san_pham'] != '' && $itemProduct['ma_danh_muc'] != '' && $itemProduct['loai_mat_hang'] != ''&& $itemProduct['muc_do_uu_tien'] != '') {
                        $dataUpdate = [
                            'sku' => $itemProduct['ma_san_pham'] ?? '',
                            'name'=> $itemProduct['ten_san_pham'] ?? '',
                            'category_id'=> $categoryCode ?? '',
                            'short_name'=> $itemProduct['ten_hien_thi_tren_tem'] ?? '',
                            'order_num'=> $itemProduct['stt_tren_tem'] ?? '',
                            'bill_name'=> $itemProduct['ten_hien_thi_tren_hoa_don'] ?? '',
                            'minimum_qty_norm'=> $itemProduct['dinh_muc_toi_thieu'] ?? '',
                            'qr_code'=> $itemProduct['link_ma_qrcode'] ?? '',
                            'unit_id'=> $idUnit ?? '',
                            'kind' => $itemProduct['loai_mat_hang'] === 'Hàng tươi sống' ? 1 : 0,
                            'purchase_priority_level' => $itemProduct['muc_do_uu_tien'] === 'Hàng cần đặt hàng ngay' ? 1 : 0,
                            'tax_default' =>$itemProduct['thue_suat_ap_dung_cho_kh_lay_hoa_don_cua_hang']?? '',
                            'tax_company' =>$itemProduct['thue_suat_cho_kh_la_cong_ty_xuat_hoa_don_tu_cong_ty'] ?? '',
                            'tax_school' =>$itemProduct['thue_suat_ap_dung_cho_kh_la_truong_hoc_lay_hoa_don_cong_ty'] ?? '',
                            'status' => $itemProduct['trang_thai'] === 1 ? 1 : 0,
                            'qty_limit' => $itemProduct['han_muc'] ?? '',
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                        if (!$messageError) {
                            $product->update($dataUpdate);
                            AdminWarehouseProduct::where('product_id', $product->id)->delete();
                        }
                    }
                    if (!$messageError) {
                        AdminWarehouseProduct::insert([
                            'warehouse_id' =>$idWarehouse ??'',
                            'product_id' => $product->id,
                            'qty' => $itemProduct['so_luong_ton_kho'] ?? '',
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                }else{
                    if ($itemProduct['ma_san_pham'] != '' && $itemProduct['ten_san_pham'] != '' && $itemProduct['ma_danh_muc'] != '' && $itemProduct['loai_mat_hang'] != ''&& $itemProduct['muc_do_uu_tien'] != '') {
                        $data = [
                            'sku' => $itemProduct['ma_san_pham'] ?? '',
                            'name'=> $itemProduct['ten_san_pham'] ?? '',
                            'category_id'=> $categoryCode ?? '',
                            'short_name'=> $itemProduct['ten_hien_thi_tren_tem'] ?? '',
                            'order_num'=> $itemProduct['stt_tren_tem'] ?? '',
                            'bill_name'=> $itemProduct['ten_hien_thi_tren_hoa_don'] ?? '',
                            'minimum_qty_norm'=> $itemProduct['dinh_muc_toi_thieu'] ?? '',
                            'qr_code'=> $itemProduct['link_ma_qrcode'] ?? '',
                            'unit_id'=> $idUnit ?? '',
                            'kind' => $itemProduct['loai_mat_hang'] === 'Hàng tươi sống' ? 1 : 0,
                            'purchase_priority_level' => $itemProduct['muc_do_uu_tien'] === 'Hàng cần đặt hàng ngay' ? 1 : 0,
                            'tax_default' =>$itemProduct['thue_suat_ap_dung_cho_kh_lay_hoa_don_cua_hang']?? '',
                            'tax_company' =>$itemProduct['thue_suat_cho_kh_la_cong_ty_xuat_hoa_don_tu_cong_ty'] ?? '',
                            'tax_school' =>$itemProduct['thue_suat_ap_dung_cho_kh_la_truong_hoc_lay_hoa_don_cong_ty'] ?? '',
                            'status' => $itemProduct['trang_thai'] === 1 ? 1 : 0,
                            'qty_limit' => $itemProduct['han_muc'] ?? '',
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                        if (!$messageError) {
                            $newImport = ShopProduct::create($data);
                        }
                    }
                    if (!$messageError) {
                        AdminWarehouseProduct::insert([
                            'warehouse_id' =>$idWarehouse ?? '',
                            'product_id' => $newImport->id ?? '',
                            'qty' => $itemProduct['so_luong_ton_kho'] ?? '',
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                }
            }
        }catch (\Throwable $e) {
            DB::rollBack();
            Log::debug($e);
            $error = !empty($errorCustomerCode) ? $errorCustomerCode : $messageError;
            return redirect()->back()->with('error_validate_import', $messageError)->with('error', $e->getMessage());
        }
        DB::commit();
        return redirect()->route('admin_product.index')->with('success', sc_language_render('product.admin.import_success'));
    }
    function getNullCol($data, $colList){
        $nullCol = [];
        foreach (array_keys($colList) as $colName){
            if(is_null($data[$colName]) || ($data[$colName] == "")){
                $nullCol[] = $colList[$colName];
            }
        }
        return empty($nullCol) ? false : $nullCol;
    }

    /**
     * Kiểm tra sản phẩm đã gán cho món ăn
     */
    public function checkProductOnDishDavicook($id)
    {
        $check = ShopDavicookMenuDetail::where('product_id',$id)->get();
        if (count($check)) {
            return false;
        }
        return true;
    }
}
