<?php

namespace App\Http\Requests\Api\V1\Admin\SalesType;

use App\Http\Requests\Api\V1\Request;

class CreateRequest extends Request
{
    public function rules()
    {
        return [
            'name' => 'string|max:255|required',
            'text_color' => 'string|required|regex:/(#([a-zA-Z\d]){6}$)/u',
        ];
    }

    public function attributes()
    {
        return [
            'name' => __('validation.attributes.sales_type.name'),
            'text_color' => __('validation.attributes.sales_type.text_color'),
        ];
    }
}
