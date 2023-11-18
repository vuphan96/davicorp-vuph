<?php

namespace App\Admin\Controllers;

use App\Admin\Models\AdminProductPrice;
use App\Admin\Models\AdminProductPriceDetail;
use App\Front\Models\ShopProduct;
use App\Front\Models\ShopUserPriceboard;
use App\Http\Requests\Admin\AdminProductPriceRequest;
use App\Http\Requests\Admin\AdminProductPriceEditRequest;
use App\Http\Requests\Admin\AdminProductPriceDetailRequest;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use App\Exports\ShopProductPriceExportMulti;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\ProductPriceImportCell;
use App\Imports\ProductPriceImportRow;
use SCart\Core\Admin\Controllers\RootAdminController;
use App\Admin\Models\AdminProduct;
use SCart\Core\Front\Models\ShopProductDescription;
use Request;
use Exception;

class AdminProductPriceController extends RootAdminController
{
    public function __construct(AdminProductPrice $productprice, AdminProductPriceDetail $productpriceDetail)
    {
        $this->productprice = $productprice;
        $this->productpricedetail = $productpriceDetail;
        parent::__construct();
    }

    public function index()
    {
        $data = [
            // ,
            'title' => sc_language_render('admin.productprice.list'),
            'subTitle' => '',
            'icon' => 'fa fa-indent',
            'urlDeleteItem' => sc_route_admin('admin_price.delete'),
            'removeList' => 1, // 1 - Enable function delete list item
            'buttonRefresh' => 1, // 1 - Enable button refresh
            'buttonSort' => 1, // 1 - Enable button sort
            'css' => '',
            'js' => '',
            'url_action' => sc_route_admin('admin_price.create'),
            'permGroup' => 'price'
        ];
        //Process add content
        $data['menuRight'] = sc_config_group('menuRight', Request::route()->getName());
        $data['menuLeft'] = sc_config_group('menuLeft', Request::route()->getName());
        $data['topMenuRight'] = sc_config_group('topMenuRight', Request::route()->getName());
        $data['topMenuLeft'] = sc_config_group('topMenuLeft', Request::route()->getName());
        $data['blockBottom'] = sc_config_group('blockBottom', Request::route()->getName());

        $listTh = [
            'price_code' => sc_language_render('admin.product.price.code'),
            'name' => sc_language_render('admin.productprice.name'),
            'created_at' => sc_language_render('admin.productprice.created'),
            'creator' => sc_language_render('admin.productprice.creator'),
            'status' => sc_language_render('customer.status'),
            'action' => sc_language_render('action.title')

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
            'price_code' => 'text-align: center; width: 13%',
            'name' => 'text-align: center; width: 25%',
            'created_at' => 'text-align: center; width: 14%',
            'creator' => 'text-align: center; width: 12%',
            'status' => 'text-align: center; width: 13%',
            'action' => 'text-align: center; width: 20%'
        ];
        $cssTd = [
            'price_code' => 'text-align: center',
            'name' => '',
            'created_at' => 'text-align: center',
            'creator' => '',
            'status' => 'text-align: center',
            'action' => 'text-align: center'
        ];
        $data['cssTh'] = $cssTh;
        $data['cssTd'] = $cssTd;

        $dataTmp = (new AdminProductPrice())->getProductPriceListAdmin($dataSearch);
        $dataTr = [];
        foreach ($dataTmp as $key => $row) {
            $objUserPrice = ShopUserPriceboard::where('product_price_id', $row['id'])->get(['product_price_id']);
            $out = [];
            foreach ($objUserPrice as $item) {
                $out[] = $item->product_price_id;
            }
            $dataTr[$row['id']] = [
                'price_code' => $row['price_code'] ?? '',
                'name' => $row['name'] ?? '',
                'created_at' => carbon::createFromFormat('Y-m-d H:i:s', $row['created_at'])->format('d-m-Y') ?? '',
                'creator' => $row->User['username'] ?? '',
                'status' => !empty($out) ? '<span class="badge badge-success">Đang sử dụng</span>' : '<span class="badge badge-danger">Chưa sử dụng</span>',
                'action' => '
                    <a data-perm="price:detail" href="' . sc_route_admin('admin_price.edit', ['id' => $row['id'] ? $row['id'] : 'not-found-id']) . '"><span title="' . sc_language_render('action.edit') . '" type="button" class="btn btn-flat btn-sm btn-primary"><i class="fa fa-edit"></i></span></a>&nbsp;

                    <span data-perm="price:delete" onclick="deleteItem(\'' . $row['id'] . '\');"  title="' . sc_language_render('action.delete') . '" class="btn btn-flat btn-sm btn-danger"><i class="fas fa-trash-alt"></i></span>&nbsp;
                    <span data-perm="price:create" onclick="cloneProduct(\'' . $row['id'] . '\');" title="' . sc_language_render('product.admin.clone') . '" type="button" class="btn btn-flat btn-sm btn-secondary">
                    <i class="fa fa-clipboard"></i>
                    </span>',
            ];

        }
        $data['listTh'] = $listTh;
        $data['dataTr'] = $dataTr;
        $data['pagination'] = $dataTmp->appends(request()->except(['_token', '_pjax']))->links($this->templatePathAdmin . 'component.pagination');
        $data['resultItems'] = sc_language_render('admin.result_item', ['item_from' => $dataTmp->firstItem(), 'item_to' => $dataTmp->lastItem(), 'total' => $dataTmp->total()]);

        //menuRight
        $data['menuRight'][] = '<a  data-perm="price:create" href="' . sc_route_admin('admin_price.create') . '" class="btn  btn-success  btn-flat" title="New" id="button_create_new">
            <i class="fa fa-plus" title="' . sc_language_render('admin.add_new') . '"></i>
            </a>
            <a  data-perm="price:import" href="' . sc_route_admin('admin_price.import') . '" class="btn  btn-success  btn-flat" title="New" id="button_import">
            <i class="fa fa-file-import" title="' . sc_language_render('category-import') . '"></i>' . sc_language_render('category-import') .
            '</a>
            <a  data-perm="price:export" class="btn  btn-success  btn-flat" title="" id="button_export">
            <i  class="fa fa-file-export" title="' . sc_language_render('category-export') . '"></i> ' . sc_language_render('category-export') .
            '</a>';
        //menuSort
        $optionSort = '';
        foreach ($arrSort as $key => $status) {
            $optionSort .= '<option  ' . (($sort_order == $key) ? "selected" : "") . ' value="' . $key . '">' . $status . '</option>';
        }
        $data['urlSort'] = sc_route_admin('admin_price.index', request()->except(['_token', '_pjax', 'sort_order']));
        $data['optionSort'] = $optionSort;
        //=menuSort
        //menuSearch
        $data['topMenuRight'][] = '
                <form action="' . sc_route_admin('admin_price.index') . '" id="button_search">
                <div class="input-group input-group">
                    <input type="text" name="keyword" class="form-control rounded-0 float-right" placeholder="' . sc_language_render('admin.supplier.search_hint') . '" value="' . $keyword . '">
                    <div class="input-group-append">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i></button>
                    </div>
                </div>
                </form>';
        //=menuSearch
        return view($this->templatePathAdmin . 'screen.listproductprice')->with($data);
    }

