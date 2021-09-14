<?php

namespace App\Console\Commands\Sync;

use App\Enums\TempStock\ItemStatus;
use App\HttpCommunication\Shohin\ItemInterface as ShohinHttpCommunication;
use App\Jobs\NotifyRedisplayRequested;
use App\Repositories\ItemDetailIdentificationRepository;
use App\Repositories\ItemDetailRepository;
use App\Repositories\ItemDetailStoreRepository;
use App\Repositories\StoreRepository;
use App\Repositories\TempStockRepository;
use Carbon\Carbon;

class Stock extends BaseSyncCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:stock';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '商品基幹と連携し在庫データの同期をする';

    /**
     * @var ShohinHttpCommunication
     */
    private $shohinHttpCommunication;

    /**
     * @var TempStockRepository
     */
    private $tempStockRepository;

    /**
     * @var StoreRepository
     */
    private $storeRepository;

    /**
     * @var ItemDetailRepository
     */
    private $itemDetailRepository;

    /**
     * @var ItemDetailIdentificationRepository
     */
    private $itemDetailIdentificationRepo;

    /**
     * @var ItemDetailStoreRepository
     */
    private $itemDetailStoreRepo;

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
        TempStockRepository $tempStockRepository,
        StoreRepository $storeRepository,
        ItemDetailRepository $itemDetailRepository,
        ItemDetailIdentificationRepository $itemDetailIdentificationRepo,
        ItemDetailStoreRepository $itemDetailStoreRepo
    ) {
        $this->sendStart();
        $this->shohinHttpCommunication = $shohinHttpCommunication;
        $this->tempStockRepository = $tempStockRepository;
        $this->storeRepository = $storeRepository;
        $this->itemDetailRepository = $itemDetailRepository;
        $this->itemDetailIdentificationRepo = $itemDetailIdentificationRepo;
        $this->itemDetailStoreRepo = $itemDetailStoreRepo;

        \DB::beginTransaction();

        try {
            $this->info('在庫データの同期を開始します。');

            // 既存在庫をクリア
            $this->itemDetailIdentificationRepo->clearStock();

            // EC在庫の取り込み
            $this->tempStockRepository->countEcStock()->each(function ($tempStock) {
                $updated = false;
                switch ((int) $tempStock->item_status_id) {
                    case ItemStatus::Sold:
                        break;
                    default:
                        $updated = $this->updateEcStock($tempStock);
                        break;
                }

                if ($updated) {
                    $this->imported($tempStock, TempStockRepository::IMPORT_TYPE_EC_STOCK);
                    ++$this->success;
                }
            });

            // 店舗在庫の取り込み(item_detail_stores用)
            $this->tempStockRepository->countStoreStock()->each(function ($tempStock) {
                $updated = $this->updateStoreStock($tempStock);

                if ($updated) {
                    $this->imported($tempStock, TempStockRepository::IMPORT_TYPE_STORE_STOCK);
                    ++$this->success;
                }
            });

            // 店舗在庫の取り込み(item_detail_identifications用)
            $this->tempStockRepository->countStoreStockByJanCode()->each(function ($tempStock) {
                $updated = $this->updateStoreStockByJanCode($tempStock);

                if ($updated) {
                    $this->imported($tempStock, TempStockRepository::IMPORT_TYPE_STORE_STOCK_BY_JAN);
                    ++$this->success;
                }
            });

            \DB::commit();
            $this->info('在庫データの同期が完了しました。');
        } catch (\Exception $e) {
            \DB::rollback();
            // ログは\App\Exceptions\Handlerで処理されるためここでは出力しない。
            $this->error('例外が発生しました: ' . $e->getMessage());
            $this->sendFailure($e->getMessage());
            throw $e;
        }
        $this->sendSuccess();
    }

    /**
     * @return bolean
     */
    private function updateEcStock($tempStock)
    {
        $itemDetailIdentification = $this->itemDetailIdentificationRepo->findByField('jan_code', $tempStock->jan_code)->first();
        if (empty($itemDetailIdentification)) {
            ++$this->failure;

            return false;
        }

        if ($itemDetailIdentification->ec_stock === 0 && $tempStock->stock > 0) {
            // 再入荷リクエスト通知
            NotifyRedisplayRequested::dispatch($itemDetailIdentification->itemDetail->id);
        }
        if ((int) $tempStock->item_status_id === ItemStatus::Reserve) {
            $itemDetailIdentification->reservable_stock = $tempStock->stock;
        } else {
            $itemDetailIdentification->ec_stock = $tempStock->stock;
        }
        $itemDetailIdentification->latest_stock_added_at = Carbon::now();
        $itemDetailIdentification->save();

        return true;
    }

    /**
     * @return bolean
     */
    private function updateStoreStock($tempStock)
    {
        $itemDetailStore = $this->itemDetailStoreRepo->firstOrNew([
            'item_detail_id' => $tempStock->item_detail_id,
            'store_id' => $tempStock->store_id,
        ]);

        $itemDetailStore->stock = $tempStock->stock;
        $itemDetailStore->save();

        return true;
    }

    /**
     * @return bolean
     */
    private function updateStoreStockByJanCode($tempStock)
    {
        $itemDetailIdentification = $this->itemDetailIdentificationRepo->findByField('jan_code', $tempStock->jan_code)->first();

        if (empty($itemDetailIdentification)) {
            ++$this->failure;

            return false;
        }

        $itemDetailIdentification->store_stock = $tempStock->stock;
        $itemDetailIdentification->latest_stock_added_at = Carbon::now();
        $itemDetailIdentification->save();

        return true;
    }

    /**
     * @return void
     */
    private function imported($tempStock, $type)
    {
        $where = $tempStock->toArray();
        unset($where['stock']);
        $this->tempStockRepository->imported($where, $type);
    }
}
