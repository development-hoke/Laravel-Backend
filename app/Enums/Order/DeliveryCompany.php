<?php

namespace App\Enums\Order;

use App\Enums\BaseEnum;

/**
 * @method static static Sagawa()
 * @method static static Post()
 */
final class DeliveryCompany extends BaseEnum
{
    const Sagawa = 1;
    const Post = 2;
}
