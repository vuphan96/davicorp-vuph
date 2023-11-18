<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use App\Admin\Models\AdminUserPriceboard;

class UserPriceboardRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }
    protected function prepareForValidation()
    {
        $this->merge([
            "start_date" => convertDate(request("start_date"), HUMAN_TO_MACHINE),
            "due_date" => convertDate(request("due_date"), HUMAN_TO_MACHINE)
        ]);
    }
    public function rules()
    {
        $rules = [
            'name' => 'required|string|max:255',
            'product_price_id' => 'required|string',
            'priceboard_code' => 'required|string|unique:"' . AdminUserPriceboard::class.'",priceboard_code',
            'start_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:start_date',
            'customer_data' => 'nullable|string'
        ];
        if(!empty(request('id'))){
            $rules['priceboard_code'] = 'required|string|unique:"' . AdminUserPriceboard::class.'",priceboard_code,' . request('id');
        }
        return $rules;
    }
    public function messages()
    {
        return [
            'due_date.after_or_equal' => sc_language_render('priceboard.validate.after_or_equal_date'),
            'priceboard_code.unique' => sc_language_render('priceboard.validate.code_not_uniquee'),
        ];
    }
}
