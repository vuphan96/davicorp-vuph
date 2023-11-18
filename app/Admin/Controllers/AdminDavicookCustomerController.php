<?php

namespace App\Admin\Controllers;

use App\Admin\Models\AdminDavicookCustomer;
use App\Admin\Models\AdminDish;
use App\Admin\Models\AdminProduct;
use App\Admin\Models\AdminReward;
use App\Exceptions\ImportException;
use App\Exports\AdminDavicookCustomerExportMulti;
use App\Exports\CustomerExport;
use App\Front\Models\ShopDavicookCustomer;
use App\Front\Models\ShopDavicookMenu;
use App\Front\Models\ShopDavicookMenuDetail;
use App\Front\Models\ShopDavicookProductSupplier;
use App\Front\Models\ShopDepartment;
use App\Front\Models\ShopDish;
use App\Front\Models\ShopProduct;
use App\Front\Models\ShopRewardTier;
use App\Front\Models\ShopSupplier;
use App\Front\Models\ShopZone;
use App\Http\Requests\Admin\AdminDavicookCustomerRequest;
use App\Http\Requests\Admin\AdminProductSupplierDavicookRequest;
use App\Http\Requests\Admin\AdminProductSupplierRequest;
use App\Http\Requests\Admin\AdminAddDishCustomerRequest;
use App\Imports\CustomerImport;
use App\Imports\DavicookCustomerImport;
use App\Imports\DavicookDishCustomerImport;
use App\Imports\DavicookProductSupplierImport;
use http\Env\Response;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use SCart\Core\Admin\Controllers\RootAdminController;
use SCart\Core\Front\Controllers\Auth\AuthTrait;
use SCart\Core\Front\Models\ShopCountry;
use SCart\Core\Front\Models\ShopLanguage;
use Throwable;
use Exception;


class AdminDavicookCustomerController extends RootAdminController
{
    use AuthTrait;

    public $languages;
    public $countries;
    public $customerDavicook;

    public function __construct(AdminDavicookCustomer $customer)
    {
        parent::__construct();
        $this->languages = ShopLanguage::getListActive();
        $this->countries = ShopCountry::getListAll();
        $this->customerDavicook = $customer;
    }

    /**
     * Show list customer davicook.
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function index()
    {
        $data = [
            'title' => sc_language_render('customer.admin.list'),
            'subTitle' => '',
            'icon' => 'fa fa-indent',
            'urlDeleteItem' => sc_route_admin('admin.davicook_customer.delete_list_customer'),
            'urlExportExcel' => sc_route_admin('admin.davicook_customer.export_list_customer'),
            'removeList' => 1, // 1 - Enable function delete list item
            'buttonRefresh' => 0, // 1 - Enable button refresh
            'buttonSort' => 1, // 1 - Enable button sort
            'css' => '',
            'js' => '',
            'method' => 'delete' ,
            'urlExport' => sc_route_admin('admin_customer.export'),
            'permGroup' => 'davicook_customer'
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
            'customer_code' => 'width: 100px',
            'name' => 'width: 250px',
            'email' => 'width: 200px',
            'phone' => 'width: 200px',
            'address' => 'width: 320px',
            'status' => 'width: 100px; text-align: center',
            'action' => 'width: 100px',
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
        $dataTmp = $this->customerDavicook->getListDavicookCustomerAdmin($dataSearch);

        $dataTr = [];

        foreach ($dataTmp as $key => $row) {
            $dataTr[$row['id']] = [
                'customer_code' => $row['customer_code'] ?? '',
                'name' => $row['name']  ?? '',
                'email' => $row['email']  ?? '',
                'phone' => $row['phone']  ?? '',
                'address' => $row['address']  ?? '',
                'status' => $row['status'] ? '<span class="badge badge-success">ON</span>' : '<span class="badge badge-danger">OFF</span>',
                'action' => '
                    <a data-perm="davicook_customer:detail" href="' . sc_route_admin('admin.davicook_customer.edit', ['id' => $row['id'] ? $row['id'] : 'not-found-id']) . '"><span title="' . sc_language_render('action.edit') . '" type="button" class="btn btn-flat btn-sm btn-primary"><i class="fa fa-edit"></i></span></a>
                    <span data-perm="davicook_customer:create" onclick="cloneCustomerDavicook(\'' . $row['id'] . '\');" title="' . sc_language_render('action.clone') . '" type="button" class="btn btn-flat btn-sm btn-secondary">
                        <i class="fa fa-clipboard"></i>
                    </span>
                    <span data-perm="davicook_customer:delete" onclick="deleteItem(\'' . $row['id'] . '\');"  title="' . sc_language_render('action.delete') . '" class="btn btn-flat btn-sm btn-danger"><i class="fas fa-trash-alt"></i></span>'
                ,
            ];
        }

        $data['listTh'] = $listTh;
        $data['dataTr'] = $dataTr;
        $data['pagination'] = $dataTmp->appends(request()->except(['_token', '_pjax']))->links($this->templatePathAdmin . 'component.pagination');
        $data['resultItems'] = sc_language_render('admin.result_item', ['item_from' => $dataTmp->firstItem(), 'item_to' => $dataTmp->lastItem(), 'total' => $dataTmp->total()]);

        //menuRight
        $data['menuRight'][] = '<a data-perm="davicook_customer:create" href="' . sc_route_admin('admin.davicook_customer.create') . '" class="btn  btn-success  btn-flat" title="New" id="button_create_new">
                           <i class="fa fa-plus" title="' . sc_language_render('admin.add_new') . '"></i>
                           </a>
                           <a data-perm="davicook_customer:import" href="' . sc_route_admin('admin.davicook_customer.import_list_customer') . '" class="btn  btn-success  btn-flat" title="New" id="button_import">
                            <i class="fa fa-file-import" title="' . sc_language_render('category-import') . '"></i> ' . sc_language_render('category-import') .
            '</a>
                            <button data-perm="davicook_customer:export" type="button" class="btn  btn-success  btn-flat" title="Xuất excel" id="btn_export_customer">
                            <i class="fa fa-file-export" title="' . sc_language_render('category-export') . '"></i> ' . sc_language_render('category-export') .
            '</button>';
        //=menuRight

        //menuSort
        $optionSort = '';
        foreach ($arrSort as $key => $status) {
            $optionSort .= '<option  ' . (($sort_order == $key) ? "selected" : "") . ' value="' . $key . '">' . $status . '</option>';
        }
        $data['urlSort'] = sc_route_admin('admin.davicook_customer.index', request()->except(['_token', '_pjax', 'sort_order']));
        $data['optionSort'] = $optionSort;
        //=menuSort

        //menuSearch
        $data['topMenuRight'][] = '
                <form action="' . sc_route_admin('admin.davicook_customer.index') . '" id="button_search">
                <div class="input-group input-group" style="width: 350px;">
                    <input type="text" id="keyword" name="keyword" class="form-control rounded-0 float-right" placeholder="Tìm theo tên hoặc Mã Khách hàng" value="' . $keyword . '">
                    <div class="input-group-append">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i></button>
                    </div>
                </div>
                </form>';
        //=menuSearch

        return view($this->templatePathAdmin . 'screen.davicook_customer.index')
            ->with($data);
    }

    /**
     * Show form submit create customer.
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
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
            'url_action' => sc_route_admin('admin.davicook_customer.create'),
            'tiers' => ShopRewardTier::all(),
            'departments' => ShopDepartment::all(),
            'is_edit' => 0,
            'data_perm_submit' => 'davicook_customer:create'
        ];

        return view($this->templatePathAdmin . 'screen.davicook_customer.create.customer')
            ->with($data);
    }

    /**
     * Handle create customer.
     * @param AdminDavicookCustomerRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postCreate(AdminDavicookCustomerRequest $request)
    {
        // Get validated value status
        $data = $request->validated();
        $data['status'] = empty($data['status']) ? 0 : 1;
        $data['kind'] = $data['kind'] ?? 0 ;

        try {
            $dataInsert = [
                "name" => $data['name'],
                "short_name" => $data['short_name'],
                "customer_code" => $data['customer_code'],
                "email" => $data['email'],
                "phone" => $data['phone'],
                "tax_code" => $data['tax_code'],
                "order_num" => $data['order_num'],
                "route" => $data['route'],
                "address" => $data['address'],
                "zone_id" => $data['zone_id'],
                "status" => $data['status'],
                "department_id" => 4,
                "serving_price" => $data['serving_price'],

            ];
            DB::beginTransaction();
            $dataInsert = sc_clean($dataInsert, [], true);
            $customer = $this->customerDavicook::create($dataInsert);// Insert Customer
            $id = $customer->id;
            if (!$customer) {
                throw new \Exception(sc_language_render('action.failed'));
            }

        } catch (\Throwable $e) {
            DB::rollBack();
            return redirect()->back()->withInput($data)->with('error', $e->getMessage());
        }
        DB::commit();
        return redirect()->route('admin.davicook_customer.edit',['id' => $id ?? ''])->with('success', sc_language_render('action.create_success'));
    }

    /**
     * Show edit customer.
     * @param $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     */
    public function edit($id)
    {
        $objSupplier = (new ShopSupplier)->getSupplierListAdmin();
        $customer = $this->customerDavicook::findOrFail($id);
        if (!$customer) {
            return redirect()->route('admin.data_not_found')->with(['url' => url()->full()]);
        }

        $customerProducts = (new ShopDavicookProductSupplier())->with(['product', 'supplier'])->where('customer_id', $id);
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
        $customerProducts = $customerProducts->paginate(config('pagination.admin.small'),['*'], 'product_page');
        $data['menuRight'] = sc_config_group('menuRight', \Request::route()->getName());
        $data['topMenuRight'] = sc_config_group('topMenuRight', \Request::route()->getName());

        $dish_name = sc_clean(request('dish_name') ?? '');
        $dish_code = sc_clean(request('dish_code') ?? '');
        $listTh = [
            'dish_code' => sc_language_render('admin.dish.code'),
            'dish_name' => sc_language_render('admin.dish.name'),
            'item' => sc_language_render('admin.dish.item'),
            'total_cost' => 'Tổng cost món ăn', 
            'action' => sc_language_render('action.title'),
        ];
        $cssTd = [
            'dish_code' => 'width: 90px',
            'dish_name' => 'width: 130px',
            'total_cost' => 'width: 150px', 
            'action' => 'width: 220px',
        ];

        $dataTr = [];
        $itemDish = '';
        $dataMenu = (new AdminDish())->getListDishCustomers($id, $dish_name, $dish_code);
        $count_modal = 1;
        $today = now()->format('Y-m-d');

        foreach ($dataMenu as $key => $row) {
            $sum_total_cost = 0;
            foreach ($row->details as $item) {
                $import_price = (new AdminDavicookCustomer())->getImportPriceToLatestPriceTable($id, $item->product_id, $today) ?? 0; // Giá nguyên liệu thời điểm hiện tại
                $product_name = $item->product ? $item->product->getName() : '<span style="color: red">Nguyên liệu đã bị xóa</span>';
                $qty = $item->qty ? preg_replace("/\.?0*$/", '', $item->qty) : 1;
                $unit = $item->product ? ($item->product->unit ? $item->product->unit->name : '') : '';
                $itemDish .= '<p>' . $product_name . '(' . $qty . $unit . ')</p>';
                $sum_total_cost += round($import_price * $qty);
            }
            $name = $row->dish ? $row->dish->name : 'Món ăn đã bị xóa';
            $is_export_menu = $row->is_export_menu;
            $qty_cooked_dish = $row->qty_cooked_dish;
            $dataTr[$row['id']] = [
                'code' => $row->dish ? $row->dish->code : 'Mã món ăn đã bị xóa',
                'name' => $row->dish ? $row->dish->name : 'Món ăn đã bị xóa',
                'item' => $itemDish  ?? '',
                'total_cost' => sc_currency_render($sum_total_cost ?? 0, 'vnd'),
                'action' => '
                    <a data-perm="davicook_customer:edit" href="#"><span title="' . sc_language_render('action.edit') . '" type="button" 
                    class="btn btn-flat btn-sm btn-primary button-edit-dish-modal" 
                    data-modal_id=" ' . $count_modal++ . ' " data-id="' . $row['id'] . ' " data-title="' . " $name " . '" data-qty_cooked_dish=" ' . $qty_cooked_dish . ' "
                    data-is_export_menu="' . " $is_export_menu " . '"  data-toggle="modal" data-target="#edit-dish-modal"><i class="fa fa-edit"></i></span></a>
                    <a data-perm="davicook_customer:edit" href="#"><span title="' . sc_language_render('action.clone') . '" 
                    type="button" class="btn btn-flat btn-sm btn-secondary button-clone-dish-modal" data-modal_id=" ' . $count_modal++ . ' " data-qty_cooked_dish=" ' . $qty_cooked_dish . ' "
                    data-id="' . $row['id'] . ' " data-toggle="modal" data-target="#clone-dish-modal"><i class="fa fa-clipboard"></i></span></a>
                    <span data-perm="davicook_customer:edit" onclick="deleteItem(\'' . $row['id'] . '\');"  title="' . sc_language_render('action.delete') . '" 
                    class="btn btn-flat btn-sm btn-danger"><i class="fas fa-trash-alt"></i></span>',
            ];
            $itemDish = '';
        }
        $data = [
            'title' => sc_language_render('action.edit'),
            'cssTd' =>  $cssTd,
            'subTitle' => '',
            'title_dish' => sc_language_render('admin.dish.list'),
            'icon' => 'fa fa-edit',
            'customer' => $customer,
            'addresses' => $customer->addresses,
            'listSupplier' => $objSupplier,
            'currentSearchSupplier' => ShopSupplier::find($supplier_search),
            'url_action' => sc_route_admin('admin.davicook_customer.edit', ['id' => $customer['id']]),
            'customerProducts' => $customerProducts,
            'is_edit' => 1,
            'method' => 1,
            'products' => $customer->productSuppliers,
            'urlDeleteItem' => sc_route_admin('admin.davicook_customer.delete_list_dish_customer'),
            'removeList' => 1,
            'data_perm_submit' => 'davicook_customer:edit'
        ];
        $data['listTh'] = $listTh;
        $data['dataTr'] = $dataTr;
        //menuRight
        $data['menuRight'][] = '<button data-perm="davicook_customer:edit" type="button" class="btn  btn-success  btn-flat" data-toggle="modal" data-target="#dish-modal">
                                        <i class="fa fa-plus"></i> Thêm món ăn
                                    </button>';
        //=menuRight
        // paginate
        $data['pagination'] = $dataMenu->appends(request()->except(['_token', '_pjax']))->links($this->templatePathAdmin . 'component.pagination');
        $data['resultItems'] = sc_language_render('admin.result_item', ['item_from' => $dataMenu->firstItem(), 'item_to' => $dataMenu->lastItem(), 'total' => $dataMenu->total()]);
        //menuSearch
        $data['topMenuRight'][] = '
                <form action="' . sc_route_admin('admin.davicook_customer.edit', ['id' => $customer['id']]) . '" id="button_search">
                <div class="input-group input-group" style="padding-left: 130px; width: 500px">
                    <div class="form-group" style="margin-right: 10px; width: 160px">
                        <label>Tên món ăn:</label>
                        <div class="input-group">
                            <input type="text" name="dish_name" class="form-control rounded-0 float-right" placeholder="Tên món ăn" value="'.$dish_name.'">
                        </div>
                    </div>
                    <div class="form-group" style="width: 200px">
                        <label>Mã món ăn:</label>
                        <div class="input-group">
                            <input type="text" name="dish_code" class="form-control rounded-0 float-right" placeholder="Mã món ăn" value="'.$dish_code.'">
                            <div class="input-group-append">
                                <button type="submit" class="btn btn-primary  btn-flat"><i class="fas fa-search"></i></button>
                            </div>
                        </div>
                    </div>
                </div>
                </form>';
        //=menuSearch
        return view($this->templatePathAdmin . 'screen.davicook_customer.edit.customer')
            ->with($data);
    }

