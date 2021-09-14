<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ItemsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::transaction(function () {
            factory(\App\Models\Item::class, 100)->create();
            factory(\App\Models\ItemDetail::class, 360)->create();
            $this->call(ItemDetailIdentificationsTableSeeder::class);
            factory(\App\Models\ItemDetailStore::class, 1080)->create();
        });
    }
}
