<?php

namespace App\Traits;

use App\Admin\Models\AdminEditTimePermission;
use App\Admin\Models\AdminUser;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

trait UserTraits
{
    public function getUserCanEdit(){
        $list = AdminUser::with(["roles.permissions"])->whereHas("roles.permissions", function ($query){
            $query->whereIn("slug", ["order:create", "order:list", "davicook_dish:list", "order:edit_info", "davicook_dish:edit"]);
        })->get();
        return $list ?? [];
    }

    public function checkUserCanEdit($id){
        $editable = AdminUser::with(["roles.permissions"])->whereHas("roles.permissions", function ($query){
            $query->whereIn("slug", ["order:create", "order:list", "davicook_dish:list", "order:edit_info", "davicook_dish:edit"]);
        })->where("id", $id ?? "")->first();
        return (bool)$editable;
    }
}