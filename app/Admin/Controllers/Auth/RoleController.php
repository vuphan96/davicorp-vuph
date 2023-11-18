<?php
namespace App\Admin\Controllers\Auth;

use App\Admin\Models\AdminPermission;
use App\Admin\Models\AdminRole;
use App\Admin\Models\AdminUser;
use Illuminate\Support\Facades\DB;
use SCart\Core\Admin\Controllers\RootAdminController;
use Validator;

class RoleController extends RootAdminController
{
    public function __construct()
    {
        parent::__construct();
    }
    
    public function index()
    {
        $data = [
            'title' => sc_language_render('admin.role.manage_group_permission'),
            'subTitle' => '',
            'icon' => 'fa fa-indent',
            'urlDeleteItem' => sc_route_admin('admin_role.delete'),
            'removeList' => 0, // 1 - Enable function delete list item
            'buttonRefresh' => 1, // 1 - Enable button refresh
            'buttonSort' => 1, // 1 - Enable button sort
            'css' => '',
            'js' => '',
        ];
        //Process add content
        $data['menuRight'] = sc_config_group('menuRight', \Request::route()->getName());
        $data['menuLeft'] = sc_config_group('menuLeft', \Request::route()->getName());
        $data['topMenuRight'] = sc_config_group('topMenuRight', \Request::route()->getName());
        $data['topMenuLeft'] = sc_config_group('topMenuLeft', \Request::route()->getName());
        $data['blockBottom'] = sc_config_group('blockBottom', \Request::route()->getName());

        $listTh = [
            'name' => sc_language_render('admin.role.name'),
            'created_at' => sc_language_render('admin.role.created_at'),
            'updated_at' => sc_language_render('admin.updated_at'),
            'action' => sc_language_render('action.title'),
        ];
        $sort_order = sc_clean(request('sort_order') ?? 'id_desc');
        $arrSort = [
            'id__desc' => sc_language_render('filter_sort.id_desc'),
            'id__asc' => sc_language_render('filter_sort.id_asc'),
            'name__desc' => sc_language_render('filter_sort.name_desc'),
            'name__asc' => sc_language_render('filter_sort.name_asc'),
        ];
        $obj = new AdminRole;
        if ($sort_order && array_key_exists($sort_order, $arrSort)) {
            $field = explode('__', $sort_order)[0];
            $sort_field = explode('__', $sort_order)[1];
            $obj = $obj->orderBy($field, $sort_field);
        } else {
            $obj = $obj->orderBy('id', 'desc');
        }
        $dataTmp = $obj->paginate(20);

        $dataTr = [];
        foreach ($dataTmp as $key => $row) {
            $showPermission = '';
            if ($row['permissions']->count()) {
                foreach ($row['permissions'] as $key => $p) {
                    $showPermission .= '<span class="badge badge-success"">' . $p->name . '</span> ';
                }
            }

            $dataTr[] = [
                'name' => $row['name'],
                'created_at' => $row['created_at'],
                'updated_at' => $row['updated_at'],
                'action' => ((in_array($row['id'], SC_GUARD_ROLES)) ? '' : '
                    <a href="' . sc_route_admin('admin_role.edit', ['id' => $row['id'] ? $row['id'] : 'not-found-id']) . '"><span title="' . sc_language_render('action.edit') . '" type="button" class="btn btn-flat btn-sm btn-primary"><i class="fa fa-edit"></i></span></a>&nbsp;
                    ')
                    . ((in_array($row['id'], SC_GUARD_ROLES)) ? '' : '<span onclick="deleteItem(\'' . $row['id'] . '\');"  title="' . sc_language_render('action.delete') . '" class="btn btn-flat btn-sm btn-danger"><i class="fas fa-trash-alt"></i></span>')
                ,
            ];
        }

        $data['listTh'] = $listTh;
        $data['dataTr'] = $dataTr;
        $data['pagination'] = $dataTmp->appends(request()->except(['_token', '_pjax']))->links($this->templatePathAdmin.'component.pagination');
        $data['resultItems'] = sc_language_render('admin.result_item', ['item_from' => $dataTmp->firstItem(), 'item_to' => $dataTmp->lastItem(), 'total' =>  $dataTmp->total()]);

        //menuRight
        $data['menuRight'][] = '<a href="' . sc_route_admin('admin_role.create') . '" class="btn  btn-success  btn-flat" title="New" id="button_create_new">
                           <i class="fa fa-plus" title="'.sc_language_render('action.add').'"></i>
                           </a>';
        //=menuRight

        //menuSort
        $optionSort = '';
        foreach ($arrSort as $key => $status) {
            $optionSort .= '<option  ' . (($sort_order == $key) ? "selected" : "") . ' value="' . $key . '">' . $status . '</option>';
        }
        $data['urlSort'] = sc_route_admin('admin_role.index', request()->except(['_token', '_pjax', 'sort_order']));
        $data['optionSort'] = $optionSort;
        //=menuSort

        return view($this->templatePathAdmin.'screen.list')
            ->with($data);
    }

    /**
     * Form create new item in admin
     * @return [type] [description]
     */
    public function create()
    {
        $data = [
            'title' => "Tạo phân quyền mới",
            'subTitle' => '',
            'title_description' => sc_language_render('admin.role.add_new_des'),
            'icon' => 'fa fa-plus',
            'role' => [],
            'permission' => (new AdminPermission)->pluck('name', 'id')->all(),
            'userList' => (new AdminUser)->pluck('name', 'id')->all(),
            'url_action' => sc_route_admin('admin_role.create'),
            'listPermission' => AdminPermission::getPermissionTree()
        ];


        return view($this->templatePathAdmin.'auth.role')
            ->with($data);
    }

    /**
     * Post create new item in admin
     * @return [type] [description]
     */
    public function postCreate()
    {
        $data = request()->all();

        $dataOrigin = request()->all();
        $validator = Validator::make($dataOrigin, [
            'name' => 'required|string|max:50|unique:"'.AdminRole::class.'",name',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $dataInsert = [
            'name' => $data['name'],
            'slug' => sc_word_format_url($data['name']),
        ];
        $dataInsert = sc_clean($dataInsert, [], true);
        $role = AdminRole::createRole($dataInsert);
        $permission = explode(",", $data['permission_id']) ?? [];
        $administrators = $data['administrators'] ?? [];
        //Insert permission
        if ($permission) {
            $role->permissions()->attach($permission);
        }
        //Insert administrators
        if ($administrators) {
            $role->administrators()->attach($administrators);
        }
        return redirect()->route('admin_role.index')->with('success', sc_language_render('action.create_success'));
    }

    /**
     * Form edit
     */
    public function edit($id)
    {
        $role = AdminRole::find($id);
        if ($role === null) {
            return 'no data';
        }
        $data = [
            'title' => "Chỉnh sửa phân quyền",
            'subTitle' => '',
            'title_description' => '',
            'icon' => 'fa fa-edit',
            'role' => $role,
            'permission' => (new AdminPermission)->pluck('name', 'id')->all(),
            'userList' => (new AdminUser)->pluck('name', 'id')->all(),
            'url_action' => sc_route_admin('admin_role.edit', ['id' => $role['id']]),
            'listPermission' => AdminPermission::getPermissionTree(),
            'currentPermission' => $role->getFormatedPermisson(),
        ];
        return view($this->templatePathAdmin.'auth.role')
            ->with($data);
    }

    /**
     * update status
     */
    public function postEdit($id)
    {
        $role = AdminRole::find($id);
        $data = request()->all();
        $dataOrigin = request()->all();
        $validator = Validator::make($dataOrigin, [
            'name' => 'required|string|max:50|unique:"'.AdminRole::class.'",name,' . $role->id . '',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        //Edit

        $dataUpdate = [
            'name' => $data['name'],
            'slug' => $data['slug'],
        ];
        $dataUpdate = sc_clean($dataUpdate, [], true);
        $role->update($dataUpdate);
        $permission = explode(",", $data['permission_id']) ?? [];
//        $administrators = $data['administrators'] ?? [];
        $role->permissions()->detach();
//        $role->administrators()->detach();
        //Insert permission
        if ($permission) {
            $role->permissions()->attach($permission);
        }
        //Insert administrators
//        if ($administrators) {
//            $role->administrators()->attach($administrators);
//        }
        return redirect()->route('admin_role.index')->with('success', sc_language_render('action.edit_success'));
    }

    /*
    Delete list Item
    Need mothod destroy to boot deleting in model
     */
    public function deleteList()
    {
        if (!request()->ajax()) {
            return response()->json(['error' => 1, 'msg' => sc_language_render('admin.method_not_allow')]);
        } else {
            $ids = request('ids');
            $arrID = explode(',', $ids);
            $usingRole = DB::table(SC_DB_PREFIX.'admin_role_user')->whereIn("role_id", $arrID)->get();
            if($usingRole && count($usingRole) > 0){
                $roleInfo = \SCart\Core\Admin\Models\AdminRole::whereIn("id", data_get($usingRole, "*.role_id"))->get();
                return response()->json(['error' => 1, 'msg' => 'Không thể xoá quyền đang được sử dụng (' . implode(', ', data_get($roleInfo, "*.name")) . ')']);
            }

            $arrID = array_diff($arrID, SC_GUARD_ROLES);
            AdminRole::destroy($arrID);
            return response()->json(['error' => 0, 'msg' => 'Thành công']);
        }
    }
}
