<?php

use Illuminate\Database\Query\JoinClause;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OrdersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::transaction(function () {
            factory(\App\Models\Order::class, 500)->create();

            $orders = \App\Models\Order::limit(500)->orderBy('id', 'desc')->get();

            foreach ($orders as $order) {
                // OrderAddress
                $faker = \Faker\Factory::create('ja_JP');

                $attributes = [
                    'order_id' => $order->id,
                    'type' => \App\Enums\OrderAddress\Type::Delivery,
                    'fname' => $faker->firstName(),
                    'lname' => $faker->lastName,
                    'fkana' => 'ナマエカナ',
                    'lkana' => 'ナマエセイ',
                    'tel' => $faker->phoneNumber,
                    'pref_id' => $faker->randomElement(\App\Models\Pref::all()->pluck('id')),
                    'zip' => $faker->numberBetween(1000000, 9999999),
                    'city' => $faker->city . $faker->citySuffix,
                    'town' => $faker->streetName . $faker->streetSuffix,
                    'address' => $faker->streetAddress,
                    'building' => $faker->buildingNumber,
                    'email' => $faker->email,
                ];

                $orderAddress = new \App\Models\OrderAddress($attributes);
                $orderAddress->save();

                $attributes = [
                    'order_id' => $order->id,
                    'type' => \App\Enums\OrderAddress\Type::Bill,
                    'fname' => $faker->firstName(),
                    'lname' => $faker->lastName,
                    'fkana' => 'ナマエカナ',
                    'lkana' => 'ナマエセイ',
                    'tel' => $faker->phoneNumber,
                    'pref_id' => $faker->randomElement(\App\Models\Pref::all()->pluck('id')),
                    'zip' => $faker->numberBetween(1000000, 9999999),
                    'city' => $faker->city . $faker->citySuffix,
                    'town' => $faker->streetName . $faker->streetSuffix,
                    'address' => $faker->streetAddress,
                    'building' => $faker->buildingNumber,
                    'email' => $faker->email,
                ];

                $orderAddress = new \App\Models\OrderAddress($attributes);
                $orderAddress->save();
            }

            $this->call([
                OrderDetailsTableSeeder::class,
                OrderDetailUnitsTableSeeder::class,
            ]);

            // 合計金額を整合させる
            $unitSub = DB::table('order_detail_units')
                ->select([
                    'order_detail_units.order_detail_id',
                    DB::raw('SUM(order_detail_units.amount) as amount'),
                ])
                ->groupBy(['order_detail_units.order_detail_id']);

            $detailSub = DB::table('order_details')->select([
                'order_details.order_id',
                DB::raw('SUM(order_detail_units2.amount * order_details.retail_price) as total_price'),
            ])
            ->joinSub($unitSub, 'order_detail_units2', function (JoinClause $join) {
                return $join->on('order_details.id', '=', 'order_detail_units2.order_detail_id');
            })
            ->groupBy(['order_details.order_id']);

            DB::table('orders')->joinSub($detailSub, 'order_details2', function (JoinClause $join) {
                return $join->on('orders.id', '=', 'order_details2.order_id');
            })
            ->update([
                'orders.price' => DB::raw('order_details2.total_price + orders.fee + orders.delivery_fee - orders.use_point'),
                'updated_at' => DB::raw('NOW()'),
            ]);
        });
    }
}