    /**
     * Form create
     */

    public function create()
    {
        $data = [
            'title' => sc_language_render('admin.productprice.add_new_des'),
            'title_action' => '<i class="fa fa-plus" aria-hidden="true"></i> ' . sc_language_render('product.admin.add_product'),
            'subTitle' => '',
            'icon' => 'fa fa-indent',
            'urlDeleteItem' => sc_route_admin('admin_price.delete_detail'),
            'removeList' => 0, // 1 - Enable function delete list item
            'buttonRefresh' => 0, // 1 - Enable button refresh
            'buttonSort' => 0, // 1 - Enable button sort
            'css' => '',
            'js' => '',
            'url_action' => sc_route_admin('admin_price.create'),
            'title_description' => sc_language_render('product.admin.list'),
        ];
        return view($this->templatePathAdmin . 'screen.productprice_add')
            ->with($data);
    }

    /**
     * Post create new item in admin
     * @return [type] [description]
     **/
    public function postCreate(AdminProductPriceRequest $request)
    {
        $data = $request->validated();
        $user_id = admin()->id();
        $dataInsert = [
            'name' => $data['name'],
            'price_code' => $data['code'],
            'user_id' => $user_id
        ];
        try {
            $dataInsert = sc_clean($dataInsert, [], true);
            $obj = AdminProductPrice::create($dataInsert);
            $idProductPrice = $obj->id;
        } catch (Exception $e) {
            return response()->json([
                'error' => sc_language_render('action.failed'),
            ]);
        }
        return redirect()->route('admin_price.edit', ['id' => $idProductPrice])->with('success', sc_language_render('action.create_success'));

    }

