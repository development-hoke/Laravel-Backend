<?php

use Illuminate\Database\Seeder;

class ItemDetailIdentificationsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $itemDetails = \App\Models\ItemDetail::all();

        foreach ($itemDetails as $itemDetail) {
            foreach (range(0, 2) as $i) {
                if ($i > 0) {
                    if (rand(0, 1)) {
                        continue;
                    }
                }

                factory(\App\Models\ItemDetailIdentification::class)->create(['item_detail_id' => $itemDetail->id]);
            }
        }
    }
}
