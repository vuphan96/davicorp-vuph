<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use App\Admin\Models\AdminUnit;
use Symfony\Component\HttpFoundation\Request;
use Validator;
class AdminWarehouseEditRequest extends FormRequest
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
    public function rules(Request $request)
    {
        return [
            'warehouse_code' => 'required',
            'name' => 'required',
            'address' => 'nullable',
        ];
    }
    public function messages()
    {
        return [
            'warehouse_code.required'=>'Mã kho hàng không được trống!',
            'name.required' => 'Tên kho hàng không được trống!'
        ];
    }
}
