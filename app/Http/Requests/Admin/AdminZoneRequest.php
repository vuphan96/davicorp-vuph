<?php

namespace App\Http\Requests\Admin;

use App\Front\Models\ShopZone;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;

class AdminZoneRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $rules['zone_code'] = empty(request('id')) ? 'string|required|max:120|unique:"' . ShopZone::class .'",zone_code' :
            'string|required|max:120|unique:"' . ShopZone::class .'",zone_code,' . request('id');
        $rules['name'] = empty(request('id')) ? 'string|required|max:250|unique:"' . ShopZone::class .'",name' :
            'string|required|max:250|unique:"' . ShopZone::class .'",name,' . request('id');
        return $rules;
    }
    public function messages()
    {
        return [
            'name.required' => sc_language_render('validation.required', ['attribute' => sc_language_render('admin.zone.name')]),
            'name.max' => sc_language_render('validation.max', ['attribute' => sc_language_render('admin.zone.name')]),
            'name.unique' => sc_language_render('validation.unique', ['attribute' => sc_language_render('admin.zone.name')]),
            'zone_code.required' => sc_language_render('validation.required', ['attribute' => sc_language_render('admin.zone.code')]),
            'zone_code.max' => sc_language_render('validation.max', ['attribute' => sc_language_render('admin.zone.code')]),
            'zone_code.unique' => sc_language_render('validation.unique', ['attribute' => sc_language_render('admin.zone.code')]),
        ];
    }
}
