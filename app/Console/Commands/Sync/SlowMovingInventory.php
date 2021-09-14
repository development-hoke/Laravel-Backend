<?php

namespace App\Console\Commands\Sync;

use App\HttpCommunication\Shohin\ItemInterface as ShohinHttpCommunication;
use App\Jobs\NotifySlowMovingInventory;
use App\Repositories\ItemDetailIdentificationRepository;
use Carbon\Carbon;

class SlowMovingInventory extends BaseSyncCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:slow_moving_inventory';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '滞留在庫の算出してメールを送る';

    /**
     * @var ShohinHttpCommunication
     */
    private $shohinHttpCommunication;

    /**
     * @var ItemDetailIdentificationRepository
     */
    private $itemDetailIdentificationRepo;

    const LIMIT = 1000;
    const CHUNK_SIZE = 100;

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
    public function handle(
        ShohinHttpCommunication $shohinHttpCommunication,
        ItemDetailIdentificationRepository $itemDetailIdentificationRepo
    ) {
        // $this->sendStart();
        $this->shohinHttpCommunication = $shohinHttpCommunication;
        $this->itemDetailIdentificationRepo = $itemDetailIdentificationRepo;

        try {
            $this->info('滞留在庫を算出する。');

            $itemDetailIdentifications = $this->itemDetailIdentificationRepo->all();

            foreach ($itemDetailIdentifications as $itemDetailIdentification) {
                $slowMovingInventoryDays = 0;
                if ($itemDetailIdentification->itemDetail->status == \App\Enums\Common\Status::Published) {
                    if ($itemDetailIdentification->itemDetail->last_sales_date == null) {
                        $statusChangeDate = Carbon::parse($itemDetailIdentification->itemDetail->status_change_date);
                        $slowMovingInventoryDays = (int) Carbon::now()->diff($statusChangeDate)->format('%d');
                    } else {
                        $lastSalesDate = Carbon::parse($itemDetailIdentification->itemDetail->last_sales_date);
                        $slowMovingInventoryDays = (int) Carbon::now()->diff($lastSalesDate)->format('%d');
                    }
                }
                $itemDetailIdentification->update(['slow_moving_inventory_days' => $slowMovingInventoryDays]);
            }

            $slowMovingIdentifications = $this->itemDetailIdentificationRepo->whereSlowMovingInventoryDayType(\App\Enums\ItemDetail\DeadInventoryDayType::GreatorThanOrEqual14)->get();
            NotifySlowMovingInventory::dispatch($slowMovingIdentifications);

            $this->info('滞留在庫通知メールが送信されています。');
        } catch (\Exception $e) {
            // ログは\App\Exceptions\Handlerで処理されるためここでは出力しない。
            $this->error('例外が発生しました: ' . $e->getMessage());
            $this->sendFailure($e->getMessage());
            throw $e;
        }
        $this->sendSuccess();
    }
}
