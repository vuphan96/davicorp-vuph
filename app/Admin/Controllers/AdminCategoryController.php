<?php

namespace App\Admin\Controllers;

use App\Admin\Models\AdminCategoryDescription;
use App\Exceptions\ImportException;
use App\Exports\ShopCategoryExport;
use App\Imports\CategoryImport;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Request;
use App\Http\Requests\Admin\AdminCategoryRequest;
use Google\Service\Dfareporting\Ad;
use SCart\Core\Admin\Controllers\RootAdminController;
use App\Admin\Models\AdminCategory;
use App\Front\Models\ShopCategory;
use SCart\Core\Front\Models\ShopCategoryDescription;
use SCart\Core\Front\Models\ShopProductCategory;
use App\Admin\Models\AdminProduct;
use SCart\Core\Front\Models\ShopLanguage;
use Validator;
use SCart\Core\Admin\Admin;
use Illuminate\Support\Str;


class AdminCategoryController extends RootAdminController
{
    public $languages;

    public function __construct()
    {
        parent::__construct();
        $this->languages = ShopLanguage::getListActive();
    }

    public function index()
    {
        $data = [
            'title' => sc_language_render('admin.category.list'),
            'subTitle' => '',
            'icon' => 'fa fa-indent',
            'urlDeleteItem' => sc_route_admin('admin_category.delete'),
            'removeList' => 1, // 1 - Enable function delete list item
            'buttonRefresh' => 1, // 1 - Enable button refresh
            'buttonSort' => 1, // 1 - Enable button sort
            'css' => '',
            'js' => '',
            'permGroup' => 'category'
        ];
        //Process add content
        $data['menuRight'] = sc_config_group('menuRight', Request::route()->getName());
        $data['menuLeft'] = sc_config_group('menuLeft', Request::route()->getName());
        $data['topMenuRight'] = sc_config_group('topMenuRight', Request::route()->getName());
        $data['topMenuLeft'] = sc_config_group('topMenuLeft', Request::route()->getName());
        $data['blockBottom'] = sc_config_group('blockBottom', Request::route()->getName());

        $listTh = [
            'image' => sc_language_render('admin.category.image'),
            'title' => sc_language_render('admin.category.title'),
            'sku' => sc_language_render('admin.category.code'),
            'status' => sc_language_render('admin.category.status'),
        ];

        if (sc_check_multi_store_installed() && session('adminStoreId') == SC_ID_ROOT) {
            // Only show store info if store is root
            $listTh['shop_store'] = sc_language_render('front.store_list');
        }

        $listTh['action'] = sc_language_render('action.title');

        $sort_order = sc_clean(request('sort_order') ?? 'id_desc');
        $keyword = sc_clean(request('keyword') ?? '');
        $arrSort = [
            'id__desc' => sc_language_render('filter_sort.id_desc'),
            'id__asc' => sc_language_render('filter_sort.id_asc'),
            'name__desc' => sc_language_render('filter_sort.title_desc'),
            'name__asc' => sc_language_render('filter_sort.title_asc'),
        ];
        $cssTd = [
            'image' => 'text-align:center',
            'title' => '',
            'sku' =>'' ,
            'status' => 'text-align:center',
            'action' => 'text-align:center'
        ];
        $data['cssTd'] = $cssTd;
        $dataSearch = [
            'keyword' => $keyword,
            'sort_order' => $sort_order,
            'arrSort' => $arrSort,
        ];
        $dataTmp = (new AdminCategory)->getCategoryListAdmin($dataSearch);

        if (sc_check_multi_store_installed() && session('adminStoreId') == SC_ID_ROOT) {
            // Only show store info if store is root
            $arrId = $dataTmp->pluck('id')->toArray();
            if (function_exists('sc_get_list_store_of_category')) {
                $dataStores = sc_get_list_store_of_category($arrId);
            } else {
                $dataStores = [];
            }
        }

        $dataTr = [];
        foreach ($dataTmp as $key => $row) {
            $dataMap = [
                'image' => sc_image_render($row->getThumb(), '50px', '50px', $row['name']),
                'title' => $row['name'] ?? '',
                'sku' => $row['sku'] ?? '',
                'status' => $row['status'] ? '<span class="badge badge-success">ON</span>' : '<span class="badge badge-danger">OFF</span>',
            ];

            if (sc_check_multi_store_installed() && session('adminStoreId') == SC_ID_ROOT) {
                // Only show store info if store is root
                if (!empty($dataStores[$row['id']])) {
                    $storeTmp = $dataStores[$row['id']]->pluck('code', 'id')->toArray();
                    $storeTmp = array_map(function ($code) {
                        return '<a target=_new href="' . sc_get_domain_from_code($code) . '">' . $code . '</a>';
                    }, $storeTmp);
                    $dataMap['shop_store'] = '<i class="nav-icon fab fa-shopify"></i> ' . implode('<br><i class="nav-icon fab fa-shopify"></i> ', $storeTmp);
                }
            }
            $dataMap['action'] = '
            <a data-perm="category:detail" href="' . sc_route_admin('admin_category.edit', ['id' => $row['id'] ? $row['id'] : 'not-found-id']) . '"><span title="' . sc_language_render('action.edit') . '" type="button" class="btn btn-flat btn-sm btn-primary"><i class="fa fa-edit"></i></span></a>&nbsp;
            <span data-perm="category:delete" onclick="deleteItem(\'' . $row['id'] . '\');"  title="' . sc_language_render('action.delete') . '" class="btn btn-flat btn-sm btn-danger"><i class="fas fa-trash-alt"></i></span>&nbsp;';
            $dataTr[$row['id']] = $dataMap;
        }

        $data['listTh'] = $listTh;
        $data['dataTr'] = $dataTr;
        $data['pagination'] = $dataTmp->appends(request()->except(['_token', '_pjax']))->links($this->templatePathAdmin . 'component.pagination');
        $data['resultItems'] = sc_language_render('admin.result_item', ['item_from' => $dataTmp->firstItem(), 'item_to' => $dataTmp->lastItem(), 'total' => $dataTmp->total()]);


        //menuRight
        $data['menuRight'][] = '
        <a data-perm="category:create" href="' . sc_route_admin('admin_category.create') . '" class="btn  btn-success  btn-flat" title="New" id="button_create_new">
            <i class="fa fa-plus" title="' . sc_language_render('action.add_new') . '"></i>
        </a>
        <a data-perm="category:import" href="' . sc_route_admin('admin_category.import') . '" class="btn  btn-success  btn-flat" title="New" id="button_import">
            <i class="fa fa-file-import" title="' . sc_language_render('category-import') . '"></i>' . sc_language_render('category-import') .
        '</a>
        <a data-perm="category:export" href="' . sc_route_admin('admin_category.export') . '" class="btn  btn-success  btn-flat" title="New" id="button_export">
            <i class="fa fa-file-export" title="' . sc_language_render('category-export') . '"></i> ' . sc_language_render('category-export') .
        '</a>';
        //=menuRight

        //menuHiddenImportForm

        //=menuHiddenImportForm

        //menuSort
        $optionSort = '';
        foreach ($arrSort as $key => $sort) {
            $optionSort .= '<option  ' . (($sort_order == $key) ? "selected" : "") . ' value="' . $key . '">' . $sort . '</option>';
        }

        $data['urlSort'] = sc_route_admin('admin_category.index', request()->except(['_token', '_pjax', 'sort_order']));
        $data['optionSort'] = $optionSort;
        //=menuSort

        //menuSearch
        $data['topMenuRight'][] = '
                <form action="' . sc_route_admin('admin_category.index') . '" id="button_search">
                <div class="input-group input-group" style="width: 350px;">
                    <input type="text" name="keyword" class="form-control rounded-0 float-right" placeholder="' . sc_language_render('admin.category.search_placeholder') . '" value="' . $keyword . '">
                    <div class="input-group-append">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i></button>
                    </div>
                </div>
                </form>';
        //=menuSearch

        return view($this->templatePathAdmin . 'screen.category.index')
            ->with($data);
    }

