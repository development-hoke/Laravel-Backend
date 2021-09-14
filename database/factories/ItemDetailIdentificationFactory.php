<?php

// phpcs:ignoreFile

/* @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\ItemDetailIdentification;
use Database\Utils\ItemDetailIdentificationFinder;
use Faker\Generator as Faker;

$factory->define(ItemDetailIdentification::class, function (Faker $faker) {
    [
        $itemDetail,
        $janCode,
    ] = ItemDetailIdentificationFinder::findSkuNumber($faker);

    return [
        'item_detail_id' => $itemDetail->id,
        'jan_code' => $janCode,
        'ec_stock' => $faker->numberBetween(0, 200),
        'store_stock' => $faker->numberBetween(0, 2000),
        'reservable_stock' => $faker->numberBetween(0, 50),
        'dead_inventory_days' => $faker->numberBetween(0, 50),
        'slow_moving_inventory_days' => $faker->numberBetween(0, 50),
        'latest_added_stock' => $faker->numberBetween(0, 50),
        'latest_stock_added_at' => $faker->dateTimeBetween('-1 years', '0years')->format('Y-m-d H:i:s'),
        'arrival_date' => $faker->dateTimeBetween('-1 years', '0years')->format('Y-m-d H:i:s'),
    ];
});
