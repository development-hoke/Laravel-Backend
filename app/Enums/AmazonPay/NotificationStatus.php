<?php

namespace App\Enums\AmazonPay;

use App\Enums\BaseEnum;

final class NotificationStatus extends BaseEnum
{
    const Processing = 1;
    const Processed = 2;
    const Failed = 3;
}
