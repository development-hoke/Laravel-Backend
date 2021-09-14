<?php

use App\Enums\Brand\Category;
use App\Enums\Brand\Section;
use App\Enums\Common\StoreBrand;
use Illuminate\Database\Seeder;

class BrandsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $datas = [
            ['store_brand' => 'Vin', 'section' => 'Origin', 'name' => 'SCOTCLUB', 'kana' => 'スコットクラブ', 'category' => 'Nothing'],
            ['store_brand' => 'Vin', 'section' => 'Origin', 'name' => 'Grandtable', 'kana' => 'グランターブル', 'category' => 'Nothing'],
            ['store_brand' => 'Vin', 'section' => 'Origin', 'name' => 'Rire Fete', 'kana' => 'リルフェテ', 'category' => 'Dress'],
            ['store_brand' => 'FENNEL', 'section' => 'Origin', 'name' => 'FENNEL', 'kana' => 'フェンネル', 'category' => 'Nothing'],
            ['store_brand' => 'FENNEL', 'section' => 'Origin', 'name' => 'Bouchon', 'kana' => 'ブション', 'category' => 'Nothing'],
            ['store_brand' => 'LASUD', 'section' => 'Origin', 'name' => 'LASUD', 'kana' => 'ラシュッド', 'category' => 'Nothing'],
            ['store_brand' => 'Aga', 'section' => 'Origin', 'name' => 'Aga', 'kana' => 'アーガ', 'category' => 'Nothing'],
            ['store_brand' => 'Aga', 'section' => 'Origin', 'name' => 'Aga Black', 'kana' => 'アーガブラック', 'category' => 'Dress'],
            ['store_brand' => 'RADIATE', 'section' => 'Origin', 'name' => 'RADIATE', 'kana' => 'ラディエイト', 'category' => 'Nothing'],
            ['store_brand' => 'RADIATE', 'section' => 'Origin', 'name' => 'radiate the lifedress', 'kana' => 'ラディエイトザライフドレス', 'category' => 'Dress'],
            ['store_brand' => 'CLOVE', 'section' => 'Origin', 'name' => 'soeur7', 'kana' => 'スール', 'category' => 'Nothing'],
            ['store_brand' => 'CLOVE', 'section' => 'Origin', 'name' => 'MIREPOIX', 'kana' => 'ミルポア', 'category' => 'Nothing'],
            ['store_brand' => 'CLOVE', 'section' => 'Origin', 'name' => 'YORT', 'kana' => 'ヨート', 'category' => 'Nothing'],
            ['store_brand' => 'Vin', 'section' => 'Origin', 'name' => 'PECHINCHAR', 'kana' => 'ペシンシャ', 'category' => 'GoodsOrAccessory'],
            ['store_brand' => 'LASUD', 'section' => 'Origin', 'name' => 'nouer', 'kana' => 'ヌエール', 'category' => 'GoodsOrAccessory'],
            ['store_brand' => 'FENNEL', 'section' => 'Origin', 'name' => 'Ample', 'kana' => 'アンプル', 'category' => 'Nothing'],
            ['store_brand' => 'FENNEL', 'section' => 'Origin', 'name' => 'PASSIONE', 'kana' => 'パシオーネ', 'category' => 'Nothing'],
            ['store_brand' => 'Aga', 'section' => 'Origin', 'name' => 'Aga×NORITAKE', 'kana' => 'アーガ×ノリタケ', 'category' => 'Nothing'],
            ['store_brand' => 'CLOVE', 'section' => 'Origin', 'name' => 'MICA＆DEAL', 'kana' => 'マイカアンドディール', 'category' => 'Nothing'],
            ['store_brand' => 'MEDOC', 'section' => 'Other', 'name' => 'HASHIBAMI', 'kana' => 'ハシバミ', 'category' => 'GoodsOrAccessory'],
            ['store_brand' => 'MEDOC', 'section' => 'Other', 'name' => 'Direction to get mosh', 'kana' => 'ディレクション トゥ ゲット モッシュ', 'category' => 'GoodsOrAccessory'],
            ['store_brand' => 'FENNEL', 'section' => 'Origin', 'name' => 'Dignite collier', 'kana' => 'ディニテ　コリエ', 'category' => 'Nothing'],
            ['store_brand' => 'FENNEL', 'section' => 'Origin', 'name' => 'De', 'kana' => 'ディー', 'category' => 'Nothing'],
            ['store_brand' => 'FENNEL', 'section' => 'Origin', 'name' => 'praia', 'kana' => 'プライア', 'category' => 'Nothing'],
            ['store_brand' => 'Vin', 'section' => 'Origin', 'name' => 'Petit Poudre', 'kana' => 'ﾌﾟﾁ・ﾌﾟｰﾄﾞﾙ', 'category' => 'Nothing'],
            ['store_brand' => 'MEDOC', 'section' => 'Other', 'name' => 'HALIN×nouer', 'kana' => 'ハリン×ヌエール', 'category' => 'GoodsOrAccessory'],
            ['store_brand' => 'Vin', 'section' => 'Other', 'name' => 'Stephanie', 'kana' => 'ステファニエ', 'category' => 'GoodsOrAccessory'],
            ['store_brand' => 'MEDOC', 'section' => 'Other', 'name' => 'meline', 'kana' => 'メリーヌ', 'category' => 'GoodsOrAccessory'],
            ['store_brand' => 'MEDOC', 'section' => 'Other', 'name' => 'VEGE×nouer', 'kana' => 'ベジ×ヌエール', 'category' => 'GoodsOrAccessory'],
            ['store_brand' => 'FENNEL', 'section' => 'Origin', 'name' => 'TORRAZZO DONNA', 'kana' => 'トラッゾドンナ', 'category' => 'Nothing'],
            ['store_brand' => 'Vin', 'section' => 'Origin', 'name' => 'LILLY LYNQUE', 'kana' => 'リリー リン', 'category' => 'Nothing'],
            ['store_brand' => 'Vin', 'section' => 'Origin', 'name' => 'BLANC', 'kana' => 'ブラン', 'category' => 'Nothing'],
            ['store_brand' => 'RADIATE', 'section' => 'Other', 'name' => 'Audrey and John Wad', 'kana' => 'オードリーアンドジョンワッド', 'category' => 'Nothing'],
            ['store_brand' => 'FENNEL', 'section' => 'Origin', 'name' => 'MONiLE', 'kana' => 'モニーレ', 'category' => 'Nothing'],
            ['store_brand' => 'Vin', 'section' => 'Origin', 'name' => 'Petirobe', 'kana' => 'プチローブ', 'category' => 'Nothing'],
            ['store_brand' => 'FENNEL', 'section' => 'Origin', 'name' => 'Doubleface Tokyo', 'kana' => 'ダブルフェース トーキョー', 'category' => 'Nothing'],
            ['store_brand' => 'FENNEL', 'section' => 'Origin', 'name' => 'hybrid modem', 'kana' => 'ハイブリッド・モデム', 'category' => 'Nothing'],
            ['store_brand' => 'FENNEL', 'section' => 'Origin', 'name' => 'CYAN', 'kana' => 'シアン', 'category' => 'Nothing'],
            ['store_brand' => 'Aga', 'section' => 'Other', 'name' => 'equo', 'kana' => 'エクオ', 'category' => 'Nothing'],
            ['store_brand' => 'Aga', 'section' => 'Origin', 'name' => 'Odour×Aga', 'kana' => 'オウダ―×アーガ', 'category' => 'Nothing'],
            ['store_brand' => 'Aga', 'section' => 'Other', 'name' => 'Sara mallika', 'kana' => 'サラマリカ', 'category' => 'Nothing'],
            ['store_brand' => 'Aga', 'section' => 'Other', 'name' => 'KOFTA', 'kana' => 'コフタ', 'category' => 'Nothing'],
            ['store_brand' => 'RADIATE', 'section' => 'Origin', 'name' => 'resil', 'kana' => 'レジル', 'category' => 'Nothing'],
            ['store_brand' => 'RADIATE', 'section' => 'Origin', 'name' => 'RADIATE×NORITAKE', 'kana' => 'ラディエイト×ノリタケ', 'category' => 'Nothing'],
            ['store_brand' => 'RADIATE', 'section' => 'Other', 'name' => 'kaene', 'kana' => 'カエン', 'category' => 'Dress'],
            ['store_brand' => 'RADIATE', 'section' => 'Other', 'name' => 'troisiemechaco', 'kana' => 'トロワズィエムチャコ', 'category' => 'Dress'],
            ['store_brand' => 'RADIATE', 'section' => 'Other', 'name' => 'GOOD ROCK SPEED', 'kana' => 'グッドロックスピード', 'category' => 'Nothing'],
            ['store_brand' => 'MEDOC', 'section' => 'Other', 'name' => 'Beau’re×nouer', 'kana' => 'ビュレ×ヌエール', 'category' => 'GoodsOrAccessory'],
            ['store_brand' => 'MEDOC', 'section' => 'Other', 'name' => 'tov×nouer', 'kana' => 'トーヴ×ヌエール', 'category' => 'GoodsOrAccessory'],
            ['store_brand' => 'MEDOC', 'section' => 'Other', 'name' => 'story.×nouer', 'kana' => 'ストーリー×ヌエール', 'category' => 'GoodsOrAccessory'],
            ['store_brand' => 'Vin', 'section' => 'Origin', 'name' => 'troisiemechaco', 'kana' => 'トロワズィエムチャコ', 'category' => 'Nothing'],
            ['store_brand' => 'Vin', 'section' => 'Origin', 'name' => 'Loungie', 'kana' => 'ラウンジ', 'category' => 'Nothing'],
            ['store_brand' => 'FENNEL', 'section' => 'Origin', 'name' => 'Days', 'kana' => 'デイズ', 'category' => 'Nothing'],
            ['store_brand' => 'MEDOC', 'section' => 'Other', 'name' => 'ROKOTA×nouer', 'kana' => 'ロコタ×ヌエール', 'category' => 'GoodsOrAccessory'],
            ['store_brand' => 'RADIATE', 'section' => 'Origin', 'name' => 'resil×NORITAKE', 'kana' => 'レジル×ノリタケ', 'category' => 'Nothing'],
            ['store_brand' => 'FENNEL', 'section' => 'Origin', 'name' => 'malla', 'kana' => 'マーラ', 'category' => 'Nothing'],
            ['store_brand' => 'Vin', 'section' => 'Origin', 'name' => 'Rdevushka', 'kana' => 'ジェブシュカ', 'category' => 'Nothing'],
            ['store_brand' => 'MEDOC', 'section' => 'Other', 'name' => 'Ampersand', 'kana' => 'アンパサンド', 'category' => 'GoodsOrAccessory'],
            ['store_brand' => 'MEDOC', 'section' => 'Other', 'name' => 'MARCO MASI', 'kana' => 'マルコマージ', 'category' => 'GoodsOrAccessory'],
            ['store_brand' => 'MEDOC', 'section' => 'Other', 'name' => 'ELVIO ZANON', 'kana' => 'エルビオザノン', 'category' => 'GoodsOrAccessory'],
            ['store_brand' => 'MEDOC', 'section' => 'Other', 'name' => 'JETEE', 'kana' => 'ジュテ', 'category' => 'GoodsOrAccessory'],
            ['store_brand' => 'MEDOC', 'section' => 'Other', 'name' => 'ROKOTA×PECHINCHAR', 'kana' => 'ロコタ×ペシンシャ', 'category' => 'GoodsOrAccessory'],
            ['store_brand' => 'FENNEL', 'section' => 'Origin', 'name' => 'GALENA', 'kana' => 'ガレナ', 'category' => 'Nothing'],
            ['store_brand' => 'FENNEL', 'section' => 'Origin', 'name' => 'FONTANA GRANDE', 'kana' => 'フォンタナグランデ', 'category' => 'Nothing'],
            ['store_brand' => 'Vin', 'section' => 'Origin', 'name' => 'B7', 'kana' => 'べーセッツ', 'category' => 'Nothing'],
            ['store_brand' => 'MEDOC', 'section' => 'Other', 'name' => 'FIORELLI', 'kana' => 'フィオレッリ', 'category' => 'GoodsOrAccessory'],
            ['store_brand' => 'MEDOC', 'section' => 'Other', 'name' => 'ATELIER BRUGGE', 'kana' => 'アトリエブルージュ', 'category' => 'GoodsOrAccessory'],
            ['store_brand' => 'MEDOC', 'section' => 'Other', 'name' => 'LAURA DI MAGGIO', 'kana' => 'ラウラディマッジオ', 'category' => 'GoodsOrAccessory'],
        ];

        foreach ($datas as &$data) {
            $data['store_brand'] = StoreBrand::fromKey(\Str::studly(strtolower($data['store_brand'])))->value;
            $data['section'] = Section::fromKey($data['section'])->value;
            $data['category'] = Category::fromKey($data['category'])->value;
            $data['created_at'] = $data['updated_at'] = date('Y-m-d H:i:s');
        }
        \DB::table('brands')->insert($datas);
    }
}
