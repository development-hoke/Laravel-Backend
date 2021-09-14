<?php

use Illuminate\Database\Seeder;

class StoreTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $response = require __DIR__.'/../../app/HttpCommunication/Ymdy/Mock/fixtures/shop_list.php';
        foreach ($response['shops'] as $data) {
            \App\Models\Store::updateOrCreate(
                ['id' => $data['id']],
                [
                    'code' => $data['code'],
                    'name' => $data['name'],
                    'title' => $data['title'],
                    'zip_code' => preg_replace('/\-/', '', $data['zip']),
                    'address1' => $data['address1'],
                    'address2' => $data['address2'],
                    'phone_number_1' => $data['tel1'],
                    'phone_number_2' => $data['tel2'],
                    'email' => $data['email'] ?? '',
                    'location' => ['longitude' => $data['longitude'], 'latitude' => $data['latitude']],
                    'open_time' => $data['open_time'],
                    'close_time' => $data['close_time'],
                ],
            );
        }
    }
}
