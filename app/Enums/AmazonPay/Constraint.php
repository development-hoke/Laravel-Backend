<?php

namespace App\Enums\AmazonPay;

use App\Enums\BaseEnum;

/**
 * @see https://developer.amazon.com/ja/docs/amazon-pay-api/order-reference-constraints.html
 */
final class Constraint extends BaseEnum
{
    const AmountNotSet = 'AmountNotSet';
    const BuyerEqualsSeller = 'BuyerEqualsSeller';
    const PaymentMethodNotAllowed = 'PaymentMethodNotAllowed';
    const PaymentPlanNotSet = 'PaymentPlanNotSet';
    const ShippingAddressNotSet = 'ShippingAddressNotSet';
}
