<?php

namespace App\Admin\Controllers;

use App\Admin\Models\AdminCustomer;
use App\Admin\Models\AdminReward;
use App\Exceptions\ImportException;
use App\Exports\CustomerExport;
use App\Front\Models\ShopCustomer;
use App\Front\Models\ShopDepartment;
use App\Front\Models\ShopProduct;
use App\Front\Models\ShopProductSupplier;
use App\Front\Models\ShopRewardTier;
use App\Front\Models\ShopSupplier;
use App\Front\Models\ShopZone;
use App\Http\Requests\Admin\AdminCustomerRequest;
use App\Http\Requests\Admin\AdminProductSupplierRequest;
use App\Imports\CustomerImport;
use App\Imports\CustomerProductImport;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use SCart\Core\Admin\Controllers\RootAdminController;
use SCart\Core\Front\Controllers\Auth\AuthTrait;
use SCart\Core\Front\Models\ShopCountry;
use SCart\Core\Front\Models\ShopLanguage;
use Throwable;


class AdminCustomerController extends RootAdminController
{
    use AuthTrait;

    public $languages;
    public $countries;

    public function __construct()
    {
        parent::__construct();
        $this->languages = ShopLanguage::getListActive();
        $this->countries = ShopCountry::getListAll();
    }

    public function index()
    {
        $data = [
            'title' => sc_language_render('customer.admin.list'),
            'subTitle' => '',
            'icon' => 'fa fa-indent',
            'urlDeleteItem' => sc_route_admin('admin_customer.delete'),
            'removeList' => 1, // 1 - Enable function delete list item
            'buttonRefresh' => 0, // 1 - Enable button refresh
            'buttonSort' => 1, // 1 - Enable button sort
            'css' => '',
            'js' => '',
            'urlExport' => sc_route_admin('admin_customer.export'),
            'permGroup' => 'customer'
        ];

        //Process add content
        $data['menuRight'] = sc_config_group('menuRight', \Request::route()->getName());
        $data['menuLeft'] = sc_config_group('menuLeft', \Request::route()->getName());
        $data['topMenuRight'] = sc_config_group('topMenuRight', \Request::route()->getName());
        $data['topMenuLeft'] = sc_config_group('topMenuLeft', \Request::route()->getName());
        $data['blockBottom'] = sc_config_group('blockBottom', \Request::route()->getName());

        $listTh = [
            'customer_code' => sc_language_render('customer.code'),
            'name' => sc_language_render('customer.name'),
            'email' => sc_language_render('customer.email'),
            'phone' => sc_language_render('customer.phone'),
            'address' => sc_language_render('customer.address'),
            'status' => sc_language_render('customer.status'),
            'action' => sc_language_render('action.title'),
        ];
        $cssTd = [
            'customer_code' => 'width: 320px',
            'name' => 'width: auto; min-width: 320px',
            'email' => 'width: 480px',
            'phone' => 'width: 480px',
            'address' => 'width: 320px',
            'status' => 'width: 128px; text-align: center',
            'action' => 'width: 120px',
        ];
        $data['cssTd'] = $cssTd;

        $sort_order = sc_clean(request('sort_order') ?? 'id_desc');
        $keyword = sc_clean(request('keyword') ?? '');
        $delivery_date = sc_clean(request('delivery_date') ?? '');
        $arrSort = [
            'id__desc' => sc_language_render('filter_sort.id_desc'),
            'id__asc' => sc_language_render('filter_sort.id_asc'),
            'name__desc' => sc_language_render('filter_sort.name_desc'),
            'name__asc' => sc_language_render('filter_sort.name_asc'),
        ];

        $dataSearch = [
            'keyword' => $keyword,
            'sort_order' => $sort_order,
            'arrSort' => $arrSort,
            'delivery_date' => $delivery_date,
        ];
        $dataTmp = (new AdminCustomer)->getCustomerListAdmin($dataSearch);
        $dataTr = [];
        foreach ($dataTmp as $key => $row) {
            $dataTr[$row['id']] = [
                'customer_code' => $row['customer_code'],
                'name' => $row['name'],
                'email' => $row['email'],
                'phone' => $row['phone'],
                'address' => $row['address'],
                'status' => $row['status'] ? '<span class="badge badge-success">ON</span>' : '<span class="badge badge-danger">OFF</span>',
                'action' => '
                    <a data-perm="customer:detail" href="' . sc_route_admin('admin_customer.edit', ['id' => $row['id'] ? $row['id'] : 'not-found-id']) . '"><span title="' . sc_language_render('action.edit') . '" type="button" class="btn btn-flat btn-sm btn-primary"><i class="fa fa-edit"></i></span></a>
                    <span data-perm="customer:create" onclick="cloneCustomer(\'' . $row['id'] . '\');" title="' . sc_language_render('action.clone') . '" type="button" class="btn btn-flat btn-sm btn-secondary">
                        <i class="fa fa-clipboard"></i>
                    </span>
                    <span data-perm="customer:delete" onclick="deleteItem(\'' . $row['id'] . '\');"  title="' . sc_language_render('action.delete') . '" class="btn btn-flat btn-sm btn-danger"><i class="fas fa-trash-alt"></i></span>'
                ,
            ];
        }

        $data['listTh'] = $listTh;
        $data['dataTr'] = $dataTr;
        $data['pagination'] = $dataTmp->appends(request()->except(['_token', '_pjax']))->links($this->templatePathAdmin . 'component.pagination');
        $data['resultItems'] = sc_language_render('admin.result_item', ['item_from' => $dataTmp->firstItem(), 'item_to' => $dataTmp->lastItem(), 'total' => $dataTmp->total()]);

        //menuRight
        $data['menuRight'][] = '<a data-perm="customer:create" href="' . sc_route_admin('admin_customer.create') . '" class="btn  btn-success  btn-flat" title="New" id="button_create_new">
                           <i class="fa fa-plus" title="' . sc_language_render('admin.add_new') . '"></i>
                           </a>
                           <a data-perm="customer:import" href="' . sc_route_admin('admin_customer.import') . '" class="btn  btn-success  btn-flat" title="New" id="button_import">
                            <i class="fa fa-file-import" title="' . sc_language_render('category-import') . '"></i> ' . sc_language_render('category-import') .
            '</a>
                            <button data-perm="customer:export" type="button" class="btn  btn-success  btn-flat" title="Xuất excel" id="btn_export">
                            <i class="fa fa-file-export" title="' . sc_language_render('category-export') . '"></i> ' . sc_language_render('category-export') .
            '</button>';
        //=menuRight

        //menuSort
        $optionSort = '';
        foreach ($arrSort as $key => $status) {
            $optionSort .= '<option  ' . (($sort_order == $key) ? "selected" : "") . ' value="' . $key . '">' . $status . '</option>';
        }
        $data['urlSort'] = sc_route_admin('admin_customer.index', request()->except(['_token', '_pjax', 'sort_order']));
        $data['optionSort'] = $optionSort;
        //=menuSort

        //menuSearch
        $data['topMenuRight'][] = '
                <form action="' . sc_route_admin('admin_customer.index') . '" id="button_search">
                <div class="input-group input-group" style="width: 350px;">
                    <input type="text" name="keyword" class="form-control rounded-0 float-right" placeholder="Tìm theo tên hoặc Mã KH" value="' . $keyword . '">
                    <div class="input-group-append">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i></button>
                    </div>
                </div>
                </form>';
        //=menuSearch

        return view($this->templatePathAdmin . 'screen.davicorp_customer.index')
            ->with($data);
    }

