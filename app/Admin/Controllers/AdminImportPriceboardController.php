<?php

namespace App\Admin\Controllers;

use App\Admin\Models\AdminNotification;
use App\Admin\Models\AdminProductPrice;
use App\Admin\Models\AdminProductPriceDetail;
use App\Exceptions\ImportException;
use App\Exports\ImportPrice\ExportNotification;
use App\Exports\ImportPriceboardExport;
use App\Front\Models\ShopDavicookOrder;
use App\Front\Models\ShopDavicookOrderDetail;
use App\Front\Models\ShopDavicookProductSupplier;
use App\Front\Models\ShopImportPriceboard;
use App\Front\Models\ShopImportPriceboardDetail;
use App\Front\Models\ShopOrder;
use App\Front\Models\ShopOrderDetail;
use App\Front\Models\ShopProduct;
use App\Front\Models\ShopProductSupplier;
use App\Front\Models\ShopSupplier;
use App\Http\Requests\Admin\AdminImportPriceboardDetailRequest;
use App\Http\Requests\Admin\AdminImportPriceboardRequest;
use App\Imports\CustomerImport;
use App\Imports\ImportPriceboardImport;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use SCart\Core\Admin\Controllers\RootAdminController;

class AdminImportPriceboardController extends RootAdminController
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
            'title' => "Quản lý giá nhập",
            'subTitle' => '',
            'icon' => 'fa fa-indent',
            'urlDeleteItem' => sc_route_admin('admin.import_priceboard.delete'),
            'removeList' => 1, // 1 - Enable function delete list item
            'buttonRefresh' => 1, // 1 - Enable button refresh
            'buttonSort' => 1, // 1 - Enable button sort
            'css' => '',
            'js' => '',
            'url_action' => sc_route_admin('admin_price.create'),
            'urlExport' => sc_route_admin('admin.import_priceboard.export'),
            'permGroup' => 'import_priceboard'
        ];
        //Process add content
        $data['menuRight'] = sc_config_group('menuRight', \Request::route()->getName());
        $data['menuLeft'] = sc_config_group('menuLeft', \Request::route()->getName());
        $data['topMenuRight'] = sc_config_group('topMenuRight', \Request::route()->getName());
        $data['topMenuLeft'] = sc_config_group('topMenuLeft', \Request::route()->getName());
        $data['blockBottom'] = sc_config_group('blockBottom', \Request::route()->getName());

        $listTh = [
            'code' => "Mã bảng giá",
            'name' => "Tên bảng giá",
            'supplier' => "Nhà cung cấp",
            'created_at' => "Ngày tạo",
            'creator' => "Người tạo",
            'start_date' => "Ngày bắt đầu",
            'end_date' => "Ngày kết thúc",
            'action' => "Hành động"
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
            'start_date' => 'text-align: center; width: 13%',
            'end_date' => 'text-align: center; width: 13%',
            'action' => 'text-align: center; width: 20%'
        ];
        $cssTd = [
            'price_code' => 'text-align: center',
            'name' => '',
            'created_at' => 'text-align: center',
            'creator' => '',
            'start_date' => 'text-align: center',
            'end_date' => 'text-align: center',
            'action' => 'text-align: center'
        ];
        $data['cssTh'] = $cssTh;


        $data['cssTd'] = $cssTd;

        $dataTmp = (new ShopImportPriceboard());

        if ($dataSearch['keyword']) {
            $keyword = $dataSearch['keyword'];
            $dataTmp = $dataTmp::whereHas('supplier', function (Builder $query) use ($keyword) {
                $query->where('name', 'like', "%$keyword%");
            });
        }

        $dataTmp = $dataTmp->orderBy("created_at", "DESC")->paginate(config('pagination.admin.small'));

        $dataTr = [];
        foreach ($dataTmp as $key => $row) {
            $dataTr[$row['id']] = [
                'price_code' => $row['code'] ?? '',
                'name' => $row['name'] ?? '',
                'supplier' => $row->supplier->name ?? '',
                'created_at' => Carbon::createFromFormat('Y-m-d H:i:s', $row['created_at'])->format('d-m-Y') ?? '',
                'creator' => $row->User['username'] ?? '',
                'start_date' => isset($row['start_date']) ? \Carbon\Carbon::parse($row['start_date'])->format('d-m-Y') : '',
                'end_date' => isset($row['end_date']) ? \Carbon\Carbon::parse($row['end_date'])->format('d-m-Y') : '',
                'action' => '
                    <a data-perm="import_priceboard:detail" href="' . sc_route_admin('admin.import_priceboard.edit', ['id' => $row['id'] ? $row['id'] : 'not-found-id']) . '"><span title="' . sc_language_render('action.edit') . '" type="button" class="btn btn-flat btn-sm btn-primary"><i class="fa fa-edit"></i></span></a>&nbsp;
                    <span data-perm="import_priceboard:delete" onclick="deleteItem(\'' . $row['id'] . '\');"  title="' . sc_language_render('action.delete') . '" class="btn btn-flat btn-sm btn-danger"><i class="fas fa-trash-alt"></i></span>&nbsp;
                   
                    ',
            ];
        }
        $data['listTh'] = $listTh;
        $data['dataTr'] = $dataTr;
        $data['pagination'] = $dataTmp->appends(request()->except(['_token', '_pjax']))->links($this->templatePathAdmin . 'component.pagination');
        $data['resultItems'] = sc_language_render('admin.result_item', ['item_from' => $dataTmp->firstItem(), 'item_to' => $dataTmp->lastItem(), 'total' => $dataTmp->total()]);

        //menuRight
        $data['menuRight'][] = '<a data-perm="import_priceboard:create" href="' . sc_route_admin('admin.import_priceboard.create') . '" class="btn  btn-success  btn-flat" title="New" id="button_create_new">
            <i class="fa fa-plus" title="' . sc_language_render('admin.add_new') . '"></i>
            </a>
            <a data-perm="import_priceboard:import" href="' . sc_route_admin('admin.import_priceboard.import') . '" class="btn  btn-success  btn-flat" title="New" id="button_import">
            <i class="fa fa-file-import" title="' . sc_language_render('category-import') . '"></i>' . sc_language_render('category-import') .
            '</a>
            <button data-perm="import_priceboard:export" type="button" class="btn  btn-success  btn-flat" title="Xuất excel" id="btn_export">
                            <i class="fa fa-file-export" title="' . sc_language_render('category-export') . '"></i> ' . sc_language_render('category-export') .
            '</button>';
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
                <form action="" id="button_search">
                <div class="input-group input-group">
                    <input type="text" name="keyword" class="form-control rounded-0 float-right" placeholder="' . sc_language_render('admin.supplier.search_hint') . '" value="' . $keyword . '">
                    <div class="input-group-append">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i></button>
                    </div>
                </div>
                </form>';
        //=menuSearch
        return view($this->templatePathAdmin . 'screen.list')->with($data);
    }

    /**
     * Form create
     */

    public function create()
    {
        $data = [
            'title' => "Tạo mới bảng báo giá nhập nhà cung cấp",
            'title_action' => '<i class="fa fa-plus" aria-hidden="true"></i> ' . sc_language_render('product.admin.add_product'),
            'subTitle' => '',
            'icon' => 'fa fa-indent',
            'urlDeleteItem' => sc_route_admin('admin_price.delete_detail'),
            'removeList' => 0, // 1 - Enable function delete list item
            'buttonRefresh' => 0, // 1 - Enable button refresh
            'buttonSort' => 0, // 1 - Enable button sort
            'css' => '',
            'js' => '',
            'url_action' => sc_route_admin('admin.import_priceboard.create'),
            'old_supplier' => old('supplier_id') ? ShopSupplier::find(old('supplier_id')) : "",
            'title_description' => "",
        ];
        return view($this->templatePathAdmin . 'screen.import_priceboard_form')
            ->with($data);
    }

    /**
     * Post create new item in admin
     * @return [type] [description]
     **/
    public function postCreate(AdminImportPriceboardRequest $request)
    {
        $data = $request->validated();
        $user_id = admin()->id();
        $checkPriceboardSupplier = ShopImportPriceboard::whereRaw(
            "(`supplier_id` = '" . $data["supplier_id"] . "') AND (((`start_date` BETWEEN '" . $data['start_date'] . "' AND '" . $data['end_date'] . "') OR (`end_date` BETWEEN '" . $data['start_date'] . "' AND '" . $data['end_date'] . "')) OR
            (('" . $data['start_date'] . "' BETWEEN `start_date` AND `end_date`) OR ('" . $data['start_date'] . "' BETWEEN `start_date` AND `end_date`)))"
        )->get();
        if (count($checkPriceboardSupplier) > 0) {
            return redirect()->back()->withInput()->with("error", "Thời gian nhập trùng thời gian với " . count($checkPriceboardSupplier) . " bảng giá (" . implode(", ", data_get($checkPriceboardSupplier, "*.name")) . ")");
        }
        $dataInsert = [
            'name' => $data['name'],
            'code' => $data['code'],
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'],
            'supplier_id' => $data['supplier_id'],
            'admin_id' => $user_id
        ];
        try {
            $dataInsert = sc_clean($dataInsert, [], true);
            $obj = ShopImportPriceboard::create($dataInsert);
            $importPriceboardId = $obj->id;
        } catch (Exception $e) {
            return response()->json([
                'error' => sc_language_render('action.failed'),
            ]);
        }
        return redirect()->route('admin.import_priceboard.edit', ['id' => $importPriceboardId])->with('success', "Tạo mới bảng giá nhập thành công!");
    }

    /**
     * Form edit
     */
    public function edit($id)
    {
        $importPriceboard = ShopImportPriceboard::findOrFail($id);
        $importPriceboard->start_date = convertDate($importPriceboard->start_date, MACHINE_TO_HUMAN);
        $importPriceboard->end_date = convertDate($importPriceboard->end_date, MACHINE_TO_HUMAN);
        $importSupplier = ShopSupplier::find(old('supplier_id', $importPriceboard->supplier_id));
        $data = [
            'title' => "Chỉnh sửa bảng giá nhập",
            'title_action' => '<i class="fa fa-plus" aria-hidden="true"></i> ' . sc_language_render('product.admin.add_product'),
            'subTitle' => '',
            'icon' => 'fa fa-indent',
            'urlDeleteItem' => sc_route_admin('admin.import_priceboard.delete_product'),
            'removeList' => 0, // 1 - Enable function delete list item
            'buttonRefresh' => 0, // 1 - Enable button refresh
            'buttonSort' => 0, // 1 - Enable button sort
            'css' => '',
            'js' => '',
            'importPriceboard' => $importPriceboard,
            'old_supplier' => $importSupplier,
            'url_action_edit' => sc_route_admin('admin.import_priceboard.edit', ['id' => $importPriceboard->id ?? '']),
            'url_action_add_product' => sc_route_admin('admin.import_priceboard.add_product', ['id' => $importPriceboard->id ?? '']),
            'title_description' => sc_language_render('product.admin.list'),
        ];


        $listTh = [
            'name' => "Sản phẩm",
            'price' => "Giá nhập",
            'action' => "Thao tác"
        ];

        $keyword = sc_clean(request('keyword') ?? '');

        $dataSearch = [
            'keyword' => $keyword,
        ];

        $dataTmp = $importPriceboard->details()->with('product');

        if ($dataSearch['keyword']) {
            $keyword = $dataSearch['keyword'];
            $dataTmp = $dataTmp->whereHas('product', function ($query) use ($keyword) {
                $query->where('name', 'like', "%$keyword%");
            });
        }

        $dataTmp = $dataTmp->paginate(config('pagination.admin.small'));
        $products = ShopProduct::get();
        $data['objPro'] = $products;
        $dataTr = [];
        foreach ($dataTmp as $key => $row) {
            $dataTr[$row['id']] = [
                'name' => $row->product ? $row->product->getName() : "",
                'price' => '<a data-perm="import_priceboard:edit" perm-type="disable" href="#" class="editable-required" data-name="price" data-type="text" data-pk="' . $row['id'] . '" data-source="" data-url="' . route('admin.import_priceboard.edit_product') . '" data-title="Giá sản phẩm" data-value="' . $row['price'] . '" data-original-title="" title="">' . number_format($row['price'], 0, ',', '.') . ' đ' ?? '0' . '</a>',
                'action' => '
                  <span data-perm="import_priceboard:edit" onclick="deleteItem(\'' . $row['id'] . '\');"  title="' . sc_language_render('action.delete') . '" class="btn btn-flat btn-sm btn-danger"><i class="fas fa-trash-alt"></i></span>
                  ',
            ];
        }
        $data['listTh'] = $listTh;
        $data['dataTr'] = $dataTr;
        $data['pagination'] = $dataTmp->appends(request()->except(['_token', '_pjax']))->links($this->templatePathAdmin . 'component.pagination_price');
        $data['resultItems'] = sc_language_render('admin.result_item', ['item_from' => $dataTmp->firstItem(), 'item_to' => $dataTmp->lastItem(), 'total' => $dataTmp->total()]);

        $data['topMenuRight'][] = '
            <form action="" id="button_search">
            <div class="input-group input-group" style="width: 200px;">
                <input type="text" name="keyword" class="form-control rounded-0 float-right" placeholder="' . sc_language_render('admin.supplier.search_hint') . '" value="' . $keyword . '">
                <div class="input-group-append">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i></button>
                </div>
            </div>
            </form>';
        return view($this->templatePathAdmin . 'screen.import_priceboard_edit')
            ->with($data);
    }

    /**
     * update status
     */
    public function postEdit(AdminImportPriceboardRequest $request, $id)
    {
        try {

            $data = $request->validated();
            $importPriceboard = ShopImportPriceboard::findOrFail($id);
            $checkPriceboardSupplier = ShopImportPriceboard::where('id', "<>", $id)->whereRaw(
                "(`supplier_id` = '" . $data["supplier_id"] . "') AND (((`start_date` BETWEEN '" . $data['start_date'] . "' AND '" . $data['end_date'] . "') OR (`end_date` BETWEEN '" . $data['start_date'] . "' AND '" . $data['end_date'] . "')) OR
            (('" . $data['start_date'] . "' BETWEEN `start_date` AND `end_date`) OR ('" . $data['start_date'] . "' BETWEEN `start_date` AND `end_date`)))"
            )->get();
            if (count($checkPriceboardSupplier) > 0) {
                return redirect()->back()->withInput()->with("error", "Thời gian nhập trùng thời gian với " . count($checkPriceboardSupplier) . " bảng báo giá (" . implode(", ", data_get($checkPriceboardSupplier, "*.name")) . ")");
            }

            $dataUpdate = sc_clean($data, [], true);
            $importPriceboard->update($dataUpdate);
        } catch (Exception $e) {
            return redirect()->back()->withInput()->with('error', "Chỉnh sửa thông tin thất bại");
        }
        return redirect()->route('admin.import_priceboard.edit', ['id' => $importPriceboard->id])->with('success', sc_language_render('action.edit_success'));
    }

    public function postAddProduct(AdminImportPriceboardDetailRequest $request)
    {
        $data = $request->validated();
        $insertData = sc_clean($data);
        $importPriceboard = ShopImportPriceboard::find($insertData['priceboard_id']);
        if (!$importPriceboard) {
            return redirect()->back()->withInput()->with(['error' => "Không tìm thấy bảng báo giá nhập"]);
        }
        $products = $importPriceboard->details;
        $productArray = data_get($products, '*.product_id');
        if (in_array($insertData['product_id'], $productArray)) {
            return redirect()->back()->withInput()->with(['error' => "Sản phẩm đã tồn tại trong bảng giá nhập này!"]);
        }
        if (empty($insertData)) {
            return redirect()->back()->withInput()->with(['error' => "Thông tin sản phẩm không hợp lệ, vui lòng kiểm tra lại!"]);
        }
        $insert = ShopImportPriceboardDetail::insert($insertData);
        if (!$insert) {
            return redirect()->back()->withInput()->with(['error' => "Thông tin sản phẩm không hợp lệ, vui lòng kiểm tra lại!"]);
        }
        return redirect()->back()->withInput()->with(["success" => "Thêm thông tin sản phẩm thành công!"]);
    }

    public function postEditProduct(Request $request)
    {
        $id = request('pk');
        $code = request('name');
        $value = request('value');
        if (empty($code) && empty($id)) {
            return response()->json([
                'error' => 1,
                'msg' => "Vui lòng nhập đủ thông tin giá!"
            ]);
        }
        $detail = ShopImportPriceboardDetail::find($id);
        if (empty($detail)) {
            return redirect()->back()->withInput()->with(['error' => 1, 'msg' => "Thông tin chi tiết sản phẩm không hợp lệ, vui lòng kiểm tra lại!"]);
        }
        if ($value <= 0) {
            return redirect()->back()->withInput()->with(['error' => 1, 'msg' => "Giá tối thiểu bằng 0"]);
        }
        $detail->price = $value ?? 0;
        if (!$detail->save()) {
            return response()->json([
                'error' => 1,
                'msg' => "Cập nhật giá thất bại"
            ]);
        }
        return response()->json([
            'error' => 0,
            'msg' => "Cập nhật giá thành công"
        ]);
    }

    public function postDeleteProduct(Request $request)
    {
        $ids = request('ids');
        if (!request()->ajax()) {
            return response()->json(['error' => 1, 'msg' => sc_language_render('admin.method_not_allow')]);
        }
        if (empty($ids)) {
            return response()->json(['error' => 1, 'msg' => "Bạn chưa chọn gì để xoá!"]);
        }
        $ids = explode(',', $ids);
        if (!empty($ids) && (count($ids) > 0)) {
            $count = 0;
            foreach ($ids as $id) {
                $deleteItem = ShopImportPriceboardDetail::find($id);
                if (!$deleteItem) {
                    return response()->json(['error' => 1, 'msg' => "Đối tượng cần xoá không tồn tại!"]);
                }
                if (!$deleteItem->delete()) {
                    return response()->json(['error' => 1, 'msg' => "Xoá không thành công!"]);
                }
                $count++;
            }
            return response()->json(['error' => 0, 'msg' => "Xoá thành công! " . ($count > 1) ? "$count bản ghi được xoá" : ""]);
        }
    }


    public function deleteList()
    {
        if (!request()->ajax()) {
            return response()->json(['error' => 1, 'msg' => sc_language_render('admin.method_not_allow')]);
        }
        $ids = request('ids');
        $arrID = explode(',', $ids);
        $delete = ShopImportPriceboard::destroy($arrID);
        if (!$delete) {
            return response()->json(['error' => 1, 'msg' => "Xoá không thành công"]);
        }
        ShopImportPriceboardDetail::whereIn('priceboard_id', $arrID)->delete();
        return response()->json(['error' => 0, 'msg' => sc_language_render('action.delete_success')]);
    }

    /**
     * Clone product
     * Only clone single product
     * @return  [type]  [return description]
     */
    public function postClone()
    {
        if (!request()->ajax()) {
            return response()->json(['error' => 1, 'msg' => sc_language_render('admin.method_not_allow')]);
        }
        $id = request('id');
        //Search
        $importPriceboard = ShopImportPriceboard::find($id);
        if (!$importPriceboard) {
            return response()->json(['error' => 1, 'msg' => "Không tìm thấy bảng giá!"]);
        }
        $details = $importPriceboard->details;
        //Modify
        $newImportPriceboard = (new ShopImportPriceboard())->fill(Arr::except($importPriceboard->toArray(), ['id', 'details']));
        $newImportPriceboard['name'] .= time() . "-copy";
        $newImportPriceboard['code'] .= time() . "-copy";
        $newImportPriceboard->save();
        $newImportPriceboardDetails = [];
        if ($details && count($details) > 0) {
            foreach ($details as $detail) {
                $detail->priceboard_id = $newImportPriceboard->id;
                (new ShopImportPriceboardDetail())->fill(Arr::except($detail->toArray(), ['id']))->save();
            }
            return response()->json(['error' => 0, 'msg' => "Nhân bản thành công!"]);
        }
        return response()->json(['error' => 1, 'msg' => "Lỗi không xác định!"]);
    }

    public function import()
    {
        return view($this->templatePathAdmin . 'screen.import_prices_product.view_import', [
            'title' => 'Nhập bảng báo giá nhập NCC'
        ]);
    }

    public function postImport()
    {
//        session()->forget('exportNotify');
        $file = request()->file('excel_file');
        $errorBags = [];
        $dupticatedErrorBags = [];
        $user_id = admin()->id();
        $dataSuccess = [];
        $dataExportNotification = [];
        DB::beginTransaction();
        try {
            $startRow = (new CustomerImport())->headingRow() + 1;
            if (!$file || !is_file($file) || !in_array($file->extension(), ['xls', 'xlsx'])) {
                throw new ImportException('Định dạng file không hợp lệ!');
            }
            $raw_excel_array = Excel::toArray(new ImportPriceboardImport(), request()->file('excel_file'));
            $excel = $raw_excel_array ? cleanExcelFile($raw_excel_array) : [];
            if (count($excel) < 1) {
                throw new ImportException('File excel phải có ít nhất 1 Sheet');
            }
            $insertData = [];
            foreach (my_array_reverse($excel) as $sheetIndex => $sheet) {
                $real_sheet = ++$sheetIndex;
                $master = [];
                $details = [];
                $products = (new ShopProduct)->pluck('sku', 'id')->toArray(); //Static data
                $suppliers = (new ShopSupplier)->pluck('supplier_code', 'id')->toArray(); //Static data
                $code = $sheet[0]['ma_bang_gia_nhap'];
                $master['admin_id'] = $user_id;
                if (empty($code)) {
                    $errorBags[$real_sheet]["master"][] = "Mã bảng giá nhập trống";
                } else {
                    $master['code'] = $code;
                }
                $name = $sheet[0]['ten_bang_gia_nhap'];
                if (empty($name)) {
                    $errorBags[$real_sheet]["master"][] = "Tên bảng giá nhập trống";
                } else {
                    $master['name'] = $name;
                }
                $supplier_code = $sheet[0]['ma_nha_cung_cap'] ?? "";
                if ($supplier_code) {
                    $supplier_id = getSupplierFromCode($supplier_code, $suppliers, $real_sheet);
                    // Lấy bảng giá nhập gần nhất để check sản phẩm thay đổi như nào.
                    $getDataPriceSupplierFirst = ShopImportPriceboard::with('details')->where('supplier_id', $supplier_id)
                        ->orderBy('created_at', 'DESC')->first();
                    $dataDetailPriceSupplier = $getDataPriceSupplierFirst->details ?? new Collection();
                    if (empty($supplier_id)) {
                        $errorBags[$real_sheet]["master"][] = "Mã nhà cung cấp không hợp lệ";
                    } else {
                        $master['supplier_id'] = $supplier_id;
                    }
                } else {
                    $errorBags[$real_sheet]["master"][] = "Mã nhà cung cấp trống";
                }
                // Solve datetime
                try {
                    $master['start_date'] = (is_numeric($sheet[0]['ngay_bat_dau_hieu_luc']) ? \Carbon\Carbon::make(Date::excelToDateTimeObject($sheet[0]['ngay_bat_dau_hieu_luc'])) : Carbon::createFromFormat('d/m/Y', $sheet[0]['ngay_bat_dau_hieu_luc']));
                } catch (\Throwable $e) {
                    Log::error($e);
                    $errorBags[$real_sheet]["master"][] = "Định dạng ngày bắt đầu không hợp lệ";
                }
                try {
                    $master['end_date'] = (is_numeric($sheet[0]['ngay_ket_thuc_hieu_luc']) ? Carbon::make(Date::excelToDateTimeObject($sheet[0]['ngay_ket_thuc_hieu_luc'])) : Carbon::createFromFormat('d/m/Y', $sheet[0]['ngay_ket_thuc_hieu_luc']));
                    if ($master['start_date'] > $master['end_date']) {
                        $errorBags[$real_sheet]["master"][] = "Ngày bắt đầu không được lớn hơn ngày kết thúc";
                    }
                } catch (\Throwable $e) {
                    Log::error($e);
                    $errorBags[$real_sheet]["master"][] = "Định dạng ngày kết thúc không hợp lệ";
                }

                $codes = data_get($sheet, "*.ma_san_pham");

                $checkProductDupticated = $this->getDupticatedArray($codes, "Mã sản phẩm", 2);
                if ($checkProductDupticated) {
                    $dupticatedErrorBags[$real_sheet]["details"]["dupticate"] = $checkProductDupticated;
                    continue;
                }
                $nameSupplier = '';
                foreach ($sheet as $index => $row) {
                    $real_index = $index + 2;
                    $temp = [];
                    if ($index == 0) {
                        $nameSupplier = $row['ten_nha_cung_cap'];
                    }
                    $product_code = $row['ma_san_pham'] ?? "";
                    if (empty($product_code)) {
                        $errorBags[$real_sheet]["details"][$real_index][] = "Mã sản phẩm không được trống";
                    } else {
                        $product_id = getProductFromSku($product_code, $products, "$index Sheet $real_index");

                        if (!$product_id) {
                            $errorBags[$real_sheet]["details"][$real_index][] = "Mã sản phẩm không hợp lệ";
                        }

                        if ($getDataPriceSupplierFirst) {
                            $flag = $dataDetailPriceSupplier->where('product_id', $product_id)->first();
                            if (!$flag) {
                                $dataSuccess[$real_sheet.'-'.$supplier_code.'-'.$nameSupplier][] = $row['ten_san_pham'] . " - Bổ sung sản phẩm";
                                $dataExportNotification[$supplier_code][] = [
                                    'supplier_code' => $supplier_code,
                                    'supplier_name' => $nameSupplier,
                                    'product_name' => $row['ten_san_pham'],
                                    'desc' => 'Bổ sung sản phẩm',
                                    'old_price' => null,
                                    'new_price' => null,
                                    'diff_price' => null,
                                ];
                            } else {
                                if ($flag->price != $row['gia_nhap']) {
                                    $dataSuccess[$real_sheet.'-'.$supplier_code.'-'.$nameSupplier][] = $row['ten_san_pham'] . " - Chỉnh sửa; Giá cũ:" . $flag->price . ";  Giá mới:" . $row['gia_nhap'] . ";  Chênh lệch:" . ($row['gia_nhap'] - $flag->price);
                                    $dataExportNotification[$supplier_code][] = [
                                        'supplier_code' => $supplier_code,
                                        'supplier_name' => $nameSupplier,
                                        'product_name' => $row['ten_san_pham'],
                                        'desc' => 'chỉnh sửa',
                                        'old_price' => ($flag->price),
                                        'new_price' => ($row['gia_nhap']),
                                        'diff_price' => ($row['gia_nhap'] - $flag->price),
                                    ];
                                }
                            }
                        }

                        $temp['product_id'] = $product_id ?? "";
                    }
                    $price = $row['gia_nhap'];
                    if (!is_numeric($price) || $price < 0) {
                        $errorBags[$real_sheet]["details"][$real_index][] = "Giá nhập không hợp lệ";
                    }
                    $temp['price'] = $price;
                    $details[$index + $startRow] = $temp;
                }
                $insertData[$real_sheet] = [
                    'master' => $master,
                    'details' => $details
                ];
            }
            if (!empty($errorBags) || !empty($dupticatedErrorBags)) {
                DB::rollBack();
                if (!empty($errorBags)) {
                    return redirect()->back()->with('error_bags', $errorBags);
                }
                if (!empty($dupticatedErrorBags)) {
                    return redirect()->back()->with('error_dupticated_bags', $dupticatedErrorBags);
                }
            }
            foreach ($insertData as $real_sheet => $data) {
                $shopImportPriceboardCode = (new ShopImportPriceboard())->whereIn('code', data_get($insertData, "*.master.code"))->get();
                $shopImportPriceboardName = (new ShopImportPriceboard())->whereIn('name', data_get($insertData, "*.master.name"))->get();
                $dbPriceboardCode = data_get($shopImportPriceboardCode, "*.code");
                $dbPriceboardName = data_get($shopImportPriceboardName, '*.name');
                $checkPriceboardSupplier = ShopImportPriceboard::whereRaw(
                    "(`supplier_id` = '" . $data["master"]["supplier_id"] . "') AND (((`start_date` BETWEEN '" . $data["master"]['start_date'] . "' AND '" . $data["master"]['end_date'] . "') OR (`end_date` BETWEEN '" . $data["master"]['start_date'] . "' AND '" . $data["master"]['end_date'] . "')) OR
                    (('" . $data["master"]['start_date'] . "' BETWEEN `start_date` AND `end_date`) OR ('" . $data["master"]['start_date'] . "' BETWEEN `start_date` AND `end_date`)))"
                )->get();
                if (count($checkPriceboardSupplier) > 0) {
                    $errorBags[$real_sheet]["master"][] = "Nhà cung cấp đã bị gán cho bảng giá khác (" . implode(", ", data_get($checkPriceboardSupplier, "*.code")) . ")";
                }
                if (array_keys($dbPriceboardCode, $data['master']['code'] ?? "")) {
                    $errorBags[$real_sheet]["master"][] = "Mã bảng giá bị trùng";
                }
                if (array_keys($dbPriceboardName, $data['master']['name'] ?? "")) {
                    $errorBags[$real_sheet]["master"][] = "Tên bảng giá bị trùng";
                }
                if ($errorBags || $dupticatedErrorBags) {
                    continue;
                }

                $insert = (new ShopImportPriceboard($data['master']));
                if (!$insert->save()) {
                    DB::rollBack();
                    return new ImportException("Lỗi không xác định");
                }
                if (!empty($data['details'])) {
                    foreach ($data['details'] as $key => &$item) {
                        $item['priceboard_id'] = $insert->id;
                    }
                    $insertDetails = ShopImportPriceboardDetail::insert($data['details']);
                }
            }
            if (!empty($errorBags) || !empty($dupticatedErrorBags)) {
                DB::rollBack();
                if (!empty($errorBags)) {
                    return redirect()->back()->with('error_bags', $errorBags);
                }
                if (!empty($dupticatedErrorBags)) {
                    return redirect()->back()->with('error_dupticated_bags', $dupticatedErrorBags);
                }
            }
        } catch (QueryException $e) {
            Db::rollBack();
            Log::error($e);
            return redirect()->back()->with('error', "Lỗi không xác định, vui lòng liên hệ bộ phận kĩ thuật");
        } catch (ImportException $e) {
            Db::rollBack();
            Log::error($e);
            return redirect()->back()->with('error', $e->getMessage());
        } catch (\Throwable $e) {
            Db::rollBack();
            Log::error($e);
            return redirect()->back()->with('error', "Lỗi không xác định, vui lòng liên hệ bộ phận kĩ thuật");
        }
        DB::commit();
        if (!empty($dataSuccess)) {
//            session()->put('exportNotify', $dataExportNotification);
            # Lưu lịch sử thông báo
            $notification = new AdminNotification();
            $notification->title = "Báo giá nhập";
            $notification->content = json_encode($dataSuccess) ?? '';
            $notification->id_order = '';
            $notification->order_code = '';
            $notification->is_import_price = 1;
            $notification->content_change_import_price = json_encode($dataExportNotification);
            $notification->customer_code = '';
            $notification->customer_name = '';
            $notification->desc = 'Admin thay đổi báo giá nhập';
            $notification->order_type = 2;
            $notification->edit_type = 5;
            $notification->display = 0;
            $notification->save();
            Db::rollBack();
            return redirect()->back()->with('data_success_import', $dataSuccess);
        }

        return redirect()->route('admin.import_priceboard.index')->with('success', sc_language_render("Nhập bảng báo giá nhập NCC thành công!"));
    }

    public function exportNotification()
    {
        $data = session('exportNotify');
        return Excel::download(new ExportNotification($data), 'ThayDoiBaoGiaNhap.xlsx');
    }

    public function export()
    {
        $filter = json_decode(json_decode(request('filter')), true, 2);
        $ids = explode(',', request('ids'));
        $option = request('option') ?? 0;

        $sheets = [];
        switch ($option) {
            case 0:
                $dataTmp = (new ShopImportPriceboard())->with('details', 'details.product');
                if ($filter['keyword']) {
                    $keyword = $filter['keyword'];
                    $dataTmp = $dataTmp->where('name', 'like', "%$keyword%");
                }
                $sheets = $dataTmp->get();
                break;
            case 1:
                $dataTmp = (new ShopImportPriceboard())->with('details', 'details.product');
                if (count($ids) > 0 && !empty($ids[0])) {
                    $dataTmp->whereIn('id', $ids);
                }
                $sheets = $dataTmp->get();
                break;
        }
        return (new ImportPriceboardExport($sheets))->download('Bang-bao-gia-nhap-ncc-' . now() . '.xlsx');
    }

    function getDupticatedArray($inputArray, $attributeName, $startRow = 0)
    {
        // Count appear time
        foreach ($inputArray as $key => $value) { // remove null value, this will be skip....
            if (is_null($value)) {
                unset($inputArray[$key]);
            }
        }
        $countValues = array_count_values($inputArray);
        // Find > 1 frequent
        $dupticatedItems = array_filter($countValues, function ($v, $k) {
            if ($v > 1) {
                return $k;
            }
        }, 1);
        if (empty($dupticatedItems)) {
            return false;
        }
        $errors = [];
        foreach ($dupticatedItems as $key => $value) {
            $result = array_keys($inputArray, $key);
            foreach ($result as $key_rs => $value) {
                $result[$key_rs] = $value + $startRow;
            }
            if ($result) {
                $errors[] = ["value" => $key, "name" => $attributeName, "index" => $result];
            }
        }
        return empty($errors) ? false : $errors;
    }
}

