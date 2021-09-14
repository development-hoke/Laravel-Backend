<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

/**
 *  管理画面や他システム連携などで更新されるが、初期投入したいデータを設定する。
 */
class InitiateDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:initiate_database';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'InitiateDatabase';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        \Artisan::call('db:seed', ['--class' => 'UrgentNoticesTableSeeder']);
        \Artisan::call('db:seed', ['--class' => 'PrefsTableSeeder']);
        \Artisan::call('db:seed', ['--class' => 'HelpsTableReleaseSeeder']);
        \Artisan::call('db:seed', ['--class' => 'PagesTableSeeder']);
        \Artisan::call('db:seed', ['--class' => 'DeliverySettingSeeder']);
        \Artisan::call('db:seed', ['--class' => 'BrandsTableSeeder']);
        \Artisan::call('db:seed', ['--class' => 'SalesTypesTableSeeder']);
        \Artisan::call('db:seed', ['--class' => 'OnlineCategoriesTableSeeder']);
        \Artisan::call('db:seed', ['--class' => 'OnlineTagsTableSeeder']);
        \Artisan::call('db:seed', ['--class' => 'SizesTableSeeder']);
        \Artisan::call('db:seed', ['--class' => 'ColorsTableSeeder']);
    }
}
