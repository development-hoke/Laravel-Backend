<?php

use Illuminate\Database\Seeder;

class TopContentsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     *
     * @SuppressWarnings(PHPMD)
     */
    public function run()
    {
        foreach ([
            [
                'store_brand' => \App\Enums\Common\StoreBrand::Medoc,
                'main_visuals' => [
                    [
                        'pc_path' => 'https://placehold.jp/150x150.png',
                        'sp_path' => 'http://placehold.jp/24/cc9999/993333/150x100.png',
                        'status' => 0,
                        'sort' => 1,
                    ],
                    [
                        'pc_path' => 'https://placehold.jp/100x150.png',
                        'sp_path' => 'http://placehold.jp/24/808000/993333/150x100.png',
                        'status' => 1,
                        'sort' => 2,
                    ],
                ],
                'new_items' => [
                    [
                        'item_id' => factory(\App\Models\Item::class)->create()->id,
                        'status' => 1,
                        'sort' => 1,
                    ],
                    [
                        'item_id' => factory(\App\Models\Item::class)->create()->id,
                        'status' => 0,
                        'sort' => 2,
                    ],
                ],
                'pickups' => [
                    [
                        'item_id' => factory(\App\Models\Item::class)->create()->id,
                        'status' => 1,
                        'sort' => 1,
                    ],
                ],
                'background_color' => 'cc9999',
                'features' => [
                    [
                        'item_id' => factory(\App\Models\Item::class)->create()->id,
                        'status' => 1,
                        'sort' => 1,
                    ],
                ],
                'news' => [
                    [
                        'item_id' => factory(\App\Models\Item::class)->create()->id,
                        'status' => 1,
                        'sort' => 1,
                    ],
                    [
                        'item_id' => factory(\App\Models\Item::class)->create()->id,
                        'status' => 0,
                        'sort' => 2,
                    ],
                ],
                'styling_sort' => \App\Enums\TopContent\StylingSort::Pv,
                'stylings' => null,
            ],
            [
                'store_brand' => \App\Enums\Common\StoreBrand::Lasud,
                'main_visuals' => [
                    [
                        'pc_path' => 'https://placehold.jp/140x140.png',
                        'sp_path' => 'http://placehold.jp/24/008b8b/99333/150x100.png',
                        'status' => 1,
                        'sort' => 1,
                    ],
                ],
                'new_items' => [
                    [
                        'item_id' => factory(\App\Models\Item::class)->create()->id,
                        'status' => 1,
                        'sort' => 1,
                    ],
                    [
                        'item_id' => factory(\App\Models\Item::class)->create()->id,
                        'status' => 0,
                        'sort' => 2,
                    ],
                ],
                'pickups' => [
                    [
                        'item_id' => factory(\App\Models\Item::class)->create()->id,
                        'status' => 1,
                        'sort' => 1,
                    ],
                    [
                        'item_id' => factory(\App\Models\Item::class)->create()->id,
                        'status' => 0,
                        'sort' => 2,
                    ],
                ],
                'background_color' => '008b8b',
                'features' => [
                    [
                        'plan_id' => factory(\App\Models\Plan::class)->create()->id,
                        'status' => 1,
                        'sort' => 1,
                    ],
                    [
                        'plan_id' => factory(\App\Models\Plan::class)->create()->id,
                        'status' => 0,
                        'sort' => 2,
                    ],
                ],
                'news' => [
                    [
                        'plan_id' => factory(\App\Models\Plan::class)->create()->id,
                        'status' => 1,
                        'sort' => 1,
                    ],
                ],
                'styling_sort' => \App\Enums\TopContent\StylingSort::Manual,
                'stylings' => [
                    [
                        'styling_id' => factory(\App\Models\Styling::class)->create()->id,
                        'sort' => 1,
                    ],
                ],
            ],
            [
                'store_brand' => \App\Enums\Common\StoreBrand::Vin,
                'main_visuals' => [
                    [
                        'pc_path' => 'https://placehold.jp/130x130.png',
                        'sp_path' => 'http://placehold.jp/24/fffacd/99333/150x100.png',
                        'status' => 1,
                        'sort' => 1,
                    ],
                ],
                'new_items' => [
                    [
                        'item_id' => factory(\App\Models\Item::class)->create()->id,
                        'status' => 1,
                        'sort' => 1,
                    ],
                    [
                        'item_id' => factory(\App\Models\Item::class)->create()->id,
                        'status' => 0,
                        'sort' => 2,
                    ],
                    [
                        'item_id' => factory(\App\Models\Item::class)->create()->id,
                        'status' => 1,
                        'sort' => 3,
                    ],
                ],
                'pickups' => [
                    [
                        'item_id' => factory(\App\Models\Item::class)->create()->id,
                        'status' => 1,
                        'sort' => 1,
                    ],
                    [
                        'item_id' => factory(\App\Models\Item::class)->create()->id,
                        'status' => 0,
                        'sort' => 2,
                    ],
                ],
                'background_color' => 'fffacd',
                'features' => [
                    [
                        'item_id' => factory(\App\Models\Item::class)->create()->id,
                        'status' => 1,
                        'sort' => 1,
                    ],
                ],
                'news' => [
                    [
                        'item_id' => factory(\App\Models\Item::class)->create()->id,
                        'status' => 1,
                        'sort' => 1,
                    ],
                ],
                'styling_sort' => \App\Enums\TopContent\StylingSort::Manual,
                'stylings' => [
                    [
                        'styling_id' => factory(\App\Models\Styling::class)->create()->id,
                        'sort' => 1,
                    ],
                ],
            ],
            [
                'store_brand' => \App\Enums\Common\StoreBrand::Aga,
                'main_visuals' => [
                    [
                        'pc_path' => 'https://placehold.jp/120x120.png',
                        'sp_path' => 'http://placehold.jp/24/4682b4/99333/150x100.png',
                        'status' => 1,
                        'sort' => 1,
                    ],
                ],
                'new_items' => [
                    [
                        'item_id' => factory(\App\Models\Item::class)->create()->id,
                        'status' => 1,
                        'sort' => 1,
                    ],
                    [
                        'item_id' => factory(\App\Models\Item::class)->create()->id,
                        'status' => 0,
                        'sort' => 2,
                    ],
                    [
                        'item_id' => factory(\App\Models\Item::class)->create()->id,
                        'status' => 1,
                        'sort' => 3,
                    ],
                    [
                        'item_id' => factory(\App\Models\Item::class)->create()->id,
                        'status' => 1,
                        'sort' => 4,
                    ],
                ],
                'pickups' => [
                    [
                        'item_id' => factory(\App\Models\Item::class)->create()->id,
                        'status' => 1,
                        'sort' => 1,
                    ],
                    [
                        'item_id' => factory(\App\Models\Item::class)->create()->id,
                        'status' => 0,
                        'sort' => 2,
                    ],
                    [
                        'item_id' => factory(\App\Models\Item::class)->create()->id,
                        'status' => 1,
                        'sort' => 3,
                    ],
                    [
                        'item_id' => factory(\App\Models\Item::class)->create()->id,
                        'status' => 1,
                        'sort' => 4,
                    ],
                ],
                'background_color' => '4682b4',
                'features' => [
                    [
                        'item_id' => factory(\App\Models\Item::class)->create()->id,
                        'status' => 1,
                        'sort' => 1,
                    ],
                ],
                'news' => [
                    [
                        'item_id' => factory(\App\Models\Item::class)->create()->id,
                        'status' => 1,
                        'sort' => 1,
                    ],
                ],
                'styling_sort' => \App\Enums\TopContent\StylingSort::Manual,
                'stylings' => [
                    [
                        'styling_id' => factory(\App\Models\Styling::class)->create()->id,
                        'sort' => 1,
                    ],
                ],
            ],
            [
                'store_brand' => \App\Enums\Common\StoreBrand::Fennel,
                'main_visuals' => [
                    [
                        'pc_path' => 'https://placehold.jp/110x110.png',
                        'sp_path' => 'http://placehold.jp/24/ff4500/99333/150x100.png',
                        'status' => 1,
                        'sort' => 1,
                    ],
                ],
                'new_items' => [
                    [
                        'item_id' => factory(\App\Models\Item::class)->create()->id,
                        'status' => 1,
                        'sort' => 1,
                    ],
                ],
                'pickups' => [
                    [
                        'item_id' => factory(\App\Models\Item::class)->create()->id,
                        'status' => 1,
                        'sort' => 1,
                    ],
                ],
                'background_color' => 'ff4500',
                'features' => [
                    [
                        'item_id' => factory(\App\Models\Item::class)->create()->id,
                        'status' => 1,
                        'sort' => 1,
                    ],
                    [
                        'item_id' => factory(\App\Models\Item::class)->create()->id,
                        'status' => 0,
                        'sort' => 2,
                    ],
                    [
                        'item_id' => factory(\App\Models\Item::class)->create()->id,
                        'status' => 1,
                        'sort' => 3,
                    ],
                    [
                        'item_id' => factory(\App\Models\Item::class)->create()->id,
                        'status' => 1,
                        'sort' => 4,
                    ],
                ],
                'news' => [
                    [
                        'item_id' => factory(\App\Models\Item::class)->create()->id,
                        'status' => 1,
                        'sort' => 1,
                    ],
                    [
                        'item_id' => factory(\App\Models\Item::class)->create()->id,
                        'status' => 0,
                        'sort' => 2,
                    ],
                ],
                'styling_sort' => \App\Enums\TopContent\StylingSort::Manual,
                'stylings' => [
                    [
                        'styling_id' => factory(\App\Models\Styling::class)->create()->id,
                        'sort' => 1,
                    ],
                ],
            ],
            [
                'store_brand' => \App\Enums\Common\StoreBrand::Radiate,
                'main_visuals' => [
                    [
                        'pc_path' => 'https://placehold.jp/100x100.png',
                        'sp_path' => 'http://placehold.jp/24/00008b/99333/150x100.png',
                        'status' => 1,
                        'sort' => 1,
                    ],
                ],
                'new_items' => [
                    [
                        'item_id' => factory(\App\Models\Item::class)->create()->id,
                        'status' => 1,
                        'sort' => 1,
                    ],
                ],
                'pickups' => [
                    [
                        'item_id' => factory(\App\Models\Item::class)->create()->id,
                        'status' => 1,
                        'sort' => 1,
                    ],
                ],
                'background_color' => '00008b',
                'features' => [
                    [
                        'item_id' => factory(\App\Models\Item::class)->create()->id,
                        'status' => 1,
                        'sort' => 1,
                    ],
                ],
                'news' => [
                    [
                        'item_id' => factory(\App\Models\Item::class)->create()->id,
                        'status' => 1,
                        'sort' => 1,
                    ],
                ],
                'styling_sort' => \App\Enums\TopContent\StylingSort::Manual,
                'stylings' => [
                    [
                        'styling_id' => factory(\App\Models\Styling::class)->create()->id,
                        'sort' => 1,
                    ],
                ],
            ],
            [
                'store_brand' => \App\Enums\Common\StoreBrand::Clove,
                'main_visuals' => [
                    [
                        'pc_path' => 'https://placehold.jp/170x170.png',
                        'sp_path' => 'http://placehold.jp/24/6a5acd/99333/150x100.png',
                        'status' => 1,
                        'sort' => 1,
                    ],
                ],
                'new_items' => [
                    [
                        'item_id' => factory(\App\Models\Item::class)->create()->id,
                        'status' => 1,
                        'sort' => 1,
                    ],
                    [
                        'item_id' => factory(\App\Models\Item::class)->create()->id,
                        'status' => 0,
                        'sort' => 2,
                    ],
                    [
                        'item_id' => factory(\App\Models\Item::class)->create()->id,
                        'status' => 1,
                        'sort' => 3,
                    ],
                    [
                        'item_id' => factory(\App\Models\Item::class)->create()->id,
                        'status' => 1,
                        'sort' => 4,
                    ],
                ],
                'pickups' => [
                    [
                        'item_id' => factory(\App\Models\Item::class)->create()->id,
                        'status' => 1,
                        'sort' => 1,
                    ],
                ],
                'background_color' => '008b8b',
                'features' => [
                    [
                        'item_id' => factory(\App\Models\Item::class)->create()->id,
                        'status' => 1,
                        'sort' => 1,
                    ],
                ],
                'news' => [
                    [
                        'item_id' => factory(\App\Models\Item::class)->create()->id,
                        'status' => 1,
                        'sort' => 1,
                    ],
                    [
                        'item_id' => factory(\App\Models\Item::class)->create()->id,
                        'status' => 0,
                        'sort' => 2,
                    ],
                ],
                'styling_sort' => \App\Enums\TopContent\StylingSort::Manual,
                'stylings' => [
                    [
                        'styling_id' => factory(\App\Models\Styling::class)->create()->id,
                        'sort' => 1,
                    ],
                ],
            ],
            [
                'store_brand' => null,
                'main_visuals' => [
                    [
                        'pc_path' => 'https://placehold.jp/160x160.png',
                        'sp_path' => 'http://placehold.jp/24/ff00ff/99333/150x100.png',
                        'status' => 1,
                        'sort' => 1,
                    ],
                ],
                'new_items' => [
                    [
                        'item_id' => factory(\App\Models\Item::class)->create()->id,
                        'status' => 1,
                        'sort' => 1,
                    ],
                ],
                'pickups' => [
                    [
                        'item_id' => factory(\App\Models\Item::class)->create()->id,
                        'status' => 1,
                        'sort' => 1,
                    ],
                ],
                'background_color' => 'ff00ff',
                'features' => [
                    [
                        'item_id' => factory(\App\Models\Item::class)->create()->id,
                        'status' => 1,
                        'sort' => 1,
                    ],
                ],
                'news' => [
                    [
                        'item_id' => factory(\App\Models\Item::class)->create()->id,
                        'status' => 1,
                        'sort' => 1,
                    ],
                ],
                'styling_sort' => \App\Enums\TopContent\StylingSort::Manual,
                'stylings' => [
                    [
                        'styling_id' => factory(\App\Models\Styling::class)->create()->id,
                        'sort' => 1,
                    ],
                ],
            ],
        ] as $data) {
            \App\Models\TopContent::create($data)->save();
        }
    }
}
