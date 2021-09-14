<?php

namespace App\Http\Requests\Api\V1\Admin\HelpCategory;

use App\Http\Requests\Api\V1\Request;
use Illuminate\Validation\Rule;

class StoreRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'id' => 'integer|unique:help_categories,id',
            'parent_id' => [
                'nullable',
                'integer',
                Rule::exists('help_categories', 'id')->where(function ($query) {
                    return $query->where('level', '<', \App\Enums\HelpCategory\EndValue::HighestLevel);
                }),
            ],
            'name' => 'required|max:255',
            'sort' => 'nullable|integer',
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
            'id' => __('validation.attributes.help_category.id'),
            'parent_id' => __('validation.attributes.help_category.parent_id'),
            'name' => __('validation.attributes.help_category.name'),
            'sort' => __('validation.attributes.help_category.sort'),
        ];
    }
}
