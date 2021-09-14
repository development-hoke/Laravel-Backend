<?php

namespace App\Http\Requests\Api\V1\Admin\DeliverySetting;

use App\Http\Requests\Api\V1\Request;

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
            'delivery_condition' => 'required|integer|min:0',
            'delivery_price' => 'required|integer|min:0',
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
            'delivery_condition' => __('validation.attributes.delivery_setting.delivery_condition'),
            'delivery_price' => __('validation.attributes.delivery_setting.delivery_price'),
        ];
    }
}
