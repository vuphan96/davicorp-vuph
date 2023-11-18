<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class SchoolmasterApiLoginRequest extends FormRequest
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
            'schoolmaster_code' => 'required|max:120|regex:/(^([0-9A-Za-z\-_]+)$)/',
            'schoolmaster_password' => 'required|max:120'
        ];
    }
}