    /**
     * Form edit
     */
    public function edit($id)
    {
        $productPrice = AdminProductPrice::findOrFail($id);
        $data = [
            'title' => sc_language_render('admin.productprice.edit_price'),
            'title_action' => '<i class="fa fa-plus" aria-hidden="true"></i> ' . sc_language_render('product.admin.add_product'),
            'subTitle' => '',
            'icon' => 'fa fa-indent',
            'urlDeleteItem' => sc_route_admin('admin_price.delete_detail'),
            'removeList' => 0, // 1 - Enable function delete list item
            'buttonRefresh' => 0, // 1 - Enable button refresh
            'buttonSort' => 0, // 1 - Enable button sort
            'css' => '',
            'productprice' => $productPrice,
            'js' => '',
            'url_action' => sc_route_admin('admin_price.edit', ['id' => $productPrice['id']]),
            'title_description' => sc_language_render('product.admin.list'),
        ];
        $listTh = [
            'name' => sc_language_render('admin.productprice.nameproduct'),
            'price_1' => sc_language_render('admin.productprice.teacher'),
            'price_2' => sc_language_render('admin.productprice.child'),
            'action' => sc_language_render('action.title')
        ];
        $keyword = sc_clean(request('keyword') ?? '');
        $dataSearch = [
            'keyword' => $keyword,
        ];
        if ($dataSearch['keyword']) {
            $dataTmp = (new AdminProductPriceDetail())->getProductPriceListAdmin($dataSearch, $id);
        } else {
            $obj = new AdminProductPriceDetail;
            $dataTmp = $obj->orderBy('id', 'desc')->where('product_price_id', $id)->paginate(config('pagination.admin.small'));
        }
        $data['objPro'] = ShopProduct::get();
        $dataTr = [];
        foreach ($dataTmp as $key => $row) {
            $dataTr[$row['id']] = [
                'product_id' => $row->product ? $row->product->name : '',
                'price_1' => '<a data-perm="price:edit" perm-type="disable" href="#" class="editable-required" data-name="' . sc_language_render('admin.productprice.teacher') . '" data-type="text" data-pk="' . $row['id'] . '" data-source="" data-url="' . route('admin_price.edit_price1', ['id' => $row['id']]) . '" data-title="' . sc_language_render('admin.productprice.teacher') . '" data-value="' . $row['price_1'] . '" data-original-title="" title="">' . number_format($row['price_1'], 0, ',', '.') . ' đ' ?? '0' . '</a>',
                'price_2' => '<a data-perm="price:edit" perm-type="disable" href="#" class="editable-required" data-name="' . sc_language_render('admin.productprice.child') . '" data-type="text" data-pk="' . $row['id'] . '" data-source="" data-url="' . route('admin_price.edit_price2', ['id' => $row['id']]) . '" data-title="' . sc_language_render('admin.productprice.child') . '" data-value="' . $row['price_2'] . '" data-original-title="" title="">' . number_format($row['price_2'], 0, ',', '.') . ' đ' ?? '0' . '</a>',
                'action' => '
                  <span data-perm="price:edit" onclick="deleteItem(\'' . $row['id'] . '\');"  title="' . sc_language_render('action.delete') . '" class="btn btn-flat btn-sm btn-danger"><i class="fas fa-trash-alt"></i></span>
                  ',
            ];
        }
        $data['listTh'] = $listTh;
        $data['dataTr'] = $dataTr;
        $data['pagination'] = $dataTmp->appends(request()->except(['_token', '_pjax']))->links($this->templatePathAdmin . 'component.pagination_price');
        $data['resultItems'] = sc_language_render('admin.result_item', ['item_from' => $dataTmp->firstItem(), 'item_to' => $dataTmp->lastItem(), 'total' => $dataTmp->total()]);

        $data['topMenuRight'][] = '
            <form action="' . sc_route_admin('admin_price.edit') . '" id="button_search">
            <div class="input-group input-group" style="width: 200px;">
                <input type="text" name="keyword" class="form-control rounded-0 float-right" placeholder="' . sc_language_render('admin.supplier.search_hint') . '" value="' . $keyword . '">
                <div class="input-group-append">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i></button>
                </div>
            </div>
            </form>';
        return view($this->templatePathAdmin . 'screen.productprice_edit')
            ->with($data);
    }

