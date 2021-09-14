<?php

namespace App\Http\Requests\Api\V1\Admin\Brand;

use App\Http\Requests\Api\V1\Request;
use Illuminate\Validation\Rule;

class CreateRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'store_brand' => ['required', Rule::in(\App\Enums\Common\StoreBrand::getValues())],
            'section' => ['required', Rule::in(\App\Enums\Brand\Section::getValues())],
            'name' => 'required|max:255',
            'kana' => 'required|max:255',
            'category' => ['nullable', Rule::in(\App\Enums\Brand\Category::getValues())],
            'sort' => 'required|integer',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            'store_brand' => __('validation.attributes.brand.store_brand'),
            'section' => __('validation.attributes.brand.section'),
            'name' => __('validation.attributes.brand.name'),
            'kana' => __('validation.attributes.brand.kana'),
            'category' => __('validation.attributes.brand.category'),
            'sort' => __('validation.attributes.brand.sort'),
        ];
    }
}
