<?php

use Illuminate\Database\Seeder;

class TopContentsMasterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('top_contents')->insert([
            [
                'store_brand' => null,
                'main_visuals' => json_encode([]),
                'new_items' => json_encode([]),
                'pickups' => json_encode([]),
                'background_color' => '#FFFFFF',
                'features' => json_encode([]),
                'news' => json_encode([]),
                'styling_sort' => \App\Enums\TopContent\StylingSort::Pv,
            ],
            [
                'store_brand' => \App\Enums\Common\StoreBrand::Medoc,
                'main_visuals' => json_encode([]),
                'new_items' => json_encode([]),
                'pickups' => json_encode([]),
                'background_color' => '#FFFFFF',
                'features' => json_encode([]),
                'news' => json_encode([]),
                'styling_sort' => \App\Enums\TopContent\StylingSort::Pv,
            ],
            [
                'store_brand' => \App\Enums\Common\StoreBrand::Lasud,
                'main_visuals' => json_encode([]),
                'new_items' => json_encode([]),
                'pickups' => json_encode([]),
                'background_color' => '#FFFFFF',
                'features' => json_encode([]),
                'news' => json_encode([]),
                'styling_sort' => \App\Enums\TopContent\StylingSort::Pv,
            ],
            [
                'store_brand' => \App\Enums\Common\StoreBrand::Vin,
                'main_visuals' => json_encode([]),
                'new_items' => json_encode([]),
                'pickups' => json_encode([]),
                'background_color' => '#FFFFFF',
                'features' => json_encode([]),
                'news' => json_encode([]),
                'styling_sort' => \App\Enums\TopContent\StylingSort::Pv,
            ],
            [
                'store_brand' => \App\Enums\Common\StoreBrand::Aga,
                'main_visuals' => json_encode([]),
                'new_items' => json_encode([]),
                'pickups' => json_encode([]),
                'background_color' => '#FFFFFF',
                'features' => json_encode([]),
                'news' => json_encode([]),
                'styling_sort' => \App\Enums\TopContent\StylingSort::Pv,
            ],
            [
                'store_brand' => \App\Enums\Common\StoreBrand::Fennel,
                'main_visuals' => json_encode([]),
                'new_items' => json_encode([]),
                'pickups' => json_encode([]),
                'background_color' => '#FFFFFF',
                'features' => json_encode([]),
                'news' => json_encode([]),
                'styling_sort' => \App\Enums\TopContent\StylingSort::Pv,
            ],
            [
                'store_brand' => \App\Enums\Common\StoreBrand::Radiate,
                'main_visuals' => json_encode([]),
                'new_items' => json_encode([]),
                'pickups' => json_encode([]),
                'background_color' => '#FFFFFF',
                'features' => json_encode([]),
                'news' => json_encode([]),
                'styling_sort' => \App\Enums\TopContent\StylingSort::Pv,
            ],
            [
                'store_brand' => \App\Enums\Common\StoreBrand::Clove,
                'main_visuals' => json_encode([]),
                'new_items' => json_encode([]),
                'pickups' => json_encode([]),
                'background_color' => '#FFFFFF',
                'features' => json_encode([]),
                'news' => json_encode([]),
                'styling_sort' => \App\Enums\TopContent\StylingSort::Pv,
            ],
        ]);
    }
}
