<?php

namespace App\Console\Commands\Sync;

use App\Repositories\OrderDetailRepository;
use Carbon\Carbon;

class OrderAggration extends BaseSyncCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:order_aggration';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '予約在庫切れメールを送る';

    /**
     * @var OrderDetailRepository
     */
    private $orderDetailRepository;

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
        OrderDetailRepository $orderDetailRepository
    ) {
        $this->orderDetailRepository = $orderDetailRepository;

        try {
            $this->info('予約在庫を算出する。');

            $totalRetailByBrandDaily = $this->orderDetailRepository
                ->whereHas('order', function ($query) {
                    $query->whereDate('order_date', Carbon::yesterday()->toDateString());
                })->get()->groupBy('itemDetail.item.brand.name')->map(function ($orderDetailsByBrand) {
                    return $orderDetailsByBrand->sum('retail_price');
                })->toArray();

            $totalRetailByBrandMonthly = $this->orderDetailRepository
                ->whereHas('order', function ($query) {
                    $query->whereDate('delivery_hope_date', '<=', Carbon::yesterday());
                    $query->whereDate('delivery_hope_date', '>=', Carbon::now()->startOfMonth());
                })->get()->groupBy('itemDetail.item.brand.name')->map(function ($orderDetailsByBrand) {
                    return $orderDetailsByBrand->sum('retail_price');
                })->toArray();
            var_dump($totalRetailByBrandDaily, $totalRetailByBrandMonthly);

            $this->info('予約在庫切れメールが送信されています。');
        } catch (\Exception $e) {
            // ログは\App\Exceptions\Handlerで処理されるためここでは出力しない。
            $this->error('例外が発生しました: ' . $e->getMessage());
            $this->sendFailure($e->getMessage());
            throw $e;
        }
    }
}
