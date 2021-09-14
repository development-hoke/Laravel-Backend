<?php

namespace App\Http\Requests\Api\V1\Front\Purchase;

class OrderRequest extends ConfirmRequest
{
    public function rules()
    {
        return array_merge(parent::rules(), [
            'amazon_order_reference_id' => [
                sprintf('required_if:payment_type,%s', \App\Enums\Order\PaymentType::AmazonPay),
                'max:255',
            ],
            'amazon_access_token' => [
                sprintf('required_if:payment_type,%s', \App\Enums\Order\PaymentType::AmazonPay),
                'max:2000',
            ],
        ]);
    }
}
