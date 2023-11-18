<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class AdminOrderEditRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }
    public function rules()
    {
        if(!request()->ajax()){
            return [
                'customer_id' => 'string|required',
                'name' => 'string|required',
                'address' => 'string|required',
                'phone' => 'string|required',
                'email' => 'email|nullable',
                'object_id' => 'numeric|required',
            ];
        }
        return [];
    }
}
