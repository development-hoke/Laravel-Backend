<?php

namespace App\Http\Requests\Api\V1\Front\Cart;

use App\Http\Requests\Api\V1\Front\BaseRequest;
use App\Http\Requests\Api\V1\Front\Cart\Traits\CartMemberAuthorize;
use Illuminate\Validation\Rule;

class CouponRequest extends BaseRequest
{
    use CartMemberAuthorize;

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'cart_id' => 'required',
            'use_coupon_ids' => 'array',
            'payment_type' => ['nullable', Rule::in(\App\Enums\Order\PaymentType::getValues())],
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
            'cart_id' => __('validation.attributes.cart.id'),
            'use_coupon_ids' => __('validation.attributes.cart.use_coupon_ids'),
            'payment_type' => __('validation.attributes.purchase.payment_type'),
        ];
    }

    /**
     * @return array
     */
    public function messages()
    {
        return [
            'cart_id.*' => __('validation.cart.not_found'),
        ];
    }
}
