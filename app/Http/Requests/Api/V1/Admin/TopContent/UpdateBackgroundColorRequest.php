<?php

namespace App\Http\Requests\Api\V1\Admin\TopContent;

use App\Http\Requests\Api\V1\Request;

class UpdateBackgroundColorRequest extends Request
{
    public function rules()
    {
        return [
            'background_color' => 'string|max:7|required',
        ];
    }

    public function attributes()
    {
        return [
            'background_color' => __('validation.attributes.top_content.background_color'),
        ];
    }
}
