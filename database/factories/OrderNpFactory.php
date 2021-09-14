<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\OrderNp;
use Faker\Generator as Faker;
use Illuminate\Support\Str;

$factory->define(OrderNp::class, function (Faker $faker) {
    return [
        'shop_transaction_id' => function () {
            return factory(OrderNp::class)->create()->id;
        },
        'np_transaction_id' => Str::random(11),
    ];
});
