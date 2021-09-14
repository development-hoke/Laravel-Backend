<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\OrderDetail;
use Faker\Generator as Faker;

$factory->define(OrderDetail::class, function (Faker $faker) {
    return [
        'order_id' => $faker->randomElement(\App\Models\Order::all()->pluck('id')),
        'item_detail_id' => $faker->randomElement(\App\Models\ItemDetail::all()->pluck('id')),
        'retail_price' => $faker->numberBetween(1000, 20000),
        'sale_type' => $faker->randomElement(\App\Enums\Order\SaleType::getValues()),
    ];
});
