<?php

namespace App\Http\Requests\Admin;

use App\Admin\Models\AdminCustomer;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;

class AdminCustomerRequest extends FormRequest
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
            'phone' => 'nullable|string',
            'department_id' => 'required|numeric',
            'tier_id' => 'required|numeric',
            'zone_id' => 'required|numeric',
            'address' => 'nullable|string|max:255',
            'tax_code' => 'nullable|string',
            'status' => 'nullable',
            'short_name' => 'required',
            'route' => 'nullable',
            'order_num' => 'nullable',
            'teacher_code' => 'nullable|max:120|regex:/(^([0-9A-Za-z\-_]+)$)/|string',
            'student_code' => 'nullable|max:120|regex:/(^([0-9A-Za-z\-_]+)$)/|string',
            'email' => 'nullable|email',
            'kind' => 'nullable',
        ];
        // Solve add data
        if (empty(request('id'))) { // Add
            $rules['name'] = 'required|unique:"' . AdminCustomer::class . '",name';
            $rules['customer_code'] = 'required|max:120|regex:/(^([0-9A-Za-z\-_]+)$)/|string|unique:"' . AdminCustomer::class . '",customer_code';
            $rules['schoolmaster_code'] = 'required|max:120|regex:/(^([0-9A-Za-z\-_]+)$)/|string|unique:"' . AdminCustomer::class . '",schoolmaster_code';
            $rules['password_confirmation'] = 'required|same:password';
            $rules['password'] = 'required|min:6|max:50|same:password';
            $rules['schoolmaster_password'] = 'required|min:6|max:50';
            return $rules;
        }
        // Solve edit data
        $rules['name'] = 'required|unique:"' . AdminCustomer::class . '",name,' . request('id');
        $rules['customer_code'] = 'required|max:120|regex:/(^([0-9A-Za-z\-_]+)$)/|string|unique:"' . AdminCustomer::class . '",customer_code,' . request('id');
        $rules['schoolmaster_code'] = 'required|max:120|regex:/(^([0-9A-Za-z\-_]+)$)/|string|unique:"' . AdminCustomer::class . '",schoolmaster_code,' . request('id');
        if (!empty(request('password'))) { // If create new password only
            $rules['password'] = 'required|min:6|max:50';
        }
        if (!empty(request('schoolmaster_password'))) { // If create new password only
            $rules['schoolmaster_password'] = 'required|min:6|max:50';
        }
        return $rules;
    }

    public function messages()
    {
        return [
            'name.required' => sc_language_render('validation.required', ['attribute' => 'customer.name']),
            'name.unique' => sc_language_render('validation.unique', ['attribute' => sc_language_render('customer.name')]),
            'email.required' => sc_language_render('validation.required', ['attribute' => sc_language_render('customer.email')]),
            'email.unique' => sc_language_render('validation.unique', ['attribute' => sc_language_render('customer.email')]),
            'email.email' => sc_language_render('validation.email', ['attribute' => sc_language_render('customer.email')]),
            'email.max' => sc_language_render('validation.max', ['attribute' => sc_language_render('customer.email')]),
            'password.required' => sc_language_render('validation.required', ['attribute' => sc_language_render('customer.password')]),
            'password.confirmed' => sc_language_render('validation.confirmed', ['attribute' => sc_language_render('customer.password')]),
            'password.min' => sc_language_render('validation.min', ['attribute' => sc_language_render('customer.password')]),
            'password_confirmation.same' => 'Giá trị mật khẩu và xác nhận mật khẩu phải trùng nhau',
            'phone.required' => sc_language_render('validation.required', ['attribute' => sc_language_render('customer.phone')]),
            'zone_id.required' => sc_language_render('validation.required', ['attribute' => sc_language_render('customer.zone')]),
            'zone_id.numeric' => sc_language_render('validation.numeric', ['attribute' => sc_language_render('customer.zone')]),
            'tier_id.required' => sc_language_render('validation.required', ['attribute' => sc_language_render('customer.tier')]),
            'department_id.required' => sc_language_render('validation.required', ['attribute' => "Trường \"Khách hàng thuộc\" là bắt buộc"]),
            'phone.regex' => sc_language_render('customer.phone_regex'),
            'address.required' => sc_language_render('validation.required', ['attribute' => sc_language_render('customer.address')]),
            'address.string' => sc_language_render('validation.string', ['attribute' => sc_language_render('customer.address')]),
            'address.max' => sc_language_render('validation.max', ['attribute' => sc_language_render('customer.address')]),
            'customer_code.required' => sc_language_render('validation.required', ['attribute' => sc_language_render('customer.code')]),
            'customer_code.string' => sc_language_render('validation.string', ['attribute' => sc_language_render('customer.code')]),
            'customer_code.max' => sc_language_render('validation.max', ['attribute' => sc_language_render('customer.code')]),
            'customer_code.unique' => sc_language_render('validation.unique', ['attribute' => sc_language_render('customer.code')]),
            'teacher_code.max' => 'Mã giáo viên không dài quá 120 ký tự',
            'teacher_code.regex' => sc_language_render('product.sku_validate'),
            'student_code.max' => 'Mã học sinh không dài quá 120 ký tự',
            'student_code.regex' => sc_language_render('product.sku_validate'),
        ];
    }
}
