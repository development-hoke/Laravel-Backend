<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\DepartmentGroup;
use Faker\Generator as Faker;

$factory->define(DepartmentGroup::class, function (Faker $faker) {
    return [
        'name' => $faker->word,
    ];
});
