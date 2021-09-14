<?php

namespace App\Http\Requests\Api\V1\Front\Cart;

use App\Exceptions\InvalidInputException;
use App\Http\Requests\Api\V1\Front\BaseRequest;
use App\Http\Requests\Api\V1\Front\Cart\Traits\CartAuthorize;
use Illuminate\Validation\Rule;

class UpdateItemRequest extends BaseRequest
{
    use CartAuthorize;

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'token' => ['string', Rule::requiredIf(function () {
                return !auth('api')->check();
            })],
            'count' => 'required|integer|min:1',
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
            'token' => __('validation.attributes.cart.token'),
            'count' => __('validation.attributes.cart.items.count'),
            'payment_type' => __('validation.attributes.purchase.payment_type'),
        ];
    }

    /**
     * @return array
     */
    public function messages()
    {
        return [
            'token.*' => __('validation.cart.not_found'),
        ];
    }

    /**
     * 追加バリデーション
     *
     * @return void
     */
    protected function passedValidation()
    {
        $cart = \App\Models\Cart::find($this->cart_id);

        if ($cart->order_type !== \App\Enums\Order\OrderType::Normal) {
            throw new InvalidInputException(__('validation.cart.max_if', [
                'type' => \App\Enums\Order\OrderType::getDescription($cart->order_type),
                'max' => 1,
            ]));
        }

        return true;
    }
}
