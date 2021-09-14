<?php

use Illuminate\Database\Seeder;

class ItemImagesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(\App\Models\ItemImage::class, 360)->create();
    }
}
