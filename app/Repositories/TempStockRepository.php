<?php

namespace App\Repositories;

use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Interface TempStockRepository.
 *
 * @package namespace App\Repositories;
 */
interface TempStockRepository extends RepositoryInterface
{
    const IMPORT_TYPE_EC_STOCK = 1;
    const IMPORT_TYPE_STORE_STOCK = 2;
    const IMPORT_TYPE_STORE_STOCK_BY_JAN = 3;

    /**
     * 在庫情報インポート用(EC用)。
     * LazyCollectionで返却したいのでBaseRepositoryEloquentのallとかは使わない。
     *
     * @param array $where
     *
     * @return \Illuminate\Support\LazyCollection
     */
    public function countEcStock();

    /**
     * 在庫情報インポート用(EC以外用)。
     * LazyCollectionで返却したいのでBaseRepositoryEloquentのallとかは使わない。
     *
     * @param array $where
     *
     * @return \Illuminate\Support\LazyCollection
     */
    public function countStoreStock();

    /**
     * 店舗在在庫数集計(item_detail_identificationsに追加)
     * LazyCollectionで返却したいのでBaseRepositoryEloquentのallとかは使わない。
     *
     * @return \Illuminate\Support\LazyCollection
     */
    public function countStoreStockByJanCode();

    /**
     * インポート完了にする
     *
     * @param array $where
     * @param int $type
     */
    public function imported(array $where, int $type);
}
