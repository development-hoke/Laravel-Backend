<?php

namespace App\Console\Commands\Sync;

use App\HttpCommunication\Shohin\ItemInterface as ShohinHttpCommunication;
use App\Jobs\NotifyLowInventory;
use App\Repositories\ItemDetailIdentificationRepository;

class LowInventory extends BaseSyncCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:low_inventory';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '予約在庫切れメールを送る';

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
            $this->info('予約在庫を算出する。');

            $lowInventoryIdentifications = $this->itemDetailIdentificationRepo->findLowInventory()->get();
            $this->success += $lowInventoryIdentifications->count();

            NotifyLowInventory::dispatch($lowInventoryIdentifications);

            $this->info('予約在庫切れメールが送信されています。');
        } catch (\Exception $e) {
            // ログは\App\Exceptions\Handlerで処理されるためここでは出力しない。
            $this->error('例外が発生しました: ' . $e->getMessage());
            $this->sendFailure($e->getMessage());
            throw $e;
        }
        $this->sendSuccess();
    }
}
