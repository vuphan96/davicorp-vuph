<?php

namespace App\Admin\Controllers;

use App\Admin\Models\AdminCustomer;
use App\Admin\Models\AdminProductPrice;
use App\Admin\Models\AdminUserPriceboard;
use App\Admin\Models\AdminUserPriceboardDetail;
use App\Exceptions\ImportException;
use App\Exports\UserPriceboardExport;
use App\Front\Models\ShopCustomer;
use App\Front\Models\ShopUserPriceboard;
use App\Front\Models\ShopUserPriceboardDetail;
use App\Front\Models\ShopZone;
use App\Http\Requests\Admin\UserPriceboardRequest;
use App\Http\Requests\Admin\UserPriceboardRequestEdit;
use App\Imports\UserPriceboardImport;
use App\Imports\UserPriceboardMainImport;
use Carbon\Carbon;
use Carbon\Exceptions\InvalidFormatException;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use SCart\Core\Admin\Controllers\RootAdminController;
use SCart\Core\Front\Models\ShopLanguage;

class AdminUserPriceboardController extends RootAdminController
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
            'title' => 'Danh sách nhóm báo giá',
            'subTitle' => '',
            'icon' => 'fa fa-indent',
            'urlDeleteItem' => sc_route_admin('admin_priceboard.delete'),
            'removeList' => 1, // Enable function delete list item
            'buttonRefresh' => 1, // 1 - Enable button refresh
            'buttonSort' => 1, // 1 - Enable button sort
            'method' => 'delete',
            'urlExport' => sc_route_admin('admin_priceboard.export'),
            'permGroup' => 'priceboard',
        ];
        //Process add content
        $data['menuRight'] = sc_config_group('menuRight', \Request::route()->getName());
        $data['menuLeft'] = sc_config_group('menuLeft', \Request::route()->getName());
        $data['topMenuRight'] = sc_config_group('topMenuRight', \Request::route()->getName());
        $data['topMenuLeft'] = sc_config_group('topMenuLeft', \Request::route()->getName());
        $data['blockBottom'] = sc_config_group('blockBottom', \Request::route()->getName());

        $listTh = [
            'user_priceboard_code' => "Mã nhóm báo giá",
            'name' => "Tên nhóm báo giá",
            'productprice_code' => "Mã báo giá",
            'productprice_name' => "Báo giá",
            'start_date' => sc_language_render('priceboard.start_date'),
            'due_date' => sc_language_render('priceboard.due_date'),
            'action' => sc_language_render('action.title')
        ];

        $keyword = sc_clean(request('keyword') ?? '');
        $sort_order = sc_clean(request('sort_order') ?? 'id_desc');

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

        $dataTmp = (new AdminUserPriceboard)->getUserPriceBoard($dataSearch);


        $dataTr = [];
        foreach ($dataTmp as $key => $row) {
            $dataMap = [
                'user_priceboard_code' => $row->priceboard_code ?? '',
                'name' => $row->name ?? '',
                'productprice_code' => $row->priceboard->price_code ?? '',
                'productprice_name' => '<a data-perm="price:detail" perm-type="disable" href="' . route('admin_price.edit', ['id' => $row->priceboard->id ?? 'notfound']) . '">' . ($row->priceboard ? $row->priceboard->name : '') .'</a>' ?? '',
                'start_date' => Carbon::make($row->start_date)->format('d/m/Y') ?? '',
                'due_date' => Carbon::make($row->due_date)->format('d/m/Y') ?? '',
            ];

            $htmlAction = '
            <a data-perm="priceboard:detail" href="' . sc_route_admin('admin_priceboard.edit', ['id' => $row['id'] ?? 'not-found-id']) . '">
            <span title="' . sc_language_render('product.admin.edit') . '" type="button" class="btn btn-flat btn-sm btn-primary">
            <i class="fa fa-edit"></i>
            </span>
            </a>
          
                <span data-perm="priceboard:delete" onclick="deleteItem(\'' . $row['id'] . '\');"  title="' . sc_language_render('action.delete') . '" class="btn btn-flat btn-sm btn-danger">
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
        $data['menuRight'][] = '<a  data-perm="priceboard:create" href="' . sc_route_admin('admin_priceboard.create') . '" class="btn btn-success btn-flat" title="' . sc_language_render('product.admin.add_new_title') . '" id="button_create_new">
        <i class="fa fa-plus"></i>
        </a>
        <a data-perm="priceboard:import" href="' . sc_route_admin('admin_priceboard.import') . '" class="btn  btn-success  btn-flat" title="New" id="button_import">
                            <i class="fa fa-file-import" title="' . sc_language_render('category-import') . '"></i> ' . sc_language_render('category-import') .
            '</a>
        <button  data-perm="priceboard:export" id="btn_export" class="btn  btn-success  btn-flat" title="Xuất excel">
                            <i class="fa fa-file-export" title="' . sc_language_render('category-export') . '"></i> ' . sc_language_render('category-export') .
            '</button>';
        //=menuRight

        //menuSort
        $optionSort = '';
        foreach ($arrSort as $key => $sort) {
            $optionSort .= '<option  ' . (($sort_order == $key) ? "selected" : "") . ' value="' . $key . '">' . $sort . '</option>';
        }
        $data['optionSort'] = $optionSort;
        $data['urlSort'] = sc_route_admin('admin_priceboard.index', request()->except(['_token', '_pjax', 'sort_order']));
        //=menuSort

        //topMenuRight
        $data['topMenuRight'][] = '
                <form action="' . sc_route_admin('admin_priceboard.index') . '" id="button_search">
                <div class="input-group input-group float-left">
                    <input type="text" name="keyword" class="form-control rounded-0 float-right" placeholder="' . sc_language_render('priceboar.search_hint') . '" value="' . $keyword . '">
                    <div class="input-group-append">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i></button>
                    </div>
                </div>
                </form>';
        //=topMenuRight

        // JS


        return view($this->templatePathAdmin . 'screen.list')
            ->with($data);
    }

    public function create()
    {
        $customers = ShopZone::getFormatedCustomerZone();
        $data = [
            'title' => sc_language_render('priceboard.create_title'),
            'subTitle' => '',
            'title_description' => sc_language_render('priceboard.create_title'),
            'icon' => 'fa fa-plus',
            'languages' => $this->languages,
            'actionRoute' => sc_route_admin('admin_priceboard.create'),
            'currentPrice' => null,
            'customers' => $customers ?? [],
        ];

        return view($this->templatePathAdmin . 'screen.user_priceboard_form')
            ->with($data);
    }

    public function postCreate(UserPriceboardRequest $request)
    {

        $insertData = $request->validated();
        $customers = $insertData['customer_data'];
        unset($insertData['customer_data']);
        DB::beginTransaction();
        $userPriceboard = new AdminUserPriceboard($insertData);
        if (!$userPriceboard->save()) {
            DB::rollBack();
            return redirect()->back()->withInput(request()->all())->with('error', sc_language_render('action.update.fail'));
        }

        if (!empty($customers)) {
            $detailData = AdminUserPriceboardDetail::getCustomerInsertList($customers, $userPriceboard->id);
            foreach ($detailData as $keyCustomer => $customer) {
                $listCustomerPriceboard = AdminUserPriceboard::with('customers')->whereHas('customers', function ($query) use ($customer, $customers) {
                    $query->where('customer_id', $customer['customer_id']);
                })->get();
                foreach ($listCustomerPriceboard as $customerPriceboardItem) {
                    $checkConflict = checkConflictRange($userPriceboard->start_date, $userPriceboard->due_date, $customerPriceboardItem->start_date, $customerPriceboardItem->due_date);
                    if ($checkConflict) {
                        DB::rollBack();
                        return redirect()->back()->withInput(request()->all())->with('error', 'Khách hàng ' . ShopCustomer::find($customer['customer_id'])->name .' ã bị gán cho bảng báo giá khác trước đó trong cùng khoảng thời gian');
                    }
                }
            }
            $insertDetail = AdminUserPriceboardDetail::insert($detailData);
            if (!$insertDetail) {
                DB::rollBack();
                return redirect()->back()->withInput(request()->all())->with('error', sc_language_render('action.create.fail'));
            }
        }
        DB::commit();
        return redirect()->route('admin_priceboard.index')->with('success', sc_language_render('action.create_success'));
    }

    public function edit($id)
    {

        $customers = ShopZone::getFormatedCustomerZone();
        $userPriceboard = AdminUserPriceboard::with('customers')->findOrFail($id); // Get bang gia nguoi dung
        $userPriceboard->start_date = convertDate($userPriceboard->start_date, MACHINE_TO_HUMAN);
        $userPriceboard->due_date = convertDate($userPriceboard->due_date, MACHINE_TO_HUMAN);
        $currentPrice = $userPriceboard->priceBoard ?? [];
        $currentCustomer = $userPriceboard->customers->pluck('customer_id')->toArray();

        $data = [
            'title' => sc_language_render('priceboard.edit_title'),
            'subTitle' => '',
            'title_description' => sc_language_render('priceboard.edit_title'),
            'icon' => 'fa fa-edit',
            'method' => 'put',
            'languages' => $this->languages,
            'userPriceboard' => $userPriceboard, // Priceboard hien tai
            'customerList' => json_encode(AdminUserPriceboard::getFormatedCustomers($id)), // Danh sach khach hang cua bang gia
            'actionRoute' => sc_route_admin('admin_priceboard.edit', ['id' => $id]),
            'currentPrice' => $currentPrice,
            'currentCustomer' => $currentCustomer,
            'customers' => $customers ?? []
        ];

        return view($this->templatePathAdmin . 'screen.user_priceboard_form')
            ->with($data);
    }

    public function postEdit(UserPriceboardRequest $request, $id)
    {
        $userPriceboard = (new AdminUserPriceboard)->findOrFail($id);
        $updateData = $request->validated();
        $customers = $updateData['customer_data'];

        unset($updateData['customer_data']);
        DB::beginTransaction();
        if (!$userPriceboard->update($updateData)) {
            DB::rollBack();
            return redirect()->back()->withInput(request()->all())->with('error', sc_language_render('action.update.fail'));
        }
        if (!empty($customers)) {
            $detailData = AdminUserPriceboardDetail::getCustomerInsertList($customers, $userPriceboard->id);
            $userPriceboard->customers()->delete();
            foreach ($detailData as $keyCustomer => $customer) {
                $listCustomerPriceboard = AdminUserPriceboard::with('customers')->whereHas('customers', function ($query) use ($customer, $customers) {
                    $query->where('customer_id', $customer['customer_id']);
                })->get();
                foreach ($listCustomerPriceboard as $customerPriceboardItem) {
                    $checkConflict = checkConflictRange($userPriceboard->start_date, $userPriceboard->due_date, $customerPriceboardItem->start_date, $customerPriceboardItem->due_date);
                    if ($checkConflict) {
                        DB::rollBack();
                        return redirect()->back()->withInput(request()->all())->with('error', 'Khách hàng ' . ShopCustomer::find($customer['customer_id'])->name .' đã bị gán cho bảng báo giá khác trước đó trong cùng khoảng thời gian');
                    }
                }
            }


            $insertDetail = AdminUserPriceboardDetail::insert($detailData);
            if (!$insertDetail) {
                DB::rollBack();
                return redirect()->back()->with('error', sc_language_render('action.update.fail'));
            }
        }
        DB::commit();
        return redirect()->route('admin_priceboard.index')->with('success', sc_language_render('action.update_success'));
    }

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
        }

        if (count($arrDontPermission)) {
            return response()->json(['error' => 1, 'msg' => sc_language_render('admin.remove_dont_permisison') . ': ' . json_encode($arrDontPermission)]);
        }

        if (!empty($arrID)) {
            $deleteFlag = AdminUserPriceboard::destroy($arrID);
            if (!$deleteFlag) {
                return response()->json(['error' => 1, 'msg' => sc_language_render('action.delete_fail') . ': ' . json_encode($arrDontPermission)]);
            }
            return response()->json(['error' => 0, 'msg' => sc_language_render('action.delete_success')]);
        }


    }

    public function checkPermisisonItem($id)
    {
        return (new AdminUserPriceboard)->getPriceboardAdmin($id);
    }


    public function getDynamicPriceBoard(Request $request)
    {
        $page = $request->page ?? 1;
        $result = AdminProductPrice::where('name', 'like', "%$request->search%")->paginate(config('pagination.search.default'), ['*'], 'page', $page);
        $output = ['pagination' => ['more' => $result->hasMorePages()], 'count_filtered' => $result->total()];
        foreach ($result as $item) {
            $output['results'][] = [
                'id' => $item->id,
                'text' => $item->name,
            ];
        }
        return response()->json($output);
    }

    public function getDynamicCustomer(Request $request)
    {
        $result = AdminCustomer::where('name', 'like', "%$request->search%")->paginate(config('pagination.search.default'));
        $output = [];
        foreach ($result as $item) {
            $output['results'][] = [
                'id' => $item->id,
                'text' => $item->name,
            ];
        }
        return response()->json($output);
    }

    public function export()
    {
        $filter = json_decode(json_decode(request('filter')), true, 2);
        $ids = explode(',', request('ids'));
        $option = request('option') ?? 0;

        $sheets = [];
        switch ($option) {
            case 0:
                $sheets = AdminUserPriceboard::getUserPriceBoard($filter, 1);
                break;
            case 1:
                $sheets = AdminUserPriceboard::with(['priceboard', 'customers']);
                if (count($ids) > 0 && !empty($ids[0])) {
                    $sheets->whereIn('id', $ids);
                }
                $sheets = $sheets->get();
                break;
        }

        return (new UserPriceboardExport($sheets))->download('Bang-bao-gia-' . now() . '.xlsx');
    }

    public function import()
    {
        return view($this->templatePathAdmin . 'screen.user_priceboard_import_excel_templete',
            [
                'title' => 'Nhập nhóm báo giá'
            ]);
    }

    public function postImport()
    {
        DB::beginTransaction();
        try {
            //Data obj
            $customers = (new ShopCustomer)->pluck('customer_code', 'id')->toArray(); //Static data
            $prices = (new AdminProductPrice)->pluck('price_code', 'id')->toArray(); //Static data
            $priceboards = (new ShopUserPriceboard())->pluck('priceboard_code')->toArray(); //Static data
            //Format requeire
            $requiredRow = ['ma_khach_hang'];
            $defaultFormatRow = ['stt', 'ma_khach_hang', 'ten_khach_hang'];
            //File
            $file = request()->file('excel_file');
            if (!$file || !is_file($file) || !in_array($file->extension(), ['xls', 'xlsx'])) {
                throw new ImportException('Định dạng file không hợp lệ!');
            }
            $raw_excel_customer = Excel::toArray(new UserPriceboardImport(), $file);
            $raw_excel_main = Excel::toArray(new UserPriceboardMainImport(), $file);

            $excelCustomer = !empty($raw_excel_customer) ? cleanExcelFile($raw_excel_customer) : [];
            $excelMain = !empty($raw_excel_main) ? cleanExcelFile($raw_excel_main) : [];
            $priceboardsInsert = [];
            $allPriceboardCode = array_merge(data_get((new ShopUserPriceboard())->get(), '*.priceboard_code'), data_get($raw_excel_main, '*.0.1'));
            $allPriceboardName = array_merge(data_get((new ShopUserPriceboard())->get(), '*.name'), data_get($raw_excel_main, '*.1.1'));
            $errorBags = [];
//            dd($excelMain);
            foreach (my_array_reverse($excelMain) as $sheet_index => $priceboard) {
                $real_sheet = $sheet_index + 1; // Real sheet index
                // Check templete
                if(empty($excelCustomer[$sheet_index][0])){
                    $errorBags[$real_sheet]["master"][] = "Thông tin gán khách hàng rỗng! Vui lòng kiểm tra lại!";
                    continue;
                }
                // Prepare data
                $name = $priceboard[1][1] ?? '';
                if (!$name) {
                    $errorBags[$real_sheet]["master"][] = "Tên nhóm báo giá trống";
                }
                $priceboard_code = $priceboard[0][1] ?? '';
                if (!$priceboard_code) {
                    $errorBags[$real_sheet]["master"][] = "Mã nhóm báo giá trống";
                }
                $temp_product_price_code = $priceboard[2][1] ?? '';
                if (!$temp_product_price_code) {
                    $errorBags[$real_sheet]["master"][] = "Mã báo giá trống";
                }
                $temp_price = getProductPirceFromCode($temp_product_price_code, $prices);
                if (!$temp_price) {
                    $errorBags[$real_sheet]["master"][] = "Mã báo giá không hợp lệ";
                }
                $startDate = '';
                $dueDate = '';
                $temp_startdate = $priceboard[4][1];
                if (!$temp_startdate) {
                    $errorBags[$real_sheet]["master"][] = "Ngày bắt đầu bị bỏ trống";
                }
                try {
                    $startDate = (is_numeric($temp_startdate)) ? Carbon::make(Date::excelToDateTimeObject($temp_startdate)) : Carbon::createFromFormat('d/m/Y', preg_replace("[-]", "/", $temp_startdate));
                } catch (\Throwable $e) {
                    Log::error($e);
                    $errorBags[$real_sheet]["master"][] = "Ngày bắt đầu không đúng định dạng";
                }
                $temp_duedate = $priceboard[5][1];
                if (!$temp_duedate) {
                    $errorBags[$real_sheet]["master"][] = "Ngày hết hiệu lục bị bỏ trống";
                }
                try {
                    $dueDate = (is_numeric($temp_duedate)) ? Carbon::make(Date::excelToDateTimeObject($temp_duedate)) : Carbon::createFromFormat('d/m/Y', preg_replace("[-]", "/", $temp_duedate));
                } catch (\Throwable $e) {
                    $errorBags[$real_sheet]["master"][] = "Ngày hết hiệu lực không đúng định dạng";
                }
                if (($startDate != "") && ($dueDate != "") && $dueDate->lt($startDate)) {
                    $errorBags[$real_sheet]["master"][] = "Ngày hêt hiệu lực trước ngày hiệu lực";
                }
                if(!empty($errorBags[$real_sheet])){
                    continue;
                }

                $priceboardsInsert = null;
                $priceboardInsert = new ShopUserPriceboard([
                    'priceboard_code' =>  $priceboard_code,
                    'name' => $name,
                    'start_date' => $startDate,
                    'due_date' => $dueDate,
                    'product_price_id' => $temp_price
                ]);
                $priceboardsInsert[] = $priceboardInsert->toArray() ?? [];

                // Check dupticated name and code
                if(count(array_keys($allPriceboardCode, $priceboardInsert['priceboard_code'])) > 1){
                    $errorBags[$real_sheet]["master"][] = "Trùng mã nhóm báo giá";
                }
                if(count(array_keys($allPriceboardName, $priceboardInsert['name'])) > 1){
                    $errorBags[$real_sheet]["master"][] = "Trùng tên nhóm báo giá";
                }
                if(!empty($errorBags[$real_sheet])){
                    continue;
                }
                // End check, Insert data
                if (!$priceboardInsert->save()) {
                    throw new ImportException('Lỗi khi tạo bảng báo giá. Vui lòng đảm bảo các thông tin!');
                }
                $priceboardDetailInsert = [];
                foreach ($excelCustomer[$sheet_index] as $index => $customer) {
                    $line = $index + (new UserPriceboardImport())->headingRow() + 1;
                    $temp_ma_khach_hang = $customer['ma_khach_hang'];
                    $customerId = null;
                    if(empty($temp_ma_khach_hang)){
                        $errorBags[$real_sheet]["details"][$line][] = "Mã khách hàng trống";
                    } else {
                        $customerId = getCustomerFromCode($temp_ma_khach_hang, $customers);
                        if(!$customerId){
                            $errorBags[$real_sheet]["details"][$line][] = "Mã khách hàng không hợp lệ";
                        }
                    }
                    if(!empty($errorBags[$real_sheet]["details"][$line])){
                        continue;
                    }
                    $listCustomerPriceboard = AdminUserPriceboard::with('customers')->whereHas('customers', function ($query) use ($customer, $customers, $customerId) {
                        $query->where('customer_id', $customerId);
                    })->get();
                    foreach ($listCustomerPriceboard as $customerPriceboardItem) {
                        $checkConflict = checkConflictRange($priceboardInsert->start_date, $priceboardInsert->due_date, $customerPriceboardItem->start_date, $customerPriceboardItem->due_date);
                        if ($checkConflict) {
                            $errorBags[$real_sheet]["details"][$line][] = "Khách hàng ". $customer["ma_khach_hang"] ." đã được gán cho nhóm báo giá khác trong cùng khoảng thời gian";
                        }
                    }
                    if(!empty($errorBags[$real_sheet]["details"][$line])){
                        continue;
                    }
                    $priceboardDetailInsert[] = [
                        'user_priceboard_id' => $priceboardInsert->id ?? '',
                        'customer_id' => $customerId
                    ];
                }
                ShopUserPriceboardDetail::insert($priceboardDetailInsert);
            }
            if(!empty($errorBags)){
                DB::rollBack();
                return redirect()->back()->with('error_bags', $errorBags);
            }
        } catch (QueryException $e) {
            Log::error($e);
            DB::rollBack();
            return redirect()->back()->with('error', "Lỗi không xác định, vui lòng liên hệ quản trị viên");
        } catch (ImportException $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage());
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error($e);
            return redirect()->back()->with('error', "Lỗi không xác định, vui lòng liên hệ quản trị viên");
        }
        DB::commit();
        return redirect()->route('admin_priceboard.index')->with('success', sc_language_render('action.success'));
    }
}
