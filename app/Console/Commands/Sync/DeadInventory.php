<?php

namespace App\Console\Commands\Sync;

use App\HttpCommunication\Shohin\ItemInterface as ShohinHttpCommunication;
use App\Jobs\NotifyDeadInventory;
use App\Repositories\ItemDetailIdentificationRepository;
use Carbon\Carbon;

class DeadInventory extends BaseSyncCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:dead_inventory';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '不動在庫を算出してメールを送る';

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
            $this->info('不動在庫を算出する。');

            $itemDetailIdentifications = $this->itemDetailIdentificationRepo->all();

            foreach ($itemDetailIdentifications as $itemDetailIdentification) {
                $deadInventoryDays = 0;
                if ($itemDetailIdentification->itemDetail->status == \App\Enums\Common\Status::Published) {
                    $statusChangeDate = Carbon::parse($itemDetailIdentification->itemDetail->status_change_date);
                    $arrivalDate = Carbon::parse($itemDetailIdentification->arrival_date);
                    if ($itemDetailIdentification->arrival_date != null) {
                        $deadInventoryDays = (int) $statusChangeDate->diff($arrivalDate)->format('%d');
                    }
                } elseif ($itemDetailIdentification->itemDetail->status == \App\Enums\Common\Status::Unpublished) {
                    $statusChangeDate = Carbon::parse($itemDetailIdentification->itemDetail->status_change_date);
                    $deadInventoryDays = (int) Carbon::now()->diff($statusChangeDate)->format('%d');
                }

                $itemDetailIdentification->update(['dead_inventory_days' => $deadInventoryDays]);
            }

            $deadInventoryIdentifications = $this->itemDetailIdentificationRepo->whereDeadInventoryDayType(\App\Enums\ItemDetail\DeadInventoryDayType::GreatorThanOrEqual14)->get();
            NotifyDeadInventory::dispatch($deadInventoryIdentifications);

            $this->info('在庫僅少メールが送信されています。');
        } catch (\Exception $e) {
            // ログは\App\Exceptions\Handlerで処理されるためここでは出力しない。
            $this->error('例外が発生しました: ' . $e->getMessage());
            $this->sendFailure($e->getMessage());
            throw $e;
        }
        $this->sendSuccess();
    }
}
