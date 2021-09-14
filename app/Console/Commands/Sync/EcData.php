<?php

namespace App\Console\Commands\Sync;

use App\HttpCommunication\Shohin\ItemInterface as ShohinHttpCommunication;
use App\Repositories\ColorRepository;
use App\Repositories\DivisionRepository;
use App\Repositories\ItemDetailIdentificationRepository;
use App\Repositories\ItemDetailRepository;
use App\Repositories\ItemRepository;
use App\Repositories\SizeRepository;

class EcData extends BaseSyncCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:ec_data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '商品一覧情報返却';

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
        $this->shohinHttpCommunication = $shohinHttpCommunication;
        $this->divisionRepository = $divisionRepository;
        $this->colorRepository = $colorRepository;
        $this->sizeRepository = $sizeRepository;
        $this->itemRepository = $itemRepository;
        $this->itemDetailRepository = $itemDetailRepository;
        $this->itemDetailIdentificationRepo = $itemDetailIdentificationRepo;

        try {
            $this->info('商品データの同期を開始します。');

            $itemDetailIdentificationRepo = $this->itemDetailIdentificationRepo;

            $itemDetailIndexes = $itemDetailIdentificationRepo
            ->select('id', 'jan_code', 'old_jan_code', 'store_stock', 'ec_stock', 'reservable_stock', 'arrival_date', 'item_detail_id')
            ->with([
                'itemDetail' => function ($query) {
                    $query->select('id', 'color_id', 'size_id', 'item_id');
                    $query->withCount('redisplayRequests');
                },
                'itemDetail.color' => function ($query) {
                    $query->select('id', 'name', 'display_name');
                },
                'itemDetail.size' => function ($query) {
                    $query->select('id', 'name');
                },
                'itemDetail.item' => function ($query) {
                    $query->select('id', 'name', 'display_name');
                },
            ])
            ->get()
            ->each(function ($itemDetailIndex) {
                $itemDetailIndex->itemDetail->makeHidden(['color_id', 'size_id', 'item_id']);
            })
            ->makeHidden(['item_detail_id'])->toArray();

            var_dump($itemDetailIndexes);

            $this->info('商品データの同期が完了しました。');
        } catch (\Exception $e) {
            // ログは\App\Exceptions\Handlerで処理されるためここでは出力しない。
            $this->error('例外が発生しました: ' . $e->getMessage());
            $this->sendFailure($e->getMessage());
            throw $e;
        }
    }
}
