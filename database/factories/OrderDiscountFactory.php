<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\OrderDiscount;
use Faker\Generator as Faker;

$factory->define(OrderDiscount::class, function (Faker $faker) {
    $order = $faker->randomElement(\App\Models\Order::get());
    $orderDetail = $faker->randomElement(\App\Models\OrderDetail::where('order_id', $order->id)->get());

    $type = $faker->randomElement(\App\Enums\OrderDiscount\Type::getValues());
    $method = $faker->randomElement(\App\Enums\OrderDiscount\Method::getValues());

    $discountableType = rand(0, 1)
        ? $faker->randomElement([\App\Models\OrderUsedCoupon::class, \App\Models\Event::class, \App\Models\ItemReserve::class])
        : null;
    $discountable = $discountableType ? $faker->randomElement(app()->make($discountableType)->get()) : null;

    $orderable = $type < 20 || $type === \App\Enums\OrderDiscount\Type::CouponItem
        ? $orderDetail
        : $order;

    $unitAppliedPrice = null;
    $appliedPrice = 0;

    if ($orderable instanceof \App\Models\OrderDetail) {
        $unitAppliedPrice = $faker->numberBetween(500, 1500);
        $appliedPrice = $unitAppliedPrice * $orderable->getAmountAttribute();
    } else {
        $appliedPrice = $faker->numberBetween(500, 1500);
    }

    return [
        'orderable_id' => $orderable->id,
        'orderable_type' => get_class($orderable),
        'unit_applied_price' => $unitAppliedPrice,
        'applied_price' => $appliedPrice,
        'type' => $type,
        'method' => $method,
        'discount_price' => $method === \App\Enums\OrderDiscount\Method::Fixed ? $faker->numberBetween(500, 1500) : null,
        'discount_rate' => $method === \App\Enums\OrderDiscount\Method::Percentile ? $faker->randomFloat(0.1, 0.5) : null,
        'discountable_type' => $discountableType,
        'discountable_id' => $discountable ? $discountable->id : null,
    ];
});
