<?php

namespace Tests\Concerns;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

trait CreateTestData
{
    /**
     *  商品作成時に必要なマスタ情報を投入するための共通処理
     */
    protected function beforeCreateItem()
    {
        Artisan::call('db:seed', ['--class' => 'ColorsTableSeeder']);
        Artisan::call('db:seed', ['--class' => 'SizesTableSeeder']);
        Artisan::call('db:seed', ['--class' => 'OrganizationsTableSeeder']);
        Artisan::call('db:seed', ['--class' => 'DepartmentsTableSeeder']);
        Artisan::call('db:seed', ['--class' => 'DivisionsTableSeeder']);

        factory(\App\Models\Term::class, 4)->create();
        factory(\App\Models\Brand::class, 24)->create();
    }

    /**
     *  beforeCreateItem で作ったデータをtruncate
     */
    protected function truncateBeforeCreateItem()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        \App\Models\Color::truncate();
        \App\Models\Size::truncate();
        \App\Models\Term::truncate();
        \App\Models\Brand::truncate();
        \App\Models\Organization::truncate();
        \App\Models\DepartmentGroup::truncate();
        \App\Models\Department::truncate();
        \App\Models\Division::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
