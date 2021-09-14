<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Term;
use Faker\Generator as Faker;

$factory->define(Term::class, function (Faker $faker) {
    return [
        'name' => $faker->lexify('??????????'),
    ];
});
