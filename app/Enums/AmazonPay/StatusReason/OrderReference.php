<?php

namespace App\Enums\AmazonPay\StatusReason;

use App\Enums\BaseEnum;

/**
 * @see https://developer.amazon.com/ja/docs/amazon-pay-api/order-reference-states-and-reason-codes.html
 */
final class OrderReference extends BaseEnum
{
    // Suspended
    const InvalidPaymentMethod = 'InvalidPaymentMethod';

    // Canceled
    const SellerCanceled = 'SellerCanceled';
    const Stale = 'Stale';
    const AmazonCanceled = 'AmazonCanceled';

    // Closed
    const Expired = 'Expired';
    const MaxAmountCharged = 'MaxAmountCharged';
    const MaxAuthorizationsCaptured = 'MaxAuthorizationsCaptured';
    const AmazonClosed = 'AmazonClosed';
    const SellerClosed = 'SellerClosed';
}
