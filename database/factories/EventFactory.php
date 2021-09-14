<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Event;
use Faker\Generator as Faker;

$factory->define(Event::class, function (Faker $faker) {
    return [
        'title' => $faker->word,
        'period_from' => $faker->dateTimeBetween('-1 years', '0years')->format('Y-m-d H:i:s'),
        'period_to' => $faker->dateTimeBetween('-1 years', '0years')->format('Y-m-d H:i:s'),
        'target' => $faker->randomElement(\App\Enums\Event\Target::getValues()),
        'sale_type' => $faker->randomElement(\App\Enums\Event\SaleType::getValues()),
        'target_user_type' => $faker->randomElement(\App\Enums\Event\TargetUserType::getValues()),
        'discount_type' => $faker->randomElement(\App\Enums\Event\DiscountType::getValues()),
        'discount_rate' => $faker->numberBetween(10, 90) / 100,
        'published' => $faker->numberBetween(0, 1),
    ];
});
