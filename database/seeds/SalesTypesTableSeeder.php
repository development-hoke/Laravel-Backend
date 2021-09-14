<?php

use Illuminate\Database\Seeder;

class SalesTypesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach ([
            [
                'name' => 'NEW',
                'text_color' => '#000000',
                'sort' => 100,
            ],
            [
                'name' => 'PICK UP',
                'text_color' => '#000000',
                'sort' => 100,
            ],
            [
                'name' => 'SALE',
                'text_color' => '#000000',
                'sort' => 100,
            ],
            [
                'name' => '予約',
                'text_color' => '#000000',
                'sort' => 100,
            ],
            [
                'name' => 'WEB限定',
                'text_color' => '#000000',
                'sort' => 100,
            ],
            [
                'name' => '先行',
                'text_color' => '#000000',
                'sort' => 100,
            ],
            [
                'name' => 'OUTLET',
                'text_color' => '#000000',
                'sort' => 100,
            ],
        ] as $data) {
            \App\Models\SalesType::create($data)->save();
        }
    }
}
