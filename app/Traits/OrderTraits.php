<?php

namespace App\Traits;

use App\Admin\Models\AdminEditTimePermission;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

trait OrderTraits
{
    function checkOrderEditable($order){
        try {
            $permissionLockOrder = AdminEditTimePermission::where("user_id", admin()->id())->orderBy("created_at", "DESC")->first();
            //Business resolve order lock edit
            if(\admin()->id() == 1){
                return 1;
            } else {
                if($permissionLockOrder && $permissionLockOrder->davicorp_due_time){
                    if(Carbon::createFromFormat("Y-m-d", $order->delivery_time) < Carbon::createFromFormat("Y-m-d", $permissionLockOrder->davicorp_due_time)){
                        return 0;
                    }
                }
            }
            return 1;
        } catch (\Throwable $e) {
            Log::error($e);
            return 0;
        }
    }
}