<?php

namespace App\Http\Requests\Api\V1\Admin\OnlineCategory;

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
                Rule::exists('online_categories', 'id')->where(function ($query) {
                    return $query->where('level', '<', \App\Enums\OnlineCategory\EndValue::HighestLevel);
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
            'parent_id' => __('validation.attributes.online_category.parent_id'),
            'name' => __('validation.attributes.online_category.name'),
            'sort' => __('validation.attributes.online_category.sort'),
        ];
    }
}
