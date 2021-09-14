<?php

namespace App\Http\Requests\Api\V1\Admin\Order;

use App\Http\Requests\Api\V1\Request;

class RemoveCouponRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'order_used_coupon_id' => 'required|integer',
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
            'order_used_coupon_id' => __('validation.attributes.order_used_coupon.id'),
        ];
    }
}
