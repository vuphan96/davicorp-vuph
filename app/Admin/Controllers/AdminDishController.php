<?php

namespace App\Admin\Controllers;

use App\Exports\AdminDishExport;
use App\Imports\DishImport;
use SCart\Core\Admin\Controllers\RootAdminController;
use App\Http\Requests\Admin\AdminDishRequest;
use App\Front\Models\ShopDish;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Validator;
use Request;
use Exception;



class AdminDishController extends RootAdminController
{
    public $languages;

    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {

        $data = [
            'title' => sc_language_render('admin.dish.list'),
            'subTitle' => '',
            'icon' => 'fa fa-indent',
            'urlDeleteItem' => sc_route_admin('admin.davicook_dish.delete'),
            'removeList' => 1, // 1 - Enable function delete list item
            'buttonRefresh' => 1, // 1 - Enable button refresh
            'buttonSort' => 1, // 1 - Enable button sort
            'css' => '',
            'js' => '',
            'method' => 'delete',
            'url_action' => sc_route_admin('admin.davicook_dish.create'),
            'url_export' => sc_route_admin('admin.davicook_dish.export'),
            'permGroup' => 'davicook_dish'
        ];
        //Process add content
        $data['menuRight'] = sc_config_group('menuRight', Request::route()->getName());
        $data['menuLeft'] = sc_config_group('menuLeft', Request::route()->getName());
        $data['topMenuRight'] = sc_config_group('topMenuRight', Request::route()->getName());
        $data['topMenuLeft'] = sc_config_group('topMenuLeft', Request::route()->getName());
        $data['blockBottom'] = sc_config_group('blockBottom', Request::route()->getName());

        $listTh = [
            'dish_code' => sc_language_render('admin.dish.code'),
            'dish_name' => sc_language_render('admin.dish.name'),
            'status' => sc_language_render('customer.status'),
            'action' => sc_language_render('action.title'),
        ];

        $sort_order = sc_clean(request('sort_order') ?? 'id_desc');
        $dish_name = sc_clean(request('dish_name') ?? '');
        $dish_code = sc_clean(request('dish_code') ?? '');
        $arrSort = [
            'id__desc' => sc_language_render('filter_sort.id_desc'),
            'id__asc' => sc_language_render('filter_sort.id_asc'),
            'name__desc' => sc_language_render('filter_sort.name_desc'),
            'name__asc' => sc_language_render('filter_sort.name_asc'),
        ];

        $cssTh = [
            'dish_code' => 'text-align: center; width: 30%',
            'dish_name' => 'text-align: center; width: 35%',
            'status' => 'text-align: center; width: 15%',
            'action' => 'text-align: center; width: 15%',
        ];
        $cssTd = [
            'dish_code' => 'text-align: center',
            'dish_name' => '',
            'status' => 'text-align: center',
            'action' => 'text-align: center',
        ];
        $data['cssTh'] = $cssTh;
        $data['cssTd'] = $cssTd;
        $dataTmp = (new ShopDish());
        if ($dish_name) {
            $dataTmp = $dataTmp->where('name', 'like', '%' . $dish_name . '%');
        }
        if ($dish_code) {
            $dataTmp = $dataTmp->where('code', 'like', '%' . $dish_code . '%');
        }
        if ($sort_order && array_key_exists($sort_order, $arrSort)) {
            $field = explode('__', $sort_order)[0];
            $sort_field = explode('__', $sort_order)[1];
            $dataTmp = $dataTmp->orderBy($field, $sort_field);
        } else {
            $dataTmp = $dataTmp->orderBy('id', 'desc');
        }
        $dataTmp = $dataTmp->paginate(config('pagination.admin.medium'));
        $dataTr = [];
        foreach ($dataTmp as $key => $row) {
            $dataTr[$row['id']] = [
                'dish_code' => $row['code'] ?? '',
                'dish_name' => $row['name'] ?? '',
                'status' => $row['status'] ? '<span class="badge badge-success">ON</span>' : '<span class="badge badge-danger">OFF</span>',
                'action' => '
                    <a data-perm="davicook_dish:detail" href="' . sc_route_admin('admin.davicook_dish.edit', ['id' => $row['id'] ?? 'not-found-id']) . '"><span title="' . sc_language_render('action.edit') . '" type="button" class="btn btn-flat btn-sm btn-primary"><i class="fa fa-edit"></i></span></a>&nbsp;

                    <span data-perm="davicook_dish:delete"  onclick="deleteItem(\'' . $row['id'] . '\');"  title="' . sc_language_render('action.delete') . '" class="btn btn-flat btn-sm btn-danger"><i class="fas fa-trash-alt"></i></span>'
                ,
            ];
        }
        $data['listTh'] = $listTh;
        $data['dataTr'] = $dataTr;
        $data['pagination'] = $dataTmp->appends(request()->except(['_token', '_pjax']))->links($this->templatePathAdmin . 'component.pagination');
        $data['resultItems'] = sc_language_render('admin.result_item', ['item_from' => $dataTmp->firstItem(), 'item_to' => $dataTmp->lastItem(), 'total' => $dataTmp->total()]);

        //menuRight
        $data['menuRight'][] = '<a data-perm="davicook_dish:create" href="' . sc_route_admin('admin.davicook_dish.create') . '" class="btn  btn-success  btn-flat" title="New" id="button_create_new">
            <i class="fa fa-plus" title="' . sc_language_render('admin.add_new') . '"></i>
            </a>
            <a data-perm="davicook_dish:import" href="' . sc_route_admin('admin.davicook_dish.import_excel') . '" class="btn  btn-success  btn-flat" title="Nhập Excel" id="button_import">
            <i class="fa fa-file-import" title="' . sc_language_render('category-import') . '"></i>' . sc_language_render('category-import') .
            '</a>
            <a data-perm="davicook_dish:export" href="javascript:void(0)" class="btn  btn-success  btn-flat" title="Xuất Excel" id="button_export_file">
            <i class="fa fa-file-export" title="' . sc_language_render('category-export') . '"></i> ' . sc_language_render('category-export') .
            '</a>';
        //=menuRight

        //menuSort
        $optionSort = '';
        foreach ($arrSort as $key => $status) {
            $optionSort .= '<option  ' . (($sort_order == $key) ? "selected" : "") . ' value="' . $key . '">' . $status . '</option>';
        }
        $data['urlSort'] = sc_route_admin('admin.davicook_dish.index', request()->except(['_token', '_pjax', 'sort_order']));
        $data['optionSort'] = $optionSort;
        //=menuSort

        //menuSearch
        $data['topMenuRight'][] = '
                <form action="' . sc_route_admin('admin.davicook_dish.index') . '" id="button_search">
                <div class="input-group input-group">
                    <div class="form-group" style="margin-right: 10px">
                        <label>Tên món ăn:</label>
                        <div class="input-group">
                            <input type="text" name="dish_name" class="form-control rounded-0 float-right" placeholder="Tên món ăn" value="'.$dish_name.'">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Mã món ăn</label>
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

        return view($this->templatePathAdmin . 'screen.list_dish')
            ->with($data);
    }
    public function create() {
        $data = [
            'title' => sc_language_render('admin.dish.add_new_title'),
            'subTitle' => '',
            'icon' => 'fa fa-plus',
            'url_action' => sc_route_admin('admin.davicook_dish.create'),
            'add_dish' => 1,
        ];

        return view($this->templatePathAdmin . 'screen.dish_form')
            ->with($data);
    }
    public function postCreate(AdminDishRequest $request) {
        $data = $request->validated();
        $data['status'] = empty($data['status']) ? 0 : 1;
        $dataInsert = [
            'name' => $data['name'],
            'code' => $data['code'],
            'status' => $data['status']
        ];
        try{
            $dataInsert = sc_clean($dataInsert, [], true);
            ShopDish::create($dataInsert);
        } catch(Exception $e) {
            return redirect()->back()->withInput($data)->with('error', sc_language_render('action.failed'));
        }
        return redirect()->route('admin.davicook_dish.index')->with('success', sc_language_render('action.create_success'));
    }

