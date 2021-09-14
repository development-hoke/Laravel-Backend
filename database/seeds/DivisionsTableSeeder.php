<?php

use Illuminate\Database\Seeder;

class DivisionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $response = require __DIR__.'/../../app/HttpCommunication/Ymdy/Mock/fixtures/division_list.php';
        foreach ($response['divisions'] as $data) {
            \App\Models\Division::updateOrCreate(
                ['id' => $data['id']],
                [
                    'organization_id' => $data['group_id'],
                    'name' => $data['name'],
                    'brand_name' => $data['name'],
                    'brand_code' => $data['code'],
                    'sign' => $data['mark'],
                ],
            );
        }
    }
}
