<?php

namespace App\Http\Requests\Admin;

use App\Admin\Models\AdminCategory;
use App\Rules\CategoryNameUnique;
use Illuminate\Foundation\Http\FormRequest;
use SCart\Core\Front\Models\ShopCategoryDescription;
use Symfony\Component\HttpFoundation\Request;
use App\Admin\Models\AdminCategoryDescription;

class AdminCategoryRequest extends FormRequest
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
    public function rules(Request $request)
    {
        if($request->id){
            $id = $request->id;
            return [
                'title_category' => 'required|string|max:200',
                'sku' => 'required|regex:/(^([0-9A-Za-z\-_]+)$)/|unique:"' . AdminCategory::class . '",sku,' . $id . ',id',
                'image' => 'nullable',
                'status' => 'nullable',
                'sort' => 'numeric'
                ];
        }else{
            return [
                'title_category' => 'required|string|max:200',
                'sku' => 'required|regex:/(^([0-9A-Za-z\-_]+)$)/|unique:"' . AdminCategory::class . '",sku',
                'image' => 'nullable',
                'status' => 'nullable',
                'sort' => 'numeric'
            ];
        }   
    }
    public function messages()
    {
        return [
            'title_category.required' => sc_language_render('validation.required', ['attribute' => sc_language_render('admin.category.title')]),
            'title_category.max' => sc_language_render('description.category.max'),
            'sku.required' => sc_language_render('category.sku.required_validate'),
            'sku.regex' => sc_language_render('product.sku_validate'),
            'sku.unique' => sc_language_render('category.sku_unique'),
        ];
    }
}
