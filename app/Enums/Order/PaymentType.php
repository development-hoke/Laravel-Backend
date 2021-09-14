<?php

namespace App\Enums\Order;

use App\Enums\BaseEnum;

/**
 * @method static static Bank()
 * @method static static Cod()
 * @method static static CreditCard()
 * @method static static NP()
 * @method static static AmazonPay()
 */
final class PaymentType extends BaseEnum
{
    const Bank = 1;
    const Cod = 2;
    const CreditCard = 3;
    const NP = 4;
    const AmazonPay = 5;
}
