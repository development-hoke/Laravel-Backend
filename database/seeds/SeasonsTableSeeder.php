<?php

use Illuminate\Database\Seeder;

class SeasonsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $response = require __DIR__.'/../../app/HttpCommunication/Ymdy/Mock/fixtures/season_group_list.php';
        foreach ($response['season_groups'] as $data) {
            \App\Models\SeasonGroup::updateOrCreate(
                ['id' => $data['id']],
                [
                    'name' => $data['name'],
                    'code' => $data['code'],
                ],
            );
        }

        $response = require __DIR__.'/../../app/HttpCommunication/Ymdy/Mock/fixtures/season_list.php';
        foreach ($response['seasons'] as $data) {
            \App\Models\Season::updateOrCreate(
                ['id' => $data['id']],
                [
                    'season_group_id' => $data['group_id'],
                    'name' => $data['name'],
                    'code' => $data['code'],
                    'sign' => $data['mark'],
                ],
            );
        }
    }
}
