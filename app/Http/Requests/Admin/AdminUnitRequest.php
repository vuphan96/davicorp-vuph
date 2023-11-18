<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use App\Admin\Models\AdminUnit;

class AdminUnitRequest extends FormRequest

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
            'description' => 'nullable',
            'type' => 'nullable',
        ];
    }
    public function messages()
    {
        return [
            'name.required' => sc_language_render('admin.unit.name_required')
        ];
    }
}
