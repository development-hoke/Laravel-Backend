<?php

namespace App\Enums\AmazonPay\StatusReason;

use App\Enums\BaseEnum;

/**
 * @see https://developer.amazon.com/ja/docs/amazon-pay-api/refund-states-and-reason-codes.html
 */
final class Refund extends BaseEnum
{
    // Declined
    const AmazonRejected = 'AmazonRejected';
    const ProcessingFailure = 'ProcessingFailure';
}
