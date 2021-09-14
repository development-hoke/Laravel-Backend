<?php

namespace App\Criteria\Item;

use App\Models\ItemFavorite;
use Illuminate\Database\Eloquent\Builder;
use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Class SearchItemCriteria.
 *
 * @package namespace App\Criteria;
 */
class AdminSearchCriteria implements CriteriaInterface
{
    /**
     * @var array
     */
    protected $params;

    /**
     * @param array $params
     */
    public function __construct(array $params)
    {
        $this->params = $params;
    }

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
        $columns = ['organization_id', 'division_id', 'department_id', 'term_id', 'fashion_speed', 'status', 'product_number', 'maker_product_number', 'name', 'main_store_brand'];
        $useLike = array_flip(['name']);

        $params = $this->params;

        foreach ($columns as $column) {
            if (isset($params[$column])) {
                if (is_array($params[$column])) {
                    $model = $model->whereIn($column, $params[$column]);
                } elseif (isset($useLike[$column])) {
                    $model = $model->where($column, 'like', "%{$params[$column]}%");
                } else {
                    $model = $model->where($column, $params[$column]);
                }
            }
        }

        if (!empty($params['jan_code'])) {
            $model = $model->whereCode('jan_code', $params['jan_code']);
        }

        if (!empty($params['old_jan_code'])) {
            $model = $model->whereCode('old_jan_code', $params['old_jan_code']);
        }

        if (!empty($params['online_category_id'])) {
            $model = $model->hasOnlineCategories($params['online_category_id']);
        }

        if (!empty($params['online_tag_id'])) {
            $model = $model->hasOnlineTags($params['online_tag_id']);
        }

        if (!empty($params['favorite_count'])) {
            $model = $model->compareFavoriteCount('=', $params['favorite_count']);
        }

        if (array_key_exists('favorite_count', $params) && $params['favorite_count'] == 0) {
            $favIds = ItemFavorite::groupBy('item_id')->pluck('item_id');
            $model = $model->whereNotIn('id', $favIds);
        }

        if (isset($params['stock_type'])) {
            $model = $model->hasStockType($params['stock_type']);
        }

        $salesStatus = [\App\Enums\Item\SalesStatus::InStoreNow];

        if (isset($params['sale_stop'])) {
            $salesStatus[] = \App\Enums\Item\SalesStatus::Stop;
        }
        if (isset($params['sale_sold_out'])) {
            $salesStatus[] = \App\Enums\Item\SalesStatus:: SoldOut;
        }

        $model = $model->whereIn('sales_status', $salesStatus);

        $model->orderBy('id', 'desc');

        return $model;
    }
}
