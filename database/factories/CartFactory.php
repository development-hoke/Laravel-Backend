<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Cart;
use Faker\Generator as Faker;

$factory->define(Cart::class, function (Faker $faker) {
    return [
        'token' => $faker->unique()->lexify('??????????'),
        'member_id' => $faker->unique()->randomNumber(),
        'items' => function () {
            return [];
        },
        'use_coupon_ids' => function () {
            return [];
        },
        'order_type' => $faker->randomElement(\App\Enums\Order\OrderType::getValues()),
    ];
});
