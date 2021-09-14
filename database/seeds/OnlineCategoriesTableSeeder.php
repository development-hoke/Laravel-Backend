<?php

use Illuminate\Database\Seeder;

class OnlineCategoriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $datas = [
            ['id' => 1, 'parent_id' => null, 'root_id' => 1, 'level' => 1, 'name' => 'トップス', 'sort' => 100],
            ['id' => 2, 'parent_id' => null, 'root_id' => 2, 'level' => 1, 'name' => 'スカート', 'sort' => 100],
            ['id' => 3, 'parent_id' => null, 'root_id' => 3, 'level' => 1, 'name' => 'パンツ', 'sort' => 100],
            ['id' => 4, 'parent_id' => null, 'root_id' => 4, 'level' => 1, 'name' => 'アウター', 'sort' => 100],
            ['id' => 5, 'parent_id' => null, 'root_id' => 5, 'level' => 1, 'name' => 'ワンピース', 'sort' => 100],
            ['id' => 6, 'parent_id' => null, 'root_id' => 6, 'level' => 1, 'name' => 'スーツ・セットアップ', 'sort' => 100],
            ['id' => 7, 'parent_id' => null, 'root_id' => 7, 'level' => 1, 'name' => 'オールインワン', 'sort' => 100],
            ['id' => 8, 'parent_id' => null, 'root_id' => 8, 'level' => 1, 'name' => 'バッグ', 'sort' => 100],
            ['id' => 9, 'parent_id' => null, 'root_id' => 9, 'level' => 1, 'name' => 'シューズ', 'sort' => 100],
            ['id' => 10, 'parent_id' => null, 'root_id' => 10, 'level' => 1, 'name' => 'アクセサリー', 'sort' => 100],
            ['id' => 11, 'parent_id' => null, 'root_id' => 11, 'level' => 1, 'name' => 'ストール', 'sort' => 100],
            ['id' => 12, 'parent_id' => null, 'root_id' => 12, 'level' => 1, 'name' => '小物', 'sort' => 100],
            ['id' => 13, 'parent_id' => null, 'root_id' => 13, 'level' => 1, 'name' => 'フォーマル', 'sort' => 100],

            ['id' => 14, 'parent_id' => 1, 'root_id' => 1, 'level' => 2, 'name' => 'ニット・セーター', 'sort' => 100],
            ['id' => 15, 'parent_id' => 1, 'root_id' => 1, 'level' => 2, 'name' => 'カットソー・Tシャツ', 'sort' => 100],
            ['id' => 16, 'parent_id' => 1, 'root_id' => 1, 'level' => 2, 'name' => 'パーカー・スエット', 'sort' => 100],
            ['id' => 17, 'parent_id' => 1, 'root_id' => 1, 'level' => 2, 'name' => 'シャツ・ブラウス', 'sort' => 100],
            ['id' => 18, 'parent_id' => 1, 'root_id' => 1, 'level' => 2, 'name' => 'カーディガン', 'sort' => 100],
            ['id' => 19, 'parent_id' => 1, 'root_id' => 1, 'level' => 2, 'name' => 'タンクトップ・キャミソール', 'sort' => 100],
            ['id' => 20, 'parent_id' => 1, 'root_id' => 1, 'level' => 2, 'name' => 'その他のトップス', 'sort' => 100],

            ['id' => 21, 'parent_id' => 2, 'root_id' => 2, 'level' => 2, 'name' => 'ミニスカート', 'sort' => 100],
            ['id' => 22, 'parent_id' => 2, 'root_id' => 2, 'level' => 2, 'name' => '膝丈スカート', 'sort' => 100],
            ['id' => 23, 'parent_id' => 2, 'root_id' => 2, 'level' => 2, 'name' => 'ロング・マキシ丈スカート', 'sort' => 100],
            ['id' => 24, 'parent_id' => 2, 'root_id' => 2, 'level' => 2, 'name' => 'ジャンバースカート', 'sort' => 100],
            ['id' => 25, 'parent_id' => 2, 'root_id' => 2, 'level' => 2, 'name' => 'その他のスカート', 'sort' => 100],

            ['id' => 26, 'parent_id' => 3, 'root_id' => 3, 'level' => 2, 'name' => 'テーパードパンツ', 'sort' => 100],
            ['id' => 27, 'parent_id' => 3, 'root_id' => 3, 'level' => 2, 'name' => 'ワイドパンツ', 'sort' => 100],
            ['id' => 28, 'parent_id' => 3, 'root_id' => 3, 'level' => 2, 'name' => 'デニムパンツ', 'sort' => 100],
            ['id' => 29, 'parent_id' => 3, 'root_id' => 3, 'level' => 2, 'name' => 'その他のパンツ', 'sort' => 100],

            ['id' => 30, 'parent_id' => 4, 'root_id' => 4, 'level' => 2, 'name' => 'ジャケット・ブルゾン', 'sort' => 100],
            ['id' => 31, 'parent_id' => 4, 'root_id' => 4, 'level' => 2, 'name' => 'コート', 'sort' => 100],
            ['id' => 32, 'parent_id' => 4, 'root_id' => 4, 'level' => 2, 'name' => 'その他のアウター', 'sort' => 100],

            ['id' => 33, 'parent_id' => 5, 'root_id' => 5, 'level' => 2, 'name' => 'ワンピース', 'sort' => 100],
            ['id' => 34, 'parent_id' => 5, 'root_id' => 5, 'level' => 2, 'name' => 'ニットワンピース', 'sort' => 100],
            ['id' => 35, 'parent_id' => 5, 'root_id' => 5, 'level' => 2, 'name' => 'ドレス', 'sort' => 100],

            ['id' => 36, 'parent_id' => 6, 'root_id' => 6, 'level' => 2, 'name' => 'スーツ', 'sort' => 100],
            ['id' => 37, 'parent_id' => 6, 'root_id' => 6, 'level' => 2, 'name' => 'セットアップ/トップス', 'sort' => 100],
            ['id' => 38, 'parent_id' => 6, 'root_id' => 6, 'level' => 2, 'name' => 'セットアップ/スカート', 'sort' => 100],
            ['id' => 39, 'parent_id' => 6, 'root_id' => 6, 'level' => 2, 'name' => 'セットアップ/パンツ', 'sort' => 100],
            ['id' => 40, 'parent_id' => 6, 'root_id' => 6, 'level' => 2, 'name' => 'セットアップ/ジャケット', 'sort' => 100],
            ['id' => 41, 'parent_id' => 6, 'root_id' => 6, 'level' => 2, 'name' => 'セットアップ/ワンピース', 'sort' => 100],
            ['id' => 42, 'parent_id' => 6, 'root_id' => 6, 'level' => 2, 'name' => 'その他のスーツ・セットアップ', 'sort' => 100],

            ['id' => 43, 'parent_id' => 7, 'root_id' => 7, 'level' => 2, 'name' => 'オールインワン', 'sort' => 100],
            ['id' => 44, 'parent_id' => 7, 'root_id' => 7, 'level' => 2, 'name' => 'オーバーオール・サロペット', 'sort' => 100],

            ['id' => 45, 'parent_id' => 8, 'root_id' => 8, 'level' => 2, 'name' => 'トートバッグ', 'sort' => 100],
            ['id' => 46, 'parent_id' => 8, 'root_id' => 8, 'level' => 2, 'name' => 'ショルダーバッグ', 'sort' => 100],
            ['id' => 47, 'parent_id' => 8, 'root_id' => 8, 'level' => 2, 'name' => 'ハンドバッグ', 'sort' => 100],
            ['id' => 48, 'parent_id' => 8, 'root_id' => 8, 'level' => 2, 'name' => 'ボディバッグ', 'sort' => 100],
            ['id' => 49, 'parent_id' => 8, 'root_id' => 8, 'level' => 2, 'name' => 'リュック/バックパック', 'sort' => 100],
            ['id' => 50, 'parent_id' => 8, 'root_id' => 8, 'level' => 2, 'name' => 'クラッチバッグ', 'sort' => 100],
            ['id' => 51, 'parent_id' => 8, 'root_id' => 8, 'level' => 2, 'name' => 'かごバッグ', 'sort' => 100],
            ['id' => 52, 'parent_id' => 8, 'root_id' => 8, 'level' => 2, 'name' => 'その他のバッグ', 'sort' => 100],

            ['id' => 53, 'parent_id' => 9, 'root_id' => 9, 'level' => 2, 'name' => 'パンプス', 'sort' => 100],
            ['id' => 54, 'parent_id' => 9, 'root_id' => 9, 'level' => 2, 'name' => 'サンダル・ミュール', 'sort' => 100],
            ['id' => 55, 'parent_id' => 9, 'root_id' => 9, 'level' => 2, 'name' => 'スニーカー', 'sort' => 100],
            ['id' => 56, 'parent_id' => 9, 'root_id' => 9, 'level' => 2, 'name' => 'ブーツ・ブーティ', 'sort' => 100],
            ['id' => 57, 'parent_id' => 9, 'root_id' => 9, 'level' => 2, 'name' => 'その他シューズ', 'sort' => 100],

            ['id' => 58, 'parent_id' => 10, 'root_id' => 10, 'level' => 2, 'name' => 'ネックレス', 'sort' => 100],
            ['id' => 59, 'parent_id' => 10, 'root_id' => 10, 'level' => 2, 'name' => 'ピアス', 'sort' => 100],
            ['id' => 60, 'parent_id' => 10, 'root_id' => 10, 'level' => 2, 'name' => 'イヤリング', 'sort' => 100],
            ['id' => 61, 'parent_id' => 10, 'root_id' => 10, 'level' => 2, 'name' => 'イヤーカフ', 'sort' => 100],
            ['id' => 62, 'parent_id' => 10, 'root_id' => 10, 'level' => 2, 'name' => 'ブレスレット/バングル', 'sort' => 100],
            ['id' => 63, 'parent_id' => 10, 'root_id' => 10, 'level' => 2, 'name' => 'リング', 'sort' => 100],
            ['id' => 64, 'parent_id' => 10, 'root_id' => 10, 'level' => 2, 'name' => 'ヘアアクセサリー', 'sort' => 100],
            ['id' => 65, 'parent_id' => 10, 'root_id' => 10, 'level' => 2, 'name' => 'その他のアクセサリー', 'sort' => 100],

            ['id' => 66, 'parent_id' => 11, 'root_id' => 11, 'level' => 2, 'name' => 'ストール', 'sort' => 100],
            ['id' => 67, 'parent_id' => 11, 'root_id' => 11, 'level' => 2, 'name' => 'スヌード', 'sort' => 100],
            ['id' => 68, 'parent_id' => 11, 'root_id' => 11, 'level' => 2, 'name' => 'スカーフ・バンダナ', 'sort' => 100],
            ['id' => 69, 'parent_id' => 11, 'root_id' => 11, 'level' => 2, 'name' => 'その他', 'sort' => 100],

            ['id' => 70, 'parent_id' => 12, 'root_id' => 12, 'level' => 2, 'name' => 'ベルト', 'sort' => 100],
            ['id' => 71, 'parent_id' => 12, 'root_id' => 12, 'level' => 2, 'name' => '財布/コインケース', 'sort' => 100],
            ['id' => 72, 'parent_id' => 12, 'root_id' => 12, 'level' => 2, 'name' => 'モバイルアクセサリー', 'sort' => 100],
            ['id' => 73, 'parent_id' => 12, 'root_id' => 12, 'level' => 2, 'name' => '帽子', 'sort' => 100],
            ['id' => 74, 'parent_id' => 12, 'root_id' => 12, 'level' => 2, 'name' => '手袋/グローブ', 'sort' => 100],
            ['id' => 75, 'parent_id' => 12, 'root_id' => 12, 'level' => 2, 'name' => 'チャーム', 'sort' => 100],
            ['id' => 76, 'parent_id' => 12, 'root_id' => 12, 'level' => 2, 'name' => 'その他小物', 'sort' => 100],

            ['id' => 77, 'parent_id' => 13, 'root_id' => 13, 'level' => 2, 'name' => 'ドレスアイテム', 'sort' => 100],
            ['id' => 78, 'parent_id' => 13, 'root_id' => 13, 'level' => 2, 'name' => 'セレモニーアイテム', 'sort' => 100],

            ['id' => 79, 'parent_id' => 77, 'root_id' => 13, 'level' => 3, 'name' => 'ドレス(D)', 'sort' => 100],
            ['id' => 80, 'parent_id' => 77, 'root_id' => 13, 'level' => 3, 'name' => 'オールインワン(D)', 'sort' => 100],
            ['id' => 81, 'parent_id' => 77, 'root_id' => 13, 'level' => 3, 'name' => 'スーツ・セットアップ(D)', 'sort' => 100],
            ['id' => 82, 'parent_id' => 77, 'root_id' => 13, 'level' => 3, 'name' => 'アウター(D)', 'sort' => 100],
            ['id' => 83, 'parent_id' => 77, 'root_id' => 13, 'level' => 3, 'name' => 'ストール(D)', 'sort' => 100],
            ['id' => 84, 'parent_id' => 77, 'root_id' => 13, 'level' => 3, 'name' => 'バッグ(D)', 'sort' => 100],
            ['id' => 85, 'parent_id' => 77, 'root_id' => 13, 'level' => 3, 'name' => 'シューズ(D)', 'sort' => 100],
            ['id' => 86, 'parent_id' => 77, 'root_id' => 13, 'level' => 3, 'name' => 'アクセサリー(D)', 'sort' => 100],
            ['id' => 87, 'parent_id' => 77, 'root_id' => 13, 'level' => 3, 'name' => 'その他(D)', 'sort' => 100],

            ['id' => 88, 'parent_id' => 78, 'root_id' => 13, 'level' => 3, 'name' => 'ワンピース(C)', 'sort' => 100],
            ['id' => 89, 'parent_id' => 78, 'root_id' => 13, 'level' => 3, 'name' => 'スーツ・セットアップ(C)', 'sort' => 100],
            ['id' => 90, 'parent_id' => 78, 'root_id' => 13, 'level' => 3, 'name' => 'アウター(C)', 'sort' => 100],
            ['id' => 91, 'parent_id' => 78, 'root_id' => 13, 'level' => 3, 'name' => 'トップス(C)', 'sort' => 100],
            ['id' => 92, 'parent_id' => 78, 'root_id' => 13, 'level' => 3, 'name' => 'スカート(C)', 'sort' => 100],
            ['id' => 93, 'parent_id' => 78, 'root_id' => 13, 'level' => 3, 'name' => 'パンツ(C)', 'sort' => 100],
            ['id' => 94, 'parent_id' => 78, 'root_id' => 13, 'level' => 3, 'name' => 'バッグ(C)', 'sort' => 100],
            ['id' => 95, 'parent_id' => 78, 'root_id' => 13, 'level' => 3, 'name' => 'シューズ(C)', 'sort' => 100],
            ['id' => 96, 'parent_id' => 78, 'root_id' => 13, 'level' => 3, 'name' => 'アクセサリー(C)', 'sort' => 100],
            ['id' => 97, 'parent_id' => 78, 'root_id' => 13, 'level' => 3, 'name' => 'その他(C)', 'sort' => 100],
        ];

        \DB::table('online_categories')->insert($datas);
    }
}
