<?php

namespace App\Enums\Cart;

use App\Enums\BaseEnum;

/**
 * Class Status
 *
 * @package App\Enums\Cart
 */
final class Status extends BaseEnum
{
    const Add = 1;
    const Reserve = 2;
    const Order = 3;
    const RestockRequest = 4;
    const SoldOut = 5;

    /**
     * 予約・取り寄せ商品
     *
     * @return int[]
     */
    public static function reserveAndOrderValues()
    {
        return [
            self::Reserve,
            self::Order,
        ];
    }
}
