<?php

namespace App\Http\Requests\Api\V1\Front\Cart;

use App\Http\Requests\Api\V1\Front\BaseRequest;
use App\Http\Requests\Api\V1\Front\Cart\Traits\CartAuthorize;
use Illuminate\Validation\Rule;

class IndexRequest extends BaseRequest
{
    use CartAuthorize {
        CartAuthorize::authorize as public innerAuthrize;
    }

    /**
     * @return bool
     */
    public function authorize()
    {
        if (!isset($this->always_create) || (bool) $this->always_create === true) {
            return true;
        }

        return $this->innerAuthrize();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'cart_id' => 'nullable|integer',
            'member_id' => 'sometimes|numeric',
            'token' => 'sometimes|string',
            'payment_type' => ['nullable', Rule::in(\App\Enums\Order\PaymentType::getValues())],
            'delete_expired' => 'nullable|boolean',
            'always_create' => 'nullable|boolean',
        ];
    }

    public function attributes()
    {
        return [
            'member_id' => __('validation.attributes.member.id'),
            'token' => __('validation.attributes.cart.token'),
            'payment_type' => __('validation.attributes.purchase.payment_type'),
        ];
    }
}
