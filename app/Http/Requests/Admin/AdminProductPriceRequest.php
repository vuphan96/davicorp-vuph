<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use App\Admin\Models\AdminProductPrice;

class AdminProductPriceRequest extends FormRequest
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
            'name' => 'required',
            'code' => 'required|regex:/(^([0-9A-Za-z\-_]+)$)/|unique:"' . AdminProductPrice::class .'",price_code',
        ];
    }
    public function messages()
    {
        return [
            'name.required' => sc_language_render('product.price.name'),
            'code.regex' => sc_language_render('product.sku_validate'),
            'code.unique' => sc_language_render('price.sku_unique'),
            'code.required' => sc_language_render('price.sku_required'),
            
        ];
    }
}
