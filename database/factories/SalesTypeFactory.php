<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\SalesType;
use Faker\Generator as Faker;

$factory->define(SalesType::class, function (Faker $faker) {
    return [
        'name' => $faker->lexify('??????????'),
        'text_color' => $faker->hexcolor,
        'sort' => $faker->numberBetween(0, 100),
    ];
});
