<?php

namespace App\Http\Requests\Admin;

use App\Admin\Models\AdminDavicookCustomer;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;

class AdminDavicookCustomerRequest extends FormRequest
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
        // Common case
        $rules = [
            'name' => 'required',
            'phone' => 'nullable|string',
            'zone_id' => 'required|numeric',
            'address' => 'nullable|string|max:255',
            'tax_code' => 'nullable|string',
            'serving_price' => 'nullable|numeric|min:0',
            'status' => 'nullable',
            'short_name' => 'required',
            'route' => 'nullable',
            'order_num' => 'nullable',
            'email' => 'nullable|email',
            'serving_price' => 'required|numeric',
        ];
        if (empty(request('id'))) {
            $rules['customer_code'] = 'required|max:120|regex:/(^([0-9A-Za-z\-_]+)$)/|string|unique:"' . AdminDavicookCustomer::class . '",customer_code';
            return $rules;
        }
        // Solve edit data
        $rules['customer_code'] = 'required|max:120|regex:/(^([0-9A-Za-z\-_]+)$)/|string|unique:"' . AdminDavicookCustomer::class . '",customer_code,' . request('id');
        return $rules;
    }

    public function messages()
    {
        return [
            'name.required' => sc_language_render('validation.required', ['attribute' => 'customer.name']),
            'email.required' => sc_language_render('validation.required', ['attribute' => sc_language_render('customer.email')]),
            'email.email' => sc_language_render('validation.email', ['attribute' => sc_language_render('customer.email')]),
            'zone_id.required' => sc_language_render('validation.required', ['attribute' => sc_language_render('customer.zone')]),
            'zone_id.numeric' => sc_language_render('validation.numeric', ['attribute' => sc_language_render('customer.zone')]),
            'phone.regex' => sc_language_render('customer.phone_regex'),
            'serving_price.numeric' => sc_language_render('validation.numeric'),
            'address.string' => sc_language_render('validation.string', ['attribute' => sc_language_render('customer.address')]),
            'address.max' => sc_language_render('validation.max', ['attribute' => sc_language_render('customer.address')]),
            'customer_code.required' => sc_language_render('validation.required', ['attribute' => sc_language_render('customer.code')]),
            'customer_code.string' => sc_language_render('validation.string', ['attribute' => sc_language_render('customer.code')]),
            'customer_code.max' => sc_language_render('validation.max', ['attribute' => sc_language_render('customer.code')]),
            'customer_code.unique' => sc_language_render('validation.unique', ['attribute' => sc_language_render('customer.code')]),
            'serving_price.required' => 'Giá suất ăn là bắt buộc nhập'

        ];
    }
}