    /**
     * Form create new item in admin
     * @return [type] [description]
     */
    public function create()
    {
        $objSupplier = (new ShopSupplier)->getSupplierListAdmin();

        $data = [
            'title' => sc_language_render('customer.admin.add_new_title'),
            'subTitle' => '',
            'title_description' => sc_language_render('customer.admin.add_new_des'),
            'icon' => 'fa fa-plus',
            'countries' => (new ShopCountry)->getCodeAll(),
            'customer' => null,
            'listSupplier' => $objSupplier,
            'url_action' => sc_route_admin('admin_customer.create'),
            'tiers' => ShopRewardTier::all(),
            'departments' => ShopDepartment::all(),
            'is_edit' => 0,
            'data_perm_submit' => 'customer:create'
        ];

        return view($this->templatePathAdmin . 'screen.davicorp_customer.form_add_and_edit')
            ->with($data);
    }

    /**
     * Post create new item in admin
     * @return [type] [description]
     */
    public function postCreate(AdminCustomerRequest $request)
    {
        // Global error indicate
        $dupticatedStack = [];
        $dupticatedFlag = 0;
        // Get validated value and process password, status
        $data = $request->validated();

        $data['status'] = empty($data['status']) ? 0 : 1;
        $data['password'] = bcrypt($data['password']);
        $data['schoolmaster_password'] = bcrypt($data['schoolmaster_password']);
        $data['kind'] = $data['kind'] ?? 0 ;

        try {
            DB::beginTransaction();

            $customer = AdminCustomer::createCustomer(array_except($data, ['password_confirmation', 'schoolmaster_password_confirmation'])); // Insert Customer
            if (!$customer->save()) {
                throw new \Exception(sc_language_render('action.failed'));
            }

            // Process product and supplier
            $listProduct = request('product') ? request('product') : [];
            $listSupplier = request('supplier') ? request('supplier') : [];
            // Check for dupticated
            $checkProductUnique = array_count_values($listProduct);
            foreach ($checkProductUnique as $key => $item) {
                if ($item > 1) {
                    $dupticatedStack[] = $key;
                    $dupticatedFlag = 1;
                }
            }
            // Prepare data
            if ($dupticatedFlag) {
                DB::rollBack();
                throw new \Exception("Trùng sản phẩm");
            }

        } catch (\Throwable $e) {
            DB::rollBack();
            if (empty($dupticatedStack)) {
                return redirect()->back()->withInput($data)->with('error', $e->getMessage());
            }
            return redirect()->back()->withInput($data)->with('error', $e->getMessage());
        }
        DB::commit();
        return redirect()->route('admin_customer.edit', ['id' => $customer->id])->with('success', sc_language_render('action.create_success'));
    }

