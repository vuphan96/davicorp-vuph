<?php

namespace App\Http\Requests\Admin;

use App\Front\Models\ShopDish;
use Illuminate\Foundation\Http\FormRequest;

class AdminDishRequest extends FormRequest
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
        $id = request('id') ?? null;
        return [
            'name' => 'required|string|max:255',
            'code' => 'required|regex:/(^([0-9A-Za-z\-_]+)$)/|unique:"' . ShopDish::class .'",code' . ($id ? ',' . $id : '') . '',
            'status' => 'nullable',
        ];
    }
    public function messages()
    {
        return [
            'name.required' => sc_language_render('dish.name.required'),
            'name.max' => sc_language_render('dish.name.max'),
            'code.required' => sc_language_render('dish.code.required'),
            'code.regex' => sc_language_render('product.sku_validate'),
            'code.unique' => sc_language_render('dish.code.unique'),
        ];
    }
}
