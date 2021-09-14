<?php

use Illuminate\Database\Seeder;

class SizesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $response = require __DIR__.'/../../app/HttpCommunication/Ymdy/Mock/fixtures/size_list.php';
        foreach ($response['sizes'] as $data) {
            \App\Models\Size::updateOrCreate(
                ['id' => $data['id']],
                [
                    'code' => $data['code'],
                    'name' => $data['name'],
                    'search_code' => $data['search_code'],
                ],
            );
        }
    }
}
