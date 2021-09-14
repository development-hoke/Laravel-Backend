<?php

namespace App\Http\Requests\Api\V1\Admin\Order;

use App\Http\Requests\Api\V1\Request;
use App\Models\Order;

class UpdatePriceRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $order = Order::findOrFail($this->id);

        return [
            'price_diff' => sprintf('required|integer|min:%s', -$order->price),
            'log_memo' => 'required|max:10000',
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
            'price_diff' => __('validation.attributes.order.price_diff'),
            'log_memo' => __('validation.attributes.order_log.log_memo'),
        ];
    }
}
