<?php
namespace App\Admin\Controllers;

use App\Admin\Models\AdminProduct;
use SCart\Core\Admin\Controllers\RootAdminController;
use Illuminate\Support\Str;
use App\Admin\Models\AdminUnit;
use App\Http\Requests\Admin\AdminUnitRequest;
use App\Http\Requests\Admin\AdminUnitEditRequest;
use Request;

class AdminUnitmoneyController extends RootAdminController
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
            'title' => sc_language_render('admin.money.unit'),
            'title_action' => '<i class="fa fa-plus" aria-hidden="true"></i> ' . sc_language_render('admin.unit.add_new_title'),
            'subTitle' => '',
            'icon' => 'fa fa-indent',
            'urlDeleteItem' => sc_route_admin('admin_unit.delete'),
            'removeList' => 1, // 1 - Enable function delete list item
            'buttonRefresh' => 0, // 1 - Enable button refresh
            'buttonSort' => 0, // 1 - Enable button sort
            'css' => '',
            'js' => '',
            'length' => '',
            'url_action' => sc_route_admin('admin_unit.create'),
            'data_perm_submit' => 'unit:create'
        ];
        // menu left
        $data['menuLeft'] = sc_config_group('menuLeft', Request::route()->getName());
        $keyword = sc_clean(request('keyword') ?? '');
        $listTh = [
            'name' => sc_language_render('admin.length.name'),
            'description' => sc_language_render('admin.length.description'),
            'type' => sc_language_render('admin.unit.type'),
            'action' => sc_language_render('action.title'),
        ];
        $obj = new AdminUnit;
        $dataTmp = $obj->orderBy('id', 'desc');
        if ($keyword) {
            $dataTmp = $dataTmp->where('name', 'like', '%' . $keyword . '%');
        }
        $dataTmp = $dataTmp->paginate(config('pagination.admin.small'));

        $dataTr = [];
        foreach ($dataTmp as $key => $row) {
            $dataTr[$row['id']] = [
                'name' => $row['name'] ?? '',
                'description' => $row['description'] ?? '',
                'type' => ($row['type'] == 1) ? sc_language_render('action.type_unit_integer') : sc_language_render('action.type_unit_decimal'),
                'action' => '
                    <a data-perm="unit:edit" href="' . sc_route_admin('admin_unit.edit', ['id' => $row['id'] ? $row['id'] : 'not-found-id']) . '"><span title="' . sc_language_render('action.edit') . '" type="button" class="btn btn-flat btn-sm btn-primary"><i class="fa fa-edit"></i></span></a>&nbsp;
                  <span data-perm="unit:delete" onclick="deleteItem(\'' . $row['id'] . '\');"  title="' . sc_language_render('action.delete') . '" class="btn btn-flat btn-sm btn-danger"><i class="fas fa-trash-alt"></i></span>
                  ',
            ];
        }
        $cssTh = [
            'name' => '; width:20%',
            'description' => '',
            'type' => 'width:20%; text-align: center',
            'action' => 'text-align :center; width:25%'
        ];
        $cssTd = [
            'name' => '',
            'description' => '',
            'type' => 'text-align :center',
            'action' => 'text-align :center'
        ];
        $data['cssTh'] = $cssTh;
        $data['cssTd'] = $cssTd;
        $data['listTh'] = $listTh;
        $data['dataTr'] = $dataTr;
        $data['pagination'] = $dataTmp->appends(request()->except(['_token', '_pjax']))->links($this->templatePathAdmin.'component.pagination');
        $data['resultItems'] = sc_language_render('admin.result_item', ['item_from' => $dataTmp->firstItem(), 'item_to' => $dataTmp->lastItem(), 'total' =>  $dataTmp->total()]);

        //menuSearch
        $data['menuRight'][] = '
                <form action="' . sc_route_admin('admin_unit.index') . '" id="button_search">
                <div class="input-group input-group" style="margin-top:0px">
                    <input type="text" name="keyword" class="form-control rounded-0 float-right" placeholder="' . sc_language_render('admin.category.search_placeholder') . '" value="' . $keyword . '">
                    <div class="input-group-append">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i></button>
                    </div>
                </div>
                </form>';
        //=menuSearch
        
        $data['layout'] = 'index';
        return view($this->templatePathAdmin.'screen.unit')
            ->with($data);
    }


    /**
     * Post create new item in admin
     * @return [type] [description]
     */
    public function postCreate(AdminUnitRequest $request)
    {
        $data = $request->validated();
        $name = $data['name'];
        $countName = (new AdminUnit())->whereRaw('LOWER(name) = CONVERT(LOWER(?),BINARY)', Str::lower($name))->count();

        if ($countName>0) {
            return redirect()->back()->withInput($data)->with('warrning', sc_language_render('admin.unit.name_unique'));
        }
        $dataInsert = [
            'name' => $data['name'] ?? '',
            'description' => $data['description'] ?? '',
            'type' => $data['type']
        ];
        try{
            $dataInsert = sc_clean($dataInsert, [], true);
            $obj = AdminUnit::create($dataInsert);
        }
        catch(Exception $e){
            return response()->json([
              'error' => sc_language_render('action.failed'),
            ]);
        }
        
        return redirect()->route('admin_unit.index')->with('success', sc_language_render('action.create_success'));
    }

    /**
     * Form edit
     */

    public function edit($id)
    {
        $length = AdminUnit::findOrFail($id);
    
        $data = [
            'title' => sc_language_render('admin.money.unit'),
            'title_action' => '<i class="fa fa-edit" aria-hidden="true"></i> ' . sc_language_render('action.edit'),
            'subTitle' => '',
            'icon' => 'fa fa-indent',
            'urlDeleteItem' => sc_route_admin('admin_unit.delete'),
            'removeList' => 1, // 1 - Enable function delete list item
            'buttonRefresh' => 0, // 1 - Enable button refresh
            'buttonSort' => 0, // 1 - Enable button sort
            'css' => '',
            'js' => '',
            'url_action' => sc_route_admin('admin_unit.edit', ['id' => $length['id']]),
            'method' => 'PUT',
            'length' => $length,
            'id' => $id,
            'data_perm_submit' => 'unit:edit'
        ];

        // menu left
        $data['menuLeft'] = sc_config_group('menuLeft', Request::route()->getName());
        $keyword = sc_clean(request('keyword') ?? '');
        $listTh = [
            'name' => sc_language_render('admin.length.name'),
            'description' => sc_language_render('admin.length.description'),
            'type' => sc_language_render('admin.unit.type'),
            'action' => sc_language_render('action.title'),
        ];
        $obj = new AdminUnit;
        $dataTmp = $obj->orderBy('id', 'desc');
        if ($keyword) {
            $dataTmp = $dataTmp->where('name', 'like', '%' . $keyword . '%');
        }
        $dataTmp = $dataTmp->paginate(config('pagination.admin.small'));

        $dataTr = [];
        foreach ($dataTmp as $key => $row) {
            $dataTr[$row['id']] = [
                'name' => $row['name'] ?? '',
                'description' => $row['description'] ?? '',
                'type' => ($row['type'] == 1) ? sc_language_render('action.type_unit_integer') : sc_language_render('action.type_unit_decimal'),
                'action' => '
                    <a  data-perm="unit:edit" href="' . sc_route_admin('admin_unit.edit', ['id' => $row['id'] ? $row['id'] : 'not-found-id']) . '"><span title="' . sc_language_render('action.edit') . '" type="button" class="btn btn-flat btn-sm btn-primary"><i class="fa fa-edit"></i></span></a>&nbsp;
                  <span data-perm="unit:delete" onclick="deleteItem(\'' . $row['id'] . '\');"  title="' . sc_language_render('action.delete') . '" class="btn btn-flat btn-sm btn-danger"><i class="fas fa-trash-alt"></i></span>
                  ',
            ];
        }
        $cssTh = [
            'name' => '; width:20%',
            'description' => '',
            'type' => 'width:20%; text-align: center',
            'action' => 'text-align :center; width:25%'
        ];
        $cssTd = [
            'name' => '',
            'description' => '',
            'type' => 'text-align :center',
            'action' => 'text-align :center'
        ];
        $data['cssTh'] = $cssTh;
        $data['cssTd'] = $cssTd;
        $data['listTh'] = $listTh;
        $data['dataTr'] = $dataTr;
        $data['pagination'] = $dataTmp->appends(request()->except(['_token', '_pjax']))->links($this->templatePathAdmin.'component.pagination');
        $data['resultItems'] = sc_language_render('admin.result_item', ['item_from' => $dataTmp->firstItem(), 'item_to' => $dataTmp->lastItem(), 'total' =>  $dataTmp->total()]);

        //menuSearch
        $data['menuRight'][] = '
                <form action="' . sc_route_admin('admin_unit.index') . '" id="button_search">
                <div class="input-group input-group" style="margin-top:0px">
                    <input type="text" name="keyword" class="form-control rounded-0 float-right" placeholder="' . sc_language_render('admin.category.search_placeholder') . '" value="' . $keyword . '">
                    <div class="input-group-append">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i></button>
                    </div>
                </div>
                </form>';
        //=menuSearch

        $data['layout'] = 'edit';
        return view($this->templatePathAdmin.'screen.unit')
            ->with($data);
    }


    /**
     * update status
     */
    public function postEdit($id,AdminUnitEditRequest $request)
    {
        $data = $request->validated();;
        $obj = AdminUnit::findOrFail($id);

        $name = $data['name'];
        $countName = (new AdminUnit())->whereRaw('LOWER(name) = CONVERT(LOWER(?),BINARY)', Str::lower($name))
                    ->where('id', '<>', $id)
                    ->count();

        if ($countName>0) {
            return redirect()->back()->withInput($data)->with('error', sc_language_render('admin.unit.name_unique'));
        }

        $dataUpdate = [
            'name' => $data['name'],
            'description' => $data['description'],
            'type' => $data['type']
        ];
        try{
            $obj->update($dataUpdate);
            $dataUpdate = sc_clean($dataUpdate, [], true);
        }catch(Exception $e){
            return response()->json([
              'error' => sc_language_render('action.failed'),
            ]);
        }

        return redirect()->route('admin_unit.index')->with('success', sc_language_render('action.edit_success'));
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
        $products = AdminProduct::whereIn('unit_id', $arrID)->first();
        if ($products) {
            return response()->json(['error' => 1, 'msg' => 'Có đơn vị tính được gán cho sản phẩm. Không thể xóa!']);
        }
        AdminUnit::destroy($arrID);
        return response()->json(['error' => 0, 'msg' => sc_language_render('action.delete_success')]);
    }
}
