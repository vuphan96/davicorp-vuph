<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class AdminProductSupplierDavicookRequest extends FormRequest
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
//        dd("a");
        return [
            'product_id' => 'required',
            'supplier_id' => 'required',
        ];
    }
    public function messages()
    {
        return [
            'product_id.required' => 'Vui lòng chọn một sản phẩm trong danh sách',
            'supplier_id.required' => 'Vui lòng chọn nhà cung cấp trong danh sách',
        ];
    }
}
