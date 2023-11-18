<?php

namespace App\Http\Requests\Admin;

use App\Front\Models\ShopProduct;
use Illuminate\Foundation\Http\FormRequest;

class AdminProductRequest extends FormRequest
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
        $rules = [
            'unit_id' => 'string|nullable',
            'descriptions.*.name' => 'required|string|max:100',
            'descriptions.*.short_name' => 'nullable',
            'descriptions.*.bill_name' => 'nullable',
            'category_id' => 'nullable|required',
            'order_num' => 'nullable|numeric',
            'sku' => 'required|regex:/(^([0-9A-Za-z\-_]+)$)/|unique:"' . ShopProduct::class . '",sku',
            'status' => 'nullable',
            'priority' => 'nullable',
            'kind' => 'nullable',
            'minimum_qty_norm' => 'nullable|numeric',
            'default' => 'nullable',
            'qr_code' => 'nullable|string',
            'school' => 'nullable',
            'company' => 'nullable',
            'warehouse_id' => 'nullable|array',
            'qty_warehouse' => 'nullable|array',
            'qty_limit' => 'nullable',
        ];
        if(!empty(request('id'))){
            $rules['sku'] = 'required|regex:/(^([0-9A-Za-z\-_]+)$)/|unique:"' . ShopProduct::class . '",sku,'. request('id');
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'descriptions.*.name.required' => sc_language_render('validation.required', ['attribute' => sc_language_render('product.name')]),
            'category.required' => sc_language_render('validation.required', ['attribute' => sc_language_render('product.category')]),
            'sku.regex' => sc_language_render('product.sku_validate'),
            'sku.product_sku_unique' => sc_language_render('product.sku_unique')
        ];
    }
}
