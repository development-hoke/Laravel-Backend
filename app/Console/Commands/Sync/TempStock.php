<?php

namespace App\Console\Commands\Sync;

use App\Enums\TempStock\ItemStatus;
use App\Exceptions\SyncException;
use App\HttpCommunication\Shohin\ItemInterface as ShohinHttpCommunication;
use App\Models\TempStock as StockModel;
use App\Repositories\ItemDetailIdentificationRepository;
use App\Repositories\StoreRepository;
use App\Repositories\TempStockRepository;
use Illuminate\Support\Collection;

class TempStock extends BaseSyncCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:temp_stock';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '商品基幹の在庫情報取得APIを実行して集計用中間テーブルにbulk insertする';

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
        TempStockRepository $tempStockRepository,
        StoreRepository $storeRepository,
        ItemDetailIdentificationRepository $itemDetailIdentificationRepo
    ) {
        $this->sendStart();
        $this->shohinHttpCommunication = $shohinHttpCommunication;
        $this->tempStockRepository = $tempStockRepository;
        $this->storeRepository = $storeRepository;
        $this->itemDetailIdentificationRepo = $itemDetailIdentificationRepo;

        try {
            $this->info('在庫データの同期を開始します。');
            \DB::table('temp_stocks')->truncate();

            // TODO: キューにするとかで並列処理化
            $count = 0;
            $loop = true;
            do {
                $loop = $this->synchronize(self::LIMIT * ($count++));
                // sleep(1000);
            } while ($loop);

            $this->info('在庫データの同期が完了しました。');
            $this->sendSuccess();
        } catch (\Exception $e) {
            // ログは\App\Exceptions\Handlerで処理されるためここでは出力しない。
            $this->error('例外が発生しました: ' . $e->getMessage());
            $this->sendFailure($e->getMessage());
            throw $e;
        }
    }

    /**
     * @return void
     */
    private function synchronize(int $offset)
    {
        $response = $this->shohinHttpCommunication->fetchStocks(['offset' => $offset, 'limit' => self::LIMIT])->getBody();
        if (!isset($response['data']) || count($response['data']) === 0) {
            return false;
        }

        foreach (collect($response['data'])->chunk(self::CHUNK_SIZE) as $chunk) {
            \DB::transaction(function () use ($chunk) {
                $models = [];
                foreach ($chunk as $data) {
                    // 販売済みはスキップ
                    if (ItemStatus::fromValue((int) $data['Item']['item_status_id'])->is(ItemStatus::Sold)) {
                        continue;
                    }
                    try {
                        if ($model = $this->buildTempStock($data)) {
                            $models[] = $model;
                        }
                    } catch (SyncException $e) {
                        ++$this->failure;
                        $this->error($e->getMessage());
                    }
                }
                StockModel::bulkInsert(Collection::make($models));
                $this->success += count($models);
            });
        }

        return true;
    }

    /**
     *  @param $data
     *
     *  @return App\Models\Item
     */
    private function buildTempStock(array $data)
    {
        if ($this->storeRepository->findWhere(['id' => $data['Item']['shop_id']])->isEmpty()) {
            throw new SyncException("Store ID ({$data['Item']['shop_id']}) does not exist.");
        }
        if (!$itemDetailIdentificationRepo = $this->itemDetailIdentificationRepo->findWhere(['jan_code' => $data['Item']['code2241']])->first()) {
            throw new SyncException("JAN code ({$data['Item']['code2241']}) / ({$data['Item']['code']}) does not exist.");
        }

        $tempStock = $this->tempStockRepository->makeModel();
        $tempStock->item_detail_id = $itemDetailIdentificationRepo->item_detail_id;
        $tempStock->store_id = $data['Item']['shop_id'];
        $tempStock->jan_code = $data['Item']['code2241'];
        $tempStock->item_status_id = $data['Item']['item_status_id'];
        $tempStock->imported = false;
        $tempStock->imported_store_stock = false;
        $tempStock->imported_store_stock_by_jan = false;

        return $tempStock;
    }
}