    /**
     * Form edit
     */
    public function edit($id)
    {

        $objSupplier = (new ShopSupplier)->getSupplierListAdmin();
        $customer = (new AdminCustomer)->getCustomerAdmin($id);

        if (!$customer) {
            return redirect()->route('admin.data_not_found')->with(['url' => url()->full()]);
        }

        $customerProducts = (new ShopProductSupplier)->with(['product', 'supplier'])->where('customer_id', $id);
        $product_search = request('product_search');
        $supplier_search = request('supplier_search');


        if (isset($product_search)) {
            $customerProducts = $customerProducts->whereHas('product', function ($query) use ($product_search) {
                $query->where('name', 'like', "%$product_search%");
            });
        }
        if (isset($supplier_search)) {
            $customerProducts = $customerProducts->where('supplier_id', $supplier_search);
        }
        $customerProducts = $customerProducts->paginate(config('pagination.admin.small'))->onEachSide(0);

        $data = [
            'title' => sc_language_render('action.edit'),
            'subTitle' => '',
            'title_description' => '',
            'icon' => 'fa fa-edit',
            'customer' => $customer,
            'countries' => (new ShopCountry)->getCodeAll(),
            'addresses' => $customer->addresses,
            'listSupplier' => $objSupplier,
            'currentSearchSupplier' => ShopSupplier::find($supplier_search),
            'url_action' => sc_route_admin('admin_customer.edit', ['id' => $customer['id']]),
            'customerProducts' => $customerProducts,
            'tiers' => ShopRewardTier::all(),
            'departments' => ShopDepartment::all(),
            'is_edit' => 1,
            'products' => $customer->productSuppliers,
            'data_perm_submit' => 'customer:edit'
        ];


        return view($this->templatePathAdmin . 'screen.davicorp_customer.form_add_and_edit')
            ->with($data);
    }

    /**
     * update status
     */
    public function postEdit($id, AdminCustomerRequest $request)
    {
        // Global error indicate
        $dupticatedStack = [];
        $dupticatedFlag = 0;
        // Get validated value and process password, status
        $data = $request->validated();
        $data['status'] = empty($data['status']) ? 0 : 1;

        if (!empty($data['password'])) {
            $data['password'] = bcrypt($data['password']);
        }
        if (!empty($data['schoolmaster_password'])) {
            $data['schoolmaster_password'] = bcrypt($data['schoolmaster_password']);
        }
        // Process update
        try {
            DB::beginTransaction();
            $customer = AdminCustomer::find($id); // Insert Customer
            $customer->fill(array_except($data, ['password_confirmation', 'id', 'schoolmaster_password_confirmation']));
            if (!$customer->save()) {
                throw new \Exception(sc_language_render('action.failed'));
            }
        } catch (\Throwable $e) {
            DB::rollBack();
            if (empty($dupticatedStack)) {
                return redirect()->back()->withInput($data)->with('error', $e->getMessage());
            }
            return redirect()->back()->with('error', $e->getMessage());
        }
        DB::commit();
        return redirect()->route('admin_customer.index')->with('success', sc_language_render('action.edit_success'));
    }

    public function productPost(AdminProductSupplierRequest $request, $id)
    {
        $allProduct = ShopProductSupplier::where('customer_id', $id)->pluck('product_id', 'id')->toArray();
        $products = $request->validated('products');
        $suppliers = $request->validated('suppliers');
        if (empty($products) || empty($suppliers)) {
            return back()->with('error', sc_language_render('admin.davicook.customer.do_not_have_product'));
        }

        foreach ($products as $key => $product) {
            $updateData = [
                'customer_id' => $id,
                'product_id' => $products[$key],
                'supplier_id' => $suppliers[$key]
            ];

            if (in_array($updateData['product_id'], Arr::except($allProduct, [$key]))) {
                return back()->with('product_error', "Lưu sản phẩm bị trùng, vui lòng kiểm tra lại");
            }

            $updateItem = ShopProductSupplier::find($key);
            if ($updateItem->product_id != $products[$key] || $updateItem->supplier_id != $suppliers[$key]) {
                $updateItem->fill($updateData);
                $check = $updateItem->save();
                if (!$check) {
                    return back()->with('product_error', "Lưu sản phẩm thất bại");
                }
                $allProduct[$key] = $updateItem->product_id;
            }
        }
        return redirect()->back()->with('success', 'cập nhât sản phẩm thành công');
    }

    public function productAddPost(Request $request, $id)
    {
        $allProduct = ShopProductSupplier::where('customer_id', $id)->pluck('product_id', 'id')->toArray();
        $validate = Validator::make($request->all(), ['product_id' => 'required', 'supplier_id' => 'required']);
        $insertData = $validate->validated();
        $insertData['customer_id'] = $id;
        if (in_array($insertData['product_id'], $allProduct)) {
            return back()->with('product_error', "Lưu sản phẩm bị trùng, vui lòng kiểm tra lại");
        }
        (new ShopProductSupplier($insertData))->save();
        return redirect(route('admin_customer.edit', ['id' => $id]))->with('success', 'Thêm sản phẩm thành công');
    }

    public function productRemovePost($id)
    {
        if (ShopProductSupplier::find($id)->delete()) {
            return redirect()->back()->with('success', 'Xoá sản phẩm thành công');
        }
        return back()->with('product_error', "Xoá sản phẩm thất bại, vui lòng kiểm tra lại");
    }

    public function deleteList()
    {
        if (!request()->ajax()) {
            return response()->json(['error' => 1, 'msg' => sc_language_render('admin.method_not_allow')]);
        } else {
            $ids = request('ids');
            $arrID = explode(',', $ids);
            $arrDontPermission = [];
            foreach ($arrID as $key => $id) {
                if (!$this->checkPermisisonItem($id)) {
                    $arrDontPermission[] = $id;
                }
            }
            if (count($arrDontPermission)) {
                return response()->json(['error' => 1, 'msg' => sc_language_render('admin.remove_dont_permisison') . ': ' . json_encode($arrDontPermission)]);
            }
            AdminCustomer::destroy($arrID);
            return response()->json(['error' => 0, 'msg' => '']);
        }
    }

    public function checkPermisisonItem($id)
    {
        return (new AdminCustomer)->getCustomerAdmin($id);
    }

