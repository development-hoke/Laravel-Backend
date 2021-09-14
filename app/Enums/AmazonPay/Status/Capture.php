<?php

namespace App\Enums\AmazonPay\Status;

use App\Enums\BaseEnum;

/**
 * @see https://developer.amazon.com/ja/docs/amazon-pay-api/capture-states-and-reason-codes.html
 */
final class Capture extends BaseEnum
{
    const Pending = 'Pending';
    const Declined = 'Declined';
    const Completed = 'Completed';
    const Closed = 'Closed';
}
