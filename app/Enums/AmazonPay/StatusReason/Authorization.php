<?php

namespace App\Enums\AmazonPay\StatusReason;

use App\Enums\BaseEnum;

/**
 * @see https://developer.amazon.com/ja/docs/amazon-pay-api/authorization-states-and-reason-codes.html
 */
final class Authorization extends BaseEnum
{
    // Declined
    const InvalidPaymentMethod = 'InvalidPaymentMethod';
    const AmazonRejected = 'AmazonRejected';
    const ProcessingFailure = 'ProcessingFailure';
    const TransactionTimedOut = 'TransactionTimedOut';

    // Closed
    const ExpireUnused = 'ExpireUnused';
    const MaxCapturesProcessed = 'MaxCapturesProcessed';
    const AmazonClosed = 'AmazonClosed';
    const OrderReferenceCanceled = 'OrderReferenceCanceled';
    const SellerClosed = 'SellerClosed';
}