    /*
     * Form create new item in admin
     * @return [type] [description]
     */
    public function create()
    {
        $data = [
            'title' => sc_language_render('admin.category.add_new_title'),
            'subTitle' => '',
            'title_description' => sc_language_render('admin.category.add_new_des'),
            'icon' => 'fa fa-plus',
            'languages' => $this->languages,
            'category' => [],
            'categories' => (new AdminCategory)->getTreeCategoriesAdmin(),
            'url_action' => sc_route_admin('admin_category.create'),
        ];


        return view($this->templatePathAdmin . 'screen.category.form_add_and_edit')
            ->with($data);
    }

    /*
     * Post create new item in admin
     * @return [type] [description]
     */
    public function postCreate(AdminCategoryRequest $request)
    {
        $data = $request->validated();
        DB::beginTransaction();
        try {
            $dataInsert = [
                'image' => $data['image'],
                'sku' => $data['sku'],
                'sort' => $data['sort'],
                'status' => !empty($data['status']) ? 1 : 0,
                'name' => $data['title_category'],
            ];
            $dataInsert = sc_clean($dataInsert, [], true);
            $category = AdminCategory::createCategoryAdmin($dataInsert);
            if (!$category) {

                throw new ImportException(sc_language_render('action.failed'));
            }
        } catch (Throwable $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage());
        }
        DB::commit();
        return redirect()->route('admin_category.index')->with('success', sc_language_render('action.create_success'));
    }

    /*
     * Form edit
     */
    public function edit($id)
    {
        $category = AdminCategory::getCategoryAdmin($id);

        if (!$category) {
            return redirect()->route('admin.data_not_found')->with(['url' => url()->full()]);
        }

        $data = [
            'title' => sc_language_render('action.edit'),
            'subTitle' => '',
            'title_description' => '',
            'icon' => 'fa fa-edit',
            'languages' => $this->languages,
            'category' => $category,
            'categories' => (new AdminCategory)->getTreeCategoriesAdmin(),
            'url_action' => sc_route_admin('admin_category.edit', ['id' => $category['id']]),
            'data_perm_submit' => 'category:edit'
        ];
        return view($this->templatePathAdmin . 'screen.category.form_add_and_edit')
            ->with($data);
    }

    /*
     * update status
     */
    public function postEdit($id, AdminCategoryRequest $request)
    {
        DB::beginTransaction();
        $category = AdminCategory::getCategoryAdmin($id);
        if (!$category) {
            return redirect()->route('admin.data_not_found')->with(['url' => url()->full()]);
        }
        $data = $request->validated();
        //Edit
        try {
            $dataUpdate = [
                'image' => $data['image'],
                'sku' => $data['sku'],
                'sort' => $data['sort'],
                'status' => !empty($data['status']) ? 1 : 0,
                'name' => $data['title_category'],
            ];
            $dataUpdate = sc_clean($dataUpdate, [], true);
            $result = $category->update($dataUpdate);

            if (!$result) {
                throw new ImportException(sc_language_render('action.failed'));
            }
        } catch (Throwable $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage());
        }
        DB::commit();
        return redirect()->route('admin_category.index')->with('success', sc_language_render('action.edit_success'));
    }

    /*
    Delete list Item
    Need mothod destroy to boot deleting in model
    */
    public function deleteList()
    {
        $error_stack = [];
        $custom_msg = '';

        if (!request()->ajax()) {
            return response()->json(['error' => 1, 'msg' => sc_language_render('admin.method_not_allow')]);
        }
        $objProductCategory = (new AdminProduct())->groupBy('category_id')->get();
        $id_search = $objProductCategory->keyBy('category_id')->toArray();
        $ids = request('ids');
        $arrID = explode(',', $ids);
        $arrDontPermission = [];
        foreach ($arrID as $key => $id) {
            if (!$this->checkPermisisonItem($id)) {
                $arrDontPermission[] = $id;
            }
            $search_id = in_array($id, array_Keys($id_search));
            if ($search_id) {
                return response()->json(['error' => 1, 'msg' => sc_language_render('admin.category_no_delete')]);
            }
        }
        if (count($arrDontPermission)) {
            return response()->json(['error' => 1, 'msg' => sc_language_render('admin.remove_dont_permisison') . ': ' . json_encode($arrDontPermission)]);
        }

        $currentItem = ShopCategory::whereIn('parent', $arrID)->get();
        if(count($currentItem)){
            return response()->json(['error' => 1, 'msg' => sc_language_render('admin.category.remove_exist_child')]);
        }

        AdminCategory::destroy($arrID);
        sc_clear_cache('cache_category');
        return response()->json(['error' => 0, 'msg' => '']);
    }

    // View Import

    /**
     * Check permisison item
     */
    public function checkPermisisonItem($id)
    {
        return AdminCategory::getCategoryAdmin($id);
    }

    // Post Import

    public function import()
    {
        return view($this->templatePathAdmin . 'screen.category.view_import',
            [
                'title' => sc_language_render('admin.category.import')
            ]);
    }

    public function importPost()
    {
        $file = request()->file('excel_file');
        $raw_excel_array = null;
        $dupticated = 0; //Dupticated flag
        $success_count = 0;
        $insert_array = [];
        $current_category = null;
        $error = 0; //Global error flag
        $row_count = 1;
        $messages = ''; //Global return message
        $error_dupticate = [];
        $error_noparent = [];


        DB::beginTransaction();
        if ($file) {
            if (in_array($file->extension(), ['xls', 'xlsx'])) {
                if (is_file($file)) {
                    $raw_excel_array = cleanExcelFile(Excel::toArray(new CategoryImport(), $file))[0];
                    if (count($raw_excel_array) > 0) {
                        $objCat = AdminCategory::get();
                        $checkListExcel = [];
                        foreach ($raw_excel_array as $key => $row) {
                            if (empty($row['ten_danh_muc']) || empty($row['ma_danh_muc'])) {
                                $error = 1;
                                $error_dupticate[($key+2)] = 'Có mục còn trống';
                                $messages = 'Lỗi dữ liệu: Các mục dấu * không được để trống';
                                break;
                            }
                            $checkExcel = [
                                'name' => $row['ten_danh_muc'],
                                'code' => $row['ma_danh_muc']
                            ];
                            // check mã danh mục sản phẩm
                            if (!preg_match(config('validate.admin.code'), $row['ma_danh_muc'])) {
                                $error = 1;
                                $messages = 'Lỗi dữ liệu: Mã danh mục không hợp lệ';
                                $error_dupticate[($key+2)] = $row['ma_danh_muc'];
                                break;
                            }
                            // check tồn tại trong excel
                            $checkListExcel[] = $checkExcel ?? [];
                            $skuListCatExcel = data_get($checkListExcel,'*.code');
                            $uniqueSkuListExcel = array_unique($skuListCatExcel);
                            if (count($skuListCatExcel) != count($uniqueSkuListExcel) ) {
                                $error = 1;
                                $messages = 'Lỗi dữ liệu: Mã danh mục bị trùng trong file excel';
                                $error_dupticate[($key+2)] = $row['ma_danh_muc'];
                                break;
                            }
                            $nameListExcel = data_get($checkListExcel, '*.name');
                            $uniqueNameListExcel = array_unique($nameListExcel);
                            if (count($nameListExcel) != count($uniqueNameListExcel)) {
                                $error = 1;
                                $messages = 'Lỗi dữ liệu: Tên danh mục bị trùng trong file excel';
                                $error_dupticate[($key+2)] = $row['ten_danh_muc'];
                                break;
                            }
                            $codeCat = $this->findskuCategoryArray(trim($row['ma_danh_muc']), $objCat);
                            $row_count++;
                            // Check tồn tại chưa
                            if ($codeCat) {
                                $error = 1;
                                $messages = 'Lỗi trùng dữ liệu. Mã danh mục đã có trên hệ thống!';
                                $error_dupticate[$row_count] = $row['ma_danh_muc'];
                                break;
                            }
                            // Chưa tồn tại tạo object mới
                            $category = new ShopCategory([
                                'sku' =>  $row['ma_danh_muc'],
                                'status' => $row['trang_thai'] ? 1 : 0,
                                'name' => $row['ten_danh_muc']
                            ]);

                            if (!$category->save()) {
                                $error = 1;
                                $messages = 'Lỗi dữ liệu: Lỗi Không xác định. Vui lòng kiểm tra dữ liệu đầu vào';
                                break;
                            }
                            $success_count++;
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
            return redirect(sc_route_admin('admin_category.index'))->with(['success' => $messages]);
        } else {
            DB::rollBack();
            $with_return = ['error' => $messages];
            if (count($error_dupticate) > 0) {
                $with_return['dupticate'] = $error_dupticate;
            };
            return redirect()->back()->with($with_return);
        }
    }

    public function export()
    {
        return Excel::download(new ShopCategoryExport, 'DanhMucSanPham-' . Carbon::now() . '.xlsx');
    }


    public function findSkuCategoryArray($input, $objCat)
    {
        $parent_search = $objCat->keyBy('sku')->toArray();
        $search_result = in_array($input, array_keys($parent_search));
        if ($search_result) {
            return $objCat[$search_result]->toArray();
        }
        return false;
    }
}