    public function export()
    {
        $filter = json_decode(json_decode(request('filter')), true, 2) ?? [];
        $ids = explode(',', request('ids'));
        $option = request('option') ?? 0;

        $data = [];
        switch ($option) {
            case 1:
                $data = (new AdminCustomer)->getCustomerExportList(null, $ids);;
                break;
            case 0:
                $data = (new AdminCustomer)->getCustomerExportList($filter['keyword']);
                break;
        }
        if (!$data) {
            return redirect()->back()->with('error', 'Vượt quá giới hạn xuất dữ liệu (' . config('export.limit') . ' dòng)');
        }

        return Excel::download(new CustomerExport($data), 'DanhSachKhachHang-' . Carbon::now() . '.xlsx');
    }

    public function import()
    {
        return view($this->templatePathAdmin . 'screen.davicorp_customer.view_import',
            [
                'title' => 'Nhập danh sách khách hàng'
            ]);
    }


    /**
     * Custom import customer
     * @return mixed
     * @throws ImportException
     */
    public function importCustomer() {
        $file = request()->file('excel_file');
        $type_import = request('type_import_customer');

        if (!$file || !is_file($file) || !in_array($file->extension(), ['xls', 'xlsx'])) {
            throw new ImportException('Định dạng file không hợp lệ!');
        }
        if ($type_import == 1) {
            return $this->importOverwrite($file);
        } else {
            return $this->importNotOverwrite($file);
        }


    }

