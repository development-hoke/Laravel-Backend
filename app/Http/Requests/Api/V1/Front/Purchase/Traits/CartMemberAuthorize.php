<?php

namespace App\Http\Requests\Api\V1\Front\Purchase\Traits;

trait CartMemberAuthorize
{
    /**
     * @return bool
     */
    public function authorize()
    {
        $cart = \App\Models\Cart::findOrFail($this->cart_id);

        return auth('api')->check()
            && (int) auth('api')->id() === (int) $cart->member_id;
    }
}
