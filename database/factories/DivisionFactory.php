<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Division;
use Faker\Generator as Faker;

$factory->define(Division::class, function (Faker $faker) {
    return [
        'organization_id' => $faker->randomElement(\App\Models\Organization::all()->pluck('id')),
        'name' => $faker->word,
        'brand_name' => $faker->word,
        'brand_code' => $faker->lexify('??????????'),
        'sign' => $faker->lexify('??????????'),
    ];
});
