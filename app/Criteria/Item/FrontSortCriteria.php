<?php

namespace App\Criteria\Item;

use App\Criteria\SortCriteria;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Facades\DB;
use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Class AdminSortCriteria.
 *
 * @package namespace App\Criteria\Item;
 */
class FrontSortCriteria extends SortCriteria implements CriteriaInterface
{
    /**
     * @var array
     */
    protected static $sortTypes = [
        'items.sales_period_from',
        'recommend',
        'displayed_sale_price',
    ];

    const DEFAULT_SORT = 'items.sales_period_from-desc';

    /**
     * Apply criteria in query repository
     *
     * @param Builder|Model $model
     * @param RepositoryInterface $repository
     *
     * @return mixed
     */
    public function apply($model, RepositoryInterface $repository)
    {
        $params = $this->params;

        if (!isset($params['sort'])) {
            $params['sort'] = self::DEFAULT_SORT;
        }

        list($sortType, $orderType) = static::extractParams($params['sort']);

        if ($sortType !== 'recommend') {
            $model = $model->orderBy($sortType, $orderType);
        } else {
            $model = $this->applyDefaultOrder($model, $params);
        }

        return $model;
    }

    /**
     * @param Builder|Model $model
     * @param array $params
     *
     * @return mixed
     */
    private function applyDefaultOrder($model, array $params)
    {
        return $model->distinct()->leftJoin('item_sorts', function (JoinClause $query) use ($params) {
            $query = $query->on('items.id', '=', 'item_sorts.item_id');

            if (isset($params['main_store_brand'])) {
                $query = $query->whereIn('item_sorts.store_brand', $params['main_store_brand']);
            } else {
                $query = $query->whereNotNull('item_sorts.store_brand');
            }

            return $query;
        })
            ->orderBy(DB::raw('item_sorts.id is null'))
            ->orderBy('item_sorts.sort')
            ->orderBy('item_sorts.store_brand');
    }
}
