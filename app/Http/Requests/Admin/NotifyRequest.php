<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class NotifyRequest extends FormRequest
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
            'region'=> 'nullable',
            'customer'=>'nullable',
            'title'=>'required',
            'editor'=>'required',
            'link'=>'nullable'
        ];
    }
    public function messages()
    {
        return [
            'title.required'=>'Tiêu đề là bắt buộc nhập',
            'editor.required'=>'Nội dung là bắt buộc nhập ',
        ];
    }
}
