<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class AdminRewardPrincipleRequest extends FormRequest
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
            'rule.*.from' => 'required',
            'rule.*.to' => 'required',
            'rule.*.point' => 'numeric|required|min:0',
            'rule.*.action' => 'nullable|string|max:10'
        ];
    }
}