    public function edit($id)
    {
        $dish = ShopDish::findOrFail($id);

        $data = [
            'title' => sc_language_render('admin.dish.edit_title'),
            'subTitle' => '',
            'icon' => 'fa fa-edit',
            'dish' => $dish,
            'method' => '1',
            'url_action' => sc_route_admin('admin.davicook_dish.edit'),
        ];

        return view($this->templatePathAdmin . 'screen.dish_form')
            ->with($data);
    }

    public function postEdit($id, AdminDishRequest $request) {
        $dish = ShopDish::findOrFail($id);
        $data = $request->validated();
        $data['status'] = empty($data['status']) ? 0 : 1;
        $dataUpdate = [
            'name' => $data['name'],
            'code' => $data['code'],
            'status' => $data['status']
        ];
        try{
            $dataUpdate = sc_clean($dataUpdate, [], true);
            $dish->update($dataUpdate);
        } catch(Exception $e) {
            return redirect()->back()->withInput($data)->with('error', sc_language_render('action.failed'));
        }
        return redirect()->route('admin.davicook_dish.index')->with('success', sc_language_render('action.edit_success'));
    }

    public function deleteList()
    {
        if (!request()->ajax()) {
            return response()->json(['error' => 1, 'msg' => sc_language_render('admin.method_not_allow')]);
        }
        $ids = request('ids');
        $arrID = explode(',', $ids);
        ShopDish::destroy($arrID);
        return response()->json(['error' => 0, 'msg' => '']);
    }

