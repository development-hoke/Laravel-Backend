<?php

use Illuminate\Database\Seeder;

class OnlineTagsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $datas = [
            ['id' => 1, 'parent_id' => null, 'name' => 'シーズン', 'sort' => 100],
            ['id' => 2, 'parent_id' => null, 'name' => '季節素材', 'sort' => 100],
            ['id' => 3, 'parent_id' => null, 'name' => '袖', 'sort' => 100],
            ['id' => 4, 'parent_id' => null, 'name' => '実験', 'sort' => 100],
            ['id' => 5, 'parent_id' => null, 'name' => 'アウトレット', 'sort' => 100],

            ['id' => 6, 'parent_id' => 1, 'name' => '68SS', 'sort' => 100],
            ['id' => 7, 'parent_id' => 1, 'name' => '68AW', 'sort' => 100],
            ['id' => 8, 'parent_id' => 1, 'name' => '69SS', 'sort' => 100],

            ['id' => 9, 'parent_id' => 2, 'name' => '麻素材', 'sort' => 100],
            ['id' => 10, 'parent_id' => 2, 'name' => 'ウール素材', 'sort' => 100],

            ['id' => 11, 'parent_id' => 3, 'name' => 'ノースリーブ', 'sort' => 100],
            ['id' => 12, 'parent_id' => 3, 'name' => '半袖', 'sort' => 100],
            ['id' => 13, 'parent_id' => 3, 'name' => '5～7部', 'sort' => 100],
            ['id' => 14, 'parent_id' => 3, 'name' => '長袖', 'sort' => 100],

            ['id' => 15, 'parent_id' => 4, 'name' => '実験', 'sort' => 100],

            ['id' => 16, 'parent_id' => 5, 'name' => '2021GW', 'sort' => 100],
            ['id' => 17, 'parent_id' => 5, 'name' => '2021BF', 'sort' => 100],

            ['id' => 18, 'parent_id' => 1, 'name' => '67AW', 'sort' => 100],
            ['id' => 19, 'parent_id' => 1, 'name' => '67SS', 'sort' => 100],
        ];

        \DB::table('online_tags')->insert($datas);
    }
}
