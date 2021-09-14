<?php

namespace App\Repositories;

use App\Enums\TempStock\ItemStatus;
use App\Models\TempStock;

/**
 * Class TempStockRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class TempStockRepositoryEloquent extends BaseRepositoryEloquent implements TempStockRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return TempStock::class;
    }

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
    }

    /**
     * 在庫情報インポート用(EC用)。
     * LazyCollectionで返却したいのでBaseRepositoryEloquentのallとかは使わない。
     *
     * @return \Illuminate\Support\LazyCollection
     */
    public function countEcStock()
    {
        $model = $this->model
            ->select(\DB::raw('count(*) as stock, store_id, jan_code, item_status_id'))
            ->where('imported', false)
            ->where('store_id', config('constants.store.ec_store_id'))
            ->groupBy('store_id', 'jan_code', 'item_status_id')
            ->cursor();

        $this->resetModel();

        return $model;
    }

    /**
     * 店舗在在庫数集計(item_detail_storesに追加)
     * LazyCollectionで返却したいのでBaseRepositoryEloquentのallとかは使わない。
     *
     * @return \Illuminate\Support\LazyCollection
     */
    public function countStoreStock()
    {
        $model = $this->model
            ->select(\DB::raw('count(*) as stock, store_id, item_detail_id'))
            ->where('imported_store_stock', false)
            ->where('store_id', '<>', config('constants.store.ec_store_id'))
            ->whereIn('item_status_id', [ItemStatus::StoreStock, ItemStatus::Stock])
            ->groupBy('store_id', 'item_detail_id')
            ->cursor();

        $this->resetModel();

        return $model;
    }

    /**
     * 店舗在在庫数集計(item_detail_identificationsに追加)
     * LazyCollectionで返却したいのでBaseRepositoryEloquentのallとかは使わない。
     *
     * @return \Illuminate\Support\LazyCollection
     */
    public function countStoreStockByJanCode()
    {
        $model = $this->model
            ->select(\DB::raw('count(*) as stock, jan_code, item_detail_id'))
            ->where('imported_store_stock_by_jan', false)
            ->where('store_id', '<>', config('constants.store.ec_store_id'))
            ->whereIn('item_status_id', [ItemStatus::StoreStock, ItemStatus::Stock])
            ->groupBy('jan_code', 'item_detail_id')
            ->cursor();

        $this->resetModel();

        return $model;
    }

    /**
     * インポート完了にする
     *
     * @param array $where
     * @param int $type
     */
    public function imported(array $where, int $type)
    {
        $model = $this->model->where($where);

        switch ($type) {
            case self::IMPORT_TYPE_EC_STOCK:
                $model = $model->update(['imported' => true]);
                break;

            case self::IMPORT_TYPE_STORE_STOCK:
                $model = $model->update(['imported_store_stock' => true]);
                break;

            case self::IMPORT_TYPE_STORE_STOCK_BY_JAN:
            default:
                $model = $model->update(['imported_store_stock_by_jan' => true]);
                break;
        }

        $this->resetModel();

        return $model;
    }
}
