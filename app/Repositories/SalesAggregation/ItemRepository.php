<?php

namespace App\Repositories\SalesAggregation;

use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * 商品売上集計用リポジトリ
 *
 * @package namespace App\Repositories\SalesAggregation;
 */
interface ItemRepository extends RepositoryInterface
{
    /**
     * @return void
     */
    public function applyAggregationQuery();

    /**
     * @param int $perPage
     *
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Pagination\LengthAwarePaginator
     */
    public function aggregate(int $perPage = null);

    /**
     * チャンクを実行する
     *
     * @param int $num
     * @param Closure $closure
     *
     * @return void
     */
    public function chunk(int $num, \Closure $closure);

    /**
     * @return $this
     */
    public function setCsvScopeQuery();

    /**
     * @param array $onlineCategoryIds
     *
     * @return array
     */
    public function getItemCategoryGroups(array $onlineCategoryIds);

    /**
     * @param int $by
     *
     * @return string
     */
    public function resolveAggregationDateField($by);
}