    /**
     * Handle update info customer
     * @param $id
     * @param AdminDavicookCustomerRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postEdit($id, AdminDavicookCustomerRequest $request)
    {
        // Global error indicate
        $data = $request->validated();
        $data['status'] = empty($data['status']) ? 0 : 1;
        $data['kind'] = $data['kind'] ?? 0 ;

        // Process update
        try {
            $dataUpdate = [
                "name" => $data['name'],
                "short_name" => $data['short_name'],
                "customer_code" => $data['customer_code'],
                "email" => $data['email'],
                "phone" => $data['phone'],
                "tax_code" => $data['tax_code'],
                "order_num" => $data['order_num'],
                "route" => $data['route'],
                "address" => $data['address'],
                "zone_id" => $data['zone_id'],
                "status" => $data['status'],
                "serving_price" => $data['serving_price'],
            ];
            DB::beginTransaction();
            $customer = $this->customerDavicook::findOrFail($id);
            $customerUpdate = $customer->update($dataUpdate);
            if (!$customerUpdate) {
                throw new \Exception(sc_language_render('action.failed'));
            }
        } catch (\Throwable $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage());
        }
        DB::commit();
        return redirect()->route('admin.davicook_customer.index')->with('success', sc_language_render('action.edit_success'));
    }

    /**
     * Create product with supplier for customer.
     * @param AdminProductSupplierDavicookRequest $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postAddProductSupplier(AdminProductSupplierDavicookRequest $request, $id)
    {
        $data = $request->validated();
        $data['customer_id'] = $id;
        $allProduct = ShopDavicookProductSupplier::where('customer_id', $id)->pluck('product_id', 'id')->toArray();
        if (in_array($data['product_id'], $allProduct)) {
            return back()->with('product_error', "Lưu sản phẩm bị trùng, vui lòng kiểm tra lại");
        }
        try {
            $dataInsert = ShopDavicookProductSupplier::create($data);
            if (!$dataInsert) {
                throw new \Exception(sc_language_render('action.failed'));
            }
        } catch (Throwable $e) {
            return redirect()->back()->with('error',$e->getMessage());
        }
        return redirect()->back()->with('success', sc_language_render('action.create_success'));
    }

    /**
     * Update product with supplier for customer.
     * @param AdminProductSupplierRequest $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postUpdateProductSupplier(AdminProductSupplierRequest $request, $id)
    {
        $allProduct = ShopDavicookProductSupplier::where('customer_id', $id)->pluck('product_id', 'id')->toArray();

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

            $updateItem = ShopDavicookProductSupplier::find($key);
            if ($updateItem->product_id != $products[$key] || $updateItem->supplier_id != $suppliers[$key]) {
                $updateItem->fill($updateData);
                if (!$updateItem->save()) {
                    return back()->with('product_error', "Lưu sản phẩm thất bại");
                }
                $allProduct[$key] = $updateItem->product_id;
            }

        }
        return redirect()->back()->with('success', 'cập nhât sản phẩm thành công');
    }

    /**
     * Tạo mới món ăn.
     * @return \Illuminate\Http\JsonResponse
     */
    public function createMenuDishCustomer()
    {
        $dish_id = $_POST['dish_id'];
        $customer_id = $_POST['customer_id'];
        $product_id = $this->removeEmptyElement($_POST['product_id'] ?? []) ;
        $is_export_menu = request('is_export_menu');
        $qty_cooked_dish = request('qty_cooked_dish') ?? '';
        $is_spice = request('is_spice') ?? [];
        $quantitative = $this->removeEmptyElement($_POST['quantitative'] ?? []) ;
        $objDishCustomer = ShopDavicookMenu::where('customer_id', $customer_id)->pluck('dish_id');
        $checkDish = $this->findDishArray(trim($dish_id), $objDishCustomer);
        if (!$product_id) {
            return Response()->json(['error' => 1, 'msg' => 'Vui lòng thêm nguyên liệu cho món ăn']);
        }
        if ($checkDish) {
            return Response()->json(['error' => 1, 'msg' => 'Món ăn đã tồn tại trên hệ thống']);
        }
        $productIdUnique = array_unique($product_id);
        if (count($product_id) != count($productIdUnique)) {
            $product_dupes = array_diff_key($product_id, $productIdUnique );
            $nameProduct = $this->getNameProductDupes($product_dupes);
            return Response()->json(["error" => 1, "msg" => "$nameProduct : đã chọn lại nhiều lần"]);
        }
        try {
            DB::beginTransaction();
            $dataInsertMenu = [
                'customer_id'  => $customer_id,
                'dish_id' => $dish_id,
                'is_export_menu' => $is_export_menu,
                'qty_cooked_dish' => $qty_cooked_dish,
            ];
            $dataMenu = ShopDavicookMenu::create($dataInsertMenu);
            if($dataMenu) {
                $menu_id = $dataMenu->id;
                foreach ($product_id as $key => $item) {
                    $dataInsertdetail = [
                        'menu_id' => $menu_id,
                        'product_id' => $product_id[$key],
                        'qty' => $quantitative[$key],
                        'is_spice' => $is_spice[$key],
                        'customer_id'  => $customer_id,
                    ];
                    $dataMenuDetail = ShopDavicookMenuDetail::create($dataInsertdetail);
                    if(!$dataMenuDetail) {
                        throw new \Exception(sc_language_render('action.failed'));
                    }
                }
            } else {
                throw new \Exception(sc_language_render('action.failed'));
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return Response()->json(['error' => 1, 'msg' => $e->getMessage()]);
        }
        DB::commit();
        return Response()->json(['success' => 0, 'msg' => sc_language_render('action.create_success')]);
    }

    /**
     * Loại bỏ mảng trống
     * @param $object
     * @return array
     */
    public function removeEmptyElement($object) {
        $object = array_filter($object);
        return $object;
    }

    /**
     * Lấy tên các nguyên liệu.
     * @param $arrayItemDupes
     * @return string
     */
    public function getNameProductDupes($arrayItemDupes)
    {
        $key = 0;
        $nameProduct = '';
        foreach ($arrayItemDupes as $id_item) {
            if ($key == 0) {
                $nameProduct = ShopProduct::findOrFail($id_item)->getName();
            }
            $key++;
        }
        return $nameProduct;

    }

    /**
     * Check món ăn có nằm trong danh sách không.
     * @param $input
     * @param $object
     * @return bool
     */
    public function findDishArray($input, $object)
    {
        $dish_search = $object->toArray();
        $search_result = in_array($input, $dish_search);
        if ($search_result) {
            return $search_result;
        }
        return false;
    }

    /**
     * Handle delete customer.
     * @return \Illuminate\Http\JsonResponse
     */
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
            AdminDavicookCustomer::destroy($arrID);
            return response()->json(['error' => 0, 'msg' => '']);
        }
    }

    /**
     * Lấy thông tin khách hàng để check xóa.
     * @param $id
     * @return mixed
     */
    public function checkPermisisonItem($id)
    {
        return (new AdminDavicookCustomer())->getCustomerAdmin($id);
    }

    /**
     * Handle delete product supplier for customer davicook.
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function productCustomerRemove($id)
    {
        if (ShopDavicookProductSupplier::find($id)->delete()) {
            return redirect()->back()->with('success', 'Xoá sản phẩm thành công');
        }
        return back()->with('error', sc_language_render('action.failed'));
    }
    /**
     * Xóa món ăn
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteListDishCustomer()
    {
        if (!request()->ajax()) {
            return response()->json(['error' => 1, 'msg' => sc_language_render('admin.method_not_allow')]);
        }
        $ids = request('ids');
        $arrID = explode(',', $ids);
        ShopDavicookMenu::destroy($arrID);
        return response()->json(['error' => 0, 'msg' => sc_language_render('action.delete_success')]);
    }

    /**
     * Show chi tiết nguyên liệu từng món ăn.
     * @return \Illuminate\Http\JsonResponse
     */
    public function getListIngredientDish()
    {
        $id = request('id');
        $data = [];
        $today = now()->format('Y-m-d');
        $dataMenu = (new ShopDavicookMenu())->with('details')->where('id', $id)->get();
        $sum_total_cost = 0;

        foreach ($dataMenu as $item) {
            foreach ($item->details as $key => $row) {
                $import_price = $this->customerDavicook->getImportPriceToLatestPriceTable($row->customer_id ?? '', $row->product_id ?? '', $today) ?? 0; // Lấy giá nguyên liệu thời điểm hiện tại
                $sum_total_cost += round($import_price * floatval($row->qty));
                $data[$key] = [
                    'key' => $key ?? '',
                    'product_id' => $row->product_id ?? '',
                    'product_name' => $row->product ? $row->product->getName() : 'Nguyên liệu đã bị xóa',
                    'qty' => $row->qty ? floatval($row->qty) : 0,
                    'is_spice' => $row->is_spice,
                    'unit' => $row->product ? ($row->product->unit ? $row->product->unit->name : '') : '',
                    'import_price' => $import_price,
                    'sum_total_cost' => $sum_total_cost
                ];
            }
        }

        return response()->json($data);
    }

    /**
     * Update chi tiết món ăn.
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateListIngredientDish()
    {
        $product_id = $this->removeEmptyElement($_POST['product_id'] ?? []) ;
        $quantitative = $this->removeEmptyElement($_POST['quantitative'] ?? []) ;
        $menu_id = \request('menu_id');
        $is_export_menu = \request('is_export_menu');
        $qty_cooked_dish = \request('qty_cooked_dish') ?? '';
        $is_spice = \request('is_spice');
        ShopDavicookMenu::find($menu_id)->update([
            'is_export_menu' => $is_export_menu,
            'qty_cooked_dish' => $qty_cooked_dish,
        ]);
        $customer_id = \request('customer_id');
        if (!$product_id) {
            return Response()->json(['error' => 1, 'msg' => 'Vui lòng thêm nguyên liệu cho món ăn']);
        }
        $productIdUnique = array_unique($product_id);
        if (count($product_id) != count($productIdUnique)) {
            $product_dupes = array_diff_key($product_id, $productIdUnique );
            $nameProduct = $this->getNameProductDupes($product_dupes);
            return Response()->json(["error" => 1, "msg" => "$nameProduct : đã chọn lại nhiều lần"]);
        }
        try {
            DB::beginTransaction();
            $resultDelete = ShopDavicookMenuDetail::where('menu_id', $menu_id)->delete();
            if ($resultDelete) {
                foreach ($product_id as $key => $item) {
                    $dataInsertdetail = [
                        'menu_id' => $menu_id,
                        'product_id' => $product_id[$key],
                        'qty' => $quantitative[$key],
                        'is_spice' => $is_spice[$key],
                        'customer_id' => $customer_id
                    ];
                    $dataMenuDetail = ShopDavicookMenuDetail::create($dataInsertdetail);
                    if(!$dataMenuDetail) {
                        throw new \Exception(sc_language_render('action.failed'));
                    }
                }
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return Response()->json(['error' => 1, 'msg' => $e->getMessage()]);
        }
        DB::commit();
        return Response()->json(['error' => 0, 'msg' => sc_language_render('action.update_success')]);

    }

    /**
     * Handle clone customer
     * @return \Illuminate\Http\JsonResponse
     * @throws Throwable
     */
    public function cloneCustomer()
    {
        if (!request()->ajax()) {
            return response()->json(['error' => 1, 'msg' => sc_language_render('admin.method_not_allow')]);
        }
        $id = request('pId') ?? '';
        $customer = $this->customerDavicook::findOrFail($id);
        if (!$customer) {
            throw new Exception('Không tìm thấy khách hàng');
        }
        DB::connection(SC_CONNECTION)->beginTransaction();

        try {
            //Product info
            $dataCustomer = \Illuminate\Support\Arr::except($customer->toArray(), ['id', 'created_at', 'updated_at']);
            $dataCustomer['customer_code'] = $dataCustomer['customer_code'] . '-COPY-' . time();
            $dataCustomer['email'] = $dataCustomer['email'] ? $dataCustomer['email'] . '-COPY-' . time() : '';
            $dataCustomer['name'] = $dataCustomer['name'] . '-COPY-' . time();

            $newCustomer = $this->customerDavicook->fill($dataCustomer);
            if (!$newCustomer->save()) {
                throw new ImportException('Nhân bản thất bại');
            }

            if (!empty($customer->davicookProductSuppliers)) {
                foreach ($customer->davicookProductSuppliers as $productSupplier) {
                    $dataNewProductSupplier = Arr::except($productSupplier, ['customer_id', 'id'])->toArray();
                    $dataNewProductSupplier['customer_id'] = $newCustomer->id ?? 0;
                    if (!(new ShopDavicookProductSupplier())->fill($dataNewProductSupplier)->save()) {
                        throw new ImportException("Lỗi nhân bản thông tin sản phẩm và nhà cung cấp. Vui lòng kiểm tra lại");
                    }
                }
            }
            if (!empty($customer->menu)) {
                foreach ($customer->menu as $item) {
                    $dataNewMenu = [
                        'dish_id' => $item->dish_id,
                        'customer_id' => $newCustomer->id ?? 0
                    ];
                    $resultMenuClone = (new ShopDavicookMenu())->create($dataNewMenu);
                    if (!$resultMenuClone) {
                        throw new ImportException("Lỗi nhân bản món ăn cho khách hàng. Vui lòng kiểm tra lại");
                    } else {
                        if (isset($item->details)) {
                            foreach ($item->details as $detail) {
                                $dataNewMenuDetail = Arr::except($detail, ['menu_id', 'id'])->toArray();
                                $dataNewMenuDetail['menu_id'] = $resultMenuClone->id ?? 0;
                                $resultMenuDetailClone = (new ShopDavicookMenuDetail())->fill($dataNewMenuDetail)->save();
                                if (!$resultMenuDetailClone) {
                                    throw new ImportException("Lỗi nhân bản nguyên liệu món ăn. Vui lòng kiểm tra lại");
                                }
                            }
                        }
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

    /**
     * Handle export file pdf customer.
     * @return \Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function exportExcelListCustomer()
    {
        $ids = request('ids');
        $out = [];
        $keyword = sc_clean(request('keyword') ?? '');
        if(!empty($ids)){
            $arrId = explode(',', $ids);
            $keyword = '';
            foreach ($arrId as $id) {
                $out[] = $id;
            }
            if(count($out)>10) {
                return redirect()->back()->with('error', 'Vui lòng xuất tối đa 10 khách hàng mỗi lần');
            }
        } else {
            $countCustomer = $this->customerDavicook->getListCustomerExport($keyword);
            if (count($countCustomer) > 10) {
                return redirect()->back()->with('error', 'Vui lòng xuất tối đa 10 khách hàng mỗi lần');
            }
        }
        if(!empty($out)){
            $data = $this->customerDavicook->getListCustomerExport($keyword, $out);
        } else{
            $data = $this->customerDavicook->getListCustomerExport($keyword);
        }
        if (empty($data)) {
            return redirect()->back()->with('error', 'không có dữ liệu');
        }
        return Excel::download(new AdminDavicookCustomerExportMulti($data), 'DanhSachKhachHangDaviCook-'. Carbon::now() .'.xlsx');
    }

    /**
     * Show view submit form import.
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function importExcel()
    {
        return view($this->templatePathAdmin . 'screen.davicook_customer.excel.import',
            [
                'title' => sc_language_render('admin.davicook.customer.import')
            ]);
    }

    /**
     * Check chức năng import ghi đè hoặc không ghi đè.
     * @return \Illuminate\Http\RedirectResponse
     * @throws Exception
     */
    public function handelImport()
    {
        $file = request()->file('excel_file');
        $type = request('type_import_customer');
        if (!$file || !is_file($file) || !in_array($file->extension(), ['xls', 'xlsx'])) {
            throw new Exception('Định dạng file không hợp lệ!');
        }
        if ($type == 1) {
            return $this->importOverwrite($file);
        } else {
            return $this->importNotOverwrite($file);
        }
    }

    /**
     * Handle import ghi đè khách hàng davicook.
     * @param $file
     * @return \Illuminate\Http\RedirectResponse
     */
    public function importOverwrite($file)
    {
        $massageErrors = [];
        DB::beginTransaction();
        try {
            $raw_excel_customer = Excel::toArray(new DavicookCustomerImport(), $file);
            if(count($raw_excel_customer) > 3) {
                throw new Exception('Cảnh báo. Dữ liệu quá tải vui lòng giảm bớt sheet!');
            }
            $notifyCustomersImport = !empty($raw_excel_customer) ? cleanExcelFile($raw_excel_customer) : [];
            $products = ShopProduct::all()->pluck('id', 'sku')->toArray();
            $suppliers = ShopSupplier::all()->pluck('id', 'supplier_code')->toArray();
            $dishs = ShopDish::all()->pluck('id', 'code')->toArray();
            foreach ($notifyCustomersImport as $keySheet => $notifyCustomers) {
                if (empty($notifyCustomers[0]['ma_khach_hang'])) {
                    $massageErrors[] = 'Vui lòng điền đầy đủ Mã khách hàng để hệ thống tiếp tục thực hiện Import';
                    $massageErrors[] = 'Thông tin mã khách hàng không được phép trống - sheet thứ ' . ($keySheet + 1);
                    throw new Exception('Lỗi thiếu Mã khách hàng');
                }

                $customer = $this->customerDavicook::where('customer_code', $notifyCustomers[0]['ma_khach_hang'])->first();
                $arrProductCode = [];
                if ($customer) {
                    $productSuppliers = ShopDavicookProductSupplier::where('customer_id', $customer->id)->get();
                    // Xử lý khi đã có khách hàng.
                    $dataList = $this->getDataUpdateImportCustomer($notifyCustomers[0]);
                    $customer->update($dataList);

                    // Foreach Xử lý import sản phẩm theo nhà cung cấp
                    foreach ($notifyCustomers as $keyProduct => $notifyProduct) {
                        if ($keyProduct < 4) {
                            continue;
                        }
                        if ($notifyProduct['ma_khach_hang'] == 'Thông tin món ăn') {
                            $numberStartDish = $keyProduct;
                            break;
                        }
                        if (!array_key_exists($notifyProduct['ma_khach_hang'], $products)) {
                            $massageErrors[] = 'Sheet thứ ' . ($keySheet + 1)  . ' có Mã sản phẩm không hợp lệ - Dòng '.($keyProduct + 3);
                        }
                        if (!array_key_exists($notifyProduct['email'],$suppliers)) {
                            $massageErrors[] = 'Sheet thứ ' . ($keySheet + 1)  . ' có Mã nhà cung cấp không hợp lệ - Dòng '.($keyProduct + 3);
                        }
                        $productSupplier = $productSuppliers->where('product_id', $products[$notifyProduct['ma_khach_hang']] ?? '');
                        $arrProductCode[] = $notifyProduct['ma_khach_hang'];
                        if ($productSupplier->isNotEmpty()) {
                            $FlagSupplier = $productSupplier->where('supplier_id', $suppliers[$notifyProduct['email']] ?? '')->first();
                            if (!empty($FlagSupplier)) {
                                continue;
                            } else {
                                if (!$massageErrors) {
                                    $firstSupplier = $productSupplier->first();
                                    $firstSupplier->supplier_id = $suppliers[$notifyProduct['email']];
                                    $firstSupplier->save();
                                }
                            }
                        } else {
                            if (!$massageErrors) {
                                $insertFlag = ShopDavicookProductSupplier::create([
                                    'supplier_id' => $suppliers[$notifyProduct['email']] ?? '',
                                    'customer_id' => $customer->id,
                                    'product_id' => $products[$notifyProduct['ma_khach_hang']] ?? '',
                                ]);

                                if (!$insertFlag) {
                                    throw new ImportException('Thông tin sản phẩm không hợp lệ. Vui lòng kiểm tra lại');
                                }
                            }
                        }
                    }

                    // Foreach xử lý món ăn theo nguyên liệu
                    foreach ($notifyCustomers as $keyDish => $dish) {
                        if ($keyDish < ($numberStartDish + 2) ) {
                            continue;
                        }

                        if ($notifyCustomers[$numberStartDish + 2]['ma_khach_hang'] == '')  {
                            $massageErrors[] = 'Sheet thứ ' . ($keySheet + 1)  . ' có Mã món ăn bị trống - Dòng - '.($keyDish + 3);
                        }

                        if (!array_key_exists($dish['so_dien_thoai'], $products)) {
                            $massageErrors[] = 'Sheet thứ ' . ($keySheet + 1)  . ' có Mã nguyên liệu không có trong danh sách sản phẩm - Dòng - '.($keyDish + 3);
                        }

                        if (!in_array($dish['so_dien_thoai'], $arrProductCode)) {
                            $massageErrors[] = 'Sheet thứ ' . ($keySheet + 1)  . ' có Mã nguyên liệu không tồn tại trong sheet - Dòng - '.($keyDish + 3);
                        }

                        $decimalQty = explode('.', $dish['gia_moi_suat_an']);
                        if(strlen(end($decimalQty)) > 7 || $dish['gia_moi_suat_an'] < 0) {
                            $massageErrors[] = 'Sheet thứ ' . ($keySheet + 1)  . ' lỗi định lượng sống tối đa 7 số thập phân và lớn hơn 0 - Dòng - '.($keyDish + 3);
                        }

                        if ($dish['ma_khach_hang'] != '') {
                            if (!array_key_exists($dish['ma_khach_hang'], $dishs)) {
                                $massageErrors[] = 'Sheet thứ ' . ($keySheet + 1)  . ' có Mã món ăn không tồn tại - Dòng - '.($keyDish + 3);
                            }
                            // Get món ăn. Chưa tồn tại -> tạo mới.
                            $menu = ShopDavicookMenu::where('customer_id', $customer->id)->where('dish_id', $dishs[$dish['ma_khach_hang']] ?? '')->first();
                            if (!$menu) {
                                if (!$massageErrors) {
                                    $menu = ShopDavicookMenu::create([
                                        'customer_id' => $customer->id,
                                        'dish_id' => $dishs[$dish['ma_khach_hang']] ?? '',
                                        'is_export_menu' => $dish['email'] == 1 ? 1 : 0,
                                        'qty_cooked_dish' => $dish['thuoc_stt'],
                                    ]);
                                }
                            } else {
                                $menu->is_export_menu = $dish['email'] == 1 ? 1 : 0;
                                $menu->qty_cooked_dish = $dish['thuoc_stt'];
                                $menu->save();
                            }
                            $menuDetail = ShopDavicookMenuDetail::where('menu_id', $menu->id ?? '')
                                ->where('customer_id', $customer->id)
                                ->delete();
                        }
                        // Xóa hết chi tiết và tạo lại chi tiết mới.
                        if (!$massageErrors) {
                            ShopDavicookMenuDetail::create([
                                'menu_id' => $menu->id,
                                'product_id' => $products[$dish['so_dien_thoai']] ?? '',
                                'customer_id' => $customer->id,
                                'qty' => $dish['gia_moi_suat_an'],
                                'is_spice' => $dish['ten_hien_thi_tren_tem'] == 1 ? 1 : 0,
                            ]);
                        }
                    }
                } else {
                    // Xử lý khi chưa có khách hàng
                    $messageErrors = [];
                    $errorNullCustomer = $this->checkErrorNullCustomer($notifyCustomers[0]);
                    if (!empty($errorNullCustomer)) {
                        foreach ($errorNullCustomer as $error) {
                            $messageErrors[] = $error . ($keySheet + 1);
                        }
                    }
                    $zone = ShopZone::where('zone_code', $notifyCustomers[0]['ma_khu_vuc'])->first();
                    if (!$zone) {
                        $messageErrors[] = 'Mã khu vực không hợp lệ - Sheet thứ '. ($keySheet + 1);
                    }
                    $dataInsert = [
                        'customer_code' => $notifyCustomers[0]['ma_khach_hang'],
                        'name' => $notifyCustomers[0]['ten_khach_hang'],
                        'email' => $notifyCustomers[0]['email'],
                        'phone' => $notifyCustomers[0]['so_dien_thoai'],
                        'tax_code' => $notifyCustomers[0]['ma_so_thue'],
                        'order_num' => $notifyCustomers[0]['thuoc_stt'],
                        'serving_price' => $notifyCustomers[0]['gia_moi_suat_an'],
                        'short_name' => $notifyCustomers[0]['ten_hien_thi_tren_tem'],
                        'route' => $notifyCustomers[0]['thuoc_tuyen_hang'],
                        'address' => $notifyCustomers[0]['dia_chi'],
                        'zone_id' => $zone->id ?? '',
                        'status' => $notifyCustomers[0]['trạng thái'] ?? 1,
                    ];
                    if (!$messageErrors) {
                        $insertFlag = ShopDavicookCustomer::create($dataInsert);
                        if (!$insertFlag) {
                            throw new ImportException('Thông tin sản phẩm không hợp lệ. Vui lòng kiểm tra lại');
                        }
                    }
                    $customerInsert = ShopDavicookCustomer::where('customer_code', $notifyCustomers[0]['ma_khach_hang'])->first();
                    // Foreach Xử lý import sản phẩm theo nhà cung cấp
                    foreach ($notifyCustomers as $keyProduct => $notifyProduct) {
                        if ($keyProduct < 4) {
                            continue;
                        }
                        if ($notifyProduct['ma_khach_hang'] == 'Thông tin món ăn') {
                            $numberStartDish = $keyProduct;
                            break;
                        }
                        if (!array_key_exists($notifyProduct['ma_khach_hang'], $products)) {
                            $massageErrors[] = 'Sheet thứ ' . ($keySheet + 1)  . ' có Mã sản phẩm không hợp lệ - Dòng '.($keyProduct + 3);
                        }
                        if (!array_key_exists($notifyProduct['email'],$suppliers)) {
                            $massageErrors[] = 'Sheet thứ ' . ($keySheet + 1)  . ' có Mã nhà cung cấp không hợp lệ - Dòng '.($keyProduct + 3);
                        }
                        if (!$massageErrors) {
                            $insertFlag = ShopDavicookProductSupplier::create([
                                'supplier_id' => $suppliers[$notifyProduct['email']] ?? '',
                                'customer_id' => $customerInsert->id ?? '',
                                'product_id' => $products[$notifyProduct['ma_khach_hang']] ?? '',
                            ]);
                            if (!$insertFlag) {
                                throw new ImportException('Thông tin sản phẩm không hợp lệ. Vui lòng kiểm tra lại');
                            }
                        }
                        $arrProductCode[] = $notifyProduct['ma_khach_hang'];
                    }
                    $dishCode = [];
                    $productDishCode = [];
                    foreach ($notifyCustomers as $keyDish => $dish) {
                        if ($keyDish < ($numberStartDish + 2) ) {
                            continue;
                        }
                        if ($notifyCustomers[$numberStartDish + 2]['ma_khach_hang'] == '')  {
                            $massageErrors[] = 'Sheet thứ ' . ($keySheet + 1)  . ' có Mã món ăn bị trống - Dòng - '.($keyDish + 3);
                        }
                        if (in_array($dish['ma_khach_hang'], array_filter($dishCode))) {
                            $massageErrors[] = 'Sheet thứ ' . ($keySheet + 1)  . ' có Món ăn bị trùng lặp - Dòng - '.($keyDish + 3);
                        }

                        if (!array_key_exists($dish['so_dien_thoai'], $products)) {
                            $massageErrors[] = 'Sheet thứ ' . ($keySheet + 1)  . ' có Mã nguyên liệu không có trong danh sách sản phẩm - Dòng - '.($keyDish + 3);
                        }

                        if (!in_array($dish['so_dien_thoai'], $arrProductCode)) {
                            $massageErrors[] = 'Sheet thứ ' . ($keySheet + 1)  . ' có Mã nguyên liệu không tồn tại trong sheet - Dòng - '.($keyDish + 3);
                        }

                        $decimalQty = explode('.', $dish['gia_moi_suat_an']);
                        if(strlen(end($decimalQty)) > 7 || !($dish['gia_moi_suat_an'] > 0)) {
                            $massageErrors[] = 'Sheet thứ ' . ($keySheet + 1)  . ' lỗi định lượng sống tối đa 7 số thập phân và lớn hơn 0 - Dòng - '.($keyDish + 3);
                        }

                        if ($dish['ma_khach_hang'] != '') {
                            $productDishCode = [];
                            if (!array_key_exists($dish['ma_khach_hang'], $dishs)) {
                                $massageErrors[] = 'Sheet thứ ' . ($keySheet + 1)  . ' có Mã món ăn không tồn tại - Dòng - '.($keyDish + 3);
                            }
                            if (!$massageErrors) {
                                $menuInsert = ShopDavicookMenu::create([
                                    'customer_id' => $customerInsert->id ?? '',
                                    'dish_id' => $dishs[$dish['ma_khach_hang']] ?? '',
                                    'is_export_menu' => $dish['email'] == 1 ? 1 : 0,
                                    'qty_cooked_dish' => $dish['thuoc_stt'],
                                ]);
                            }
                        }
                        if (in_array($dish['so_dien_thoai'], $productDishCode)) {
                            $massageErrors[] = 'Sheet thứ ' . ($keySheet + 1)  . ' có Mã nguyên liệu bị trùng lặp trong món ăn - Dòng - '.($keyDish + 3);
                        }
                        $productDishCode[] = $dish['so_dien_thoai'];
                        $dishCode[] = $dish['ma_khach_hang'];
                        if (!$massageErrors) {
                            ShopDavicookMenuDetail::create([
                                'menu_id' => $menuInsert->id,
                                'product_id' => $products[$dish['so_dien_thoai']] ?? '',
                                'customer_id' => $customerInsert->id,
                                'qty' => $dish['gia_moi_suat_an'],
                                'is_spice' => $dish['ten_hien_thi_tren_tem'] == 1 ? 1 : 0,
                            ]);
                        }
                    }
                }
            }
            if (!empty($massageErrors)){
                throw new Exception('Lỗi khi nhập dữ liệu: Vui lòng kiểm tra lại!');
            }
        } catch (QueryException $e) {
            DB::rollBack();
            $with_return = ['massageErrors' => $e->getMessage()];
            return redirect()->back()->with($with_return);
        } catch (\Throwable $e) {
            DB::rollBack();
            $with_return = [
                'massageErrors' => $massageErrors,
                'massageUndefined' => $e->getMessage(),
            ];
            return redirect()->back()->with($with_return);
        }
        DB::commit();
        return redirect()->route('admin.davicook_customer.index')->with('success', sc_language_render('action.success'));
    }

    /**
     * Handle import không ghi đè khách hàng davicook.
     * @return \Illuminate\Http\RedirectResponse
     */
    public function importNotOverwrite($file)
    {
        $massageErrors = [];
        DB::beginTransaction();
        try {
            $objCustomers = $this->customerDavicook->pluck('customer_code')->toArray();
            $objZones = (new ShopZone())->get()->keyBy('zone_code')->toArray();
            $objProducts = (new AdminProduct())->get()->keyBy('sku')->toArray();
            $objsuppliers = (new ShopSupplier())->get()->keyBy('supplier_code')->toArray();
            $objDishs = (new AdminDish())->get()->keyBy('code')->toArray();
            $file = request()->file('excel_file');
            if (!$file || !is_file($file) || !in_array($file->extension(), ['xls', 'xlsx'])) {
                throw new Exception('Định dạng file không hợp lệ!');
            }

            $raw_excel_customer = Excel::toArray(new DavicookCustomerImport(), $file);
            if(count($raw_excel_customer) > 5) {
                throw new Exception('Cảnh báo. Dữ liệu quá tải vui lòng giảm bớt sheet!');
            }
            $notifyCustomersImport = !empty($raw_excel_customer) ? cleanExcelFile($raw_excel_customer) : [];
            $raw_excel_product_supplier = Excel::toArray(new DavicookProductSupplierImport(), $file);
            $notifyProductSupplierImport = !empty($raw_excel_product_supplier) ? cleanExcelFile($raw_excel_product_supplier) : [];
            $customerInsert = [];
            foreach ($notifyCustomersImport as $keySheet => $notifyCustomers) {
                if(isset($notifyCustomers[0])) {
                    $customerInsert[] = $notifyCustomers[0];
                } else {
                    $massageErrors[] = 'Thông tin khách hàng Sheet thứ ' . ($keySheet + 1)  . ' có mục còn trống' ;
                }

            //  check notification customer
                // check null items *
                if (empty($notifyCustomers[0]['ma_khach_hang']) || empty($notifyCustomers[0]['ten_khach_hang']) || empty($notifyCustomers[0]['thuoc_stt']) || empty($notifyCustomers[0]['ten_hien_thi_tren_tem']) || empty($notifyCustomers[0]['thuoc_tuyen_hang']) || empty($notifyCustomers[0]['ma_khu_vuc']) || !isset($notifyCustomers[0]['gia_moi_suat_an'] )) {
                    $massageErrors[] = 'Thông tin khách hàng Sheet thứ ' . ($keySheet + 1)  . ' có mục còn trống!';
                    throw new Exception('Đã có lỗi xãy ra khi nhập thông tin khách hàng.');
                }

                // check customer code
                if (!preg_match(config('validate.admin.code'), $notifyCustomers[0]['ma_khach_hang'])) {
                    $massageErrors[] = 'Thông tin khách hàng Sheet thứ ' . ($keySheet + 1)  . ' có mã khách hàng không hợp lệ - Mã khách hàng -' .$notifyCustomers[0]['ma_khach_hang'];
                }
                // check customer code in excel
                $listCustomerExcel = data_get($customerInsert,'*.ma_khach_hang') ?? [];
                $listCustomerExcelUnique = array_unique($listCustomerExcel) ?? [];

                if (count($listCustomerExcel) != count($listCustomerExcelUnique)) {
                    $massageErrors[] = 'Thông tin khách hàng Sheet thứ ' . ($keySheet + 1)  . ' có mã khách hàng bi lặp trên file excel - Mã khách hàng -' .$notifyCustomers[0]['ma_khach_hang'];
                }
                // check customer code in system
                if(count(array_intersect($listCustomerExcel, $objCustomers)) > 0){
                    $massageErrors[] = 'Thông tin khách hàng Sheet thứ ' . ($keySheet + 1)  . ' có mã khách hàng tồn tại trên hệ thống - Mã khách hàng -' .$notifyCustomers[0]['ma_khach_hang'];
                }
                // check giá suất ăn
                if ($notifyCustomers[0]['gia_moi_suat_an'] < 0) {
                    $massageErrors[] = 'Thông tin khách hàng Sheet thứ ' . ($keySheet + 1)  . ' có giá suất ăn không hợp lệ - Giá suất ăn -' .$notifyCustomers[0]['gia_moi_suat_an'];
                }
                if (!is_numeric($notifyCustomers[0]['gia_moi_suat_an'])) {
                    $massageErrors[] = 'Thông tin khách hàng Sheet thứ ' . ($keySheet + 1)  . ' có giá suất ăn không hợp lệ - Giá suất ăn -' .$notifyCustomers[0]['gia_moi_suat_an'];
                }

                // check zone code in system
                $checkZoneCode = $this->checkExistSystem(trim($notifyCustomers[0]['ma_khu_vuc']), $objZones);
                if(!$checkZoneCode){
                    $massageErrors[] = 'Thông tin khách hàng Sheet thứ ' . ($keySheet + 1)  . ' có mã khu vực không tồn tại - Mã khu vực -' .$notifyCustomers[0]['ma_khu_vuc'];
                }
                // check status
                if($notifyCustomers[0]['trang_thai'] != 1 && $notifyCustomers[0]['trang_thai'] !== 0) {
                    $massageErrors[] = 'Thông tin khách hàng Sheet thứ ' . ($keySheet + 1)  . ' có trạng thái không hợp lệ - Trạng thái -' .$notifyCustomers[0]['trang_thai'];
                }
                $zone_id = (new ShopZone())->where('zone_code', $notifyCustomers[0]['ma_khu_vuc'])->first()->id ?? '';
                $dataCustomerInsert = [
                    "name" => $notifyCustomers[0]['ten_khach_hang'],
                    "short_name" => $notifyCustomers[0]['ten_hien_thi_tren_tem'],
                    "customer_code" => $notifyCustomers[0]['ma_khach_hang'],
                    "email" => $notifyCustomers[0]['email'],
                    "phone" => $notifyCustomers[0]['so_dien_thoai'],
                    "tax_code" =>$notifyCustomers[0]['ma_so_thue'],
                    "order_num" => $notifyCustomers[0]['thuoc_stt'],
                    "route" => $notifyCustomers[0]['thuoc_tuyen_hang'],
                    "address" => $notifyCustomers[0]['dia_chi'],
                    "zone_id" => $zone_id,
                    "status" => $notifyCustomers[0]['trang_thai'],
                    "serving_price" => $notifyCustomers[0]['gia_moi_suat_an'],
                ];
                $resultCustomerInsert = AdminDavicookCustomer::create($dataCustomerInsert);
                if (!$resultCustomerInsert) {
                    throw new Exception('Đã có lỗi xãy ra khi nhập thông tin khách hàng. Vui lòng liên hệ đội kỹ thuật');
                }
                // add product supplier
                $i = 0;
                $arrayProductSupplierInsert = [];
                $productsInsert = [];
                $listProductExcel = [];
                foreach ($notifyProductSupplierImport[$keySheet] as $key => $notifyProduct ) {
                    if ($notifyProduct['ma_san_pham'] == 'Thông tin món ăn') {
                        break;
                    }
                    $productsInsert[] = $notifyProduct;
                // check notification product supplier
                    //check empty
                    if (empty($notifyProduct['ma_san_pham']) || empty($notifyProduct['ma_nha_cung_cap'])) {
                        $massageErrors[] = 'Thông tin sản phẩm Sheet thứ ' . ($keySheet + 1)  . ' có mục còn trống - Dòng -'.($key + 7);
                    } else {
                        // check product sku
                        if (!preg_match(config('validate.admin.code'), $notifyProduct['ma_san_pham'])) {
                            $massageErrors[] = 'Thông tin sản phẩm Sheet thứ ' . ($keySheet + 1)  . ' có mã sản phẩm không hợp lệ - Dòng - '.($key + 7). ' - Mã sản phẩm - ' .$notifyProduct['ma_san_pham'];
                        }
                        $checkProductCode = $this->checkExistSystem(trim($notifyProduct['ma_san_pham']), $objProducts);
                        if (!$checkProductCode) {
                            $massageErrors[] = 'Thông tin sản phẩm Sheet thứ ' . ($keySheet + 1)  . ' có mã sản phẩm không tồn tại - Dòng - '.($key + 7). ' - Mã sản phẩm - ' .$notifyProduct['ma_san_pham'];
                        }
                        // check supplier code
                        if (!preg_match(config('validate.admin.code'), $notifyProduct['ma_nha_cung_cap'])) {
                            $massageErrors[] = 'Thông tin sản phẩm Sheet thứ ' . ($keySheet + 1)  . ' có mã nhà cung cấp không hợp lệ - Dòng - ' . ($key + 7) . ' - Mã nhà cung cấp  - ' . $notifyProduct['ma_nha_cung_cap'];
                        }
                        $checkProductCode = $this->checkExistSystem(trim($notifyProduct['ma_nha_cung_cap']), $objsuppliers);
                        if (!$checkProductCode) {
                            $massageErrors[] = 'Thông tin sản phẩm Sheet thứ ' . ($keySheet + 1)  . ' có mã nhà cung cấp không tồn tại - Dòng - ' . ($key + 7) . ' - Mã nhà cung cấp - ' . $notifyProduct['ma_nha_cung_cap'];
                        }
                    }

                    if (in_array($notifyProduct['ma_san_pham'], $listProductExcel)) {
                        $massageErrors[] = 'Thông tin sản phẩm Sheet thứ ' . ($keySheet + 1)  . ' có mã sản phẩm bị lặp trên file excel - Dòng - '.($key + 7). ' - Mã sản phẩm - ' .$notifyProduct['ma_san_pham'];
                    }
                    $listProductExcel[] = $notifyProduct['ma_san_pham'];
                    // lấy id sản phẩm
                    $objProductId = (new AdminProduct())->where('sku', $notifyProduct['ma_san_pham'])->get(['id'])->first();
                    $product_id = $objProductId->id ?? '';
                    // lấy id nha cung cấp
                    $objSupplierId = (new ShopSupplier())->where('supplier_code', $notifyProduct['ma_nha_cung_cap'])->get(['id'])->first();
                    $supplier_id = $objSupplierId->id ?? '';
                    $dataProductSupplierInsert = [
                        "customer_id" => $resultCustomerInsert->id,
                        "product_id" => $product_id,
                        "supplier_id" => $supplier_id,
                    ];
                    if (!empty($product_id) && !empty($supplier_id)) {
                        $resultProductSupplierInsert = ShopDavicookProductSupplier::create($dataProductSupplierInsert)->toArray();
                        $arrayProductSupplierInsert[] =  $resultProductSupplierInsert ;
                        if (!$resultProductSupplierInsert) {
                            throw new Exception('Đã có lỗi xãy ra khi nhập thông tin món ăn. Vui lòng liên hệ đội kỹ thuật');
                        }
                    }
                    $i++;
                }
                $arrayProductId = data_get($arrayProductSupplierInsert,'*.product_id');

                //import dish
                $start = 0;
                foreach ($notifyCustomers as $key => $value)
                {
                    $keyMax = max(array_keys($notifyCustomers));
                    if ($value['ma_khach_hang'] == 'Thông tin món ăn')
                    {
                        $start = $key + 2;
                        $dishInsert = [];
                        $resultDishInsert = [];
                        $listDishExcel = [];
                        $ingredientExcelImport = [];
                        for ($i = $start; $i <= $keyMax; $i++){
                            if (!isset($notifyCustomers[$i])) {
                                $massageErrors[] = 'Thông tin sản phẩm Sheet thứ ' . ($keySheet + 1)  .' Lỗi file - Dòng '. ($i + 3);
                                continue;
                            }
                            $dishCode = $notifyCustomers[$i]['ma_khach_hang'];
                            $dishName = $notifyCustomers[$i]['ten_khach_hang'];
                            $ingredientCode = $notifyCustomers[$i]['so_dien_thoai'];
//                            $ingredientName = $notifyCustomers[$i]['so_dien_thoai'];
                            $qty = $notifyCustomers[$i]['gia_moi_suat_an'];
                            $qty_cooked = $notifyCustomers[$i]['thuoc_stt'];
                            $is_spice = $notifyCustomers[$i]['ten_hien_thi_tren_tem'] == 1 ? 1 : 0;
                            if(!empty($dishCode)) {
                                $ingredientExcelImport = [];
                                $ingredientExcelImport[] = $ingredientCode;
                                $arrayIngredient =[];
                                $checkDishCode = $this->checkExistSystem(trim($dishCode), $objDishs);
                                if (!$checkDishCode) {
                                    $massageErrors[] = 'Thông tin món ăn Sheet thứ ' . ($keySheet + 1)  . ' có mã món ăn không tồn tại  - Dòng - ' . ($i + 3) . ' - Mã món ăn - ' . $dishCode;
                                }
                                $objDishId = (new ShopDish())->where('code', $dishCode)->get(['id'])->first();
                                $dish_id = $objDishId->id ?? '';
                                $dishInsert = [
                                    'dish_id' => $dish_id,
                                    'customer_id' => $resultCustomerInsert->id,
                                    'is_export_menu' => $notifyCustomers[$i]['email'] == 1 ? 1 : 0,
                                    'qty_cooked_dish' => $qty_cooked,
                                ];
                                // check dish code
                                if (!preg_match(config('validate.admin.code'), $dishCode)) {
                                    $massageErrors[] = 'Thông tin món ăn Sheet thứ ' . ($keySheet + 1)  . ' có mã món ăn không hợp lệ  - Dòng - ' . ($i + 3) . ' - Mã món ăn - ' . $dishCode;
                                }
                                if (in_array($dishCode, $listDishExcel)) {
                                    $massageErrors[] = 'Thông tin món ăn Sheet thứ ' . ($keySheet + 1)  . ' có mã món ăn bị lặp trên file excel  - Dòng - ' . ($i + 3) . ' - Mã món ăn - ' . $dishCode;
                                }
                                $listDishExcel[] = $dishCode;
                                if (!empty($dish_id)) {
                                    $resultDishInsert = ShopDavicookMenu::create($dishInsert)->toArray();
                                    if (!$resultDishInsert) {
                                        throw new Exception('Đã có lỗi xãy ra khi nhập thông tin món ăn. Vui lòng liên hệ đội kỹ thuật');
                                    }
                                }
                                if (empty($ingredientCode)) {
                                    $massageErrors[] = 'Thông tin món ăn Sheet thứ ' . ($keySheet + 1)  . ' có mã nguyên liệu để trống - Dòng - ' . ($i + 3);
                                }
                                if (!preg_match(config('validate.admin.code'), $ingredientCode)) {
                                    $massageErrors[] = 'Thông tin món ăn Sheet thứ ' . ($keySheet + 1)  . ' có mã nguyên liệu không hợp lệ  - Dòng - ' . ($i + 3) . ' - Mã nguyên liệu - ' . $ingredientCode;
                                }
                                $checkProductCode = $this->checkExistSystem(trim($ingredientCode), $objProducts);
                                if (!$checkProductCode) {
                                    $massageErrors[] = 'Thông tin món ăn Sheet thứ ' . ($keySheet + 1)  . ' có mã nguyên liệu không tồn tại  - Dòng - ' . ($i + 3) . ' - Mã nguyên liệu - ' . $ingredientCode;
                                }
                                $objIngredientId = (new AdminProduct())->where('sku',$ingredientCode)->get(['id'])->first();
                                $ingredient_id = $objIngredientId->id ?? '';
                                $checkProductCustomer = in_array($ingredient_id, $arrayProductId);
                                if (!$checkProductCustomer) {
                                    $massageErrors[] = 'Thông tin món ăn Sheet thứ ' . ($keySheet + 1)  . ' có mã nguyên liệu chưa áp dụng cho khách hàng  - Dòng - ' . ($i + 3) . ' - Mã nguyên liệu - ' . $ingredientCode;
                                }
                                // check định lượng món ăn
                                if (empty($qty)) {
                                    $massageErrors[] = 'Thông tin món ăn Sheet thứ ' . ($keySheet + 1)  . ' có định lượng món ăn để trống  - Dòng - ' . ($i + 3);
                                }
                                if (!is_numeric($qty)) {
                                    $massageErrors[] = 'Thông tin món ăn Sheet thứ ' . ($keySheet + 1)  . ' có định lượng món ăn là chữ  - Dòng - ' . ($i + 3) . ' - Định lượng - ' . $qty;
                                }
                                if ($qty < 0) {
                                    $massageErrors[] = 'Thông tin món ăn Sheet thứ ' . ($keySheet + 1)  . ' có định lượng món ăn số âm  - Dòng - ' . ($i + 3) . ' - Định lượng - ' . $qty;
                                }
                                if(is_float($qty)){
                                    $decimalQty = explode('.', $qty);
                                    if(strlen(end($decimalQty)) > 7) {
                                        $massageErrors[] = 'Thông tin món ăn Sheet thứ ' . ($keySheet + 1)  . ' định lượng tối đa 7 số thập phân!  - Dòng - ' . ($i + 3) . ' - Định lượng - ' . $qty;
                                    }
                                }

                                if (!isset($resultDishInsert['id'])) {
                                    $massageErrors[] = 'Thông tin món ăn Sheet thứ ' . ($keySheet + 1)  . ' chưa có món ăn  - Dòng - ' . ($i + 3);
                                }

                                $dataMenuDetail = [
                                    'menu_id' => $resultDishInsert['id'],
                                    'product_id' => $ingredient_id,
                                    'qty' => $qty ?? 0,
                                    'customer_id' => $resultCustomerInsert->id,
                                    'is_spice' => $is_spice,
                                ];

                                if (!empty($ingredient_id)) {
                                    $resultMenuDetailInsert = ShopDavicookMenuDetail::create($dataMenuDetail)->toArray();
                                    $arrayIngredient[] = $resultMenuDetailInsert;
                                    if (!$resultMenuDetailInsert) {
                                        throw new Exception('Đã có lỗi xãy ra khi nhập thông tin món ăn. Vui lòng liên hệ đội kỹ thuật');
                                    }
                                }
                            } else {
                                if (!empty($dishName)) {
                                    $massageErrors[] = 'Thông tin món ăn Sheet thứ ' . ($keySheet + 1)  . ' có mã món ăn để trống  - Dòng - ' . ($i + 3);
                                } else {
                                    if (empty($ingredientCode)) {
                                        $massageErrors[] = 'Thông tin món ăn Sheet thứ ' . ($keySheet + 1)  . ' có mã nguyên liệu để trống - Dòng - ' . ($i + 3);
                                    }
                                    if (!preg_match(config('validate.admin.code'), $ingredientCode)) {
                                        $massageErrors[] = 'Thông tin món ăn Sheet thứ ' . ($keySheet + 1)  . ' có mã nguyên liệu không hợp lệ  - Dòng - ' . ($i + 3) . ' - Mã nguyên liệu - ' . $ingredientCode;
                                    }
                                    $checkProductCode = $this->checkExistSystem(trim($ingredientCode), $objProducts);
                                    if (!$checkProductCode) {
                                        $massageErrors[] = 'Thông tin món ăn Sheet thứ ' . ($keySheet + 1)  . ' có mã nguyên liệu không tồn tại  - Dòng - ' . ($i + 3);
                                    }
                                    $objIngredientId = (new AdminProduct())->where('sku',$ingredientCode)->get(['id'])->first();
                                    $ingredient_id = $objIngredientId->id ?? '';
                                    $checkProductCustomer = in_array($ingredient_id, $arrayProductId);
                                    if (!$checkProductCustomer) {
                                        $massageErrors[] = 'Thông tin món ăn Sheet thứ ' . ($keySheet + 1)  . ' có mã nguyên liệu chưa áp dụng cho khách hàng  - Dòng - ' . ($i + 3) . ' - Mã nguyên liệu - ' . $ingredientCode;
                                    }

                                    // check định lượng món ăn
                                    if (empty($qty)) {
                                        $massageErrors[] = 'Thông tin món ăn Sheet thứ ' . ($keySheet + 1)  . ' có định lượng món ăn để trống  - Dòng - ' . ($i + 3);
                                    }
                                    if (!is_numeric($qty)) {
                                        $massageErrors[] = 'Thông tin món ăn Sheet thứ ' . ($keySheet + 1)  . ' có định lượng món ăn là chữ  - Dòng - ' . ($i + 3) . ' - Định lượng - ' . $qty;
                                    }
                                    if ($qty < 0) {
                                        $massageErrors[] = 'Thông tin món ăn Sheet thứ ' . ($keySheet + 1)  . ' có định lượng món ăn là số âm  - Dòng - ' . ($i + 3) . ' - Định lượng - ' . $qty;
                                    }
                                    if(is_float($qty)){
                                        $decimalQty = explode('.', $qty);
                                        if(strlen(end($decimalQty)) > 7) {
                                            $massageErrors[] = 'Thông tin món ăn Sheet thứ ' . ($keySheet + 1)  . ' Định lượng tối đa 7 số thập phân!  - Dòng - ' . ($i + 3) . ' - Định lượng - ' . $qty;
                                        }
                                    }

                                    if (!isset($resultDishInsert['id'])) {
                                        $massageErrors[] = 'Thông tin món ăn Sheet thứ ' . ($keySheet + 1)  . ' chưa có món ăn  - Dòng - ' . ($i + 3);
                                    }
                                    $dataMenuDetail = [
                                        'menu_id' => $resultDishInsert['id'],
                                        'product_id' => $ingredient_id,
                                        'qty' => $qty ?? 0,
                                        'customer_id' => $resultCustomerInsert->id
                                    ];
                                    if (!empty($ingredient_id)) {
                                        $resultMenuDetailInsert = ShopDavicookMenuDetail::create($dataMenuDetail)->toArray();
                                        $arrayIngredient[] = $resultMenuDetailInsert;
                                        if (!$resultMenuDetailInsert) {
                                            throw new Exception('Đã có lỗi xãy ra khi nhập thông tin món ăn. Vui lòng liên hệ đội kỹ thuật');
                                        }
                                    }
                                }
                                if (in_array($ingredientCode, $ingredientExcelImport)) {
                                    $massageErrors[] = 'Thông tin món ăn Sheet thứ ' . ($keySheet + 1)  . ' có mã nguyên liệu bị trùng trong món ăn  - Dòng - ' . ($i + 3) . ' - Mã nguyên liệu - ' . $ingredientCode;
                                }
                                $ingredientExcelImport[] = $ingredientCode;
                            }
                        }
                    }
                }
            }
            if (!empty($massageErrors)){
                throw new Exception('Lỗi khi nhập dữ liệu: Vui lòng kiểm tra lại!');
            }
        } catch (QueryException $e) {
            DB::rollBack();
            $with_return = ['massageErrors' => $e->getMessage()];
            return redirect()->back()->with($with_return);
        } catch (\Throwable $e) {
            DB::rollBack();
            $with_return = [
                'massageErrors' => $massageErrors,
                'massageUndefined' => $e->getMessage(),
            ];
            return redirect()->back()->with($with_return);
        }
        DB::commit();
        return redirect()->route('admin.davicook_customer.index')->with('success', sc_language_render('action.success'));
    }

    public function checkExistSystem($input, $object)
    {
        $search_result = in_array($input, array_keys($object));
        if ($search_result) {
            return $search_result;
        }
        return false;
    }

    /**
     * Lấy thông tin khi update customer.
     * @param $data
     * @return array
     */
    public function getDataUpdateImportCustomer($data) {
        $dataList = [];
        if ($data['ten_khach_hang'] != '') {
            $dataList['name'] = $data['ten_khach_hang'];
        }
        if ($data['email'] != '') {
            $dataList['email'] = $data['email'];
        }
        if ($data['so_dien_thoai'] != '') {
            $dataList['phone'] = $data['so_dien_thoai'];
        }
        if ($data['ma_so_thue'] != '') {
            $dataList['tax_code'] = $data['ma_so_thue'];
        }
        if ($data['gia_moi_suat_an'] != '' && $data['gia_moi_suat_an'] > 0) {
            $dataList['serving_price'] = $data['gia_moi_suat_an'];
        }
        if ($data['thuoc_stt'] != '') {
            $dataList['order_num'] = $data['thuoc_stt'];
        }
        if ($data['ten_hien_thi_tren_tem'] != '') {
            $dataList['short_name'] = $data['ten_hien_thi_tren_tem'];
        }
        if ($data['thuoc_tuyen_hang'] != '') {
            $dataList['route'] = $data['thuoc_tuyen_hang'];
        }
        if ($data['dia_chi'] != '') {
            $dataList['address'] = $data['dia_chi'];
        }
        if ($data['ma_khu_vuc'] != '') {
            $zone = ShopZone::where('zone_code', $data['ma_khu_vuc'])->first();
            if ($zone) {
                $listData['zone_id'] = $zone->id;
            }
        }
        if ($data['trang_thai'] != '') {
            $dataList['status'] = $data['trang_thai'] ?? 1;
        }

        return $dataList;
    }

    /**
     * Xử lý lỗi khi Insert dữ liệu Customer.
     * @param $data
     * @return array
     */
    public function checkErrorNullCustomer($data) {
        $listError = [];
        if ($data['ten_khach_hang'] == '') {
            $listError[] = 'Tên khách hàng bị trống - Sheet thứ ';
        }
        if ($data['gia_moi_suat_an'] == '' || $data['gia_moi_suat_an'] < 0) {
            $listError[] = 'Giá suất ăn bị trống hoặc không hợp lệ - Sheet thứ ';
        }
        if ($data['thuoc_stt'] == '' || $data['thuoc_stt'] < 0) {
            $listError[] = 'Thuộc STT bị trống hoặc không hơp lệ  - Sheet thứ ';
        }
        if ($data['ten_hien_thi_tren_tem'] == '') {
            $listError[] = 'Tên hiển thị trên tem bị trống  - Sheet thứ ';
        }
        if ($data['thuoc_tuyen_hang'] == '') {
            $listError[] = 'thuộc tuyến hàng bị trống  - Sheet thứ ';
        }
        if ($data['ma_khu_vuc'] == '') {
            $listError[] = 'Mã khu vực bị trống  - Sheet thứ ';
        }
        if ($data['ten_khu_vuc'] == '') {
            $listError[] = 'Tên khu vực bị trống  - Sheet thứ ';
        }

        return $listError;
    }

    /**
     * Sao chép món ăn menu khách hàng Davicook
     **/
    public function cloneMenuDishCustomer()
    {
        $dish_id = request('dish_id');
        $customer_id = request('customer_id');
        $is_export_menu = request('is_export_menu');
        $product_id = $this->removeEmptyElement(request('product_id') ?? []);
        $quantitative = request('quantitative') ?? [];
        $qty_cooked_dish = request('qty_cooked_dish') ?? '';
        $is_spice = request('is_spice') ?? [];
        $objDishCustomer = ShopDavicookMenu::where('customer_id', $customer_id)->pluck('dish_id');
        $checkDish = $this->findDishArray(trim($dish_id), $objDishCustomer);
        $productIdUnique = array_unique($product_id);
        
        if (!$dish_id) {
            return Response()->json(['error' => 1, 'msg' => 'Vui lòng chọn món ăn mới!']);
        }

        if (!$product_id) {
            return Response()->json(['error' => 1, 'msg' => 'Vui lòng thêm nguyên liệu cho món ăn!']);
        }

        if ($checkDish) {
            return Response()->json(['error' => 1, 'msg' => 'Món ăn đã tồn tại trên hệ thống!']);
        }

        if (count($product_id) != count($productIdUnique)) {
            $product_dupes = array_diff_key($product_id, $productIdUnique);
            $nameProduct = $this->getNameProductDupes($product_dupes);
            return Response()->json(["error" => 1, "msg" => "$nameProduct : đã chọn lại nhiều lần!"]);
        }

        DB::beginTransaction();
        try {
            $dataInsertMenu = [
                'customer_id'  => $customer_id,
                'dish_id' => $dish_id,
                'is_export_menu' => $is_export_menu,
                'qty_cooked_dish' => $qty_cooked_dish,
            ];
            $dataMenu = ShopDavicookMenu::create($dataInsertMenu);
            if ($dataMenu) {
                $menu_id = $dataMenu->id;
                foreach ($product_id as $key => $item) {
                    if ($quantitative[$key] <= 0) {
                        throw new \Exception('Định lượng bằng 0. Vui lòng kiểm tra lại');
                    }
                    $dataInsertdetail = [
                        'menu_id' => $menu_id,
                        'product_id' => $product_id[$key],
                        'qty' => $quantitative[$key],
                        'is_spice' => $is_spice[$key],
                        'customer_id'  => $customer_id,
                    ];
                    $dataMenuDetail = ShopDavicookMenuDetail::create($dataInsertdetail);
                    if (!$dataMenuDetail) {
                        throw new \Exception(sc_language_render('action.failed'));
                    }
                }
            } else {
                throw new \Exception(sc_language_render('action.failed'));
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return Response()->json(['error' => 1, 'msg' => $e->getMessage()]);
        }
        DB::commit();
        return Response()->json(['success' => 0, 'msg' => sc_language_render('action.create_success')]);
    }
}