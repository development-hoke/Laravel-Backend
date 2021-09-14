<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OrderDetailUnitsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::transaction(function () {
            $orderDetails = \App\Models\OrderDetail::all();

            foreach ($orderDetails as $orderDetail) {
                foreach (range(0, 2) as $i) {
                    if ($i > 0) {
                        if (rand(0, 1)) {
                            continue;
                        }
                    }

                    $faker = \Faker\Factory::create('ja_JP');
                    $itemDetailIdents = \App\Models\ItemDetailIdentification::where('item_detail_id', $orderDetail->item_detail_id)->get();
                    $itemDetailIdent = $faker->randomElement($itemDetailIdents);

                    \App\Models\OrderDetailUnit::create([
                        'order_detail_id' => $orderDetail->id,
                        'item_detail_identification_id' => $itemDetailIdent->id,
                        'amount' => $faker->numberBetween(1, 5),
                    ]);
                }
            }
        });
    }
}
