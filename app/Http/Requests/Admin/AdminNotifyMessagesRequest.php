<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class AdminNotifyMessagesRequest extends FormRequest
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
            'description' => 'required|string',
            'editor'  => 'required|string',
            'schedule' => ['required','regex:/^([0-9]|0[0-9]|1[0-9]|2[0-3]):[0-5][0-9]$/'],
            'code' => 'nullable'

        ];
    }
    public function messages()
    {
        return [
            'description.required' => 'Tiêu đề không được trống',
            'editor.required' => 'Nội dung không được bỏ trống',
            'schedule.regex' => 'Thời gian không đúng định dạng HH:MM',
            'schedule.required' => 'Thời gian thông báo là bắt buộc',
        ];
    }
}
