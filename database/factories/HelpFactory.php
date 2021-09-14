<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Help;
use Faker\Generator as Faker;

$factory->define(Help::class, function (Faker $faker) {
    return [
        'title' => $faker->word,
        'body' => $faker->sentence(10),
        'sort' => $faker->numberBetween(0, 100),
        'is_faq' => $faker->numberBetween(0, 1),
        'good' => $faker->numberBetween(0, 100),
        'bad' => $faker->numberBetween(0, 100),
    ];
});
