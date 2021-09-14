<?php

namespace App\Http\Requests\Api\V1\Front\Purchase;

use App\Enums\Order\DeliveryTime;
use App\Enums\Order\DeliveryType;
use App\Enums\Order\PaymentMethod;
use App\Enums\Order\PaymentType;
use App\Enums\Order\Request;
use App\Exceptions\InvalidInputException;
use App\Http\Requests\Api\V1\Front\BaseRequest;
use App\Http\Requests\Api\V1\Front\Purchase\Traits\CartMemberAuthorize;
use App\Validation\Rules\AllowedDeliveryDate;
use App\Validation\Rules\Kana;
use Illuminate\Validation\Rule;

class ConfirmRequest extends BaseRequest
{
    use CartMemberAuthorize;

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $paymentTypeCreditCard = PaymentType::CreditCard;

        return [
            'member.lname' => 'required|min:1|max:255',
            'member.fname' => 'required|min:1|max:255',
            'member.lkana' => [
                'required',
                'min:1',
                'max:255',
                new Kana(),
            ],
            'member.fkana' => [
                'required',
                'min:1',
                'max:255',
                new Kana(),
            ],
            'member.tel' => 'required|max:50',
            'member.zip' => 'required|digits:7',
            'member.pref_id' => 'required|exists:prefs,id',
            'member.city' => 'required|min:1|max:50',
            'member.town' => 'required|min:1|max:50',
            'member.address' => 'required|min:1|max:100',
            'member.building' => 'sometimes|nullable|max:100',
            'cart_id' => 'required|exists:carts,id,deleted_at,NULL',
            'destination_id' => [
                Rule::requiredIf(function () {
                    return in_array($this->payment_type, [
                        PaymentType::Cod,
                        PaymentType::NP,
                        PaymentType::AmazonPay,
                    ], true) === false;
                }),
                'numeric',
            ],
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
                Rule::in(PaymentType::getValues()),
            ],
            'use_point' => 'required|numeric',
            'use_point_type' => ['required', Rule::in(\App\Enums\Order\UsePointType::getValues())],
            'card.masked_pan' => 'sometimes',
            'card.token' => [
                Rule::RequiredIf(function () {
                    return ((int) $this->payment_type) === PaymentType::CreditCard && !$this->card['use_saved_card_info'];
                }),
            ],
            'card.validity' => 'sometimes',
            'card.masked_security_code' => [
                Rule::RequiredIf(function () {
                    return ((int) $this->payment_type) === PaymentType::CreditCard && !$this->card['use_saved_card_info'];
                }),
            ],
            'card.is_save_card_info' => [
                'boolean',
                Rule::RequiredIf(function () {
                    return ((int) $this->payment_type) === PaymentType::CreditCard && !$this->card['use_saved_card_info'];
                }),
            ],
            'card.use_saved_card_info' => [
                'boolean',
                Rule::RequiredIf(function () {
                    return ((int) $this->payment_type) === PaymentType::CreditCard;
                }),
            ],
            'card.payment_method' => [
                Rule::RequiredIf(function () {
                    return ((int) $this->payment_type) === PaymentType::CreditCard;
                }),
                Rule::in(PaymentMethod::getValues()),
            ],
            'card.member_credit_card_id' => 'integer|required_if:card.use_saved_card_info,1',
            'billing_address.lname' => [
                "required_if:payment_type,{$paymentTypeCreditCard}",
                'min:1',
                'max:255',
            ],
            'billing_address.fname' => [
                "required_if:payment_type,{$paymentTypeCreditCard}",
                'min:1',
                'max:255',
            ],
            'billing_address.lkana' => [
                "required_if:payment_type,{$paymentTypeCreditCard}",
                'min:1',
                'max:255',
                new Kana(),
            ],
            'billing_address.fkana' => [
                "required_if:payment_type,{$paymentTypeCreditCard}",
                'min:1',
                'max:255',
                new Kana(),
            ],
            'billing_address.tel' => [
                "required_if:payment_type,{$paymentTypeCreditCard}",
                'max:50',
            ],
            'billing_address.zip' => [
                "required_if:payment_type,{$paymentTypeCreditCard}",
                'digits:7',
            ],
            'billing_address.pref_id' => [
                "required_if:payment_type,{$paymentTypeCreditCard}",
                'exists:prefs,id',
            ],
            'billing_address.city' => [
                "required_if:payment_type,{$paymentTypeCreditCard}",
                'min:1',
                'max:50',
            ],
            'billing_address.town' => [
                "required_if:payment_type,{$paymentTypeCreditCard}",
                'min:1',
                'max:50',
            ],
            'billing_address.address' => [
                "required_if:payment_type,{$paymentTypeCreditCard}",
                'min:1',
                'max:100',
            ],
            'billing_address.building' => 'sometimes|nullable|max:100',
            'has_message' => 'boolean|required',
            'message.type' => [
                'required_if:has_message,1',
                Rule::in(Request::getValues()),
            ],
            'message.content' => 'required_if:has_message,1|max:255',
            'amazon_order_reference_id' => [
                sprintf('required_if:payment_type,%s', PaymentType::AmazonPay),
                'max:255',
            ],
            'amazon_access_token' => [
                sprintf('required_if:payment_type,%s', PaymentType::AmazonPay),
                'max:2000',
            ],
            'cart_token' => 'nullable|string:max:64',
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
            'member.lname' => __('validation.attributes.purchase.member.lname'),
            'member.fname' => __('validation.attributes.purchase.member.fname'),
            'member.lkana' => __('validation.attributes.purchase.member.lkana'),
            'member.fkana' => __('validation.attributes.purchase.member.fkana'),
            'member.tel' => __('validation.attributes.purchase.member.tel'),
            'member.zip' => __('validation.attributes.purchase.member.zip'),
            'member.pref_id' => __('validation.attributes.purchase.member.pref_id'),
            'member.city' => __('validation.attributes.purchase.member.city'),
            'member.town' => __('validation.attributes.purchase.member.town'),
            'member.address' => __('validation.attributes.purchase.member.address'),
            'member.building' => __('validation.attributes.purchase.member.building'),
            'cart_id' => __('validation.attributes.purchase.cart'),
            'destination_id' => __('validation.attributes.purchase.destination'),
            'delivery_type' => __('validation.attributes.purchase.delivery_type'),
            'delivery_hope_date' => __('validation.attributes.purchase.delivery_hope_date'),
            'delivery_hope_time' => __('validation.attributes.purchase.delivery_hope_time'),
            'payment_type' => __('validation.attributes.purchase.payment_type'),
            'use_point' => __('validation.attributes.purchase.use_point'),
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
            'card.token.*' => __('error.invalid_fregi'),
        ];
    }

    /**
     * @return void
     */
    protected function passedValidation()
    {
        $cart = \App\Models\Cart::findOrFail($this->cart_id);

        if ((int) $this->delivery_has_time && in_array((int) $cart->order_type, [
            \App\Enums\Order\OrderType::Reserve,
            \App\Enums\Order\OrderType::BackOrder,
        ], true)) {
            throw new InvalidInputException(__('validation.purchase.disabled_delivery_schedule', [
                'type' => \App\Enums\Order\OrderType::getDescription($cart->order_type),
            ]));
        }
    }
}
