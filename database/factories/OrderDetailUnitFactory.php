<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\OrderDetailUnit;
use Faker\Generator as Faker;

$factory->define(OrderDetailUnit::class, function (Faker $faker) {
    $orderDetail = $faker->randomElement(\App\Models\OrderDetail::all());
    $itemDetailIdent = $faker->randomElement(\App\Models\ItemDetailIdentification::where('item_detail_id', $orderDetail->item_detail_id)->get());

    return [
        'order_detail_id' => $orderDetail->id,
        'item_detail_identification_id' => $itemDetailIdent->id,
        'amount' => $faker->numberBetween(1, 5),
    ];
});
