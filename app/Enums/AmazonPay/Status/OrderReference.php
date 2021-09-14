<?php

namespace App\Enums\AmazonPay\Status;

use App\Enums\BaseEnum;

/**
 * @see https://developer.amazon.com/ja/docs/amazon-pay-api/order-reference-states-and-reason-codes.html
 */
final class OrderReference extends BaseEnum
{
    const Draft = 'Draft';
    const Open = 'Open';
    const Suspended = 'Suspended';
    const Canceled = 'Canceled';
    const Closed = 'Closed';
}
