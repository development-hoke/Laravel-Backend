<?php

use Illuminate\Database\Seeder;

class HelpsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // helps
        factory(\App\Models\Help::class, 10)->create();
        // help_categories
        DB::table('help_categories')->insert([
            ['id' => 1, 'parent_id' => null, 'root_id' => 1, 'level' => 1, 'name' => 'アカウント関連', '_lft' => 1, '_rgt' => 12, 'sort' => 1],
            ['id' => 2, 'parent_id' => 1, 'root_id' => 1, 'level' => 2, 'name' => 'ログイン関連', '_lft' => 2, '_rgt' => 3, 'sort' => 2],
            ['id' => 3, 'parent_id' => 1, 'root_id' => 1, 'level' => 2, 'name' => 'プロフィール関連', '_lft' => 4, '_rgt' => 11, 'sort' => 3],
            ['id' => 4, 'parent_id' => 3, 'root_id' => 1, 'level' => 3, 'name' => 'プロフィール登録情報', '_lft' => 5, '_rgt' => 10, 'sort' => 4],
            ['id' => 5, 'parent_id' => 4, 'root_id' => 1, 'level' => 4, 'name' => '登録情報の変更', '_lft' => 6, '_rgt' => 9, 'sort' => 5],
            ['id' => 6, 'parent_id' => 5, 'root_id' => 1, 'level' => 5, 'name' => 'クレジットカード情報', '_lft' => 7, '_rgt' => 8, 'sort' => 6],
            ['id' => 7, 'parent_id' => null, 'root_id' => 7, 'level' => 1, 'name' => 'サイトの使い方関連', '_lft' => 13, '_rgt' => 18, 'sort' => 7],
            ['id' => 8, 'parent_id' => 7, 'root_id' => 7, 'level' => 2, 'name' => '商品の探し方', '_lft' => 14, '_rgt' => 17, 'sort' => 8],
            ['id' => 9, 'parent_id' => 8, 'root_id' => 7, 'level' => 3, 'name' => 'お気に入り登録', '_lft' => 15, '_rgt' => 16, 'sort' => 9],
            ['id' => 10, 'parent_id' => null, 'root_id' => 10, 'level' => 1, 'name' => '返品・交換関連', '_lft' => 19, '_rgt' => 22, 'sort' => 10],
            ['id' => 11, 'parent_id' => 10, 'root_id' => 10, 'level' => 2, 'name' => '返品・交換条件', '_lft' => 20, '_rgt' => 21, 'sort' => 11],
            ['id' => 12, 'parent_id' => null, 'root_id' => 12, 'level' => 1, 'name' => 'キャンペーン関連', '_lft' => 23, '_rgt' => 28, 'sort' => 12],
            ['id' => 13, 'parent_id' => 12, 'root_id' => 12, 'level' => 2, 'name' => '友達紹介キャンペーン', '_lft' => 24, '_rgt' => 25, 'sort' => 13],
            ['id' => 14, 'parent_id' => 12, 'root_id' => 12, 'level' => 2, 'name' => '新規入会キャンペーン', '_lft' => 26, '_rgt' => 27, 'sort' => 14],
            ['id' => 15, 'parent_id' => null, 'root_id' => 15, 'level' => 1, 'name' => 'おすすめ商品関連', '_lft' => 29, '_rgt' => 30, 'sort' => 15],
        ]);
        // help_category_relations
        factory(\App\Models\HelpCategoryRelation::class, 10)->create();
    }
}
