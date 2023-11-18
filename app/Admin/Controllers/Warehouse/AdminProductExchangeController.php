<?php
namespace App\Admin\Controllers\Warehouse;

use App\Admin\Models\AdminProductExchange;
use App\Exceptions\ImportException;
use App\Exports\ProductExchange\AdminProductExchangeExport;
use App\Front\Models\ShopProduct;
use App\Imports\ProductExchange\ImportProductExchange;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use SCart\Core\Admin\Controllers\RootAdminController;

class AdminProductExchangeController extends RootAdminController
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function index()
    {
        $data = [
            'title' => 'Danh sách sản phẩm quy đổi',
            'subTitle' => '',
            'icon' => 'fa fa-indent',
            'removeList' => 1, // Enable function delete list item
            'buttonRefresh' => 1, // 1 - Enable button refresh
            'buttonSort' => 1, // 1 - Enable button sort
        ];
        //Process add content
        $data['menuRight'] = sc_config_group('menuRight', \Request::route()->getName());
        $data['menuLeft'] = sc_config_group('menuLeft', \Request::route()->getName());
        $data['topMenuRight'] = sc_config_group('topMenuRight', \Request::route()->getName());
        $data['topMenuLeft'] = sc_config_group('topMenuLeft', \Request::route()->getName());
        $data['blockBottom'] = sc_config_group('blockBottom', \Request::route()->getName());

        $listTh = [
            'id' => 'ID',
            'product_code' => 'Mã sản phẩm cơ sở',
            'product_name' => 'Tên sản phẩm cơ sở',
            'product_code_exchange' => 'Mã sản phẩm quy đổi',
            'product_name_exchange' => 'Tên sản phẩm quy đổi',
            'qty_exchange' => 'Số lượng',
            'status' => 'Trạng thái',
            'action' => sc_language_render('action.title'),
        ];

        $cssTh = [
            'id' => 'max-width: 120px',
            'product_code' => 'width: 8% !important; min-width:80px; white-space: normal !important',
            'product_name' => 'max-width: 300px',
            'product_code_exchange' => 'width: 8% !important; min-width:80px; white-space: normal !important',
            'product_name_exchange' => 'max-width: 300px',
            'qty_exchange' => 'max-width: 120px',
            'status' => 'width: 130px',
            'action' => 'min-width: 120px; max-width:120px',
        ];
        $data['cssTh'] = $cssTh;

        $dataSearch = [
            'keyword' => request('keyword'),
            'status' => request('status'),
        ];

        $dataTmp = AdminProductExchange::getList($dataSearch)->paginate(15);
        $status = AdminProductExchange::STATUS;
        $dataTr = [];
        foreach ($dataTmp as $row) {
            $dataMap = [
                'id' => $row->id,
                'product_code' => $row->product_code,
                'product_name' => $row->product_name,
                'product_code_exchange' => $row->product_code_exchange,
                'product_name_exchange' => $row->product_name_exchange,
                'qty_exchange' =>$row->qty_exchange ." : 1",
                'status' => $row->status == 1 ? '<span class="badge badge-success">ON</span>' : '<span class="badge badge-danger">OFF</span>',
            ];
            $htmlAction = '
            <a data-perm="product_exchange:detail" href="' . sc_route_admin('product_exchange.edit', ['id' => $row->id ? $row->id : 'not-found-id']) . '">
                <span title="' . sc_language_render('product.admin.edit') . '" type="button" class="btn btn-flat btn-sm btn-primary">
                    <i class="fa fa-edit"></i>
                </span>
            </a>
            <span data-perm="product_exchange:delete" onclick="deleteItem(\'' . $row->id . '\');"  title="' . sc_language_render('action.delete') . '" class="btn btn-flat btn-sm btn-danger">
                <i class="fas fa-trash-alt"></i>
            </span>';

            $dataMap['action'] = $htmlAction;
            $dataTr[$row->id] = $dataMap;
        }

        $data['listTh'] = $listTh;
        $data['dataTr'] = $dataTr;
        $data['pagination'] = $dataTmp->appends(request()->except(['_token', '_pjax']))->links($this->templatePathAdmin . 'component.pagination');
        $data['resultItems'] = sc_language_render('admin.result_item', ['item_from' => $dataTmp->firstItem(), 'item_to' => $dataTmp->lastItem(), 'total' => $dataTmp->total()]);

        //menuRight
        $data['menuRight'][] = '
            <a data-perm="product_exchange:create" href="' . sc_route_admin('product_exchange.create') . '" class="btn btn-success btn-flat" title="Tạo mới danh mục quy đổi" id="button_create_new">
                <i class="fa fa-plus"></i>
            </a>
            <a data-perm="product_exchange:import" href="' . sc_route_admin('product_exchange.view_import') . '" class="btn  btn-success  btn-flat" title="New" id="button_import">
                <i class="fa fa-file-import" title="Nhập Excel"></i> ' . sc_language_render('category-import').
            '</a>
            <a data-perm="product_exchange:export" class="btn  btn-success  btn-flat" title="New" id="button_export">
                <i class="fa fa-file-export" title="Xuất excel"></i> ' . sc_language_render('category-export').
            '</a>';
        //=menuRight

        //Search with category
        $optionStatus = '';

        if ($status) {
            foreach ($status as $k => $v) {
                $optionStatus .= "<option value='{$k}' " . (( request('status') === $k) ? 'selected' : '') . ">{$v}</option>";
            }
        }

        //topMenuRight
        $data['topMenuRight'][] = '
                <form action="' . sc_route_admin('product_exchange.index') . '" id="button_search">
                <div class="input-group input-group float-left">
                    <select class="form-control rounded-0 select2" name="status" id="status">
                    <option value="" selected>Chọn trạng thái</option>
                    ' . $optionStatus . '
                    </select> &nbsp;
                    <input type="text" name="keyword" id="keyword" class="form-control rounded-0 float-right" placeholder="Tìm tên sp, Mã sp" value="' . request('keyword') . '">
                    <div class="input-group-append">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i></button>
                    </div>
                </div>
                </form>';
        //=topMenuRight

        return view($this->templatePathAdmin . 'screen.product_exchange.index')
            ->with($data);
    }

    /**
     * Create
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View [type] [description]
     */
    public function create()
    {
        $products = ShopProduct::with('unit')->where('status', 1)->get();

        $data = [
            'title' => 'Tạo danh mục quy đổi',
            'subTitle' => '',
            'products' => $products,
            'title_description' => 'Tạo danh mục quy đổi',
            'icon' => 'fa fa-plus',
        ];

        return view($this->templatePathAdmin . 'screen.product_exchange.create')
            ->with($data);
    }


    /**
     * Store
     * @return \Illuminate\Http\RedirectResponse [type] [description]
     */
    public function store()
    {
        $product_id = request('product_id');
        $productExchangeId = request('product_exchange_id') ?? [];
        $qtyExchange = request('qty_exchange');
        try {
            DB::beginTransaction();
            foreach ($productExchangeId as $key => $idExchange) {
                $flagCheck = AdminProductExchange::where('product_id', $product_id)->where('product_exchange_id', $idExchange)->first();
                if ($flagCheck) {
                    throw new \Exception("Có sản phẩm cơ sở và sản phẩm qui đổi đã tồn tại. Vui lòng check lại!");
                }
                $arrInsert = [
                    'product_id' => $product_id,
                    'product_exchange_id' => $idExchange,
                    'qty_exchange' => $qtyExchange[$key] ?? 1,
                    'status' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
                AdminProductExchange::create($arrInsert);
            }
            DB::commit();
            return redirect()->route('product_exchange.index')->with([
                'success' => 'Thêm thành công!',
            ]);
        } catch (\throwable $e) {
            Log::error($e);
            return redirect()->back()->with([
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Edit
     * @param $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function edit($id)
    {
        $nameTable = SC_DB_PREFIX . "shop_product_exchange";
        $products = ShopProduct::with('unit')->where('status', 1)->get();
        $productExchange = AdminProductExchange::getList([])->where($nameTable.'.id', $id)->first();

        $data = [
            'title' => 'Chỉnh sửa danh mục quy đổi',
            'subTitle' => '',
            'masterProductExchange' => $productExchange,
            'products' => $products,
            'title_description' => 'Chỉnh sửa danh mục quy đổi',
            'icon' => 'fa fa-plus',
        ];

        return view($this->templatePathAdmin . 'screen.product_exchange.edit')
            ->with($data);
    }


    /**
     * Update
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update($id)
    {
        $productExchange = AdminProductExchange::where('id', $id)->first();

        $product_id = request('product_id');
        $productExchangeId = request('product_exchange_id') ?? [];
        $qtyExchange = request('qty_exchange') ?? [];

        try {
            DB::beginTransaction();
            foreach ($productExchangeId as $key => $idExchange) {
                $flag = AdminProductExchange::where('product_id', $product_id)->where('product_exchange_id', $idExchange)->where('id', '!=', $id)->first();
                if ($flag) {
                    throw new \Exception("Có sản phẩm cơ sở và sản phẩm qui đổi đã tồn tại. Vui lòng check lại!");
                }
                $arrInsert = [
                    'product_id' => $product_id,
                    'product_exchange_id' => $idExchange,
                    'qty_exchange' => $qtyExchange[$key] ?? 1,
                ];
                $productExchange->update($arrInsert);
            }
            DB::commit();
            return redirect()->route('product_exchange.index')->with([
                'success' => 'Update thành công!',
            ]);
        } catch (\throwable $e) {
            Log::error($e);
            return redirect()->back()->with([
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * View Import
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function viewImport()
    {
        $data = [
            'title' => 'Nhập excel sản phẩm quy đổi',
            'title_description' => 'Nhập excel sản phẩm quy đổi',
            'icon' => 'fa fa-plus',
        ];

        return view($this->templatePathAdmin . 'screen.product_exchange.view_import')
            ->with($data);
    }

    /**
     * import
     * @return \Illuminate\Http\RedirectResponse
     * @throws ImportException
     */
    public function import()
    {
        $file = request()->file('excel_file');
        if (!$file || !is_file($file) || !in_array($file->extension(), ['xls', 'xlsx'])) {
            throw new ImportException('Định dạng file không hợp lệ!');
        }

        return $this->importOverwriteProductExchange($file);
    }

    /**
     * Xử lý import dữ liệu
     * @param $file
     * @return \Illuminate\Http\RedirectResponse
     * @throws ImportException
     */
    private function importOverwriteProductExchange($file) {
        if (!$file || !is_file($file) || !in_array($file->extension(), ['xls', 'xlsx'])) {
            throw new ImportException('Định dạng file không hợp lệ!');
        }
        $messageError = [];
        DB::beginTransaction();
        try {
            $startRow = 2;
            $raw_excel_array = Excel::toArray(new ImportProductExchange(), $file);
            $excel = $raw_excel_array ? cleanExcelFile($raw_excel_array)[0] : [];
            if (!$excel) {
                throw new ImportException('File không hợp lệ! Đảm bảo file được định dạng theo mẫu cho sẵn');
            }
            $j = 1 ;
            $products = ShopProduct::all()->pluck('id', 'sku')->toArray();
            $productExchanges = AdminProductExchange::get();
            foreach ($excel as $key => $item) {
                $j++;
                $line = $key + $startRow;

                if ($j != $line) {
                    $messageError[] = 'Vui lòng kiểm tra kỹ file excel trước khi nhập';
                    $messageError[] = 'File không hợp lệ! File trống dòng số '. $line - 1;
                    throw new \Exception('Lỗi import Sản phẩm quy đổi, vui lòng kiểm tra lại!');
                }

                if ($item['ma_san_pham_co_so'] == '') {
                    $messageError[] = 'Mã sản phẩm cơ sở trống - Dòng '. $line;
                }

                if (!array_key_exists($item['ma_san_pham_co_so'], $products)) {
                    $messageError[] = 'Mã sản phẩm cơ sở không tồn tại - Dòng '. $line;
                }

                if ($item['ma_san_pham_quy_doi'] == '') {
                    $messageError[] = 'Mã sản phẩm quy đổi trống - Dòng '. $line;
                }

                if (!array_key_exists($item['ma_san_pham_quy_doi'], $products)) {
                    $messageError[] = 'Mã sản phẩm quy đổi không tồn tại - Dòng '. $line;
                }

                if (!array_key_exists($item['ma_san_pham_quy_doi'], $products)) {
                    $messageError[] = 'Mã sản phẩm quy đổi không tồn tại - Dòng '. $line;
                }

                if ($item['so_luong_quy_doi'] == '' || $item['so_luong_quy_doi'] < 1) {
                    $messageError[] = 'Số lượng quy đổi trống hoặc không hợp lệ - Dòng '. $line;
                }

                if (empty($messageError)) {
                    $productExchangeDetail = $productExchanges->where('product_id', $products[$item['ma_san_pham_co_so']])->where('product_exchange_id', $products[$item['ma_san_pham_quy_doi']])->first();
                    if ($productExchangeDetail) {
                        AdminProductExchange::find($productExchangeDetail->id)->update([
                            'qty_exchange' => (int)$item['so_luong_quy_doi'],
                            'status' => $item['trang_thai'] == 0 ? 0 : 1,
                        ]);
                    } else {
                        $productExchangeInsert = [
                            'product_id' => $products[$item['ma_san_pham_co_so']],
                            'product_exchange_id' => $products[$item['ma_san_pham_quy_doi']],
                            'qty_exchange' => (int)$item['so_luong_quy_doi'],
                            'status' => $item['trang_thai'] == 0 ? 0 : 1,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                        AdminProductExchange::create($productExchangeInsert);
                    }
                }
            }
            if (!empty($messageError)) {
                throw new \Exception('Lỗi, vui lòng kiểm tra lại!');
            }
        } catch (ImportException $e) {
            Log::debug($e);
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage());
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::debug($e);
            return redirect()->back()->with('error_validate_import', $messageError);
        }
        DB::commit();
        return redirect()->route('product_exchange.index')->with('success', sc_language_render('product.admin.import_success'));
    }

    /**
     * Xuất excel
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function export()
    {
        $ids = request('ids') ?? '';
        if ($ids) {
            $ids = explode(',', request('ids'));
        }

        $nameTable = SC_DB_PREFIX . "shop_product_exchange";
        $dataSearch = [
            'keyword' => request('keyword') ?? '',
            'status' => request('status') ?? '',
        ];
        if (!empty($ids)) {

            $productExchange = AdminProductExchange::getList([])->whereIn($nameTable.'.id', $ids)->get();
        } else {
            $productExchange = AdminProductExchange::getList($dataSearch)->get();
        }

        return Excel::download(new AdminProductExchangeExport($productExchange), 'SanPhamQuyDoi-' . Carbon::now() . '.xlsx');
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
        $ids = request('ids');
        $arrID = explode(',', $ids);
        AdminProductExchange::destroy($arrID);
        return response()->json(['error' => 0, 'msg' => sc_language_render('action.delete_success')]);
    }
}
