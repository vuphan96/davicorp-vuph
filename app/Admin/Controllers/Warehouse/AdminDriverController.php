<?php

namespace App\Admin\Controllers\Warehouse;

use App\Admin\Api\ApiController;
use App\Admin\Models\AdminDriver;
use App\Admin\Models\AdminDriverCustomer;
use App\Exceptions\ImportException;
use App\Exports\DriverExport;
use App\Front\Models\ShopCustomer;
use App\Front\Models\ShopDavicookCustomer;
use App\Front\Models\ShopGenId;
use App\Http\Requests\Admin\AdminDriverEditRequest;
use App\Http\Requests\Admin\AdminDriverRequest;
use App\Imports\DriverDetailImport;
use App\Imports\DriverImport;
use Exception;
use FontLib\Table\Type\name;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use SCart\Core\Admin\Controllers\RootAdminController;
use SCart\Core\Front\Models\ShopLanguage;

class AdminDriverController extends RootAdminController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $data = [
            'title' => 'Danh sách nhân viên giao hàng',
            'subTitle' => '',
            'icon' => 'fa fa-indent',
            'urlDeleteItem' => sc_route_admin('driver.delete'),
            'removeList' => 1,
            'buttonRefresh' => 1,
            'buttonSort' => 1,
            'css' => '',
            'js' => '',
            'method' => 'delete',
            'url_action' => sc_route_admin('driver.create'),
            'url_export' => sc_route_admin('driver.export'),
            'permGroup' => 'driver'
        ];
        $sort_order = sc_clean(request('sort_order') ?? 'id_desc');
        $keyword = sc_clean(request('keyword') ?? '');
        $arrSort = [
            'id__desc' => sc_language_render('filter_sort.id_desc'),
            'id__asc' => sc_language_render('filter_sort.id_asc'),
        ];
        //Process add content
        $data['menuRight'] = sc_config_group('menuRight', \Request::route()->getName());
        $data['menuLeft'] = sc_config_group('menuLeft', \Request::route()->getName());
        $data['topMenuRight'] = sc_config_group('topMenuRight', \Request::route()->getName());
        $data['topMenuLeft'] = sc_config_group('topMenuLeft', \Request::route()->getName());
        $data['blockBottom'] = sc_config_group('blockBottom', \Request::route()->getName());
        $listTh = [
            'ID' => 'ID',
            'full_name' => 'Tên nhân viên',
            'phone' => 'Số điện thoại',
            'email' => 'Email',
            'address' => 'Địa chỉ',
            'customer_of_driver' => 'Khách hàng được phụ trách bởi NV',
            'status' => sc_language_render('admin.category.status'),
            'action' => 'Thao tác',
        ];
        $obj = new AdminDriver;
        $dataTmp = $obj->orderBy('id', 'desc');
        $dataSearch = [
            'keyword' => $keyword,
            'sort_order' => $sort_order,
            'arrSort' => $arrSort,
        ];
        $dataTmp = (new AdminDriver())->getListDriver($dataSearch);
        $dataTr = [];
        foreach ($dataTmp as $key => $row) {
            $dataTr[$row['id']] = [
                'ID' => $row['id_name'] ?? '',
                'full_name' => $row['full_name'] ?? '',
                'phone' => $row['phone'] ?? '',
                'email' => $row['email'] ?? '',
                'address' => $row['address'] ?? '',
                'customer_of_driver' => $row['customer_of_driver'] ?? '',
                'status' => $row['status'] ? '<span class="badge badge-success">ON</span>' : '<span class="badge badge-danger">OFF</span>',
                'action' => '
                    <a data-perm="driver:edit" href="' . sc_route_admin('driver.edit', ['id' => $row['id'] ? $row['id'] : 'not-found-id']) . '"><span title="' . sc_language_render('action.edit') . '" type="button" class="btn btn-flat btn-sm btn-primary"><i class="fa fa-edit"></i></span></a>&nbsp;
                  <span data-perm="driver:delete" onclick="deleteItem(\'' . $row['id'] . '\');"  title="' . sc_language_render('action.delete') . '" class="btn btn-flat btn-sm btn-danger"><i class="fas fa-trash-alt"></i></span>
                  ',
            ];
        }
        $cssTh = [
            'ID' => 'text-align: center; width: 10%',
            'full_name' => 'text-align: center; width: 15%',
            'phone' => 'text-align: center; width: 8%',
            'email' => 'text-align: center; width: 15%',
            'address' => 'text-align: center; width: 20%',
            'customer_of_driver' => 'text-align: center; width: 35%',
            'status' => 'text-align: center; width: 2%',
            'action' => 'text-align: center; width: 5%'
        ];
        $cssTd = [
            'ID' => 'text-align:center',
            'full_name' => 'text-align:center',
            'phone' => 'text-align:center',
            'email' => '',
            'address' => '',
            'customer_of_driver' => '',
            'status' => 'text-align:center',
            'action' => 'text-align:center'
        ];
        $data['cssTh'] = $cssTh;
        $data['cssTd'] = $cssTd;
        $data['listTh'] = $listTh;
        $data['dataTr'] = $dataTr;
        $data['pagination'] = $dataTmp->appends(request()->except(['_token', '_pjax']))->links($this->templatePathAdmin . 'component.pagination');
        $data['resultItems'] = sc_language_render('admin.result_item', ['item_from' => $dataTmp->firstItem(), 'item_to' => $dataTmp->lastItem(), 'total' => $dataTmp->total()]);

        //menuRight
        $data['menuRight'][] = '
        <a data-perm="driver:create" href="' . sc_route_admin('driver.create') . '" class="btn  btn-success  btn-flat" title="New" id="button_create_new">
            <i class="fa fa-plus" title="' . sc_language_render('action.add_new') . '"></i>
        </a>
        <a data-perm="driver:import" href="' . sc_route_admin('driver.import') . '" class="btn  btn-success  btn-flat" title="New" id="button_import">
            <i class="fa fa-file-import" title="' . sc_language_render('category-import') . '"></i>' . sc_language_render('category-import') .
            '</a>
        <a data-perm="driver:export" href="' . sc_route_admin('driver.export') . '" class="btn  btn-success  btn-flat" title="New" id="button_export">
            <i class="fa fa-file-export" title="' . sc_language_render('category-export') . '"></i> ' . sc_language_render('category-export') .
            '</a>';

        //menuSort
        $optionSort = '';
        foreach ($arrSort as $key => $sort) {
            $optionSort .= '<option  ' . (($sort_order == $key) ? "selected" : "") . ' value="' . $key . '">' . $sort . '</option>';
        }
        $data['urlSort'] = sc_route_admin('driver.index', request()->except(['_token', '_pjax', 'sort_order']));
        $data['optionSort'] = $optionSort;
        //=menuSort
        //menuSearch
        $data['topMenuRight'][] = '
                <form action="' . sc_route_admin('driver.index') . '" id="button_search">
                <div class="input-group input-group" style="width: 350px;">
                    <input type="text" name="keyword" class="form-control rounded-0 float-right" placeholder="' . sc_language_render('admin.category.search_placeholder') . '" value="' . $keyword . '">
                    <div class="input-group-append">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i></button>
                    </div>
                </div>
                </form>';
        //=menuSearch
        return view($this->templatePathAdmin . 'screen.warehouse.driver.index')
            ->with($data);
    }

    public function create()
    {
        $dataSearch = [
            'customer' => sc_clean(request('customer') ?? ''),
        ];
        $customer_corp = ShopCustomer::where('status', 1)->get();
        $customer_cook = ShopDavicookCustomer::where('status', 1)->get();
        $customers = $customer_corp->merge($customer_cook);
        $optionCustomer = '';
        foreach ($customers as $key => $customer) {
            $optionCustomer .= '<option  ' . (is_array($dataSearch['customer']) ? (in_array($customer->customer_code, $dataSearch['customer']) ? "selected" : "") : '') . ' value="' . $customer->id . '">' . $customer->name . '</option>';
        }
        $data = [
            'title' => 'Thêm mới nhân viên giao hàng',
            'subTitle' => '',
            'title_description' => 'Thông tin nhân viên giao hàng',
            'icon' => 'fa fa-plus',
            'optionCustomer' => $optionCustomer,
            'url_action' => sc_route_admin('driver.create'),
        ];
        return view($this->templatePathAdmin . 'screen.warehouse.driver.form_add_and_edit')
            ->with($data);
    }
    public function PostCreate(AdminDriverRequest $request)
    {
        $data = $request->validated();
        $nameDriver1 =[];
        $nameDriver2 =[];
        $driverTypeOne = null;
        $driverTypeTwo = null;
        $check_customer_order1 = AdminDriverCustomer::where('type_order', 1)->whereIn('customer_id', $data['customer_list1'])->pluck('customer_id', 'staff_id')->toArray();
        $staffIdOne = array_keys($check_customer_order1);
        $drivers = AdminDriver::whereIn('id', $staffIdOne)->get();
        foreach ($check_customer_order1 as $staff_id => $customer_id) {
            $driverTypeOne = $drivers->where('id', $staff_id)->first();
            if ($driverTypeOne) {
                $nameDriver1[] = $driverTypeOne->full_name;
            }
        }
        if ($driverTypeOne) {
            return redirect()->back()->withInput()->with('error', 'Khách hàng đợt 1 đã được gắn cho nhân viên giao hàng ' . implode(',',$nameDriver1) . ', vui lòng chọn lại!');
        }
        $check_customer_order2 = AdminDriverCustomer::where('type_order', 2)->whereIn('customer_id', $data['customer_list2'])->pluck('customer_id', 'staff_id')->toArray();
        $staffIdTwo = array_keys($check_customer_order2);
        $drivers = AdminDriver::whereIn('id', $staffIdTwo)->get();
        foreach ($check_customer_order2 as $staff_id => $customer_id) {
            $driverTypeTwo = $drivers->where('id', $staff_id)->first();
            if ($driverTypeTwo) {
                $nameDriver2[] = $driverTypeTwo->full_name;
            }
        }
        if ($driverTypeTwo) {
            return redirect()->back()->withInput()->with('error', 'Khách hàng đợt 2 đã được gắn cho nhân viên giao hàng ' . implode(',',$nameDriver2) . ', vui lòng chọn lại!');
        }
        $statusValue = 0;
        if (isset($data['status'])) {
            $statusValue = 1;
        }
        if (isset($data['customer_list1']) || isset($data['customer_list2'])) {
            $customer_groups = [];
            if (isset($data['customer_list1'])) {
                $customer_groups = array_merge($customer_groups, $data['customer_list1']);
            }
            if (isset($data['customer_list2'])) {
                $customer_groups = array_merge($customer_groups, $data['customer_list2']);
            }
            $customer_corp_id = ShopCustomer::where('status', 1)->pluck('name', 'id')->toArray();
            $customer_cook_id = ShopDavicookCustomer::where('status', 1)->pluck('name', 'id')->toArray();
            $customer_group = array_unique($customer_groups);
            $CustomerArray = array();
            foreach ($customer_group as $customer) {
                $CustomerArray[] = $customer_corp_id[$customer] ?? $customer_cook_id[$customer];
            }
            $CustomerString = implode(';', $CustomerArray);
        }
        try {
            $dataInsert = [
                'id' => sc_uuid(),
                'id_name' => ShopGenId::genNextId('driver'),
                'full_name' => $data['full_name'],
                'phone' => $data['phone'] ?? '',
                'email' => $data['email'] ?? '',
                'address' => $data['address'] ?? '',
                'login_name' => $data['login_name'],
                'password' => hash::make($data['password']),
                'location_Ing' => 0,
                'location_at' => 0,
                'customer_of_driver' => $CustomerString ?? '',
                'status' => $statusValue,
                'created_at' => now(),
                'updated_at' => now(),
            ];
            $newImport = AdminDriver::create($dataInsert);
            $dataDetail = [];
            if (isset($data['customer_list1'])) {
                foreach ($data['customer_list1'] as $key => $customer) {
                    $type_customer = ShopCustomer::where('id', $customer)->first();
                    $dataDetail [] = array(
                        'staff_id' => $newImport->id,
                        'customer_id' => $customer,
                        'type_customer' => $type_customer ? 1 : 2,
                        'type_order' => 1,
                        'created_at' => now(),
                        'updated_at' => now(),
                    );
                }
            }
            if (isset($data['customer_list2'])) {
                foreach ($data['customer_list2'] as $key => $customer) {
                    $type_customer = ShopCustomer::where('id', $customer)->first();
                    $dataDetail [] = array(
                        'staff_id' => $newImport->id,
                        'customer_id' => $customer,
                        'type_customer' => $type_customer ? 1 : 2,
                        'type_order' => 2,
                        'created_at' => now(),
                        'updated_at' => now(),
                    );
                }
            }
            AdminDriverCustomer::insert($dataDetail);
        } catch (Exception $e) {
            return redirect()->back()->withInput($data)->with('error', sc_language_render('action.failed'));
        }
        return redirect()->route('driver.index')->with('success', sc_language_render('action.create_success'));
    }
    public function edit($id)
    {
        $driver = AdminDriver::find($id);
        $dataSearch = [
            'customer' => sc_clean(request('customer') ?? ''),
        ];
        $customer_corp = ShopCustomer::where('status', 1)->get();
        $customer_cook = ShopDavicookCustomer::where('status', 1)->get();
        $customers = $customer_corp->merge($customer_cook);
        $optionCustomer1 = '';
        $optionCustomer2 = '';
        //view customer detail follow type
        $drivers = AdminDriverCustomer::where('staff_id', $driver['id'])->get();
        $customer_type1 = $drivers->where('type_order', 1)->pluck('customer_id')->toArray();
        $customer_type2 = $drivers->where('type_order', 2)->pluck('customer_id')->toArray();
        $list_customer_type1 = $customers->whereIn('id', $customer_type1)->map(function ($customer) {
            return [
                'id' => $customer->id,
                'name' => $customer->name,
            ];
        })->toArray();
        $list_customer_type2 = $customers->whereIn('id', $customer_type2)->map(function ($customer) {
            return [
                'id' => $customer->id,
                'name' => $customer->name,
            ];
        })->toArray();

        // get customer follow type 1
        foreach ($customers as $key => $customer) {
            $optionCustomer1 .= '<option ' . (is_array($customer_type1) ? (in_array($customer->id, $customer_type1) ? "selected" : "") : '') . ' value="' . $customer->id . '">' . $customer->name . '</option>';
        }
        // get customer follow type 2
        foreach ($customers as $key => $customer) {
            $optionCustomer2 .= '<option ' . (is_array($customer_type2) ? (in_array($customer->id, $customer_type2) ? "selected" : "") : '') . ' value="' . $customer->id . '">' . $customer->name . '</option>';
        }
        $data = [
            'title' => 'Sửa thông tin nhân viên giao hàng',
            'subTitle' => '',
            'title_description' => 'Chỉnh sửa thông tin nhân viên giao hàng',
            'icon' => 'fa fa-edit',
            'driver' => $driver,
            'optionCustomer1' => $optionCustomer1,
            'optionCustomer2' => $optionCustomer2,
            'list_customer_type1' => $list_customer_type1,
            'list_customer_type2' => $list_customer_type2,
            'url_action' => sc_route_admin('driver.edit'),
        ];
        return view($this->templatePathAdmin . 'screen.warehouse.driver.form_add_and_edit')
            ->with($data);
    }

    public function postEdit(AdminDriverEditRequest $request, $id)
    {
        $data = $request->validated();
        $driver = AdminDriver::find($id);
        $statusValue = 0;
        if (isset($data['status'])) {
            $statusValue = 1;
        }
        $driverDetail = AdminDriverCustomer::where('staff_id', $id);
        if (isset($data['customer_list1']) || isset($data['customer_list2'])) {
            $customer_groups = [];
            if (isset($data['customer_list1'])) {
                $customer_groups = array_merge($customer_groups, $data['customer_list1']);
            }
            if (isset($data['customer_list2'])) {
                $customer_groups = array_merge($customer_groups, $data['customer_list2']);
            }
            $customer_group = array_unique($customer_groups);
            // view customer of driver
            $CustomerArray = [];
            foreach ($customer_group as $customer) {
                $customer_corp = ShopCustomer::where('id', $customer)->pluck('name')->first();
                $customer_cook = ShopDavicookCustomer::where('id', $customer)->pluck('name')->first();
                if ($customer_corp) {
                    $CustomerArray[] = $customer_corp;
                } elseif ($customer_cook) {
                    $CustomerArray[] = $customer_cook;
                }
            }
            $CustomerString = implode(';', $CustomerArray);
        }
        $dataUpdate = [
            'id' => $driver->id,
            'id_name' => $driver->id_name,
            'full_name' => $data['full_name'],
            'phone' => $data['phone'] ?? '',
            'email' => $data['email'] ?? '',
            'address' => $data['address'] ?? '',
            'login_name' => $data['login_name'],
            'password' => hash::make($data['password']) ?? '',
            'location_Ing' => $driver->location_Ing,
            'location_at' => $driver->location_at,
            'customer_of_driver' => $CustomerString ?? '',
            'status' => $statusValue,
            'created_at' => now(),
            'updated_at' => now(),
        ];
        try {
            $dataUpdate = sc_clean($dataUpdate, [], true);
            $driver->update($dataUpdate);
            $driverDetail->delete();
            $dataDetailUpdate = [];
            if (isset($data['customer_list1'])) {
                foreach ($data['customer_list1'] as $key => $customer) {
                    $type_customer = ShopCustomer::where('id', $customer)->first();
                    $dataDetailUpdate [] = array(
                        'staff_id' => $driver->id,
                        'customer_id' => $customer,
                        'type_customer' => $type_customer ? 1 : 2,
                        'type_order' => 1,
                        'created_at' => now(),
                        'updated_at' => now(),
                    );
                }
            }
            if (isset($data['customer_list2'])) {
                foreach ($data['customer_list2'] as $key => $customer) {
                    $type_customer = ShopCustomer::where('id', $customer)->first();
                    $dataDetailUpdate [] = array(
                        'staff_id' => $driver->id,
                        'customer_id' => $customer,
                        'type_customer' => $type_customer ? 1 : 2,
                        'type_order' => 2,
                        'created_at' => now(),
                        'updated_at' => now(),
                    );
                }
            }
            $driverDetail->insert($dataDetailUpdate);
        } catch (Exception $e) {
            return redirect()->back()->withInput($data)->with('error', sc_language_render('action.failed'));
        }
        return redirect()->route('driver.index')->with('success', 'Chỉnh sửa thành công!');
    }

    public function deleteDriver()
    {
        $ids = request('ids');
        $arrID = explode(',', $ids);
        try {
            DB::beginTransaction();
            AdminDriver::destroy($arrID);
            DB::commit();
            return response()->json(['error' => 0, 'msg' => sc_language_render('action.update_success')]);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error($e);
            return response()->json(['error' => 1, 'msg' => 'Xóa đơn nhập hàng lỗi!']);
        }
    }

    public function export()
    {
        return Excel::download(new DriverExport(), 'DanhSachNhanVienGiaoHang-' . Carbon::now() . '.xlsx');
    }

    public function import()
    {
        return view($this->templatePathAdmin . 'screen.warehouse.driver.excel.view_import',
            [
                'title' => 'Nhập danh mục nhân viên giao hàng'
            ]);
    }

    public function importDriver()
    {
        $file = request()->file('excel_file');
        $messageError = [];
        $arrCustomer1 = [];
        $arrCustomer2 = [];
        $arrLoginName = [];
        $arrCodeCustomer = [];
        $driverCode = '';
        $i = 0;
        $customerNames = [];
        $tempArr = [];
        $tempMaKhachHang = [];
        $customerDavicorp = ShopCustomer::where('status', 1)->pluck('id', 'customer_code')->toArray();
        $customerDavicook = ShopDavicookCustomer::where('status', 1)->pluck('id', 'customer_code')->toArray();
        $customerForDatabase1 = AdminDriverCustomer::where('type_order', 1)->get();
        $customerForDatabase2 = AdminDriverCustomer::where('type_order', 2)->get();
        $adminDriver = AdminDriver::all();
        DB::beginTransaction();
        try {
            $startRow = (new DriverImport())->headingRow() + 1;
            $raw_excel_array_driver = Excel::toArray(new DriverImport(), $file);
            $excel_driver = $raw_excel_array_driver ? cleanExcelFile($raw_excel_array_driver)[0] : [];
            if (!$excel_driver) {
                throw new ImportException('File không hợp lệ! Đảm bảo file được định dạng theo mẫu cho sẵn');
            }
            if (count($excel_driver) > 5000) {
                throw new ImportException('Kích thước dữ liệu khi import ghi đè quá tải. Vui lòng tách file để tránh quá tải');
            }
            foreach ($excel_driver as $key => $item) {
                if ($item['ma_nhan_vien']) {
                    $currentId = $item['ma_nhan_vien'];
                } elseif ($currentId !== null) {
                    $item['ma_nhan_vien'] = $currentId;
                }
                $arrCodeCustomer[] = $item;
            }
            foreach ($excel_driver as $keyDriver => $itemDriver) {
                if ($itemDriver['ma_nhan_vien'] == '' && $keyDriver == 0) {
                    $messageError[] = 'Mã khách hàng hàng không được trống - Dòng ' . ($keyDriver + 2);
                }
                if (in_array($itemDriver['ten_dang_nhap'], $arrLoginName)) {
                    $messageError[] = 'Tên đăng nhập đã trùng trong excel - Dòng ' . ($keyDriver + 2);
                }
                if ($itemDriver['ten_dang_nhap'] != '') {
                    $arrLoginName[] = $itemDriver['ten_dang_nhap'];
                }
                if ($itemDriver['ma_nhan_vien'] == '' && $itemDriver['ten_nhan_vien'] != '' && $itemDriver['ten_dang_nhap'] != '') {
                    $messageError[] = 'Mã nhân viên trống - Dòng ' . ($keyDriver + 2);
                }
                if ($itemDriver['ten_dang_nhap'] == '' && $itemDriver['ten_nhan_vien'] != '' && $itemDriver['ma_nhan_vien'] != '') {
                    $messageError[] = 'Tên đăng nhập trống - Dòng ' . ($keyDriver + 2);
                }
                if ($itemDriver['ten_nhan_vien'] == '' && $itemDriver['ten_dang_nhap'] != '' && $itemDriver['ma_nhan_vien'] != '') {
                    $messageError[] = 'Tên nhân viên trống - Dòng ' . ($keyDriver + 2);
                }
                if ($itemDriver['ma_khach_hang'] == '') {
                    $messageError[] = 'Mã khách hàng trống - Dòng ' . ($keyDriver + 2);
                }
                if ($itemDriver['dot'] == 1) {
                    if (in_array($itemDriver['ma_khach_hang'], $arrCustomer1)) {
                        $messageError[] = 'Mã khách hàng đợt 1 đã trùng trong excel - Dòng ' . ($keyDriver + 2);
                    }
                    $flag1 = $customerForDatabase1->where('staff_id', '!=', $driverCode)->where('customer_id', $customerDavicorp[$itemDriver['ma_khach_hang']] ?? $customerDavicook[$itemDriver['ma_khach_hang']])->first();
                    if ($flag1) {
                        $messageError[] = 'Mã khách hàng đợt 1 đã trùng trong Data hệ thống - Dòng ' . ($keyDriver + 2);
                    }
                    $arrCustomer1[] = $itemDriver['ma_khach_hang'];
                } else {
                    if (in_array($itemDriver['ma_khach_hang'], $arrCustomer2)) {
                        $messageError[] = 'Mã khách hàng đợt 2 đã trùng trong excel - Dòng ' . ($keyDriver + 2);
                    }
                    $flag2 = $customerForDatabase2->where('staff_id', '!=', $driverCode)->where('customer_id', $customerDavicorp[$itemDriver['ma_khach_hang']] ?? $customerDavicook[$itemDriver['ma_khach_hang']])->first();
                    if ($flag2) {
                        $messageError[] = 'Mã khách hàng đợt 2 đã trùng trong Data hệ thống - Dòng ' . ($keyDriver + 2);
                    }
                    $arrCustomer2[] = $itemDriver['ma_khach_hang'];
                }
                $objDriver = $adminDriver->where('login_name', $itemDriver['ten_dang_nhap'])->first();
                if ($objDriver) {
                    $messageError[] = 'Tên đăng nhập đã bị trùng trong Data hệ thống! - Dòng ' . ($keyDriver + 2);
                }
                if ($itemDriver['ma_nhan_vien'] != '') {
                    $driver = AdminDriver::where('id_name', $itemDriver['ma_nhan_vien'])->first();
                    $driverCode = $driver->id ?? '';
                }
                if ($driver) {
                    if ($itemDriver['ma_nhan_vien'] != '' && $itemDriver['ten_nhan_vien'] != '' && $itemDriver['ten_dang_nhap'] != '') {
                        if (isset($arrCodeCustomer)) {
                            foreach ($arrCodeCustomer as $itemCode) {
                                $maNhanVien = $itemCode['ma_nhan_vien'];
                                $maKhachHang = $itemCode['ma_khach_hang'];
                                if (!isset($tempMaKhachHang[$maNhanVien]) || !in_array($maKhachHang, $tempMaKhachHang[$maNhanVien])) {
                                    $tempMaKhachHang[$maNhanVien][] = $maKhachHang;
                                    $customerName = ShopCustomer::where('customer_code', $maKhachHang)->pluck('name')->first() ?? ShopDavicookCustomer::where('customer_code', $maKhachHang)->pluck('name')->first();
                                    if (!isset($tempArr[$maNhanVien])) {
                                        $tempArr[$maNhanVien] = $customerName;
                                    } else {
                                        $tempArr[$maNhanVien] .= '; ' . $customerName;
                                    }
                                }
                            }
                            $customerNames = array_values($tempArr);
                        }
                        $dataUpdate = [
                            'full_name' => $itemDriver['ten_nhan_vien'] ?? '',
                            'login_name' => $itemDriver['ten_dang_nhap'] ?? '',
                            'password' => $itemDriver['mat_khau'] != '' ? hash::make($itemDriver['mat_khau']) : hash::make('123456'),
                            'email' => $itemDriver['email'] ?? '',
                            'address' => $itemDriver['dia_chi'] ?? '',
                            'phone' => $itemDriver['so_dien_thoai'],
                            'id_name' => $itemDriver['ma_nhan_vien'],
                            'customer_of_driver' => $customerNames[$i++] ?? '',
                            'location_Ing' => 0,
                            'location_at' => 0,
                            'status' => $itemDriver['trang_thai'] ?? 0,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                        if (!$messageError) {
                            $driver->update($dataUpdate);
                            AdminDriverCustomer::where('staff_id', $driver->id)->delete();
                        }
                    }
                    if (!$messageError) {
                        AdminDriverCustomer::insert([
                            'staff_id' => $driverCode,
                            'customer_id' => $customerDavicorp[$itemDriver['ma_khach_hang']] ?? $customerDavicook[$itemDriver['ma_khach_hang']],
                            'type_customer' => $itemDriver['loai_kh'],
                            'type_order' => $itemDriver['dot'],
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                } else {
                    if ($itemDriver['ma_nhan_vien'] != '' && $itemDriver['ten_nhan_vien'] != '' && $itemDriver['ten_dang_nhap'] != '') {
                        if (isset($arrCodeCustomer)) {
                            foreach ($arrCodeCustomer as $itemCode) {
                                $maNhanVien = $itemCode['ma_nhan_vien'];
                                $maKhachHang = $itemCode['ma_khach_hang'];
                                if (!isset($tempMaKhachHang[$maNhanVien]) || !in_array($maKhachHang, $tempMaKhachHang[$maNhanVien])) {
                                    $tempMaKhachHang[$maNhanVien][] = $maKhachHang;
                                    $customerName = ShopCustomer::where('customer_code', $maKhachHang)->pluck('name')->first() ?? ShopDavicookCustomer::where('customer_code', $maKhachHang)->pluck('name')->first();
                                    if (!isset($tempArr[$maNhanVien])) {
                                        $tempArr[$maNhanVien] = $customerName;
                                    } else {
                                        $tempArr[$maNhanVien] .= '; ' . $customerName;
                                    }
                                }
                            }
                            $customerNames = array_values($tempArr);
                        }
                        $data = [
                            'full_name' => $itemDriver['ten_nhan_vien'] ?? '',
                            'login_name' => $itemDriver['ten_dang_nhap'] ?? '',
                            'password' => $itemDriver['mat_khau'] != '' ? hash::make($itemDriver['mat_khau']) : hash::make('123456'),
                            'email' => $itemDriver['email'] ?? '',
                            'address' => $itemDriver['dia_chi'] ?? '',
                            'phone' => $itemDriver['so_dien_thoai'],
                            'id_name' => $itemDriver['ma_nhan_vien'],
                            'customer_of_driver' => $customerNames[$i++] ?? '',
                            'location_Ing' => 0,
                            'location_at' => 0,
                            'status' => $itemDriver['trang_thai'] ?? 0,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                        if (!$messageError) {
                            $newImport = AdminDriver::create($data);
                        }
                    }
                    if (!$messageError) {
                        AdminDriverCustomer::insert([
                            'staff_id' => $newImport->id,
                            'customer_id' => $customerDavicorp[$itemDriver['ma_khach_hang']] ?? $customerDavicook[$itemDriver['ma_khach_hang']],
                            'type_customer' => $itemDriver['loai_kh'],
                            'type_order' => $itemDriver['dot'],
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                }
            }

            if (!empty($messageError)) {
                throw new \Exception('Lỗi file, vui lòng kiểm tra lại!');
            }

        } catch (\Throwable $e) {
//            dd($e, $messageError,);
            DB::rollBack();
            Log::debug($e);
            $error = !empty($errorCustomerCode) ? $errorCustomerCode : $messageError;
            return redirect()->back()->with('error_validate_import', $messageError)->with('error', $e->getMessage());
        }
        DB::commit();
        return redirect()->route('driver.index')->with('success', sc_language_render("Nhập danh sách nhân viên giao hàng thành công!"));
    }

}