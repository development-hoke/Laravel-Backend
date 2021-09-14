<?php

namespace App\Http\Requests\Api\V1\Admin\HelpCategory;

use App\Http\Requests\Api\V1\Request;
use Illuminate\Validation\Rule;

class UpdateRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'parent_id' => [
                'nullable',
                'integer',
                Rule::exists('help_categories', 'id')->where(function ($query) {
                    return $query->where('level', '<', \App\Enums\HelpCategory\EndValue::HighestLevel);
                }),
            ],
            'name' => 'max:255',
            'sort' => 'integer',
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
            'parent_id' => __('validation.attributes.help_category.parent_id'),
            'name' => __('validation.attributes.help_category.name'),
            'sort' => __('validation.attributes.help_category.sort'),
        ];
    }
}
