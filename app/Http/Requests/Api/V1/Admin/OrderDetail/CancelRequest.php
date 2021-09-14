<?php

namespace App\Http\Requests\Api\V1\Admin\OrderDetail;

use App\Http\Requests\Api\V1\Request;
use Illuminate\Validation\Rule;

class CancelRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $orderId = (int) $this->route('order_id');
        $order = \App\Models\Order::with('orderDetails')->findOrFail($orderId);

        return [
            'ids' => ['array', 'required', sprintf('max:%s', $order->getCountedOrderDetails()->count() - 1)],
            'ids.*' => [
                'required',
                'integer',
                Rule::exists('order_details', 'id')->where(function ($query) use ($orderId) {
                    return $query->where('order_id', '=', $orderId);
                }),
            ],
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
            'ids' => __('validation.attributes.order_detail.id'),
            'ids.*' => __('validation.attributes.order_detail.id'),
        ];
    }

    /**
     * @return array
     */
    public function messages()
    {
        return [
            'ids.max' => __('validation.order_detail.ids.min'),
        ];
    }
}
