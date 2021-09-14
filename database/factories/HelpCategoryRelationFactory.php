<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\HelpCategoryRelation;
use Faker\Generator as Faker;

$factory->define(HelpCategoryRelation::class, function (Faker $faker) {
    return [
        'help_id' => $faker->randomElement(\App\Models\Help::all()->pluck('id')),
        'help_category_id' => $faker->randomElement(\App\Models\HelpCategory::all()->pluck('id')),
    ];
});
