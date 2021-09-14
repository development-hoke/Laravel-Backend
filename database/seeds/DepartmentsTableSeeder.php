<?php

use Illuminate\Database\Seeder;

class DepartmentsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $response = require __DIR__.'/../../app/HttpCommunication/Ymdy/Mock/fixtures/section_group_list.php';
        foreach ($response['section_groups'] as $data) {
            \App\Models\DepartmentGroup::updateOrCreate(
                ['id' => $data['id']],
                [
                    'name' => $data['name'],
                ],
            );
        }

        $response = require __DIR__.'/../../app/HttpCommunication/Ymdy/Mock/fixtures/section_list.php';
        foreach ($response['sections'] as $data) {
            \App\Models\Department::updateOrCreate(
                ['id' => $data['id']],
                [
                    'department_group_id' => $data['group_id'],
                    'name' => $data['name'],
                    'code' => $data['code'],
                    'short_name' => $data['abbreviation'],
                    'sign' => $data['mark'],
                ],
            );
        }
    }
}
