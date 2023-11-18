<?php
namespace App\Admin\Controllers;

use App\Admin\Models\AdminProduct;
use App\Admin\Models\AdminWarehouse;
use App\Http\Requests\Admin\AdminWarehouseEditRequest;
use App\Http\Requests\Admin\AdminWarehouseRequest;
use SCart\Core\Admin\Controllers\RootAdminController;
use Illuminate\Support\Str;
use App\Admin\Models\AdminUnit;
use App\Http\Requests\Admin\AdminUnitRequest;
use App\Http\Requests\Admin\AdminUnitEditRequest;
use Request;

class AdminWarehouseController extends RootAdminController
{
    public function __construct()
    {
        parent::__construct();
    }
    /**
     * Index interface.
     *
     * @return Content
     */
    public function index()
    {
        $data = [
            'title' => "Danh sách kho",
            'title_action' => '<i class="fa fa-plus" aria-hidden="true"></i> ' . "Thêm mới kho",
            'subTitle' => '',
            'icon' => 'fa fa-indent',
            'urlDeleteItem' => sc_route_admin('admin_warehouse.delete'),
            'removeList' => 1, // 1 - Enable function delete list item
            'buttonRefresh' => 0, // 1 - Enable button refresh
            'buttonSort' => 0, // 1 - Enable button sort
            'css' => '',
            'js' => '',
            'length' => '',
            'url_action' => sc_route_admin('admin_warehouse.create'),
            'data_perm_submit' => 'admin_warehouse:create'
        ];
        // menu left
        $data['menuLeft'] = sc_config_group('menuLeft', Request::route()->getName());
        $keyword = sc_clean(request('keyword') ?? '');
        $listTh = [
            'warehouse_code' => 'Mã kho',
            'name' => 'Tên kho',
            'address' => 'Địa chỉ kho',
            'action' => sc_language_render('action.title'),
        ];
        $obj = new AdminWarehouse;
        $dataTmp = $obj->orderBy('id', 'desc');
        if ($keyword) {
            $dataTmp = $dataTmp->whereRaw('(warehouse_code LIKE ? OR name LIKE ?)', ['%' . $keyword . '%', '%' . $keyword . '%']);
        }
        $dataTmp = $dataTmp->paginate(config('pagination.admin.small'));

        $dataTr = [];
        foreach ($dataTmp as $key => $row) {
            $dataTr[$row['id']] = [
                'warehouse_code' => $row['warehouse_code'] ?? '',
                'name' => $row['name'] ?? '',
                'address' => $row['address'] ?? '',
                'action' => '
                    <a data-perm="warehouse:edit" href="' . sc_route_admin('admin_warehouse.edit', ['id' => $row['id'] ? $row['id'] : 'not-found-id']) . '"><span title="' . sc_language_render('action.edit') . '" type="button" class="btn btn-flat btn-sm btn-primary"><i class="fa fa-edit"></i></span></a>&nbsp;
                  <span data-perm="warehouse:delete" onclick="deleteItem(\'' . $row['id'] . '\');"  title="' . sc_language_render('action.delete') . '" class="btn btn-flat btn-sm btn-danger"><i class="fas fa-trash-alt"></i></span>
                  ',
            ];
        }
        $cssTh = [
            'warehouse_code' => 'text-align :center',
            'name' => 'text-align :center',
            'address' => 'text-align :center',
            'action' => 'text-align :center'
        ];
        $cssTd = [
            'warehouse_code' => 'text-align: center',
            'name' => 'word-break: break-word; width: 25%;',
            'address' => 'word-break: break-word; width: 35%;',
            'action' => 'text-align: center'
        ];
            
        $data['cssTh'] = $cssTh;
        $data['cssTd'] = $cssTd;
        $data['listTh'] = $listTh;
        $data['dataTr'] = $dataTr;
        $data['pagination'] = $dataTmp->appends(request()->except(['_token', '_pjax']))->links($this->templatePathAdmin.'component.pagination');
        $data['resultItems'] = sc_language_render('admin.result_item', ['item_from' => $dataTmp->firstItem(), 'item_to' => $dataTmp->lastItem(), 'total' =>  $dataTmp->total()]);

        //menuSearch
        $data['menuRight'][] = '
                <form action="' . sc_route_admin('admin_warehouse.index') . '" id="button_search">
                <div class="input-group input-group" style="margin-top:0px">
                    <input type="text" name="keyword" class="form-control rounded-0 float-right" placeholder="' . sc_language_render('admin.category.search_placeholder') . '" value="' . $keyword . '">
                    <div class="input-group-append">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i></button>
                    </div>
                </div>
                </form>';
        //=menuSearch
        
        $data['layout'] = 'index';
        return view($this->templatePathAdmin.'screen.warehouse.warehouse_list.index')
            ->with($data);
    }


    /**
     * Post create new item in admin
     * @return [type] [description]
     */
    public function postCreate(AdminWarehouseRequest $request)
    {
        $data = $request->validated();
        $warehouse_code = $data['warehouse_code'];
        $countCode = (new AdminWarehouse())->whereRaw('LOWER(warehouse_code) = CONVERT(LOWER(?),BINARY)', Str::lower($warehouse_code))->count();
        if ($countCode>0) {
            return redirect()->back()->withInput($data)->with('warrning', 'Mã kho hàng đã tồn tại!');
        }
        $dataInsert = [
            'warehouse_code' => $data['warehouse_code'] ?? '',
            'name' => $data['name'] ?? '',
            'address' => $data['address']
        ];
        try{
            $dataInsert = sc_clean($dataInsert, [], true);
            $obj = AdminWarehouse::create($dataInsert);
        }
        catch(Exception $e){
            return response()->json([
              'error' => sc_language_render('action.failed'),
            ]);
        }
        
        return redirect()->route('admin_warehouse.index')->with('success', sc_language_render('action.create_success'));
    }

