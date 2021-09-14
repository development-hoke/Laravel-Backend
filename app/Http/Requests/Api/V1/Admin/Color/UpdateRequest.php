<?php

namespace App\Http\Requests\Api\V1\Admin\Color;

use App\Http\Requests\Api\V1\Request;

class UpdateRequest extends Request
{
    public function rules()
    {
        return [
            'name' => 'string|max:255|required',
            'color_panel' => 'string|max:7|required',
            'display_name' => 'string|max:255|required',
        ];
    }

    public function attributes()
    {
        return [
            'name' => __('validation.attributes.name.id'),
            'color_panel' => __('validation.attributes.color_panel.id'),
            'display_name' => __('validation.attributes.display_name.id'),
        ];
    }
}
