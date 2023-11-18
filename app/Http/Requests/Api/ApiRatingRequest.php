<?php

namespace App\Http\Requests\Api;

use App\Admin\Models\AdminCustomer;
use Illuminate\Foundation\Http\FormRequest;

class ApiRatingRequest extends FormRequest
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
            'month' => 'required|numeric|min:1|max:12',
            'year' => 'required|numeric',
            'content' => 'nullable|string|max:1500',
            'point' => 'numeric|min:0:max:5'
        ];
    }
    public function messages()
    {
        return [
            "point.max" => "Điểm tối đa là 5",
            "point.min" => "Điểm tối thiểu là 1",
            "month.max" => "Tháng tối đa là 12",
            "month.min" => "Thánh tối thiểu là 1",
            'content.max' => 'Đánh giá tối đa là 1500 kí tự',
        ];
    }
}
