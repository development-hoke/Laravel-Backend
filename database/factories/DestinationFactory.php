<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;

$factory->define(\App\Models\Destination::class, function (Faker $faker) {
    return [
        'member_id' => $faker->randomElement([200000000, 200000001, 200000002, 200000003, 200000004, 200000005, 200000006, 200000007, 200000008, 200000009, 200000010, 200000011]),
        'last_name' => $faker->lastName,
        'first_name' => $faker->firstName,
        'last_name_kana' => $faker->lastName,
        'first_name_kana' => $faker->firstName,
        'phone' => $faker->phoneNumber,
        'postal' => $faker->postcode,
        'pref_id' => function () {
            return factory(\App\Models\Pref::class)->create()->id;
        },
        'address' => $faker->word,
        'building' => $faker->word,
    ];
});
