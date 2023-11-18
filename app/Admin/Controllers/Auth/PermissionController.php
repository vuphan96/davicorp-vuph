<?php

namespace App\Admin\Controllers\Auth;

use App\Admin\Models\AdminCategory;
use App\Admin\Models\AdminPermission;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use SCart\Core\Admin\Controllers\RootAdminController;
use Validator;

class PermissionController extends RootAdminController
{
    public $routeAdmin;

    public function __construct()
    {
        parent::__construct();

        $routes = app()->routes->getRoutes();

        foreach ($routes as $route) {
            if (Str::startsWith($route->uri(), SC_ADMIN_PREFIX)) {
                $prefix = ltrim($route->getPrefix(), '/');
                $routeAdmin[$prefix] = [
                    'uri' => 'ANY::' . $prefix . '/*',
                    'name' => $prefix . '/*',
                    'method' => 'ANY',
                ];
                foreach ($route->methods as $key => $method) {
                    if ($method != 'HEAD' && !collect($this->without())->first(function ($exp) use ($route) {
                            return Str::startsWith($route->uri, $exp);
                        })) {
                        $routeAdmin[] = [
                            'uri' => $method . '::' . $route->uri,
                            'name' => $route->uri,
                            'method' => $method,
                        ];
                    }
                }
            }
        }

        $this->routeAdmin = $routeAdmin;
    }

    public function index()
    {
        $perms = AdminPermission::getListAll()->groupBy('parent_id');
        if (!$perms || !count($perms)){
            $perms = [];
        }
        $data = [
            'title' => 'Quản lý phân quyền',
            'subTitle' => '',
            'icon' => 'fa fa-indent',
            'menu' => [],
            'perms' => $perms,
            'routeAdmin' => $this->routeAdmin,
            'permission' => [
                'type' => '0'
            ],
            'permissionGroup' => AdminPermission::getPermissionGroup(),
            'url_action' => sc_route_admin('admin_permission.create'),
            'urlDeleteItem' => sc_route_admin('admin_permission.delete'),
            'title_form' => '<i class="fa fa-plus" aria-hidden="true"></i> Tạo mới quyền ',
        ];
        $data['layout'] = 'index';
        return view($this->templatePathAdmin.'screen.list_permission')
            ->with($data);
    }

    /**
     * Post create new item in admin
     * @return [type] [description]
     */
    public function postCreate()
    {
        $dataOrigin = request()->all();
        Log::info(json_encode($dataOrigin));
        $validator = Validator::make($dataOrigin, [
            'name' => 'required|string|max:50',
            'type' => 'required',
            'parent_id' => 'nullable',
            'slug' => 'nullable',
            'http_uri' => 'nullable'
        ]);
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        $data = $validator->validated();
        $dataInsert = [
            'name' => $data['name'] ?? '',
            'slug' => $data['type'] ? null : $data['slug'],
            'type' => $data['type'] ?? 0,
            'parent_id' =>  $data['type'] ? 0 : $data['parent_id'],
            'http_uri' =>  $data['type'] ? '' : implode(',', ($data['http_uri'] ?? [])),
        ];
        $dataInsert = sc_clean($dataInsert, [], true);
        $permission = AdminPermission::createPermission($dataInsert);

        return redirect()->route('admin_permission.index')->with('success', sc_language_render('action.create_success'));
    }

    /**
     * Form edit
     */
    public function edit($id)
    {
        $permission = AdminPermission::find($id);
        if ($permission === null) {
            return redirect()->route('admin.data_not_found');
        }

        $perms = AdminPermission::getListAll()->groupBy('parent_id');
        if (!$perms || !count($perms)){
            $perms = [];
        }

        $data = [
            'title' => 'Quản lý phân quyền',
            'subTitle' => '',
            'icon' => 'fa fa-edit',
            'menu' => [],
            'perms' => $perms,
            'routeAdmin' => $this->routeAdmin,
            'permission' => $permission,
            'permissionGroup' => AdminPermission::getPermissionGroup(),
            'url_action' => sc_route_admin('admin_permission.edit', ['id' => $permission['id']]),
            'urlDeleteItem' => sc_route_admin('admin_permission.delete'),
            'title_form' => '<i class="fa fa-edit" aria-hidden="true"></i> Chỉnh sửa quyền ',
        ];
        $data['id'] = $id;
        $data['layout'] = 'edit';

        return view($this->templatePathAdmin.'screen.list_permission')
            ->with($data);
    }

    /**
     * update status
     */
    public function postEdit($id)
    {
        $permission = AdminPermission::find($id);
        $dataOrigin = request()->all();
        $validator = Validator::make($dataOrigin, [
            'name' => 'required|string|max:50',
            'type' => 'required',
            'parent_id' => 'nullable',
            'slug' => 'nullable',
            'http_uri' => 'nullable'
        ], [
            'slug.regex' => sc_language_render('admin.permission.slug_validate'),
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        //Edit
        $data = $validator->validated();
        $dataUpdate = [
            'name' => $data['name'] ?? '',
            'slug' => $data['type'] ? '' : $data['slug'],
            'type' => $data['type'] ?? 0,
            'parent_id' =>  $data['type'] ? 0 : $data['parent_id'],
            'http_uri' =>  $data['type'] ? '' : implode(',', ($data['http_uri'] ?? [])),
        ];
        if ($dataUpdate['type']) {
            $dataUpdate['parent_id'] = 0;
        }
        $dataUpdate = sc_clean($dataUpdate, [], true);
        $permission->update($dataUpdate);
        return redirect()->route('admin_permission.index')->with('success', sc_language_render('action.edit_success'));
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
            if(count(AdminPermission::find($ids)->childPermission) > 0) return response()->json(['error' => 1, 'msg' => 'Tồn tại quyền thuộc nhóm quyền này. Vui lòng kiểm tra lại']);
            if(!AdminPermission::destroy($arrID)){
                return response()->json(['error' => 1, 'msg' => 'Lỗi xoá quyền. Vui lòng kiểm tra lại']);
            };
            return response()->json(['error' => 0, 'msg' => 'Thành công!']);

        }
    }

    public function without()
    {
        $prefix = SC_ADMIN_PREFIX ? SC_ADMIN_PREFIX . '/' : '';
        return [
            $prefix . 'login',
            $prefix . 'logout',
            $prefix . 'forgot',
            $prefix . 'deny',
            $prefix . 'locale',
            $prefix . 'uploads',
        ];
    }
}
