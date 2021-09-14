<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\OrderCredit;
use Faker\Generator as Faker;
use Illuminate\Support\Str;

$factory->define(OrderCredit::class, function (Faker $faker) {
    return [
        'order_id' => function () {
            return factory(OrderCredit::class)->create()->id;
        },
        'authorization_number' => Str::random(6),
        'transaction_number' => Str::random(20),
    ];
});
