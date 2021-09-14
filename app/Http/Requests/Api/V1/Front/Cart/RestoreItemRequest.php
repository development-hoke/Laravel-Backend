<?php

namespace App\Http\Requests\Api\V1\Front\Cart;

use App\Http\Requests\Api\V1\Front\BaseRequest;
use App\Http\Requests\Api\V1\Front\Cart\Traits\CartAuthorize;
use Illuminate\Validation\Rule;

class RestoreItemRequest extends BaseRequest
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
}
