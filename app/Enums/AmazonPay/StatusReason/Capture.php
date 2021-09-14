<?php

namespace App\Enums\AmazonPay\StatusReason;

use App\Enums\BaseEnum;

/**
 * @see https://developer.amazon.com/ja/docs/amazon-pay-api/capture-states-and-reason-codes.html
 */
final class Capture extends BaseEnum
{
    // Declined
    const AmazonRejected = 'AmazonRejected';
    const ProcessingFailure = 'ProcessingFailure';

    // Closed
    const MaxAmountRefunded = 'MaxAmountRefunded';
    const MaxRefundsProcessed = 'MaxRefundsProcessed';
    const AmazonClosed = 'AmazonClosed';
}