    /**
     * update status
     */
    public function postEdit($id, AdminProductPriceEditRequest $request)
    {

        $data = $request->validated();
        $productPrice = AdminProductPrice::findOrFail($id);
        $dataUpdate = [
            'name' => $data['name'],
            'price_code' => $data['code']
        ];
        try {
            $dataUpdate = sc_clean($dataUpdate, [], true);
            $productPrice->update($dataUpdate);
        } catch (Exception $e) {
            return response()->json([
                'error' => sc_language_render('action.failed'),
            ]);
        }
        return redirect()->route('admin_price.index')->with('success', sc_language_render('action.edit_success'));
    }

    public function deleteList()
    {
        if (!request()->ajax()) {
            return response()->json(['error' => 1, 'msg' => sc_language_render('admin.method_not_allow')]);
        }
        $objUserPrice = (new ShopUserPriceboard())->groupBy('product_price_id')->get();
        $id_search = $objUserPrice->keyBy('product_price_id')->toArray();
        $ids = request('ids');
        $arrID = explode(',', $ids);
        foreach ($arrID as $value) {
            $search_id = in_array($value, array_keys($id_search));
            if ($search_id) {
                return response()->json(['error' => 1, 'msg' => sc_language_render('action.no_delete_using')]);
            }
        }
        $delete = AdminProductPrice::destroy($arrID);
        if ($delete == true) {
            AdminProductPriceDetail::where('product_price_id', $arrID)->delete();
        }
        return response()->json(['error' => 0, 'msg' => sc_language_render('action.delete_success')]);
    }

    /**
     * Clone product
     * Only clone single product
     * @return  [type]  [return description]
     */
    public function cloneProductPrice()
    {
        if (!request()->ajax()) {
            return response()->json(['error' => 1, 'msg' => sc_language_render('admin.method_not_allow')]);
        }
        $pId = request('pId');
        $productPrice = AdminProductPrice::findorFail($pId);
        if (!$productPrice) {
            return response()->json(['error' => 1, 'msg' => 'Price list not found']);
        }
        $dataInsert = [
            'name' => $productPrice['name'],
            'price_code' => $productPrice['price_code'] . '-Copy-' . time(),
            'user_id' => $productPrice['user_id']
        ];
        try {
            $newProductPrice = AdminProductPrice::create($dataInsert);
            $idProductPrice = $newProductPrice->id;
            $objProductPriceDetail = $this->productpricedetail->getItems($pId);
            foreach ($objProductPriceDetail as $itemDetail) {
                $data = [
                    'product_price_id' => $idProductPrice,
                    'price_1' => $itemDetail->price_1,
                    'price_2' => $itemDetail->price_2,
                    'price_3' => $itemDetail->price_3,
                    'price_4' => $itemDetail->price_4,
                    'product_id' => $itemDetail->product_id
                ];
                $newProductPriceDetail = AdminProductPriceDetail::create($data);
            }

            DB::connection(SC_CONNECTION)->commit();
            return response()->json(['error' => 0, 'msg' => sc_language_render('product.admin.clone_success')]);
        } catch (Exception $e) {
            DB::connection(SC_CONNECTION)->rollBack();
            return response()->json(['error' => 1, 'msg' => $e->getMessage()]);
        }
    }

