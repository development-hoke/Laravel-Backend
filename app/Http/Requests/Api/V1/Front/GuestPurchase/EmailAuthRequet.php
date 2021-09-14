<?php

namespace App\Http\Requests\Api\V1\Front\GuestPurchase;

use App\Http\Requests\Api\V1\Front\BaseRequest;

class EmailAuthRequet extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'token' => 'required|string|max:64',
            'email' => 'required|string|max:255|email',
            'email_confirmation' => 'required|same:email',
        ];
    }

    /**
     * @return array
     */
    public function attributes()
    {
        return [
            'token' => __('validation.attributes.cart.token'),
            'email' => __('validation.attributes.guest_purchase.email'),
            'email_confirmation' => __('validation.attributes.guest_purchase.email_confirmation'),
        ];
    }
}
