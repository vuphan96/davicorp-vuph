<?php

namespace App\Http\Requests\Api;

use App\Admin\Models\AdminCustomer;
use Illuminate\Foundation\Http\FormRequest;

class ApiLoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'customer_code' => 'required',
            'password' => 'required'
        ];
    }
    public function messages()
    {
        return [
            'customer_code.required'=> 'Vui lòng nhập tên đăng nhập!',
            'password.required' => 'Vui lòng nhập mật khẩu!'
        ];
    }
}
