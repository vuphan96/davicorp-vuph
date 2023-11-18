<?php

namespace App\Http\Requests\Admin;

use App\Front\Models\ShopImportPriceboard;
use Illuminate\Foundation\Http\FormRequest;
use App\Admin\Models\AdminUserPriceboard;

class AdminImportPriceboardDetailRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }
    public function rules()
    {
        $rules = [
            'priceboard_id' => 'required|string|max:36',
            'product_id' => 'required|string|max:36',
            'price' => 'required|numeric|min:0'
        ];
        return $rules;
    }
    public function messages()
    {
        return [
            'price.min' => "Gía tối thiểu là 0",
            'price.numeric' => "Gía phải là số",
            'price.required' => "Gía là trường bắt buộc",
            'product_id.required' => "Sản phẩm là bắt buộc"
        ];
    }
}
