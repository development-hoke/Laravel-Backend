<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Page;
use Faker\Generator as Faker;

$factory->define(Page::class, function (Faker $faker) {
    return [
        'slug' => $faker->slug,
        'title' => $faker->word,
        'body' => $faker->sentence(10),
        'status' => $faker->numberBetween(0, 1),
        'publish_from' => $faker->dateTimeBetween('-1 years', '0years')->format('Y-m-d H:i:s'),
        'publish_to' => $faker->dateTimeBetween('0 years', '1years')->format('Y-m-d H:i:s'),
    ];
});
