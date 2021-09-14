<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\ItemReserve;
use Faker\Generator as Faker;

$factory->define(ItemReserve::class, function (Faker $faker) {
    return [
        'item_id' => $faker->randomElement(\App\Models\Item::all()->pluck('id')),
        'is_enable' => $faker->numberBetween(0, 1),
        'period_from' => $faker->dateTimeBetween('-1 years', '0years')->format('Y-m-d H:i:s'),
        'period_to' => $faker->dateTimeBetween('0 years', '1years')->format('Y-m-d H:i:s'),
        'reserve_price' => $faker->numberBetween(1000, 10000),
        'is_free_delivery' => $faker->numberBetween(0, 1),
        'limited_stock_threshold' => $faker->numberBetween(10, 50),
        'out_of_stock_threshold' => $faker->numberBetween(10, 50),
        'expected_arrival_date' => $faker->dateTimeBetween('0 years', '1years')->format('Y-m-d'),
        'note' => $faker->word,
    ];
});
