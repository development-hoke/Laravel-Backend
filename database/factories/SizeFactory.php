<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Size;
use Faker\Generator as Faker;

$factory->define(Size::class, function (Faker $faker) {
    return [
        'id' => $faker->unique()->numberBetween(0, 9),
        'name' => $faker->lexify('??????????'),
        'code' => $faker->unique()->numberBetween(0, 9),
        'search_code' => $faker->unique()->numberBetween(0, 9),
    ];
});