    /**
     * Form edit
     */

    public function edit($id)
    {
        $length = AdminWarehouse::findOrFail($id);
    
        $data = [
            'title' => 'Danh sách kho',
            'title_action' => '<i class="fa fa-edit" aria-hidden="true"></i> ' . sc_language_render('action.edit'),
            'subTitle' => '',
            'icon' => 'fa fa-indent',
            'urlDeleteItem' => sc_route_admin('admin_warehouse.delete'),
            'removeList' => 1, // 1 - Enable function delete list item
            'buttonRefresh' => 0, // 1 - Enable button refresh
            'buttonSort' => 0, // 1 - Enable button sort
            'css' => '',
            'js' => '',
            'url_action' => sc_route_admin('admin_warehouse.edit', ['id' => $length['id']]),
            'method' => 'PUT',
            'length' => $length,
            'id' => $id,
            'data_perm_submit' => 'admin_warehouse:edit'
        ];

        // menu left
        $data['menuLeft'] = sc_config_group('menuLeft', Request::route()->getName());
        $keyword = sc_clean(request('keyword') ?? '');
        $listTh = [
            'warehouse_code' => 'Mã kho',
            'name' => 'Tên kho',
            'address' => 'Địa chỉ kho',
            'action' => sc_language_render('action.title'),
        ];
        $obj = new AdminWarehouse();
        $dataTmp = $obj->orderBy('id', 'desc');
        if ($keyword) {
            $dataTmp = $dataTmp->whereRaw('(warehouse_code LIKE ? OR name LIKE ?)', ['%' . $keyword . '%', '%' . $keyword . '%']);
        }
        $dataTmp = $dataTmp->paginate(config('pagination.admin.small'));

        $dataTr = [];
        foreach ($dataTmp as $key => $row) {
            $dataTr[$row['id']] = [
                'warehouse_code' => $row['warehouse_code'] ?? '',
                'name' => $row['name'] ?? '',
                'address' => $row['address'] ?? '',
                'action' => '
                    <a  data-perm="warehouse:edit" href="' . sc_route_admin('admin_warehouse.edit', ['id' => $row['id'] ? $row['id'] : 'not-found-id']) . '"><span title="' . sc_language_render('action.edit') . '" type="button" class="btn btn-flat btn-sm btn-primary"><i class="fa fa-edit"></i></span></a>&nbsp;
                  <span data-perm="warehouse:delete" onclick="deleteItem(\'' . $row['id'] . '\');"  title="' . sc_language_render('action.delete') . '" class="btn btn-flat btn-sm btn-danger"><i class="fas fa-trash-alt"></i></span>
                  ',
            ];
        }
        $cssTh = [
            'warehouse_code' => 'text-align :center',
            'name' => 'text-align :center',
            'address' => 'text-align :center',
            'action' => 'text-align :center'
        ];
        $cssTd = [
            'warehouse_code' => 'text-align: center',
            'name' => 'word-break: break-word; width: 25%;',
            'address' => 'word-break: break-word; width: 35%;',
            'action' => 'text-align: center'
        ];
        $data['cssTh'] = $cssTh;
        $data['cssTd'] = $cssTd;
        $data['listTh'] = $listTh;
        $data['dataTr'] = $dataTr;
        $data['pagination'] = $dataTmp->appends(request()->except(['_token', '_pjax']))->links($this->templatePathAdmin.'component.pagination');
        $data['resultItems'] = sc_language_render('admin.result_item', ['item_from' => $dataTmp->firstItem(), 'item_to' => $dataTmp->lastItem(), 'total' =>  $dataTmp->total()]);

        //menuSearch
        $data['menuRight'][] = '
                <form action="' . sc_route_admin('admin_warehouse.index') . '" id="button_search">
                <div class="input-group input-group" style="margin-top:0px">
                    <input type="text" name="keyword" class="form-control rounded-0 float-right" placeholder="' . sc_language_render('admin.category.search_placeholder') . '" value="' . $keyword . '">
                    <div class="input-group-append">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i></button>
                    </div>
                </div>
                </form>';
        //=menuSearch

        $data['layout'] = 'index';
        return view($this->templatePathAdmin.'screen.warehouse.warehouse_list.index')
            ->with($data);
    }


    /**
     * update status
     */
    public function postEdit($id,AdminWarehouseEditRequest $request)
    {
        $data = $request->validated();;
        $obj = AdminWarehouse::findOrFail($id);

        $warehouse_code = $data['warehouse_code'];
        $countName = (new AdminWarehouse())->whereRaw('LOWER(warehouse_code) = CONVERT(LOWER(?),BINARY)', Str::lower($warehouse_code))
                    ->where('id', '<>', $id)
                    ->count();

        if ($countName>0) {
            return redirect()->back()->withInput($data)->with('error', "Mã kho hàng đã tồn tại!");
        }

        $dataUpdate = [
            'warehouse_code' => $data['warehouse_code'],
            'name' => $data['name'],
            'address' => $data['address']
        ];
        try{
            $obj->update($dataUpdate);
            $dataUpdate = sc_clean($dataUpdate, [], true);
        }catch(Exception $e){
            return response()->json([
              'error' => sc_language_render('action.failed'),
            ]);
        }

        return redirect()->route('admin_warehouse.index')->with('success', sc_language_render('action.edit_success'));
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
        AdminWarehouse::destroy($arrID);
        return response()->json(['error' => 0, 'msg' => sc_language_render('action.delete_success')]);
    }
}
