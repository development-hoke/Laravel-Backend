<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Store;
use Faker\Generator as Faker;

$factory->define(Store::class, function (Faker $faker) {
    return [
        'code' => $faker->lexify('??????????'),
        'name' => $faker->company,
        'zip_code' => $faker->postcode,
        'address1' => $faker->streetAddress,
        'address2' => $faker->streetName,
        'phone_number_1' => $faker->phoneNumber,
        'phone_number_2' => $faker->phoneNumber,
        'email' => $faker->safeEmail,
        'location' => [
            'longitude' => rand(-1800, 1800) * 0.1,
            'latitude' => rand(-900, 900) * 0.1,
        ],
    ];
});