    /**
     * Form add
     */

    public function postAdd($id, AdminProductPriceDetailRequest $request)
    {

        $data = $request->validated();
        $objProductPrice = new AdminProductPriceDetail();
        $countPrice = $objProductPrice->where('product_id', $data['idProduct'])
            ->where('product_price_id', $data['name'])->get()->count();
        if ($countPrice > 0) {
            return redirect()->route('admin_price.edit', ['id' => $id])->with('error', 'Sản phẩm đã có trong bảng giá');
        }
        $dataInsert = [
            'product_price_id' => $data['name'],
            'price_1' => $data['price1'],
            'price_2' => $data['price2'],
            'product_id' => $data['idProduct']
        ];
        try {
            $dataInsert = sc_clean($dataInsert, [], true);
            $obj = AdminProductPriceDetail::create($dataInsert);
        } catch (Exception $e) {
            return response()->json([
                'error' => sc_language_render('action.failed'),
            ]);
        }
        return redirect()->route('admin_price.edit', ['id' => $id])->with('success', sc_language_render('action.create_success'));

    }

    public function deleteDetail()
    {
        if (!request()->ajax()) {
            return response()->json(['error' => 1, 'msg' => sc_language_render('admin.method_not_allow')]);
        }
        $ids = request('ids');

        $arrID = explode(',', $ids);
        $delete = AdminProductPriceDetail::destroy($arrID);
        return response()->json(['error' => 0, 'msg' => sc_language_render('action.delete_success')]);
    }

    public function postEditPrice2($id)
    {
        if (!request()->ajax()) {
            return response()->json(['error' => 1, 'msg' => sc_language_render('admin.method_not_allow')]);
        } else {
            $data = request()->all();
            $value = sc_clean($data['value']); //giá thay đổi
            $idProduct = sc_clean($data['pk']); //id của sản phẩm
            $productPrice = AdminProductPriceDetail::findOrFail($idProduct);
            $dataUpdate = [
                'price_2' => $value
            ];
            $log = "Khách hàng đã thay đổi giá học sinh từ " . $productPrice->price_2 ." đổi thành ". $value ;
            Log::info($log);
            try {
                $dataUpdate = sc_clean($dataUpdate, [], true);
                $productPrice->update($dataUpdate);
            } catch (Exception $e) {
                return response()->json([
                    'error' => sc_language_render('action.failed'),
                ]);
            }
            return response()->json(['error' => 0, 'msg' => sc_language_render('action.update_success')]);
        }
    }

    public function postEditPrice1($id)
    {
        if (!request()->ajax()) {
            return response()->json(['error' => 1, 'msg' => sc_language_render('admin.method_not_allow')]);
        } else {
            $data = request()->all();
            $value = sc_clean($data['value']); //giá thay đổi
            $idProduct = sc_clean($data['pk']); //id của sản phẩm
            $productPrice = AdminProductPriceDetail::findOrFail($idProduct);
            $dataUpdate = [
                'price_1' => $value,
            ];
            $log = "Khách hàng đã thay đổi giá giáo viên từ " . $productPrice->price_1 ." đổi thành ". $value;
            Log::info($log);
            try {
                $dataUpdate = sc_clean($dataUpdate, [], true);
                $productPrice->update($dataUpdate);
            } catch (Exception $e) {
                return response()->json([
                    'error' => sc_language_render('action.failed'),
                ]);
            }
            return response()->json(['error' => 0, 'msg' => sc_language_render('action.update_success')]);
        }
    }

