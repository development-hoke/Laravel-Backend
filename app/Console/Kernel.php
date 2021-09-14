<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        \App\Console\Commands\InitiateDatabase::class,
        \App\Console\Commands\DeleteItemPreviewImages::class,
        \App\Console\Commands\DeleteExpiredPlans::class,
        \App\Console\Commands\Sync\ColorMaster::class,
        \App\Console\Commands\Sync\CounterPartyMaster::class,
        \App\Console\Commands\Sync\DivisionMaster::class,
        \App\Console\Commands\Sync\DepartmentMaster::class,
        \App\Console\Commands\Sync\StoreMaster::class,
        \App\Console\Commands\Sync\SeasonMaster::class,
        \App\Console\Commands\Sync\SizeMaster::class,
        \App\Console\Commands\Sync\Item::class,
        \App\Console\Commands\Sync\TempStock::class,
        \App\Console\Commands\Sync\Stock::class,
        \App\Console\Commands\Sync\DeadInventory::class,
        \App\Console\Commands\Sync\SlowMovingInventory::class,
        \App\Console\Commands\Sync\LowInventory::class,
        \App\Console\Commands\Sync\EcUpdate::class,
        \App\Console\Commands\Sync\OrderAggration::class,
        \App\Console\Commands\Sync\EcData::class,
        \App\Console\Commands\Sync\PastItems\PastItemsUpload::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param \Illuminate\Console\Scheduling\Schedule $schedule
     *
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command(\App\Console\Commands\Sync\ColorMaster::class)->cron(config('schedule.sync_color_master'));
        $schedule->command(\App\Console\Commands\Sync\CounterPartyMaster::class)->cron(config('schedule.sync_counter_party_master'));
        $schedule->command(\App\Console\Commands\Sync\DivisionMaster::class)->cron(config('schedule.sync_division_master'));
        $schedule->command(\App\Console\Commands\Sync\DepartmentMaster::class)->cron(config('schedule.sync_department_master'));
        $schedule->command(\App\Console\Commands\Sync\StoreMaster::class)->cron(config('schedule.sync_store_master'));
        $schedule->command(\App\Console\Commands\Sync\SeasonMaster::class)->cron(config('schedule.sync_season_master'));
        $schedule->command(\App\Console\Commands\Sync\SizeMaster::class)->cron(config('schedule.sync_size_master'));
        $schedule->command(\App\Console\Commands\Sync\Item::class)->cron(config('schedule.sync_item'));
        $schedule->command(\App\Console\Commands\Sync\TempStock::class)->cron(config('schedule.sync_temp_stock'));
        $schedule->command(\App\Console\Commands\Sync\Stock::class)->cron(config('schedule.sync_stock'));
        $schedule->command(\App\Console\Commands\Sync\DeadInventory::class)->cron(config('schedule.sync_dead_inventory'));
        $schedule->command(\App\Console\Commands\Sync\SlowMovingInventory::class)->cron(config('schedule.sync_slow_moving_inventory'));
        $schedule->command(\App\Console\Commands\Sync\LowInventory::class)->cron(config('schedule.sync_low_inventory'));
        $schedule->command(\App\Console\Commands\DeleteItemPreviewImages::class)->cron(config('schedule.delete_item_preview_images'));
        $schedule->command(\App\Console\Commands\DeleteExpiredPlans::class)->cron(config('schedule.delete_expired_plans'));
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        require base_path('routes/console.php');
    }
}
