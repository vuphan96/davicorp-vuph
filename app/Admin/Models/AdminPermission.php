<?php

namespace App\Admin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Http\Request;

class AdminPermission extends Model
{
    public $table = SC_DB_PREFIX . 'admin_permission';
    protected $fillable = ['name', 'slug', 'type', 'parent_id', 'http_uri'];
    private static $getList = null;

    /**
     * Permission belongs to many roles.
     *
     * @return BelongsToMany
     */
    public static function getPermissionGroup()
    {
        return self::where('type', 1)->get();
    }

    public function childPermission(){
        return $this->hasMany(self::class, 'parent_id', 'id');
    }

    public static function getPermissionTree()
    {
        $allPermission = self::where('parent_id', 0)->with('childPermission')->get();
        $output = [];
        foreach ($allPermission as $permission){
            $output[$permission->id] = $permission->name;
            if(!empty($permission->childPermission)){
                foreach ($permission->childPermission as $childPermisson){
                    $output[$childPermisson->id] = '-' . $childPermisson->name;
                }
            }
        }
        return $output;
    }

    public function roles()
    {
        return $this->belongsToMany(AdminRole::class, SC_DB_PREFIX . 'admin_role_permission', 'permission_id', 'role_id');
    }

    /**
     * If request should pass through the current permission.
     *
     * @param Request $request
     *
     * @return bool
     */
    public function passRequest(Request $request): bool
    {
        if (empty($this->http_uri)) {
            return false;
        }

        $uriCurrent = \Route::getCurrentRoute()->uri;
        $methodCurrent = $request->method();
        $actions = explode(',', $this->http_uri);

        foreach ($actions as $key => $action) {
            $method = explode('::', $action);
            if ($method[0] === 'ANY' && ($request->path() . '/*' == $method[1] || $request->is($method[1]))) {
                return true;
            }
            if ($methodCurrent . '::' . $uriCurrent == $action) {
                return true;
            }
        }

        return false;
    }

    /**
     * Detach models from the relationship.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::deleted(function ($model) {
            $model->roles()->detach();
        });
    }

    /**
     * Update info
     * @param  [array] $dataUpdate
     * @param  [int] $id
     */
    public static function updateInfo($dataUpdate, $id)
    {
        $dataUpdate = $dataUpdate;
        $obj = self::find($id);
        return $obj->update($dataUpdate);
    }

    /**
     * Create new permission
     * @return [type] [description]
     */
    public static function createPermission($dataInsert)
    {
        $dataUpdate = $dataInsert;
        return self::create($dataUpdate);
    }

    public static function getListAll()
    {
        if (self::$getList == null) {
            self::$getList = self::orderBy('id', 'asc')
                ->get();
        }
        return self::$getList;
    }
}
