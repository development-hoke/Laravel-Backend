<?php

namespace App\Enums\OrderDiscount;

use App\Enums\BaseEnum;

final class Type extends BaseEnum
{
    // displayed_discount
    const Normal = 1;
    const Member = 2;
    const Staff = 3;
    const EventSale = 4;

    // bundle_sale_discount
    const EventBundle = 5;

    // 予約販売
    const Reservation = 6;

    // 注文全体
    const DeliveryFee = 20;
    const ReservationDeliveryFee = 21;

    // クーポン
    const CouponItem = 40;
    const CouponDeliveryFee = 41;
}
