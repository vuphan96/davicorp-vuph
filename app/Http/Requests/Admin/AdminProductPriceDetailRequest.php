<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use App\Admin\Models\AdminProductPriceDetail;
use Symfony\Component\HttpFoundation\Request;
class AdminProductPriceDetailRequest extends FormRequest
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
            'price1' => 'required|numeric|regex:/(^[0-9]+)/|not_in:none',
            'price2' => 'required|numeric|regex:/(^[0-9]+)/|not_in:none',
            'idProduct' => 'nullable',
            'name' => 'nullable'
        ];
    }
    public function messages()
    {
        return [
            'price1.required' => sc_language_render('product.price.teacher'),
            'price1.numeric' => 'Không đúng định dạng',
            'price1.regex'  =>sc_language_render('product.price.no.negative'),
            'price2.required' => sc_language_render('product.price.child'),
            'price2.numeric' => 'Không đúng định dạng',
            'price2.regex'  =>sc_language_render('product.price.no.negative')
        ];
    }
}