    public function exportExcel() {
        $ids = [];
        if (!empty(request('ids'))) {
            $ids = explode(',',request('ids'));
        }
        $dataSearch = [
            'keyword' => sc_clean(request('keyword') ?? '')
        ];
        return Excel::download(new AdminDishExport($dataSearch, $ids), 'DanhSachMonAn-' . Carbon::now() . '.xlsx');
    }
    // Import Excel

    public function importExcel()
    {
        return view($this->templatePathAdmin . 'screen.dish_import_excel_templete',
            [
                'title' => sc_language_render('admin.dish.import')
            ]);
    }

    public function postImportExcel()
    {
        $error_dupticate = [];
        $file = request()->file('excel_file');
        $raw_excel_array = null;
        $count = 0;
        $messages = '';

        DB::beginTransaction();
        try {
            if ($file) {
                if (!$file || !is_file($file) || !in_array($file->extension(), ['xls', 'xlsx'])) {
                    throw new Exception('Định dạng file không hợp lệ!');
                }
                $raw_excel_array = cleanExcelFile(Excel::toArray(new DishImport(), $file))[0];
                if (count($raw_excel_array) > 0) {
                    $objDish = ShopDish::get();
                    $checkListExcel = [];
                    foreach ($raw_excel_array as $key => $row) {
                        // check giá trị nhập vào rỗng với các trường có dấu *
                        if (empty($row['ma_mon_an']) || empty($row['ten_mon_an'])) {
                            $error_dupticate[($key+2)] = 'Có mục trống, vui lòng kiểm tra lại';
                            throw new Exception('Lỗi dữ liệu: Các mục dấu * bắt buộc nhập!');
                            break;
                        }
                        // check giá trị nhập vào của trạng thái
                        if($row['trang_thai'] != 1 && $row['trang_thai'] !== 0){
                            $error_dupticate[($key+2)] =  $row['trang_thai'];
                            throw new Exception('Lỗi dữ liệu: Trạng thái chỉ được nhập giá trị 1 hoặc 0');
                            break;
                        }
                        $checkExcel = [
                            'name' => $row['ten_mon_an'],
                            'code' => $row['ma_mon_an'],
                            'status' => $row['trang_thai'],
                        ];
                        //check mã món ăn
                        if (!preg_match(config('validate.admin.code'), $row['ma_mon_an'])) {
                            $error_dupticate[($key + 2)] = $row['ma_mon_an'];
                            throw new Exception('Lỗi dữ liệu: Mã món ăn không hợp lệ!');
                            break;
                        }
                        // check tồn tại trong excel
                        $checkListExcel[] = $checkExcel ?? [];
                        $codeListDishExcel = data_get($checkListExcel,'*.code');
                        $uniqueCodeListDishExcel = array_unique($codeListDishExcel);
                        if (count($codeListDishExcel) != count($uniqueCodeListDishExcel) ) {
                            $error_dupticate[($key+2)] = $row['ma_mon_an'];
                            throw new Exception('Lỗi dữ liệu: Mã món ăn đã tồn tại trong file excel');
                            break;
                        }
                        $codeDish = $this->findCodeDishArray(trim($row['ma_mon_an']), $objDish);
                        if ($codeDish) {
                            $error_dupticate[($key+2)] = $row['ma_mon_an'];
                            throw new Exception('Lỗi dữ liệu: Mã món ăn đã tồn tại trên hệ thống');
                            break;
                        }
                        $dataInsert = new ShopDish($checkExcel);
                        if ($dataInsert->save()) {
                            $count++;
                        } else {
                            throw new Exception('Lỗi không xác định, vui lòng kiểm tra lại');
                            break;
                        }
                    }
                }
            }
        } catch (\Throwable $e) {
            DB::rollBack();
            $messages = $e->getMessage();
            $with_return = ['error' => $messages];
            if (count($error_dupticate) > 0) {
                $with_return['dupticateCode'] = $error_dupticate;
            };
            return redirect()->back()->with($with_return);
        }
        DB::commit();
        $messages = "đã nhập thành công $count bản ghi";
        return redirect()->route('admin.davicook_dish.index')->with(['success' => $messages ]);

    }

    public function findCodeDishArray($input, $objDish)
    {
        $array_code = $objDish->keyBy('code')->toArray();
        $search_result = in_array($input, array_keys($array_code));
        if ($search_result) {
            return $objDish[$search_result]->toArray();
        }
        return false;
    }

}