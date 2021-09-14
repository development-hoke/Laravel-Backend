<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HelpsTableReleaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('helps')->delete();

        DB::table('helps')->insert([
            ['id' => 1, 'title' => 'サイズガイド', 'body' => '', 'created_at' => DB::raw('NOW()')],
            ['id' => 2, 'title' => '返品について', 'body' => '', 'created_at' => DB::raw('NOW()')],
        ]);
    }
}
