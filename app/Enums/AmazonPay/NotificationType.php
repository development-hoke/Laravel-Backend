<?php

namespace App\Enums\AmazonPay;

use App\Enums\BaseEnum;

final class NotificationType extends BaseEnum
{
    const OrderReferenceNotification = 'OrderReferenceNotification';
    const PaymentAuthorize = 'PaymentAuthorize';
    const PaymentCapture = 'PaymentCapture';
    const PaymentRefund = 'PaymentRefund';
    const ChargebackDetailedNotification = 'ChargebackDetailedNotification';
}
