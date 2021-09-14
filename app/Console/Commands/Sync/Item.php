<?php

namespace App\Console\Commands\Sync;

use App\HttpCommunication\Shohin\ItemInterface as ShohinHttpCommunication;
use App\Models\Item as ItemModel;
use App\Repositories\ColorRepository;
use App\Repositories\DivisionRepository;
use App\Repositories\ItemDetailIdentificationRepository;
use App\Repositories\ItemDetailRepository;
use App\Repositories\ItemRepository;
use App\Repositories\SizeRepository;
use Carbon\Carbon;

class Item extends BaseSyncCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:item';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '商品基幹と連携し商品データの同期をする';

    /**
     * @var DivisionRepository
     */
    private $divisionRepository;

    /**
     * @var ColorRepository
     */
    private $colorRepository;

    /**
     * @var SizeRepository
     */
    private $sizeRepository;

    /**
     * @var ShohinHttpCommunication
     */
    private $shohinHttpCommunication;

    /**
     * @var ItemRepository
     */
    private $itemRepository;

    /**
     * @var ItemDetailRepository
     */
    private $itemDetailRepository;

    /**
     * @var ItemDetailIdentificationRepository
     */
    private $itemDetailIdentificationRepo;

    // 更新したID
    private $itemIds = [];
    private $itemDetailIds = [];

    const LIMIT = 1000;

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
        DivisionRepository $divisionRepository,
        ColorRepository $colorRepository,
        SizeRepository $sizeRepository,
        ItemRepository $itemRepository,
        ItemDetailRepository $itemDetailRepository,
        ItemDetailIdentificationRepository $itemDetailIdentificationRepo
    ) {
        $this->sendStart();
        $this->shohinHttpCommunication = $shohinHttpCommunication;
        $this->divisionRepository = $divisionRepository;
        $this->colorRepository = $colorRepository;
        $this->sizeRepository = $sizeRepository;
        $this->itemRepository = $itemRepository;
        $this->itemDetailRepository = $itemDetailRepository;
        $this->itemDetailIdentificationRepo = $itemDetailIdentificationRepo;

        try {
            $this->info('商品データの同期を開始します。');

            $count = 0;
            $loop = true;
            do {
                $loop = $this->synchronize(self::LIMIT * ($count++));
                // sleep(1000);
            } while ($loop);

            $this->info('商品データの同期が完了しました。');
        } catch (\Exception $e) {
            // ログは\App\Exceptions\Handlerで処理されるためここでは出力しない。
            $this->error('例外が発生しました: ' . $e->getMessage());
            $this->sendFailure($e->getMessage());
            throw $e;
        }
        $this->sendSuccess();
    }

    /**
     * @return void
     */
    private function synchronize(int $offset)
    {
        $this->info("offset={$offset}");
        $response = $this->shohinHttpCommunication->fetchMasters(['offset' => $offset, 'limit' => self::LIMIT])->getBody();
        if (!isset($response['data']) || count($response['data']) === 0) {
            return false;
        }

        foreach ($response['data'] as $data) {
            \DB::transaction(function () use ($data) {
                // 商品基幹で物理削除はされないので削除処理はなくて大丈夫。
                // コメントだけは残しておく。
                // https://www.chatwork.com/#!rid209191898-1405783489130811392
                // $this->deleteItem();
                $this->createOrUpdateItemDetailIdentification($data);
            });
        }

        return true;
    }

    /**
     *  各ID等ををAPIから取得したデータ、もしくはJANコードから抜き出して取得します。
     *
     *  @param $data
     *
     *  @return string
     */
    private function getCodes(array $data)
    {
        if (!isset($data['Color']['code']) || !isset($data['Size']['code'])) {
            throw new \Exception('The size or color is not set.(jan_code='.$data['ItemMaster']['code'].')');
        }
        $divisionCode = $data['Division']['code'];
        $division = $this->divisionRepository->findWhere(['brand_code' => $divisionCode])->first();
        $params = [
            'organization_id' => optional($division)->organization_id,
            'division_id' => optional($division)->id,
            'department_id' => $data['Section']['id'],
            'item_jibu_code' => $data['ItemMaster']['item_jibu_code'],
            'short_product_number' => $data['ItemMaster']['item_code'],
            'color_code' => $data['Color']['code'],
            'size_code' => $data['Size']['code'],
        ];
        $params['sku'] = $params['item_jibu_code'].$params['color_code'].$params['size_code'];

        return $params;
    }

    /**
     *  @param $data
     *
     *  @return \App\Models\Item
     */
    private function createOrUpdateItem(array $data)
    {
        $codes = $this->getCodes($data);
        $itemJibuCode = $data['ItemMaster']['item_jibu_code'];
        $item = $this->itemRepository->findWhere([
            'product_number' => $itemJibuCode,
        ])->first();

        if (!empty($item)) {
            if (in_array($item->id, $this->itemIds)) {
                return $item;
            }
        } else {
            $item = $this->itemRepository->makeModel();
            $item->product_number = $itemJibuCode;
            $item->sales_status = \App\Enums\Item\SalesStatus::InStoreNow;
            $item->price_change_period = Carbon::now();
            $item->price_change_rate = 0;
        }

        $item->season_id = $data['ItemMaster']['season_id'];
        $item->term_id = $data['ItemMaster']['period'];
        $item->short_product_number = $data['ItemMaster']['item_code'];
        $item->maker_product_number = $data['Maker']['code'].$data['ItemMaster']['maker_code'];
        $item->name = $data['ItemMaster']['name'];
        $item->display_name = $data['ItemMaster']['name'];
        $item->retail_price = $data['ItemMaster']['retail_price'];
        $item->retail_tax = $data['ItemMaster']['retail_tax'];
        $item->note_staff_ok = $data['ItemMaster']['notice'];

        if (isset($data['PriceChange']) && count($data['PriceChange']) > 0) {
            $priceChange = $data['PriceChange'][count($data['PriceChange']) - 1];
            $item->price_change_period = $priceChange['price_change_period'];
            $item->price_change_rate = $priceChange['price_change_rate'] ?? 0;
        }

        // todo k_mst_tax_rate_idで経営基幹のマスタの値がくるのでそのマスタ作ったらここなおす
        // $item->tax_rate = $data['ItemMaster']['tax_rate'] ? $data['ItemMaster']['tax_rate'] / 100 : 0;
        $item->tax_rate = 0.1;

        $item->organization_id = $codes['organization_id'];
        $item->division_id = $codes['division_id'];
        $item->department_id = $codes['department_id'];
        $item->fashion_speed = $data['ItemMaster']['fashion_velocity_id'];

        $item->save();
        $this->itemIds[] = $item->id;

        return $item;
    }

    /**
     *  @param array $data
     *  @param \App\Models\Item $item
     *
     *  @return \App\Models\ItemDetail
     */
    private function createOrUpdateItemDetails(array $data, ItemModel $item)
    {
        $codes = $this->getCodes($data);
        $itemDetail = $this->itemDetailRepository->findWhere([
            'sku_number' => $codes['sku'],
        ])->first();

        if (!empty($itemDetail)) {
            if (in_array($itemDetail->id, $this->itemDetailIds)) {
                return $itemDetail;
            }
        } else {
            $itemDetail = $this->itemDetailRepository->makeModel();
            $itemDetail->item_id = $item->id;
            $itemDetail->sku_number = $codes['sku'];
            $itemDetail->color_id = $this->getColorId($codes['color_code']);
            $itemDetail->size_id = $this->getSizeId($codes['size_code']);
            $itemDetail->status_change_date = Carbon::now();
            $itemDetail->save();
        }

        $this->itemDetailIds[] = $itemDetail->id;

        return $itemDetail;
    }

    /**
     *  @param $data
     *
     *  @return \App\Models\ItemDetailIdentification
     */
    private function createOrUpdateItemDetailIdentification(array $data)
    {
        try {
            $itemDetailIdentification = $this->itemDetailIdentificationRepo->findWhere([
                'jan_code' => $data['ItemMaster']['code'],
            ])->first();

            $item = $this->createOrUpdateItem($data);
            $itemDetail = $this->createOrUpdateItemDetails($data, $item);

            if (empty($itemDetailIdentification)) {
                $itemDetailIdentification = $this->itemDetailIdentificationRepo->makeModel();
                $itemDetailIdentification->id = $data['ItemMaster']['id'];
                $itemDetailIdentification->item_detail_id = $itemDetail->id;
                $itemDetailIdentification->jan_code = $data['ItemMaster']['code'];
                $itemDetailIdentification->old_jan_code = $data['ItemMaster']['code225'];
                $itemDetailIdentification->ec_stock = 0;
                $itemDetailIdentification->store_stock = 0;
            }

            $itemDetailIdentification->save();
            ++$this->success;
        } catch (\Illuminate\Database\QueryException $e) {
            ++$this->failure;
            $this->error('SQLエラー: ' . $e->getMessage());
        } catch (\Exception $e) {
            ++$this->failure;
            $this->error('エラー: ' . $e->getMessage());
        }
    }

    /**
     *  @param $code
     *
     *  @return $color_id
     */
    private function getColorId(string $code)
    {
        return optional($this->colorRepository->findByField('code', $code)->first())->id;
    }

    /**
     *  @param $code
     *
     *  @return $color_id
     */
    private function getSizeId(string $code)
    {
        return optional($this->sizeRepository->findByField('code', $code)->first())->id;
    }
}
