<?php

namespace App\Repositories\SalesAggregation;

use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * 注文集計用リポジトリ
 *
 * @package namespace App\Repositories\SalesAggregation;
 */
interface OrderRepository extends RepositoryInterface
{
    /**
     * groupbyを適用する
     *
     * @param array $params
     * @param int $perPage
     *
     * @return \Illuminate\Database\Eloquent\Collection|\App\Pagination\LengthAwarePaginator;
     */
    public function aggregate(array $params, int $perPage = null);

    /**
     * CSV出力クエリを設定
     *
     * @param array $params
     *
     * @return $this
     */
    public function setAggregateCsvScopeQuery(array $params);

    /**
     * CSV出力クエリを設定（商品あり）
     *
     * @param array $params
     *
     * @return $this
     */
    public function setAggregateOrderDetailCsvScopeQuery(array $params);
}
