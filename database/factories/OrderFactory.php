<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Order;
use App\Utils\Cache;
use Carbon\Carbon;
use Faker\Generator as Faker;

$factory->define(Order::class, function (Faker $faker) {
    $today = Carbon::today();

    return [
        'member_id' => $faker->randomElement([200000001, 200000002]),
        'code' => $today->format('Ymd') . '-09967-999-' . sprintf('%05d', Cache::increment(
            sprintf(Cache::KEY_ORDER_CODE, $today->format('Ymd'))
        )),
        'order_date' => $faker->dateTimeBetween('-1 years', '0years')->format('Y-m-d H:i:s'),
        'payment_type' => $faker->randomElement(\App\Enums\Order\PaymentType::getValues()),
        'delivery_type' => $faker->randomElement(\App\Enums\Order\DeliveryType::getValues()),
        'delivery_hope_date' => $faker->dateTimeBetween('-1 years', '0years')->format('Y-m-d H:i:s'),
        'delivery_hope_time' => $faker->randomElement(\App\Enums\Order\DeliveryTime::getValues()),
        'delivery_fee' => $faker->randomElement([500, 1080, 1500]),
        'price' => $faker->numberBetween(1000, 20000),
        'fee' => $faker->randomElement([500, 1080]),
        'use_point' => $faker->numberBetween(0, 200),
        'order_type' => $faker->randomElement(\App\Enums\Order\OrderType::getValues()),
        'paid' => $faker->numberBetween(0, 1),
        'paid_date' => $faker->dateTimeBetween('-1 years', '0years')->format('Y-m-d H:i:s'),
        'inspected' => $faker->numberBetween(0, 1),
        'inspected_date' => $faker->dateTimeBetween('-1 years', '0years')->format('Y-m-d H:i:s'),
        'deliveryed' => $faker->numberBetween(0, 1),
        'deliveryed_date' => $faker->dateTimeBetween('-1 years', '0years')->format('Y-m-d H:i:s'),
        'status' => $faker->randomElement(\App\Enums\Order\Status::getValues()),
        'device_type' => $faker->randomElement(\App\Enums\Common\DeviceType::getValues()),
        'add_point' => $faker->numberBetween(1000, 20000),
        'delivery_number' => $faker->numberBetween(1000, 20000),
        'delivery_company' => $faker->randomElement(\App\Enums\Order\Status::getValues()),
        'memo1' => $faker->sentence(10),
        'memo2' => $faker->sentence(10),
        'shop_memo' => $faker->sentence(10),
    ];
});