    public function importOverwrite($file) {
        $messageError = [];
        $errorCustomerCode = [];
        DB::beginTransaction();
        try {
            $startRow = (new CustomerImport())->headingRow() + 1;
            $raw_excel_array_product = Excel::toArray(new CustomerProductImport(), $file);
            $excel_customer = $raw_excel_array_product ? cleanExcelFile($raw_excel_array_product)[0] : [];
            $raw_excel_array = Excel::toArray(new CustomerImport(), $file);
            $excel = $raw_excel_array ? cleanExcelFile($raw_excel_array)[0] : [];
            if (!$excel) {
                throw new ImportException('File không hợp lệ! Đảm bảo file được định dạng theo mẫu cho sẵn');
            }
            if (count($excel) > 5000) {
                throw new ImportException('Kích thước dữ liệu khi import ghi đè quá tải. Vui lòng tách file để tránh quá tải');
            }
            foreach ($excel_customer as $keyCustomer => $itemCustomer) {
                if ($itemCustomer['ma_khach_hang'] == '') {
                    $errorCustomerCode[] = 'Mã khách hàng hàng không được trống - Dòng '. ($keyCustomer + 2);
                }
            }
            if (!empty($errorCustomerCode)) {
                throw new ImportException('Vui lòng điền đầy đủ mã khách hàng để hệ thống hoạt động tiếp tục!');
            }
            $products = ShopProduct::all()->pluck('id', 'sku')->toArray();
            $suppliers = ShopSupplier::all()->pluck('id', 'supplier_code')->toArray();

            foreach ($excel_customer as $keyCustomer => $itemCustomer) {
                $customer = ShopCustomer::where('customer_code', $itemCustomer['ma_khach_hang'])->first();
                if ($customer) {
                    //  Check khi Customer đã tồn tại.
                    //  Xử lý update Customer.
                    $listUpdate = $this->getDataUpdateImportCustomer($itemCustomer);
                    $customer->update($listUpdate);
                    $j = 1 ;
                    $productSuppliers = ShopProductSupplier::where('customer_id', $customer->id)->get();
                    $productCode = [];
                    foreach ($excel as $keyProduct => $itemProduct) {
                        $j++;
                        $line = $keyProduct + $startRow;
                        if ($j != $line) {
                            $messageError[] = 'Vui lòng kiểm tra kỹ file excel trước khi nhập';
                            $messageError[] = 'File không hợp lệ! File trống dòng số '. $line - 1;
                            throw new \Exception('Lỗi cập nhật sản phẩm, vui lòng kiểm tra lại!');
                        }
                        if ($keyProduct < $keyCustomer) {
                            continue;
                        }
                        if ( ($itemProduct['ma_khach_hang'] != '') && ($keyProduct != $keyCustomer) ) {
                            break;
                        }
                        if (in_array($itemProduct['ma_san_pham'], $productCode)) {
                            $messageError[] = 'Mã sản phẩm bị trùng lặp - Dòng '. ($keyProduct + 2);
                        }
                        $productCode[] = $itemProduct['ma_san_pham'];
                        if (!array_key_exists($itemProduct['ma_san_pham'], $products)) {
                            $messageError[] = 'Mã sản phẩm không hợp lệ - Dòng '. ($keyProduct + 2);
                        }
                        if (!array_key_exists($itemProduct['ma_nha_cung_cap'],$suppliers)) {
                            $messageError[] = 'Mã nhà cung cấp không hợp lệ - Dòng '. ($keyProduct + 2);
                        }
                        $productSupplier = $productSuppliers->where('product_id', $products[$itemProduct['ma_san_pham']] ?? '');
                        if ($productSupplier->isNotEmpty()) {
                            $checkProductSupplier =  $productSupplier->where('supplier_id', $suppliers[$itemProduct['ma_nha_cung_cap']]);
                            if ($checkProductSupplier->isNotEmpty()) {
                                continue;
                            } else {
                                $supplier = ShopProductSupplier::find($productSupplier->first()->id);
                                $supplier->supplier_id = $suppliers[$itemProduct['ma_nha_cung_cap']];
                                $supplier->save();
                            }
                        } else {
                            if (!$messageError) {
                                $insertFlag = ShopProductSupplier::insert([
                                    'supplier_id' => $suppliers[$itemProduct['ma_nha_cung_cap']] ?? '',
                                    'customer_id' => $customer->id,
                                    'product_id' => $products[$itemProduct['ma_san_pham']] ?? '',
                                ]);

                                if (!$insertFlag) {
                                    throw new ImportException('Thông tin sản phẩm không hợp lệ. Vui lòng kiểm tra lại');
                                }
                            }
                        }
                    }
                } else {
                    //  Check khi Customer chưa tồn tại.
                    //  Xử lý insert Customer.
                    $errorNullCustomer = $this->checkErrorNullCustomer($itemCustomer);
                    if (!empty($errorNullCustomer)) {
                        foreach ($errorNullCustomer as $error) {
                            $messageError[] = $error . ($keyCustomer + 2);
                        }
                    }
                    $department = ShopDepartment::where('name', $itemCustomer['khach_hang_thuoc'])->first();
                    if (!$department) {
                        $messageError[] = 'Khách hàng thuộc không hợp lệ - Dòng '. ($keyCustomer + 2);
                    }

                    $tier = ShopRewardTier::where('name', $itemCustomer['hang_khach_hang'])->first();
                    if (!$tier) {
                        $messageError[] = 'Hạng khách hàng không hợp lệ - Dòng '. ($keyCustomer + 2);
                    }

                    $zone = ShopZone::where('zone_code', $itemCustomer['ma_khu_vuc'])->first();
                    if (!$zone) {
                        $messageError[] = 'Mã khu vực không hợp lệ - Dòng '. ($keyCustomer + 2);
                    }

                    $dataInsert = [
                        'customer_code' => $itemCustomer['ma_khach_hang'],
                        'name' => $itemCustomer['ten_khach_hang'],
                        'schoolmaster_code' => $itemCustomer['ten_dang_nhap_tk_hieu_truong'],
                        'email' => $itemCustomer['email'],
                        'phone' => $itemCustomer['so_dien_thoai'],
                        'department_id' => $department->id,
                        'tier_id' => $tier->id,
                        'tax_code' => $itemCustomer['ma_so_thue'],
                        'order_num' => $itemCustomer['thuoc_stt'],
                        'short_name' => $itemCustomer['ten_hien_thi_tren_tem'],
                        'route' => $itemCustomer['thuoc_tuyen_hang'],
                        'address' => $itemCustomer['dia_chi'],
                        'zone_id' => $zone->id ?? '',
                        'kind' => $itemCustomer['loai_khach_hang'] == 'CTY HĐ CT' ? 1 : ( $itemCustomer['loai_khach_hang'] == 'TH HĐ CTY' ? 2 : ( $itemCustomer['loai_khach_hang'] == 'HĐ CH' ? 0 : 3)),
                        'teacher_code' => $itemCustomer['ma_giao_vien'],
                        'student_code' => $itemCustomer['ma_hoc_sinh'],
                        'status' => $itemCustomer['trạng thái'] ?? 1,
                    ];
                    if (!$messageError) {
                        $insertFlag = ShopCustomer::create($dataInsert);
                        if (!$insertFlag) {
                            throw new ImportException('Thông tin sản phẩm không hợp lệ. Vui lòng kiểm tra lại');
                        }
                    }
                    $customerInsert = ShopCustomer::where('customer_code', $itemCustomer['ma_khach_hang'])->first();
                    $k = 1;
                    $productCode = [];
                    foreach ($excel as $keyProduct => $itemProduct) {
                        $k++;
                        $line = $keyProduct + $startRow;
                        if ($k != $line) {
                            $messageError[] = 'Vui lòng kiểm tra kỹ file excel trước khi nhập';
                            $messageError[] = 'File không hợp lệ! File trống dòng số '. $line - 1;
                            throw new \Exception('Lỗi cập nhật sản phẩm, vui lòng kiểm tra lại!');
                        }
                        if ($keyProduct < $keyCustomer) {
                            continue;
                        }
                        if ($itemProduct['ma_khach_hang'] != '' && $keyProduct != $keyCustomer) {
                            break;
                        }
                        if (in_array($itemProduct['ma_san_pham'], $productCode)) {
                            $messageError[] = 'Mã sản phẩm bị trùng lặp - Dòng '. ($keyProduct + 2);
                        }
                        if (!array_key_exists($itemProduct['ma_san_pham'], $products)) {
                            $messageError[] = 'Mã sản phẩm không hợp lệ - Dòng '. ($keyProduct + 2);
                        }
                        if (!array_key_exists($itemProduct['ma_nha_cung_cap'],$suppliers)) {
                            $messageError[] = 'Mã nhà cung cấp không hợp lệ - Dòng '. ($keyProduct + 2);
                        }
                        if (!$messageError) {
                            $dataInsertProduct = [
                                'supplier_id' => $suppliers[$itemProduct['ma_nha_cung_cap']] ?? '',
                                'customer_id' => $customerInsert->id ?? '',
                                'product_id' => $products[$itemProduct['ma_san_pham']] ?? '',
                            ];
                            $insertFlag = ShopProductSupplier::insert($dataInsertProduct);
                            if (!$insertFlag) {
                                throw new ImportException('Thông tin sản phẩm không hợp lệ. Vui lòng kiểm tra lại');
                            }
                        }
                        $productCode[] = $itemProduct['ma_san_pham'];
                    }
                }
            }

            if (!empty($messageError)) {
                throw new \Exception('Lỗi file, vui lòng kiểm tra lại!');
            }
        } catch (ImportException $e) {
            Log::debug($e);
//            dd($e);
            DB::rollBack();
            $error = !empty($errorCustomerCode) ? $errorCustomerCode : $messageError;
            return redirect()->back()->with('error_validate_import', $error)->with('error', $e->getMessage());
        } catch (\Throwable $e) {
//            dd($e);
            DB::rollBack();
            Log::debug($e);
            $error = !empty($errorCustomerCode) ? $errorCustomerCode : $messageError;
            return redirect()->back()->with('error_validate_import', $error)->with('error', $e->getMessage());
        }

        DB::commit();
        return redirect()->route('admin_customer.index')->with('success', sc_language_render("Nhập danh sách khách hàng thành công!"));
    }

