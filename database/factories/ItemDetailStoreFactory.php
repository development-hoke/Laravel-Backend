<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\ItemDetailStore;
use Faker\Generator as Faker;

$factory->define(ItemDetailStore::class, function (Faker $faker) {
    return [
        'item_detail_id' => $faker->randomElement(\App\Models\ItemDetail::all()->pluck('id')),
        'store_id' => $faker->randomElement(\App\Models\Store::all()->pluck('id')),
        'stock' => $faker->numberBetween(0, 50),
    ];
});
