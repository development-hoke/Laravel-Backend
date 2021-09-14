<?php

namespace App\Http\Requests\Api\V1\Admin\Order;

use App\Http\Requests\Api\V1\Request;

class SendMessageRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'title' => 'string|max:255',
            'body' => 'string|max:100000',
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
            'title' => __('validation.attributes.order_message.title'),
            'body' => __('validation.attributes.order_message.body'),
        ];
    }
}
