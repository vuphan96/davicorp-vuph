<?php

namespace App\Http\Requests\Admin;

use App\Front\Models\ShopSupplier;
use Illuminate\Foundation\Http\FormRequest;

class AdminSupplierRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $id = request('id') ?? null;

        return [
            'name' => 'required|string|max:255',
            'name_login' => 'required|string|unique:"' . ShopSupplier::class .'",name_login' . ($id ? ',' . $id : '') . '',
            'password' => 'required|min:6|max:50|same:password',
            'type_form_report' => 'nullable',
            'phone' => 'nullable|regex:/^0[^0][0-9\-]{6,12}$/|unique:"' . ShopSupplier::class .'",phone' . ($id ? ',' . $id : '') . '',
            'email' => 'nullable|email|max:100',
            'address' => 'nullable|string|max:255',
            'sort' => 'numeric|min:0',
            'status' => 'nullable',
            'supplier_code' => 'required|regex:/(^([0-9A-Za-z\-_]+)$)/|unique:"' . ShopSupplier::class .'",supplier_code' . ($id ? ',' . $id : '') . '',
            'category_data' => 'string|nullable',
            'customer_data' => 'string|nullable'
        ];
    }
    public function messages()
    {
        return [
            'name.required' => sc_language_render('supplier.name.required'),
            'name_login.required' => ' Tên đăng nhập không được để trống',
            'password.required' => ' Mật khẩu không được để trống',
            'name.max' => sc_language_render('supplier.name.max'),
            'supplier_code.required' => sc_language_render('supplier.code.required'),
            'supplier_code.regex' => sc_language_render('product.sku_validate'),
            'supplier_code.unique' => sc_language_render('supplier.code.unique'),
            'phone.regex' => sc_language_render('supplier.phone.regex'),
            'phone.unique' => sc_language_render('supplier.phone.unique'),
            'email.email' => sc_language_render('supplier.email.email')
        ];
    }
}
