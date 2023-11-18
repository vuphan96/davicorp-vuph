<?php

namespace App\Http\Requests\Admin;

use App\Front\Models\ShopImportPriceboard;
use Illuminate\Foundation\Http\FormRequest;
use App\Admin\Models\AdminUserPriceboard;

class AdminImportPriceboardRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }
    protected function prepareForValidation()
    {
        $this->merge([
            "start_date" => convertDate(request("start_date"), HUMAN_TO_MACHINE),
            "end_date" => convertDate(request("end_date"), HUMAN_TO_MACHINE)
        ]);
    }
    public function rules()
    {
        $rules = [
            'name' => 'required|string|max:255',
            'code' => 'required|regex:/^[A-Za-z0-9\-\_]+$/|max:120|unique:"' . ShopImportPriceboard::class.'",code',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'supplier_id' => 'required|string|max:36'
        ];
        if(!empty(request('id'))){
            $rules['code'] = 'required|regex:/^[A-Za-z0-9\-\_]+$/|max:120|unique:"' . ShopImportPriceboard::class.'",code,' . request('id');
        }
        return $rules;
    }
    public function messages()
    {
        return [
            'due_date.after_or_equal' => "Ngày kết thúc không được trước ngày hiệu lực",
            'code.unique' => "Mã bảng báo giá nhập phải là duy nhất",
            'code.regex' => "Mã bảng báo giá chỉ được bao gồm số, tiếng việt có dấu, các kí tự - _",
            'code.max' => "Mã bảng giá tối đa là 120 kí tự",
            'name.required' => "Tên bảng báo giá nhập là bắt buộc",
            'name.string' => "Tên bảng báo phải là dạng chuỗi",
            'name.max' => "Độ dài tên bảng giá tối đa là 255 kí tự",
            'start_date.date' => "Ngày bắt đầu phải là định dạng ngày",
            'start_date.required' => "Ngày bắt đầu là bắt buộc",
            'end_date.date' => "Ngày kết thúc phải là định dạng ngày",
            'end_date.required' => "Ngày kết thúc phải là bắt buộc",
            'supplier_id.required' => "Mã nhà cung cấp là bắt buộc"
        ];
    }
}
