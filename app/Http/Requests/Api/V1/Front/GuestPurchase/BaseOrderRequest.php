<?php

namespace App\Http\Requests\Api\V1\Front\GuestPurchase;

use App\Enums\Order\DeliveryTime;
use App\Enums\Order\DeliveryType;
use App\Enums\Order\PaymentMethod;
use App\Enums\Order\PaymentType;
use App\Enums\Order\Request;
use App\Http\Requests\Api\V1\Front\BaseRequest;
use App\Validation\Rules\AllowedDeliveryDate;
use App\Validation\Rules\Kana;
use Illuminate\Validation\Rule;

abstract class BaseOrderRequest extends BaseRequest
{
    /**
     * @return bool
     */
    public function authorize()
    {
        $cart = \App\Models\Cart::findOrFail($this->cart_id);

        return $cart->guest_verified && $this->cart_token === $cart->token;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'cart_id' => 'required|integer',
            'cart_token' => 'required|string|max:64',
            'member_token' => 'required|string|max:50',
            'shipping_address.lname' => 'required|min:1|max:255',
            'shipping_address.fname' => 'required|min:1|max:255',
            'shipping_address.lkana' => [
                'required',
                'min:1',
                'max:255',
                new Kana(),
            ],
            'shipping_address.fkana' => [
                'required',
                'min:1',
                'max:255',
                new Kana(),
            ],
            'shipping_address.tel' => 'required|max:50',
            'shipping_address.zip' => 'required|digits:7',
            'shipping_address.pref_id' => 'required|exists:prefs,id',
            'shipping_address.city' => 'required|min:1|max:50',
            'shipping_address.town' => 'required|min:1|max:50',
            'shipping_address.address' => 'required|min:1|max:100',
            'shipping_address.building' => 'sometimes|nullable|max:100',
            'delivery_has_time' => 'boolean|required',
            'delivery_type' => [
                'required',
                Rule::in(DeliveryType::getValues()),
            ],
            'delivery_hope_date' => [
                'required_if:delivery_has_time,1',
                'date',
                new AllowedDeliveryDate(),
            ],
            'delivery_hope_time' => [
                'required_if:delivery_has_time,1',
                Rule::in(DeliveryTime::getValues()),
            ],
            'payment_type' => [
                'required',
                Rule::in([PaymentType::CreditCard]),
            ],
            'card.masked_pan' => 'sometimes',
            'card.token' => 'required',
            'card.validity' => 'sometimes',
            'card.masked_security_code' => 'required',
            'card.is_save_card_info' => 'boolean|required',
            'card.payment_method' => [
                'required',
                Rule::in(PaymentMethod::getValues()),
            ],
            'has_message' => 'boolean|required',
            'message.type' => [
                'required_if:has_message,1',
                Rule::in(Request::getValues()),
            ],
            'message.content' => 'required_if:has_message,1|max:255',
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
            'shipping_address.lname' => __('validation.attributes.destination.lname'),
            'shipping_address.fname' => __('validation.attributes.destination.fname'),
            'shipping_address.lkana' => __('validation.attributes.destination.lkana'),
            'shipping_address.fkana' => __('validation.attributes.destination.fkana'),
            'shipping_address.tel' => __('validation.attributes.destination.tel'),
            'shipping_address.zip' => __('validation.attributes.destination.zip'),
            'shipping_address.pref_id' => __('validation.attributes.destination.pref_id'),
            'shipping_address.city' => __('validation.attributes.destination.city'),
            'shipping_address.town' => __('validation.attributes.destination.town'),
            'shipping_address.address' => __('validation.attributes.destination.address'),
            'shipping_address.building' => __('validation.attributes.destination.building'),
            'cart_id' => __('validation.attributes.purchase.cart'),
            'delivery_type' => __('validation.attributes.purchase.delivery_type'),
            'delivery_hope_date' => __('validation.attributes.purchase.delivery_hope_date'),
            'delivery_hope_time' => __('validation.attributes.purchase.delivery_hope_time'),
            'payment_type' => __('validation.attributes.purchase.payment_type'),
            'card.masked_pan' => __('validation.attributes.purchase.card.masked_pan'),
            'card.validity' => __('validation.attributes.purchase.card.validity'),
            'card.payment_method' => __('validation.attributes.purchase.card.payment_method'),
            'message.type' => __('validation.attributes.purchase.message.type'),
            'message.content' => __('validation.attributes.purchase.message.content'),
        ];
    }

    public function messages()
    {
        return [
            'card.token.required_if' => __('error.invalid_fregi'),
        ];
    }
}