    public function exportExcel()
    {
        $ids = [];
        $out = [];
        if (!empty(request('ids'))) {
            $ids = explode(',', request('ids'));
            foreach ($ids as $id) {
                $out[] = (int)$id;
            }
            if (count($out) > 15) {
                return redirect()->back()->with('error', 'Chỉ được phép xuất tối đa 15 báo giá');
            }
        } else {
            return redirect()->back()->with('error', 'Lỗi xuất excel');
        }

        return Excel::download(new ShopProductPriceExportMulti($out), 'BangGiaSanPham-' . Carbon::now() . '.xlsx');
    }

    public function importExcel()
    {
        return view($this->templatePathAdmin . 'screen.product_price_import_excel_templete',
            [
                'title' => sc_language_render('admin.product.price.import')
            ]);
    }

    public function importExcelPost()
    {
        $messages = '';
        $error_dupticate = [];
        $error_dupticate_code = [];

        DB::beginTransaction();
        try {

            $objProduct = (new ShopProduct)->pluck('sku', 'id')->toArray(); //Static data
            $objProductPrice = (new AdminProductPrice())->pluck('price_code')->toArray();
            $objProductPriceDetail = (new AdminProductPriceDetail())->pluck('product_id')->toArray();

            $file = request()->file('excel_file');
            $requiredRow = ['ma_san_pham', 'gia_cho_giao_vien', 'gia_cho_hoc_sinh'];
            $defaultFormatRow = ['stt', 'ma_san_pham', 'ten_san_pham', 'gia_cho_giao_vien', 'gia_cho_hoc_sinh'];

            if (!$file || !is_file($file) || !in_array($file->extension(), ['xls', 'xlsx'])) {
                throw new Exception('Định dạng file không hợp lệ!');
            }

            $raw_excel_productPrice = Excel::toArray(new ProductPriceImportCell(), $file);
            $raw_excel_productPriceDetail = Excel::toArray(new ProductPriceImportRow(), $file);

            $rawProductPrice = !empty($raw_excel_productPrice) ? cleanExcelFile($raw_excel_productPrice) : [];
            $rawProductPriceDetail = !empty($raw_excel_productPriceDetail) ? cleanExcelFile($raw_excel_productPriceDetail) : [];
            $productPricesInsert = [];

            $errorBags = [];

            $insertedPriceboard = [];
            foreach ($rawProductPrice as $sheet_key => $productPrice) {
                $real_sheet = $sheet_key + 1;
                //check templates
                if (!checkTemplate(array_filter(array_keys($rawProductPriceDetail[$sheet_key][0])), $defaultFormatRow)) {
                    $errorBags[$real_sheet]["master"][] = "Mẫu nhập không đúng";
                }
                if (empty($productPrice[0][2])) {
                    $errorBags[$real_sheet]["master"][] = "Mã báo giá không hợp lệ";
                }
                if (empty($productPrice[1][2])) {
                    $errorBags[$real_sheet]["master"][] = "Tên báo giá không hợp lệ";
                }
                $productPriceInsert = new AdminProductPrice([
                    'price_code' => $productPrice[0][2] ?? '',
                    'name' => $productPrice[1][2] ?? '',
                    'user_id' => admin()->id()
                ]);
                $insertedPriceboard[] = $productPriceInsert;
                $productPricesInsert[] = $productPriceInsert->toArray() ?? [];
                // Check before save
                $productPriceList = data_get($productPricesInsert, '*.price_code') ?? [];
                $uniqueProductPriceList = array_unique($productPriceList) ?? [];
                //check trùng mã bảng giá
                if (count($productPriceList) != count($uniqueProductPriceList)) {
                    $errorBags[$real_sheet]["master"][] = "Mã bảng giá trùng trong file";
                }

                if (count(array_intersect($productPriceList, $objProductPrice)) > 0) {
                    $errorBags[$real_sheet]["master"][] = "Mã bảng giá trùng trong hệ thống";
                }
                //check mã bảng giá
                if (!preg_match(config('validate.admin.code'), $productPrice[0][2])) {
                    $errorBags[$real_sheet]["master"][] = "Mã báo giá không đúng định dạng (A -> Z, a -> z, 0 -> 9, -, _)";
                }
                if (!empty($errorBags[$real_sheet]["master"])) {
                    continue;
                }
                if (!$productPriceInsert->save()) {
                    $errorBags[$real_sheet]["master"][] = "Lỗi không lưu được sản phẩm";
                    continue;
                }
            }
            if (!empty($errorBags)) {
                DB::rollBack();
                return redirect()->back()->with('error_bags', $errorBags);
            }

            foreach ($rawProductPrice as $sheet_key => $productPrice) {
                $real_sheet = $sheet_key + 1;
                $productPriceDetailsInsert = [];

                foreach ($rawProductPriceDetail[$sheet_key] as $key => $detail) {
                    $line = $key + (new ProductPriceImportRow())->headingRow() + 1;
                    if (empty($detail['ma_san_pham'])) {
                        $errorBags[$real_sheet]["details"][$line][] = "Mã sản phẩm trống";
                    }
                    if (!isset($detail['gia_cho_giao_vien'])) {
                        $errorBags[$real_sheet]["details"][$line][] = "Giá giáo viên trống";
                    } elseif (!is_numeric($detail['gia_cho_giao_vien']) || $detail['gia_cho_giao_vien'] < 0) {
                        $errorBags[$real_sheet]["details"][$line][] = "Giá giáo viên tối thiểu 0";
                    }
                    if (!isset($detail['gia_cho_hoc_sinh'])) {
                        $errorBags[$real_sheet]["details"][$line][] = "Giá học sinh trống";
                    } elseif (!is_numeric($detail['gia_cho_hoc_sinh']) || $detail['gia_cho_hoc_sinh'] < 0) {
                        $errorBags[$real_sheet]["details"][$line][] = "Giá học sinh tối thiểu 0";
                    }
                    // check mã sản phẩm trong product
                    $checkProductSku = "";
                    if ($detail['ma_san_pham']) {
                        $checkProductSku = getProductFromSku($detail['ma_san_pham'], $objProduct);
                    }
                    if (!$checkProductSku) {
                        $errorBags[$real_sheet]["details"][$line][] = "Mã sản phẩm không hợp lệ";
                        continue;
                    }
                    // lấy id sản phẩm
                    $productPriceDetailInsert = new AdminProductPriceDetail([
                        'product_price_id' => $insertedPriceboard[$sheet_key]->id ?? "",
                        'price_1' => $detail['gia_cho_giao_vien'],
                        'price_2' => $detail['gia_cho_hoc_sinh'],
                        'product_id' => $checkProductSku
                    ]);

                    $productPriceDetailsInsert[] = $productPriceDetailInsert->toArray() ?? [];
                    // check mã sản phẩm trong excel 
                    $productPriceDetailList = data_get($productPriceDetailsInsert, '*.product_id') ?? [];

                    $uniqueProductPriceDetailList = array_unique($productPriceDetailList) ?? [];
                    if(count($productPriceDetailList) != count($uniqueProductPriceDetailList)){
                        $errorBags[$real_sheet]["details"][$line][] = "Mã sản phẩm " . $detail['ma_san_pham'] . " trùng trong excel";
                        array_pop($productPriceDetailsInsert);
                    }
                    if (!$productPriceDetailInsert->save()) {
                        $errorBags[$real_sheet]["details"][$line][] = "Lỗi không xác định";
                    }
                }
            }
            if (!empty($errorBags)) {
                DB::rollBack();
                return redirect()->back()->with('error_bags', $errorBags);
            }
        } catch (QueryException $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage());
        } catch (\Throwable $e) {
            DB::rollBack();
            $messages = $e->getMessage();
            $with_return = ['error' => $messages];
            if (count($error_dupticate) > 0) {
                $with_return['dupticate'] = $error_dupticate;
                $with_return['dupticateCode'] = $error_dupticate_code;
            };
            if (count($error_dupticate_code) > 0) {
                $with_return['dupticateCode'] = $error_dupticate_code;
            };
            return redirect()->back()->with($with_return);
        }
        DB::commit();
        return redirect()->route('admin_price.index')->with('success', sc_language_render('action.success'));


    }
}

