<?php

namespace App\Http\Requests\Api\V1\Admin\Plan;

use App\Http\Requests\Api\V1\Request;

class UpdateItemSettingRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'is_item_setting' => 'required|boolean',
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
            'is_item_setting' => __('validation.attributes.plan.is_item_setting'),
        ];
    }
}
