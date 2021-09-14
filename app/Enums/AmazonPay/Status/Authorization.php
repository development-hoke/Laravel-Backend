<?php

namespace App\Enums\AmazonPay\Status;

use App\Enums\BaseEnum;

/**
 * @see https://developer.amazon.com/ja/docs/amazon-pay-api/authorization-states-and-reason-codes.html
 */
final class Authorization extends BaseEnum
{
    const Pending = 'Pending';
    const Open = 'Open';
    const Declined = 'Declined';
    const Closed = 'Closed';
}
