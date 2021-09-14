<?php

namespace App\Http\Requests\Api\V1\Front\Purchase;

use App\Enums\Order\PaymentType;
use App\Http\Requests\Api\V1\Front\BaseRequest;
use App\Http\Requests\Api\V1\Front\Purchase\Traits\CartMemberAuthorize;
use Illuminate\Validation\Rule;

class ChangePaymentTypeRequest extends BaseRequest
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
            'payment_type' => [
                'required',
                Rule::in(PaymentType::getValues()),
            ],
            'cart_id' => 'required|integer',
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
            'payment_type' => __('validation.attributes.purchase.payment_type'),
        ];
    }
}
