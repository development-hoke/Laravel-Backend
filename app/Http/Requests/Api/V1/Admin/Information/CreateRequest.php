<?php

namespace App\Http\Requests\Api\V1\Admin\Information;

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
            'publish_at' => 'required|date',
            'priority' => 'required|integer|between:1,1000',
            'is_store_top' => 'required|boolean',
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
            'title' => __('validation.attributes.information.title'),
            'body' => __('validation.attributes.information.body'),
            'publish_at' => __('validation.attributes.information.publish_at'),
            'priority' => __('validation.attributes.information.priority'),
            'is_store_top' => __('validation.attributes.information.is_store_top'),
        ];
    }
}
