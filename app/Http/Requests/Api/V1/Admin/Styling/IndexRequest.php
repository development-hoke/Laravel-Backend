<?php

namespace App\Http\Requests\Api\V1\Admin\Styling;

use App\Http\Requests\Api\V1\Request;

class IndexRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'product_number' => 'nullable|string|max:255',
            'page' => 'integer',
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
            'product_number' => __('validation.attributes.item.product_number'),
            'page' => __('validation.attributes.page'),
        ];
    }
}
