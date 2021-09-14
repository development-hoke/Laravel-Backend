<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Information;
use Faker\Generator as Faker;

$factory->define(Information::class, function (Faker $faker) {
    return [
        'title' => $faker->word,
        'body' => $faker->sentence(10),
        'priority' => $faker->numberBetween(1, 1000),
        'is_store_top' => $faker->numberBetween(0, 1),
        'status' => $faker->numberBetween(0, 1),
        'publish_at' => $faker->dateTimeBetween('-1 years', '0years')->format('Y-m-d H:i:s'),
    ];
});
