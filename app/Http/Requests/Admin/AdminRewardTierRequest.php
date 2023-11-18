<?php

namespace App\Http\Requests\Admin;

use App\Front\Models\ShopRewardTier;
use App\Front\Models\ShopZone;
use Illuminate\Foundation\Http\FormRequest;

class AdminRewardTierRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $rules = [
            'name' => 'string|max:120|unique:' . ShopRewardTier::class .  ',name'. (request('id') ? (',' . request('id')) : '') . '|required',
            'rate' => 'numeric|required|min:0'
        ];
        return $rules;
    }

    public function messages()
    {
        return [
            'name.string' => sc_language_render('validation.string', ['attribute' => sc_language_render('reward.tier.name')]),
            'name.max' => sc_language_render('validation.max', ['attribute' => sc_language_render('reward.tier.name')]),
            'name.required' => sc_language_render('validation.required', ['attribute' => sc_language_render('reward.tier.name')]),
            'name.unique' => sc_language_render('validation.unique', ['attribute' => sc_language_render('reward.tier.name')]),
            'rate.required' => sc_language_render('validation.required', ['attribute' => sc_language_render('reward.tier.rate')]),
            'rate.numeric' => sc_language_render('validation.numeric', ['attribute' => sc_language_render('reward.tier.rate')]),
            'rate.min' => sc_language_render('validation.min', ['attribute' => sc_language_render('reward.tier.rate')]),
        ];
    }
}

