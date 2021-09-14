<?php

use Illuminate\Database\Seeder;

class UrgentNoticesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('urgent_notices')->truncate();
        DB::table('urgent_notices')->insert([
            'body' => '緊急お知らせ情報入力',
            'status' => false,
        ]);
    }
}
