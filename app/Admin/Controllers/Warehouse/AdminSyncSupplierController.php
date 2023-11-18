<?php

namespace App\Admin\Controllers\Warehouse;

use App\Admin\Models\AdminSyncSupplier;
use App\Front\Models\ShopRewardPrinciple;
use Illuminate\Support\Facades\DB;
use SCart\Core\Admin\Controllers\RootAdminController;

class AdminSyncSupplierController extends RootAdminController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function sync()
    {
        $data = [
            'title' => 'Đồng bộ hóa đơn đặt đơn nhà cung cấp',
            'subTitle' => '',
            'title_description' => 'Đồng bộ hóa đơn đặt đơn nhà cung cấp',
            'icon' => 'fas fa-cog',
            'url_action' => sc_route_admin('sync_supplier.edit'),
            'data_sync' => AdminSyncSupplier::where('group','sync_supplier_setting')->get()->toArray(),
            'data_notification' => AdminSyncSupplier::where('group','notification_supplier_setting')->get()->toArray()
        ];

        return view($this->templatePathAdmin . 'screen.warehouse.sync_supplier.index')
            ->with($data);
    }
    public function changeStatus(): \Illuminate\Http\JsonResponse
    {
        $id = request('id');
        try {
            $configStatus = AdminSyncSupplier::find($id);
            if (!$configStatus) {
                return response()->json(['error' => 1, 'msg' => sc_language_render('admin.data_not_found_detail', ['msg' => 'detail#' . $id]), 'detail' => '']);
            }
            if ($configStatus->status == 0) {
                $configStatus->status = 1;
                $configStatus->save();
            } else {
                $configStatus->status = 0;
                $configStatus->save();
            }
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'error' => 1,
                'msg' => sc_language_render('action.update_fail')
            ]);
        }
        DB::commit();

        return response()->json([
            'error' => 0,
            'msg' => sc_language_render('action.update_success')
        ]);

    }
    public function changeTime(): \Illuminate\Http\JsonResponse
    {
        $id = request('id');
        $time = request('time');
        try {
            $configTime = AdminSyncSupplier::find($id);
            if (!$configTime) {
                return response()->json(['error' => 1, 'msg' => sc_language_render('admin.data_not_found_detail', ['msg' => 'detail#' . $id]), 'detail' => '']);
            }
            $configTime->value = $time;
            $configTime->save();
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'error' => 1,
                'msg' => sc_language_render('action.update_fail')
            ]);
        }
        DB::commit();
        return response()->json([
            'error' => 0,
            'msg' => sc_language_render('action.update_success')
        ]);
    }

}
