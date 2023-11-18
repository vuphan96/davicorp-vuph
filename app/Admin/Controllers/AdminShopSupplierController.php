<?php

namespace App\Admin\Controllers;


use App\Admin\Models\AdminCategory;
use App\Admin\Models\AdminUserPriceboard;
use App\Front\Models\ShopCustomer;
use App\Front\Models\ShopDavicookProductSupplier;
use App\Front\Models\ShopImportPriceboard;
use App\Front\Models\ShopProductSupplier;
use App\Front\Models\ShopSupplier;
use App\Exports\ShopSupplierExport;
use App\Http\Requests\Admin\AdminSupplierEditRequest;
use App\Imports\SupplierImport;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Carbon;
use App\Http\Requests\Admin\AdminSupplierRequest;
use Illuminate\Support\Facades\DB;
use Request;
use SCart\Core\Admin\Controllers\RootAdminController;
use Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use function Symfony\Component\HttpKernel\Debug\format;


class AdminShopSupplierController extends RootAdminController
{
    public $languages;

    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {

        $data = [
            'title' => sc_language_render('admin.supplier.list'),
            'subTitle' => '',
            'icon' => 'fa fa-indent',
            'urlDeleteItem' => sc_route_admin('admin_supplier.delete'),
            'removeList' => 1, // 1 - Enable function delete list item
            'buttonRefresh' => 1, // 1 - Enable button refresh
            'buttonSort' => 1, // 1 - Enable button sort
            'css' => '',
            'js' => '',
            'method' => 'delete',
            'url_action' => sc_route_admin('admin_supplier.create'),
            'url_export' => sc_route_admin('admin_supplier.export'),
            'permGroup' => 'supplier'
        ];
        //Process add content
        $data['menuRight'] = sc_config_group('menuRight', Request::route()->getName());
        $data['menuLeft'] = sc_config_group('menuLeft', Request::route()->getName());
        $data['topMenuRight'] = sc_config_group('topMenuRight', Request::route()->getName());
        $data['topMenuLeft'] = sc_config_group('topMenuLeft', Request::route()->getName());
        $data['blockBottom'] = sc_config_group('blockBottom', Request::route()->getName());

        $listTh = [
            'supplier_code' => sc_language_render('admin.supplier.code'),
            'name' => sc_language_render('admin.supplier.name'),
            'type_form_report'=>'Mẫu nhập hàng',
            'address' => sc_language_render('admin.supplier.address'),
            'phone' => sc_language_render('admin.supplier.phone'),
            'email' => sc_language_render('admin.supplier.email'),
            'status' => sc_language_render('customer.status'),
            'action' => sc_language_render('action.title'),
        ];

        $sort_order = sc_clean(request('sort_order') ?? 'id_desc');
        $keyword = sc_clean(request('keyword') ?? '');
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
        ];

        $cssTh = [
            'supplier_code' => 'text-align: center; width: 10%',
            'name' => 'text-align: center; width: 20%',
            'type_form_report' => 'text-align: center; width: 10%',
            'address' => 'text-align: center; width: 17%',
            'phone' => 'text-align: center; width: 13%',
            'email' => 'text-align: center; width: 15%',
            'status' => 'text-align: center; width: 13%',
            'action' => 'text-align: center; width: 7%',
        ];
        $cssTd = [
            'supplier_code' => 'text-align: center',
            'name' => '',
            'type_form_report' =>'',
            'address' => '',
            'phone' => 'text-align: center',
            'email' => '',
            'status' => 'text-align: center',
            'action' => 'text-align: center',
        ];
        $data['cssTh'] = $cssTh;
        $data['cssTd'] = $cssTd;
        $dataTmp = (new ShopSupplier())->getSupplierListAdmin($dataSearch);
        $dataTr = [];
        foreach ($dataTmp as $key => $row) {
            $dataTr[$row['id']] = [
                'supplier_code' => $row['supplier_code'] ? $row['supplier_code'] : 'NoSku',
                'name' => $row['name'],
                'type_form_report' =>  $row['type_form_report'] == 1 ? 'Mẫu 1' : ($row['type_form_report'] == 2 ? 'Mẫu 2':''),
                'address' => $row['address'],
                'phone' => $row['phone'],
                'email' => $row['email'],
                'status' => $row['status'] ? '<span class="badge badge-success">ON</span>' : '<span class="badge badge-danger">OFF</span>',
                'action' => '
                    <a data-perm="supplier:detail" href="' . sc_route_admin('admin_supplier.edit', ['id' => $row['id'] ? $row['id'] : 'not-found-id']) . '"><span title="' . sc_language_render('action.edit') . '" type="button" class="btn btn-flat btn-sm btn-primary"><i class="fa fa-edit"></i></span></a>&nbsp;

                    <span data-perm="supplier:delete" onclick="deleteItem(\'' . $row['id'] . '\');"  title="' . sc_language_render('action.delete') . '" class="btn btn-flat btn-sm btn-danger"><i class="fas fa-trash-alt"></i></span>'
                ,
            ];
        }
        $data['listTh'] = $listTh;
        $data['dataTr'] = $dataTr;
        $data['pagination'] = $dataTmp->appends(request()->except(['_token', '_pjax']))->links($this->templatePathAdmin . 'component.pagination');
        $data['resultItems'] = sc_language_render('admin.result_item', ['item_from' => $dataTmp->firstItem(), 'item_to' => $dataTmp->lastItem(), 'total' => $dataTmp->total()]);

