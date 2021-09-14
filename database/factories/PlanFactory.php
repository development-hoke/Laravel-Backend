<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Plan;
use Faker\Generator as Faker;

$factory->define(Plan::class, function (Faker $faker) {
    return [
        'store_brand' => $faker->randomElement(\App\Enums\Common\StoreBrand::getValues()),
        'slug' => $faker->slug,
        'title' => $faker->word,
        'status' => $faker->numberBetween(0, 1),
        'thumbnail' => $faker->imageUrl(),
        'place' => $faker->randomElement(\App\Enums\Plan\Place::getValues()),
        'body' => $faker->sentence(10),
        'is_item_setting' => $faker->numberBetween(0, 1),
        'period_from' => $faker->dateTimeBetween('-1 years', '0years')->format('Y-m-d H:i:s'),
        'period_to' => $faker->dateTimeBetween('0 years', '1years')->format('Y-m-d H:i:s'),
    ];
});
