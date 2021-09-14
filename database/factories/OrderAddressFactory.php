<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\OrderAddress;
use Faker\Generator as Faker;

$factory->define(OrderAddress::class, function (Faker $faker) {
    return [
        'order_id' => $faker->randomElement(\App\Models\Order::all()->pluck('id')),
        'type' => $faker->randomElement(\App\Enums\OrderAddress\Type::getValues()),
        'fname' => $faker->firstName,
        'lname' => $faker->lastName,
        'fkana' => $faker->firstName,
        'lkana' => $faker->lastName,
        'tel' => $faker->phoneNumber,
        'pref_id' => $faker->randomElement(\App\Models\Pref::all()->pluck('id')),
        'zip' => $faker->postcode,
        'city' => $faker->city,
        'town' => $faker->streetAddress,
        'address' => $faker->streetName,
        'building' => $faker->word,
        'email' => $faker->safeEmail,
    ];
});
