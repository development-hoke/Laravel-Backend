<?php

use Illuminate\Database\Seeder;

class ColorsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $response = require __DIR__.'/../../app/HttpCommunication/Ymdy/Mock/fixtures/color_list.php';
        \Log::debug($response);

        foreach ($response['colors'] as $data) {
            \App\Models\Color::updateOrCreate(
                ['id' => $data['id']],
                [
                    'code' => $data['code'],
                    'name' => $data['name'],
                    'color_panel' => '#000000',
                    'brightness' => '0.0000000',
                    'display_name' => $data['name'],
                ]
            );
        }
    }
}