        //menuRight
        $data['menuRight'][] = '<a data-perm="supplier:create" href="' . sc_route_admin('admin_supplier.create') . '" class="btn  btn-success  btn-flat" title="New" id="button_create_new">
            <i class="fa fa-plus" title="' . sc_language_render('admin.add_new') . '"></i>
            </a>
            <a data-perm="supplier:import" href="' . sc_route_admin('admin_supplier.import') . '" class="btn  btn-success  btn-flat" title="New" id="button_import">
            <i class="fa fa-file-import" title="' . sc_language_render('category-import') . '"></i>' . sc_language_render('category-import') .
            '</a>
            <a data-perm="supplier:export" href="' . sc_route_admin('admin_supplier.export') . '" class="btn  btn-success  btn-flat" title="New" id="button_export">
            <i class="fa fa-file-export" title="' . sc_language_render('category-export') . '"></i> ' . sc_language_render('category-export') .
            '</a>';
        //=menuRight

        //menuSort
        $optionSort = '';
        foreach ($arrSort as $key => $status) {
            $optionSort .= '<option  ' . (($sort_order == $key) ? "selected" : "") . ' value="' . $key . '">' . $status . '</option>';
        }
        $data['urlSort'] = sc_route_admin('admin_supplier.index', request()->except(['_token', '_pjax', 'sort_order']));
        $data['optionSort'] = $optionSort;
        //=menuSort

        //menuSearch
        $data['topMenuRight'][] = '
                <form action="' . sc_route_admin('admin_supplier.index') . '" id="button_search">
                <div class="input-group input-group">
                    <input type="text" name="keyword" class="form-control rounded-0 float-right" placeholder="' . sc_language_render('admin.supplier.search_hint') . '" value="' . $keyword . '">
                    <div class="input-group-append">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i></button>
                    </div>
                </div>
                </form>';
        //=menuSearch

