<?php

namespace App\Http\Requests\Api\V1\Admin\Help;

use App\Http\Requests\Api\V1\Request;

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
            'title' => 'required|max:255',
            'body' => 'required|max:10000',
            'sort' => 'required|integer',
            'is_faq' => 'required|boolean',
            'help_categories' => 'required|array',
            'help_categories.*' => 'required|integer|distinct',
            'status' => 'required|boolean',
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
            'title' => __('validation.attributes.help.title'),
            'body' => __('validation.attributes.help.body'),
            'sort' => __('validation.attributes.help.sort'),
            'is_faq' => __('validation.attributes.help.is_faq'),
            'help_categories' => __('validation.attributes.help.helpCategories'),
            'status' => __('validation.attributes.help.status'),
        ];
    }
}
