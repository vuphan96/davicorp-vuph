<?php
namespace App\Admin\Api\Warehouse\Supplier\Account;

use App\Admin\Api\ApiController;
use App\Front\Models\ShopSupplier;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;

class ProfileSupllierController extends ApiController
{
    public function getAccountSupplier(Request $request){
        $user = $request->user();
        return $this->responseSuccess($user);
    }
    public function changePassWord(Request $request, $id){
        $user = $request->user();
        if(!$user || $user->id != $id){
            return $this->responseError([], Response::HTTP_BAD_REQUEST, 'Lỗi truy cập');
        }
        $currentPassword = $request->input('current_password');
        if (!Hash::check($currentPassword, $user->password)) {
            return $this->responseError([], Response::HTTP_BAD_REQUEST, 'Mật khẩu hiện tại không đúng');
        }
        $newPassword = $request->input('new_password');
        $user->password = Hash::make($newPassword);
        $user->save();
        return $this->responseSuccess([], Response::HTTP_OK, 'Đổi mật khẩu thành công');
    }
}