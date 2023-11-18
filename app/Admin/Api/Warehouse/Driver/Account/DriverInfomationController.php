<?php
namespace App\Admin\Api\Warehouse\Driver\Account;

use App\Admin\Api\ApiController;
use App\Admin\Models\AdminDriverCustomer;
use App\Front\Models\ShopCustomer;
use App\Front\Models\ShopDavicookCustomer;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class DriverInfomationController extends ApiController {

    public function getInfo(){
        $user = Auth::guard('driver')->user();
        $idCustomerDavicorp1 = $this->getIdCustomer($user->id, 1, 1) ?? [];
        $idCustomerDavicook1 = $this->getIdCustomer($user->id, 1, 2) ?? [];
        $idCustomerDavicorp2 = $this->getIdCustomer($user->id, 2, 1) ?? [];
        $idCustomerDavicook2 = $this->getIdCustomer($user->id, 2, 2) ?? [];


        $CustomerDavicorp1 = ShopCustomer::whereIn('id', $idCustomerDavicorp1)->get()->pluck('name', 'id')->toArray();
        $CustomerDavicook1 = ShopDavicookCustomer::whereIn('id', $idCustomerDavicook1)->get()->pluck('name', 'id')->toArray();
        $CustomerDavicorp2 = ShopCustomer::whereIn('id', $idCustomerDavicorp2)->get()->pluck('name', 'id')->toArray();
        $CustomerDavicook2 = ShopDavicookCustomer::whereIn('id', $idCustomerDavicook2)->get()->pluck('name', 'id')->toArray();

        $dataCustomer1 = array_merge($CustomerDavicorp1, $CustomerDavicook1);
        $dataCustomer2 = array_merge($CustomerDavicorp2, $CustomerDavicook2);

        $data = [
            'customer1' => $dataCustomer1,
            'customer2' => $dataCustomer2,
            'driver' => $user,
        ];

        return $this->responseSuccess($data);
    }

    public function getIdCustomer($user_id, $type_order, $type_customer){
        $id = AdminDriverCustomer::where('staff_id',$user_id)->where('type_order',$type_order)->where('type_customer', $type_customer)->get()->pluck('customer_id');
        return $id;
    }

    public function postUpdatePassword()
    {
        $user = Auth::guard('driver')->user();
        if(!$user){
            return $this->responseError([], Response::HTTP_BAD_REQUEST, 'Lỗi mạng. Vui lòng thử lại!');
        }
        $currentPassword = request('current_password');
        if (!Hash::check($currentPassword, $user->password)) {
            return $this->responseError([], Response::HTTP_BAD_REQUEST, 'Mật khẩu hiện tại chưa đúng');
        }
        $newPassword = request('new_password');

        $validator = Validator::make(['newPassword' => $newPassword], [
            'newPassword' => 'required|string'
        ]);
        if ($validator->fails()) {
            return $this->responseError([], Response::HTTP_BAD_REQUEST, 'Mật khẩu mới không hợp lệ');
        }
        $user->password = Hash::make($newPassword);
        $user->save();
        return $this->responseSuccess();
    }


}
