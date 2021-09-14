<?php

// phpcs:ignoreFile

/* @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\ItemDetail;
use Database\Utils\ItemDetailFinder;
use Faker\Generator as Faker;

$factory->define(ItemDetail::class, function (Faker $faker) {
    [
        $item,
        $colorId,
        $sizeId,
        $skuNumber,
    ] = ItemDetailFinder::findSkuNumber($faker);

    return [
        'item_id' => $item->id,
        'color_id' => $colorId,
        'size_id' => $sizeId,
        'sku_number' => $skuNumber,
        'sort' => $faker->numberBetween(0, 100),
        'status' => $faker->randomElement(\App\Enums\Common\Status::getValues()),
        'status_change_date' => $faker->dateTimeBetween('-1 years', '0years')->format('Y-m-d H:i:s'),
        'redisplay_requested' => $faker->numberBetween(0, 1),
        'last_sales_date' => $faker->dateTimeBetween('-1 years', '0years')->format('Y-m-d H:i:s'),
    ];
});
