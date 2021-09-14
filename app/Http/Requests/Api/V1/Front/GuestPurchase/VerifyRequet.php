<?php

namespace App\Http\Requests\Api\V1\Front\GuestPurchase;

use App\Http\Requests\Api\V1\Front\BaseRequest;

class VerifyRequet extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'member_id' => 'required|integer',
            'cart_token' => 'required|string|max:64',
            'guest_token' => 'required|string|max:32',
        ];
    }

    /**
     * @return array
     */
    public function attributes()
    {
        return [
            'member_id' => __('validation.attributes.cart.member_id'),
            'cart_token' => __('validation.attributes.cart.token'),
            'guest_token' => __('validation.attributes.guest_purchase.guest_token'),
        ];
    }
}