    public function importNotOverwrite()
    {
        $file = request()->file('excel_file');
        $startRow = (new CustomerImport())->headingRow() + 1;
        DB::beginTransaction();
        try {
            if (!$file || !is_file($file) || !in_array($file->extension(), ['xls', 'xlsx'])) {
                throw new ImportException('Định dạng file không hợp lệ!');
            }
            $raw_excel_array = Excel::toArray(new CustomerImport(), request()->file('excel_file'));
            $excel = $raw_excel_array ? cleanExcelFile($raw_excel_array)[0] : [];
            if (count($excel) < 1) {
                throw new ImportException('File excel phải có ít nhất 1 bản ghi');
            }
            if (count($excel) > 10000) {
                throw new ImportException('Kích thước dữ liệu quá 10.000 dòng, Vui lòng tách file để tránh quá tải');
            }
            $solved_row = [];
            foreach ($excel as $row_i => $row) {
                $solved_row[] = solveRow($row,
                    [
                        'ma_khach_hang', 'ten_khach_hang', 'email', 'khach_hang_thuoc', 'so_dien_thoai', 'dia_chi',
                        'ma_khu_vuc', 'hang_khach_hang', 'ma_so_thue', 'thuoc_stt', 'thuoc_tuyen_hang',
                        'ten_hien_thi_tren_tem', 'mat_khau', 'trang_thai', 'loai_khach_hang', 'ma_giao_vien',
                        'ma_hoc_sinh', 'ten_dang_nhap_tk_hieu_truong', 'mat_khau_tk_hieu_truong'
                    ],
                    ['ma_nha_cung_cap', 'ma_san_pham', 'san_pham', 'nha_cung_cap']
                );
            }
            $validateRequired = $this->checkRequiredRow($solved_row);
            if($validateRequired){
                DB::rollBack();
                return redirect()->back()->with('error_required', $validateRequired);
            }

            $outputMain = [];
            $outputDetails = [];
            $tempMain = [];
            $real_index = 0;

            foreach ($solved_row as $row_index => $row) {
                $real_index = $row_index + $startRow;
                if ($row['type'] == 'both') {
                    $row['items']['row_index'] = $real_index;
                    $row['details']['row_index'] = $real_index;
                    $outputMain[] = $row['items'];
                    $tempMain = $row['items'];
                    $temp = $row['details'];
                    $temp['ma_khach_hang'] = $tempMain['ma_khach_hang'] ?? "";
                    $outputDetails[] = $temp;
                }
                if ($row['type'] == 'detail_only') {
                    $row['details']['row_index'] = $real_index;
                    $temp = $row['details'];
                    $temp['ma_khach_hang'] = $tempMain['ma_khach_hang'] ?? "";
                    $outputDetails[] = $temp;
                }
                if ($row['type'] == 'customer_only') {
                    $row['items']['row_index'] = $real_index;
                    $outputMain[] = $row['items'];
                    $tempMain = $row['items'];
                    $temp = $row['details'];
                    $temp['ma_khach_hang'] = $tempMain['ma_khach_hang'] ?? "";
                }
            }

            // Mapping and insert customer
            $zone = (new ShopZone)->pluck('zone_code', 'id')->toArray();
            $departments = (new ShopDepartment)->pluck('name', 'id')->toArray(); //Static data
            $tiers = (new ShopRewardTier)->pluck('name', 'id')->toArray(); //Static data
            $insertCustomer = mapingCustomerImport($outputMain, $zone, $departments, $tiers, $startRow);
            if(!empty($insertCustomer['error'])){
                DB::rollBack();
                return redirect()->back()->with('error_validate', $insertCustomer['error']);
            }
            $insertCustomer = $insertCustomer['data'] ?? [];

            $dbCustomer = (new ShopCustomer)->get()->toArray();
            $codeInsertList = data_get($insertCustomer, '*.customer_code');
            $schoolmasterCodeInsertList = data_get($insertCustomer, '*.schoolmaster_code');
            $codeDBList = data_get($dbCustomer, '*.customer_code');
            $schoolmasterCodeDBList = data_get($dbCustomer, '*.schoolmaster_code');
            $nameInsertList = data_get($insertCustomer, '*.name');
            $nameDBList = data_get($dbCustomer, '*.name');

            $dupticatedErrorBags = [];
            foreach (my_array_reverse($insertCustomer) as $key => $customer) {
                if (isDupticatedItem(array_keys(array_merge($codeInsertList, $codeDBList), $customer['customer_code']))) {
                    $dupticatedErrorBags[$key][] = "Mã khách hàng";
                };
                if (isDupticatedItem(array_keys(array_merge($schoolmasterCodeInsertList, $schoolmasterCodeDBList), $customer['schoolmaster_code']))) {
                    $dupticatedErrorBags[$key][] = "Tên đăng nhập hiệu trưởng";
                };
                if (isDupticatedItem(array_keys(array_merge($nameInsertList, $nameDBList), $customer['name']))) {
                    $dupticatedErrorBags[$key][] = "Tên khách hàng";
                };
            }
            if(!empty($dupticatedErrorBags)){
                DB::rollBack();
                return redirect()->back()->with('error_dupticated', $dupticatedErrorBags);
            }
            $failedCustomer = [];
            foreach ($insertCustomer as $key => $customer) {
                $insertObj = (new ShopCustomer())->fill($customer);
                if (!$insertObj->save()) {
                    $failedCustomer[] = $key;
                }
            }
            $products = (new ShopProduct)->pluck('sku', 'id')->toArray(); //Static data
            $suppliers = (new ShopSupplier)->pluck('supplier_code', 'id')->toArray(); //Static data
            $customers = (new ShopCustomer)->pluck('customer_code', 'id')->toArray(); //Static data

            $insertProduct = mapingProductDetail($outputDetails, $products, $suppliers, $customers, $startRow);
            if(!empty($insertProduct['error'])){
                DB::rollBack();
                return redirect()->back()->with('error_validate', $insertProduct['error']);
            }
            $insertProduct = $insertProduct['data'] ?? [];
            $arrayCustomerSearch = ShopCustomer::whereIn('customer_code', data_get($insertCustomer, '*.customer_code'))->get();
            foreach (array_unique(data_get($arrayCustomerSearch, '*.id')) as $customer) {
                $customerProductList = array_filter($insertProduct, function ($item) use ($customer) {
                    return ($item['customer_id'] == $customer) ? $item : [];
                });
                $array_compare = data_get($customerProductList, '*.product_id');
                foreach (my_array_reverse($customerProductList) as $key => $product) {
                    if (count(array_keys($array_compare, $product['product_id'])) > 1) {
                        $dupticatedErrorBags[$key][] = "Sản phẩm";
                    }
                }
            }
            if(!empty($dupticatedErrorBags)){
                DB::rollBack();
                return redirect()->back()->with('error_dupticated', $dupticatedErrorBags);
            }
            $insertFlag = ShopProductSupplier::insert($insertProduct);
            if (!$insertFlag) {
                throw new ImportException('Thông tin sản phẩm không hợp lệ. Vui lòng kiểm tra lại');
            }
        } catch (QueryException $e) {
            Log::error($e);
            DB::rollBack();
            return redirect()->back()->with('error', "Lỗi không xác định, vui lòng liên hệ bộ phận kĩ thuật");
        } catch (ImportException $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage());
        } catch (\Throwable $e) {
            Log::error($e);
            DB::rollBack();
            return redirect()->back()->with('error', "Lỗi không xác định, vui lòng liên hệ bộ phận kĩ thuật");
        }
        DB::commit();
        return redirect()->route('admin_customer.index')->with('success', sc_language_render("Nhập danh sách khách hàng thành công!"));
    }

    public function clone()
    {
        if (!request()->ajax()) {
            return response()->json(['error' => 1, 'msg' => sc_language_render('admin.method_not_allow')]);
        }
        $id = request('id') ?? '';
        $customer = AdminCustomer::find($id);
        if (!$customer) {
            throw new ImportException('Không tìm thấy khách hàng');
        }
        DB::connection(SC_CONNECTION)->beginTransaction();
        try {
            //Product info
            $dataCustomer = \Illuminate\Support\Arr::except($customer->toArray(), ['id', 'created_at', 'updated_at']);
            $dataCustomer['customer_code'] = $dataCustomer['customer_code'] . '-COPY-' . time();
            $dataCustomer['email'] = $dataCustomer['email'] . '-COPY-' . time();
            $dataCustomer['name'] = $dataCustomer['name'] . '-COPY-' . time();
            $dataCustomer['password'] = bcrypt("khachhang@davicorp");

            $newCustomer = (new AdminCustomer)->fill($dataCustomer);
            if (!$newCustomer->save()) {
                throw new ImportException('Nhân bản thất bại');
            }

            if (!empty($customer->productSuppliers)) {
                foreach ($customer->productSuppliers as $productSupplier) {
                    $newProductSupplierData = Arr::except($productSupplier, ['customer_id', 'id'])->toArray();
                    $newProductSupplierData['customer_id'] = $newCustomer->id ?? 0;
                    if (!(new ShopProductSupplier)->fill($newProductSupplierData)->save()) {
                        throw new ImportException("Lỗi nhân bản thông tin sản phẩm và nhà cung cấp. Vui lòng kiểm tra lại");
                    }
                }
            }
        } catch (Throwable $e) {
            DB::connection(SC_CONNECTION)->rollBack();
            return response()->json(['error' => 1, 'msg' => $e->getMessage()]);
        }
        DB::connection(SC_CONNECTION)->commit();
        return response()->json(['error' => 0, 'msg' => sc_language_render('customer.admin.clone_success')]);
    }
    function checkRequiredRow(array $data){
        $master = [
            "ma_khach_hang" => "Mã khách hàng",
            "ten_khach_hang" => "Tên khách hàng",
            "khach_hang_thuoc" => "Khách hàng thuộc",
            "hang_khach_hang" => "Hạng khách hàng",
            "thuoc_stt" => "Thuộc STT",
            "ten_hien_thi_tren_tem" => "Tên hiển thị trên tem",
            "thuoc_tuyen_hang" => "Thuộc tuyến hàng",
            "ma_khu_vuc" => "Mã khu vực"
        ];
        $detail = [
            "ma_san_pham" => "Mã sản phẩm",
            "ma_nha_cung_cap" => "Mã nhà cung cấp"
        ];
        $master_col = array_keys($master);
        $detail_col = array_keys($detail);
        $errorBags = [];

        foreach ($data as $index => $item){
            $row = $index + 2;
            switch ($item["type"]){
                case "both":
                    $masterCheck = $this->getNullCol($item['items'], $master);
                    $detailCheck = $this->getNullCol($item['details'], $detail);
                    if($masterCheck){
                        $errorBags[$row] = array_merge($masterCheck, $errorBags[$row] ?? []);
                    }
                    if($detailCheck){
                        $errorBags[$row] = array_merge($detailCheck, $errorBags[$row] ?? []);
                    }
                    break;
                case "customer_only":
                    $masterCheck = $this->getNullCol($item['items'], $master);
                    if($masterCheck){
                        $errorBags[$row] = array_merge($masterCheck, $errorBags[$row] ?? []);
                    }
                    break;
                case "detail_only":
                    $detailCheck = $this->getNullCol($item['details'], $detail);
                    if($detailCheck){
                        $errorBags[$row] = array_merge($detailCheck, $errorBags[$row] ?? []);
                    }
                    break;
            }
        }
        return empty($errorBags) ? false : $errorBags;
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
     * Xử lý update dữ liệu customer
     * @param $data
     * @return array
     */
    public function getDataUpdateImportCustomer($data) {
        $listData = [];
        if ($data['ten_khach_hang'] != '') {
            $listData['name'] = $data['ten_khach_hang'];
        }
        if ($data['ten_dang_nhap_tk_hieu_truong'] != '') {
            $listData['schoolmaster_code'] = $data['ten_dang_nhap_tk_hieu_truong'];
        }
        if ($data['email'] != '') {
            $listData['email'] = $data['email'];
        }
        if ($data['so_dien_thoai'] != '') {
            $listData['phone'] = $data['so_dien_thoai'];
        }
        if ($data['khach_hang_thuoc'] != '') {
            $department = ShopDepartment::where('name', $data['khach_hang_thuoc'])->first();
            if ($department) {
                $listData['department_id'] = $department->id;
            }
        }
        if ($data['hang_khach_hang'] != '') {
            $tier = ShopRewardTier::where('name', $data['hang_khach_hang'])->first();
            if ($tier) {
                $listData['tier_id'] = $tier->id;
            }
        }
        if ($data['ma_so_thue'] != '') {
            $listData['tax_code'] = $data['ma_so_thue'];
        }
        if ($data['thuoc_stt'] != '' && $data['thuoc_stt'] > 0) {
            $listData['order_num'] = $data['thuoc_stt'];
        }
        if ($data['ten_hien_thi_tren_tem'] != '') {
            $listData['short_name'] = $data['ten_hien_thi_tren_tem'];
        }
        if ($data['thuoc_tuyen_hang'] != '' ) {
            $listData['route'] = $data['thuoc_tuyen_hang'];
        }
        if ($data['dia_chi'] != '') {
            $listData['address'] = $data['dia_chi'];
        }
        if ($data['ma_khu_vuc'] != '') {
            $zone = ShopZone::where('zone_code', $data['ma_khu_vuc'])->first();
            if ($zone) {
                $listData['zone_id'] = $zone->id;
            }
        }
//        if ($data['mat_khau'] != '') {
//            $listData['password'] = $data['mat_khau'];
//        }
        if ($data['loai_khach_hang'] != '') {
            $listData['kind'] = $data['loai_khach_hang'] == 'CTY HĐ CT' ? 1 : ( $data['loai_khach_hang'] == 'TH HĐ CTY' ? 2 : ( $data['loai_khach_hang'] == 'HĐ CH' ? 0 : 3));
        }
        if ($data['ma_giao_vien'] != '') {
            $listData['teacher_code'] = $data['ma_giao_vien'];
        }
        if ($data['ma_hoc_sinh'] != '') {
            $listData['student_code'] = $data['ma_hoc_sinh'];
        }

        return $listData;
    }

    /**
     * Xử lý lỗi khi Insert dữ liệu Customer.
     * @param $data
     * @return array
     */
    public function checkErrorNullCustomer($data) {
        $listError = [];
        if ($data['ten_khach_hang'] == '') {
            $listError[] = 'Tên khách hàng bị trống  - Dòng ';
        }
        if ($data['ten_dang_nhap_tk_hieu_truong'] == '') {
            $listError[] = 'Tên đăng nhập hiệu trưởng bị trống  - Dòng ';
        }
        if ($data['khach_hang_thuoc'] == '') {
            $listError[] = 'Khách hàng thuộc bị trống  - Dòng ';
        }
        if ($data['hang_khach_hang'] == '') {
            $listError[] = 'Hạng khách hàng bị trống  - Dòng ';
        }
        if ($data['thuoc_stt'] == '' || $data['thuoc_stt'] < 0) {
            $listError[] = 'Thuộc STT bị trống hoặc không hơp lệ  - Dòng ';
        }
        if ($data['ten_hien_thi_tren_tem'] == '') {
            $listError[] = 'Tên hiển thị trên tem bị trống  - Dòng ';
        }
        if ($data['thuoc_tuyen_hang'] == '') {
            $listError[] = 'thuộc tuyến hàng bị trống  - Dòng ';
        }
        if ($data['ma_khu_vuc'] == '') {
            $listError[] = 'Mã khu vực bị trống  - Dòng ';
        }
        if ($data['ten_khu_vuc'] == '') {
            $listError[] = 'Tên khu vực bị trống  - Dòng ';
        }
        if ($data['loai_khach_hang'] == '') {
            $listError[] = 'Loại khách hàng bị trống  - Dòng ';
        }

        return $listError;
    }
}