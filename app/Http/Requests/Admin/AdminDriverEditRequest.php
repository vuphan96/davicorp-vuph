<?php

namespace App\Http\Requests\Admin;

use App\Admin\Models\AdminDriver;
use App\Front\Models\ShopSupplier;
use Illuminate\Foundation\Http\FormRequest;

class AdminDriverEditRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $id = request('id') ?? null;

        return [
            'full_name' => 'nullable|string|max:255',
            'login_name' => 'required|string|unique:"' . AdminDriver::class .'",login_name' . ($id ? ',' . $id : '') . '',
            'password' => 'nullable|min:6|max:50|same:password',
            'phone' => 'required|regex:/^0[^0][0-9\-]{6,12}$/|unique:"' . AdminDriver::class .'",phone' . ($id ? ',' . $id : '') . '',
            'email' => 'nullable|email|max:100',
            'address' => 'nullable|string|max:255',
            'status' => 'nullable',
            'customer_list1' => 'nullable|array',
            'customer_list2' => 'nullable|array',
        ];
    }
    public function messages()
    {
        return [
            'full_name.required' => 'Tên tài xế không được trống',
            'login_name.required' => ' Tên đăng nhập không được để trống',
            'password.required' => ' Mật khẩu không được để trống',
            'full_name.max' => 'Độ dài tên không hợp lệ!',
            'phone.regex' => 'Sai định dạng số điện thoại',
            'phone.unique' => 'Số điện thoại tài xế đã tồn tại',
            'email.email' => 'Email sai định dạng'
        ];
    }
}