        return view($this->templatePathAdmin . 'screen.supplier.index')
            ->with($data);
    }
    /**
     * Form create
     */

    public function create()
    {

        $data = [
            'title' => sc_language_render('admin.supplier.add_new_des'),
            'subTitle' => '',
            'title_description' => sc_language_render('admin.supplier.add_new_des'),
            'icon' => 'fa fa-plus',
            'url_action' => sc_route_admin('admin_supplier.create'),
            'customers' => ShopCustomer::where('status', 1)->get(),
        ];
        return view($this->templatePathAdmin . 'screen.supplier.form_add_and_edit')
            ->with($data);
    }
    /**
     * Post create new item in admin
     * @return [type] [description]
     */
    public function postCreate(AdminSupplierRequest $request)
    {
        $data = $request->validated();
        $data['status'] = empty($data['status']) ? 0 : 1;
        $name = $data['name'];
        $countName = (new ShopSupplier())->whereRaw('LOWER(name) = CONVERT(LOWER(?),BINARY)', Str::lower($name))->count();

        if ($countName>0) {
            return redirect()->back()->withInput($data)->with('exist', sc_language_render('admin.supplier.name_unique'));
        }
        $dataInsert = [
            'name' => $data['name'],
            'name_login'=>$data['name_login'],
            'password' =>hash::make($data['password']),
            'type_form_report'=>$data['type_form_report'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'address' => $data['address'],
            'supplier_code' => $data['supplier_code'],
            'status' => $data['status'],
        ];
        try{
            $dataInsert = sc_clean($dataInsert, [], true);
            $obj = ShopSupplier::create($dataInsert);
        } catch(Exception $e) {
            return redirect()->back()->withInput($data)->with('error', sc_language_render('action.failed'));
        }
        return redirect()->route('admin_supplier.index')->with('success', sc_language_render('action.create_success'));
    }


    /**
     * Form edit
     */
    public function edit($id)
    {
        $supplier = ShopSupplier::findOrFail($id);

        $data = [
            'title' => sc_language_render('admin.supplier.edit_title'),
            'subTitle' => '',
            'title_description' => sc_language_render('admin.supplier.edit_title'),
            'icon' => 'fa fa-edit',
            'supplier' => $supplier,
            'customers' => ShopCustomer::where('status', 1)->get(),
            'url_action' => sc_route_admin('admin_supplier.edit'),
        ];

        return view($this->templatePathAdmin . 'screen.supplier.form_add_and_edit')
            ->with($data);
    }

    /**
     * update status
     */
    public function postEdit(AdminSupplierEditRequest $request, $id)
    {
        $supplier = ShopSupplier::findOrFail($id);
        $data = $request->validated();
        $data['status'] = empty($data['status']) ? 0 : 1;
        $name = $data['name'];
        $countName = (new ShopSupplier())->whereRaw('LOWER(name) = CONVERT(LOWER(?),BINARY)', Str::lower($name))
                    ->where('id', '<>', $id)
                    ->count();

        if ($countName>0) {
            return redirect()->back()->withInput($data)->with('exist', sc_language_render('admin.supplier.name_unique'));
        }

        //Edit
        $dataUpdate = [
            'name' => $data['name'],
            'name_login'=>$data['name_login'],
            'password' =>hash::make($data['password']) ?? '',
            'type_form_report'=>$data['type_form_report'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'address' => $data['address'],
            'supplier_code' => $data['supplier_code'],
            'status' => $data['status']
        ];

        try{
            $dataUpdate = sc_clean($dataUpdate, [], true);
            $flag_update = $supplier->update($dataUpdate);
        } catch(Exception $e) {
            return redirect()->back()->withInput($data)->with('error', sc_language_render('action.failed'));
        }

        return redirect()->route('admin_supplier.index')->with('success', sc_language_render('action.edit_success'));
    }

    /*
    Delete list item
    Need mothod destroy to boot deleting in model
     */
    public function deleteList()
    {
        if (!request()->ajax()) {
            return response()->json(['error' => 1, 'msg' => sc_language_render('admin.method_not_allow')]);
        }
        $IdSupplierProductDavicorp = ShopProductSupplier::get()->pluck('supplier_id')->toArray();
        $arrayIdSupplierProductDavicorp = array_unique($IdSupplierProductDavicorp);
        $IdSupplierProductDavicook = ShopDavicookProductSupplier::get()->pluck('supplier_id')->toArray();
        $arrayIdSupplierProductDavicook = array_unique($IdSupplierProductDavicook);
        $IdSupplierPriceImport = ShopImportPriceboard::get()->pluck('supplier_id')->toArray();
        $arrayIdSupplierPriceImport = array_unique($IdSupplierPriceImport);
        $ids = request('ids');
        $arrID = explode(',', $ids);
        foreach ($arrID as $id) {
            $checkInDavicorp = in_array($id, $arrayIdSupplierProductDavicorp);
            $checkInDavicook = in_array($id, $arrayIdSupplierProductDavicook);
            $checkInPriceImport = in_array($id, $arrayIdSupplierPriceImport);
            if($checkInDavicorp || $checkInDavicook || $checkInPriceImport) {
                return response()->json(['error' => 1, 'msg' => sc_language_render('do_not_delete_supplier')]);
            }
        }
        Log::debug($arrID);

        ShopSupplier::destroy($arrID);
        return response()->json(['error' => 0, 'msg' => '']);
    }

    public function export()
    {
        return Excel::download(new ShopSupplierExport, 'DanhMucNhaCungCap-' . Carbon::now() . '.xlsx');
    }

    public function import()
    {
        return view($this->templatePathAdmin . 'screen.supplier.view_import',
        [
            'title' => sc_language_render('admin.supplier.import')
        ]);
    }
    public function importPost()
    {
        $file = request()->file('excel_file');
        $raw_excel_array = null;
        $dupticated = 0;
        $success_count = 0;
        $insert_array = [];
        $error = 0; //Global error flag
        $messages = '';
        $error_dupticate = [];

        DB::beginTransaction();
        if ($file) {
            if (in_array($file->extension(), ['xls', 'xlsx'])) { 
                if (is_file($file)) { 
                    $raw_excel_array = cleanExcelFile(Excel::toArray(new SupplierImport(), $file))[0];
                    if (count($raw_excel_array) > 0) {
                        $objSupplier = ShopSupplier::get(['name', 'phone','supplier_code','name_login']);
                        $checkListExcel = [];
                        $checkListExcelPhone = [];
                        foreach ($raw_excel_array as $key => $row) {
                            if (empty($row['ma_nha_cung_cap']) || empty($row['ten_nha_cung_cap']) || empty($row['ten_dang_nhap'])) {
                                $error = 1;
                                $error_dupticate[($key+2)] = 'Có mục còn trống';
                                $messages = 'Lỗi dữ liệu: Các mục dấu * không được để trống';
                                break;
                            }
                            if($row['trang_thai'] != 1 && $row['trang_thai'] !== 0){
                                $error = 1;
                                $error_dupticate[($key+2)] =  $row['trang_thai'];
                                $messages = 'Lỗi dữ liệu: Trạng thái chỉ được nhập giá trị 1 hoặc 0';
                                break;
                            }
                            $row['mau_hang_nhap'] = ($row['mau_hang_nhap'] === 'Mẫu 1' ? 1 : 2);
                            if(empty($row['password'])){
                                $row['password'] = 123456;
                            }
                            // check mã nhà cung cấp 
                            if (!preg_match(config('validate.admin.code'), $row['ma_nha_cung_cap'])) {
                                $error = 1;
                                $messages = 'Lỗi dữ liệu: Mã nhà cung cấp không hợp lệ';
                                $error_dupticate[($key+2)] = $row['ma_nha_cung_cap'];
                                break;
                            }
                            //check tồn file excel
                            $checkExcel = [
                                'supplier_code' => $row['ma_nha_cung_cap'],
                                'supplier_name' => $row['ten_nha_cung_cap'],
                                'name_login' => $row['ten_dang_nhap']
                            ];
                            $checkListExcel[] = $checkExcel ?? [];
                            $skuListCatExcel = data_get($checkListExcel,'*.supplier_code');
                            $uniqueSkuListExcel = array_unique($skuListCatExcel);
                            if (count($skuListCatExcel) != count($uniqueSkuListExcel) ) {
                                $error = 1;
                                $messages = 'Lỗi dữ liệu: Mã nhà cung cấp bị trùng trong file excel';
                                $error_dupticate[($key+2)] = $row['ma_nha_cung_cap'];
                                break;
                            }
                            $nameListExcel = data_get($checkListExcel, '*.supplier_name');
                            $uniqueNameListExcel = array_unique($nameListExcel);
                            if (count($nameListExcel) != count($uniqueNameListExcel)) {
                                $error = 1;
                                $messages = 'Lỗi dữ liệu: Tên nhà cung cấp bị trùng trong file excel';
                                $error_dupticate[($key+2)] = $row['ten_nha_cung_cap'];
                                break;
                            }
                            if(!empty($row['so_dien_thoai'])) {
                                $checkExcelphone = [
                                    'supplier_phone' => $row['so_dien_thoai']
                                ];
                                $checkListExcelPhone[] = $checkExcelphone ?? [];
                                $nameListExcel = data_get($checkListExcelPhone, '*.supplier_phone');
                                $uniqueNameListExcel = array_unique($nameListExcel);
                                if (count($nameListExcel) != count($uniqueNameListExcel)) {
                                    $error = 1;
                                    $messages = 'Lỗi dữ liệu: Số điện thoại nhà cung cấp bị trùng trong file excel';
                                    $error_dupticate[($key+2)] = $row['so_dien_thoai'];
                                    break;
                                }
                            }

//                            check tồn trên hệ thống
                            $checkName = $this->findNameArray(trim($row['ten_nha_cung_cap']), $objSupplier);
                            $checkCode = $this->findCodeArray(trim($row['ma_nha_cung_cap']), $objSupplier);
                            $checkPhone = $this->findPhoneArray(trim($row['so_dien_thoai']), $objSupplier);
                            $checkLoginName = $this->findNameLoginArray(trim($row['ten_dang_nhap']), $objSupplier);

                            if ($checkCode) {
                                $error = 1;
                                $messages = 'Lỗi trùng dữ liệu. Mã nhà cung cấp đã có trên hệ thống!';
                                $error_dupticate[($key+2)] = $row['ma_nha_cung_cap'];
                                break;
                            }
                            if (!empty($row['so_dien_thoai'])) {
                                if ($checkPhone) {
                                    $error = 1;
                                    $messages = 'Lỗi trùng dữ liệu. Số điện thoại nhà cung cấp đã có trên hệ thống!';
                                    $error_dupticate[($key+2)] = $row['so_dien_thoai'];
                                    break;
                                }
                            }
                            if ($checkLoginName){
                                $error = 1;
                                $messages = 'Lỗi trùng dữ liệu. Tên đăng nhập đã có trên hệ thống!';
                                $error_dupticate[($key+2)] = $row['ten_dang_nhap'];
                                break;
                            }
                            if ($raw_excel_array[$key] == '') {
                                break;
                            }

                            $supplier = new ShopSupplier([
                                'name' =>  $row['ten_nha_cung_cap'],
                                'name_login'=>$row['ten_dang_nhap'],
                                'password' =>hash::make($row['mat_khau']),
                                'type_form_report'=>$row['mau_hang_nhap'] ?? 1,
                                'email' => $row['email'],
                                'address' =>  $row['dia_chi'],
                                'phone' => $row['so_dien_thoai'],
                                'supplier_code' =>  $row['ma_nha_cung_cap'],
                                'status' => $row['trang_thai'] ?? 0,
                            ]);
                            if ($supplier->save()) {
                                $success_count++;
                            } else {
                                $error = 1;
                                $messages = 'Lỗi dữ liệu: Lỗi Không xác định. Vui lòng kiểm tra dữ liệu đầu vào';
                                break;
                            }
                        }
                    } else {
                        $error = 1;
                        $messages = 'Lỗi dữ liệu: Ít nhất phải có 1 bản ghi trong file excel';
                    }
                } else {
                    $error = 1;
                    $messages = 'Lỗi dữ liệu: Dữ liệu không hợp lệ! Vui lòng kiểm tra lại';
                }
            } else {
                $error = 1;
                $messages = 'Lỗi dữ liệu: Dữ liệu không hợp lệ! Dữ liệu đầu vào phải là xls hoặc xlsx';
            }
        } else {
            $error = 1;
            $messages = 'Lỗi dữ liệu: Không có tập tin đầu vào. Vui lòng kiểm tra lại';
        }

        if (!$error) {
            DB::commit();
            $messages = "Nhập thành công! Có $success_count bản danh mục được nhập";
            return redirect(sc_route_admin('admin_supplier.index'))->with(['success' => $messages]);
        } else {
            DB::rollBack();
            $with_return = ['error' => $messages];
            if (count($error_dupticate) > 0) {
                $with_return['dupticate'] = $error_dupticate;
            }
            return redirect()->back()->with($with_return);
        }
    }

    public function findNameArray($input, $name)
    {
        $name_search = $name->keyBy('name')->toArray();
        $search_result = in_array($input, array_keys($name_search));
        if ($search_result) {
            return $name[$search_result]->toArray();
        }
        return false;
    }
    public function findNameLoginArray($input, $name_login)
    {
        $name_login_search = $name_login->keyBy('name_login')->toArray();
        $search_result = in_array($input, array_keys($name_login_search));
        if ($search_result) {
            return $name_login[$search_result]->toArray();
        }
        return false;
    }
    public function findCodeArray($input, $supplierCode)
    {
        $code_search = $supplierCode->keyBy('supplier_code')->toArray();
        $search_result = in_array($input, array_keys($code_search));
        if ($search_result) {
            return $supplierCode[$search_result]->toArray();
        }
        return false;
    }
    public function findPhoneArray($input, $phone)
    {
        $phone_search = $phone->keyBy('phone')->toArray();
        $search_result = in_array($input, array_keys($phone_search));
        if ($search_result) {
            return $phone[$search_result]->toArray();
        }
        return false;
    }
}
