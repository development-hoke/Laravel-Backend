<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Brand;
use Faker\Generator as Faker;

$factory->define(Brand::class, function (Faker $faker) {
    return [
        'section' => $faker->randomElement(\App\Enums\Brand\Section::getValues()),
        'store_brand' => $faker->randomElement(\App\Enums\Common\StoreBrand::getValues()),
        'name' => $faker->lexify('??????????'),
        'kana' => $faker->lexify('??????????'),
        'category' => $faker->randomElement(\App\Enums\Brand\Category::getValues()),
        'sort' => $faker->unique()->numberBetween(0, 999),
    ];
});
