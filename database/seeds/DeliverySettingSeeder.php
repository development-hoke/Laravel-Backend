<?php

use Illuminate\Database\Seeder;

class DeliverySettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\Models\DeliverySetting::create([
            'delivery_condition' => 10000,
            'delivery_price' => 0,
        ]);
    }
}
