<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\HelpCategory;
use Faker\Generator as Faker;

$factory->define(HelpCategory::class, function (Faker $faker) {
    return [
        'parent_id' => $faker->randomElement(\App\Models\HelpCategory::all()->pluck('id')),
        'name' => $faker->lexify('??????????'),
        'sort' => $faker->unique()->numberBetween(0, 999),
    ];
});
