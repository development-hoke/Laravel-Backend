<?php

use App\Models\Page;
use Illuminate\Database\Seeder;

class PagesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $pages = [
            [
                'slug' => 'law',
                'title' => '特定商取引法に基づく表示',
                'body' => '特定商取引法に基づく表示',
                'status' => 1,
                'publish_from' => date('Y-m-d 00:00:00'),
            ],
            [
                'slug' => 'privacy',
                'title' => 'プライバシーポリシー',
                'body' => 'プライバシーポリシー',
                'status' => 1,
                'publish_from' => date('Y-m-d 00:00:00'),
            ],
            [
                'slug' => 'terms',
                'title' => '利用規約',
                'body' => '利用規約',
                'status' => 1,
                'publish_from' => date('Y-m-d 00:00:00'),
            ],
            [
                'slug' => 'guide',
                'title' => 'ご利用ガイド',
                'body' => 'ご利用ガイド',
                'status' => 1,
                'publish_from' => date('Y-m-d 00:00:00'),
            ],
        ];

        foreach ($pages as $page) {
            if (Page::where('slug', $page['slug'])->exists()) {
                continue;
            }
            Page::create($page);
        }
    }
}
