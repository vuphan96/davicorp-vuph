<?php
namespace App\Admin\Api\Warehouse\Driver;

use App\Admin\Api\ApiController;
use App\Admin\Models\AdminDavicookOrder;
use App\Admin\Models\AdminDriver;
use App\Admin\Models\AdminDriverCustomer;
use App\Admin\Models\AdminNotification;
use App\Admin\Models\AdminNotificationCustomer;
use App\Admin\Models\AdminOrder;
use App\Front\Models\ShopCustomer;
use App\Front\Models\ShopDavicookCustomer;
use App\Front\Models\ShopDavicookOrder;
use App\Front\Models\ShopDavicookOrderDetail;
use App\Front\Models\ShopOrder;
use App\Front\Models\ShopOrderDetail;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ApiDriverController extends ApiController
{
    public function getListDeliveryOrder(){
        $user = Auth::guard('driver')->user();
        $id_user = $user->id;
        $pageSize = request("page_size");
        if (!isset($pageSize)){
            $pageSize = 10;
        }

        $dataFilter = request(['keyword', 'start_day', 'end_day']);
        $dataDeliveryOrderDavicorp = (new AdminDriver())->getDeliveryOrderDavicorp($id_user, $dataFilter);
        $dataDeliveryOrderDavicook = (new AdminDriver())->getDeliveryOrderDavicook($id_user, $dataFilter);
        $dataDeliveryOrderMerge = $dataDeliveryOrderDavicorp->mergeRecursive($dataDeliveryOrderDavicook);
        $dataTmp = $this->paginate($dataDeliveryOrderMerge, $perPage = $pageSize)->toArray();

        return $this->responseSuccess($dataTmp);
    }

    public function getDetailDeliveryOrder(){
        $user = Auth::guard('driver')->user();
        $type_customer = request('type_customer');
        $order_id = request('order_id');

        if($type_customer == 1){
            $dataOrder = (new AdminOrder())->with('details')->find($order_id)->toArray();
        } else {
            $dataOrder = (new AdminDavicookOrder())->with('details')->find($order_id)->toArray();
        }

        return $this->responseSuccess($dataOrder);
    }

    public function updateStatusDelivery(){
        $type_customer = request('type_customer');
        $order_id = request('order_id');
        $status = request('status');
        DB::beginTransaction();
        try {
            if($type_customer == 1){
                $order = (new AdminOrder())->find($order_id);
                $customer_name = $order->name;
                $order_type = 1;

            } else {
                $order = (new AdminDavicookOrder())->find($order_id);
                $customer_name = $order->customer_name;
                $order_type = 1;
            }
            if ($order){

                if ($status == 3) {
                    $notification = new AdminNotification();
                    $notification->title = "Tài xế giao hàng";
                    $notification->content = "Đơn hàng #$order->id_name đã được giao thành công ";
                    $notification->id_order = $order->id;
                    $notification->order_code = $order->id_name;
                    $notification->customer_code = $order->customer_code;
                    $notification->customer_name = $customer_name ?? '';
                    $notification->type_user = 1;
                    $notification->save();


                    $notification_amdin = new AdminNotification();
                    $notification_amdin->title = "Tài xế giao hàng";
                    $notification_amdin->content = "Đơn hàng #$order->id_name đã được giao thành công";
                    $notification_amdin->id_order = $order->id;
                    $notification_amdin->link = '/sc_admin/order';
                    $notification_amdin->is_admin = 1;
                    $notification_amdin->type_user = 1;
                    $notification_amdin->save();
                    AdminNotificationCustomer::sendNotifyToAdmin($notification->content, $notification->title);
                }
                $order->delivery_status = $status;
                $order->save();
            }
            DB::commit();
            return $this->responseSuccess();
        } catch (\Throwable $e) {
            Log::error($e);
            return $this->responseError([], Response::HTTP_BAD_REQUEST, 'Lỗi mạng. Vui lòng thử lại!');
        }

    }


    public function paginate($items, $perPage = 20, $page = null, $options = ['path' => ''])
    {
        $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);
        $items = $items instanceof Collection ? $items : Collection::make($items);
        return new LengthAwarePaginator($items->forPage($page, $perPage), $items->count(), $perPage, $page, $options);

    }

}