<?php

namespace App\Http\Requests\Api\V1\Admin\SalesType;

use App\Http\Requests\Api\V1\Request;

class UpdateRequest extends Request
{
    public function rules()
    {
        return [
            'id' => 'integer|required',
            'name' => 'string|max:255|required',
            'text_color' => 'string|max:7|required',
        ];
    }

    public function attributes()
    {
        return [
            'id' => __('validation.attributes.sales_type_id'),
            'name' => __('validation.attributes.sales_type.name'),
            'text_color' => __('validation.attributes.sales_type.text_color'),
        ];
    }
}
