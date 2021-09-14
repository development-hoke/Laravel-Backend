<?php

use Illuminate\Database\Seeder;

class OrganizationsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $response = require __DIR__.'/../../app/HttpCommunication/Ymdy/Mock/fixtures/division_group_list.php';
        foreach ($response['division_groups'] as $data) {
            \App\Models\Organization::updateOrCreate(
                ['id' => $data['id']],
                ['name' => $data['name']],
            );
        }
    }
}
