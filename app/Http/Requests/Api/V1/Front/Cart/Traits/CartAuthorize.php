<?php

namespace App\Http\Requests\Api\V1\Front\Cart\Traits;

trait CartAuthorize
{
    /**
     * @return bool
     */
    public function authorize()
    {
        $cartId = $this->cart_id ?: $this->route('cart_id');

        if (empty($cartId)) {
            return true;
        }

        $cart = \App\Models\Cart::find($cartId);

        if (empty($cart)) {
            return true;
        }

        if (auth('api')->check()) {
            if (!empty($cart->member_id) && auth('api')->id() !== (int) $cart->member_id) {
                return false;
            }

            return $this->token === $cart->token;
        }

        if (!empty($this->token)) {
            if ($this->token !== $cart->token) {
                return false;
            }

            return $cart->is_guest || empty($cart->member_id);
        }

        return false;
    }
}
