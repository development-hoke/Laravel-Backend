<?php

use Illuminate\Database\Seeder;

class DestinationsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(\App\Models\Destination::class)->create([
            'member_id' => 1,
            'last_name' => '田中',
            'first_name' => '太郎',
            'last_name_kana' => 'たなか',
            'first_name_kana' => 'たろう',
            'phone' => '08012341234',
            'postal' => '111-1111',
            'pref_id' => 13,
            'address' => '中央区東日本橋1-6-9',
            'building' => 'グリーンパーク東日本橋２ ２０１',
        ]);
    }
}
