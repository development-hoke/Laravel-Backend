<?php

namespace App\Criteria\SalesAggregation;

use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Class AdminSearchCriteria.
 *
 * @package namespace App\Criteria\Order;
 */
class AdminItemCriteria implements CriteriaInterface
{
    /**
     * @var array
     */
    protected $itemConditionFileds = [
        'organization_id',
        'division_id',
        'main_store_brand',
        'department_id',
        'product_number',
        'maker_product_number',
    ];

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
        $params = $this->params;

        $fiels = $this->itemConditionFileds;

        foreach ($fiels as $field) {
            if (isset($params[$field])) {
                if (is_array($params[$field])) {
                    $model = $model->whereIn('items.' . $field, $params[$field]);
                } else {
                    $model = $model->where('items.' . $field, $params[$field]);
                }
            }
        }

        if (isset($params['online_category_id'])) {
            $model = $model->hasOnlineCategories($params['online_category_id']);
        }

        $model = static::appliyOrderConditions($model, $params, $repository);

        return $model;
    }

    public static function appliyOrderConditions($model, $params, $repository)
    {
        $by = 'orders.' . $repository->resolveAggregationDateField($params['by']);

        $model = $model->whereNotNull($by);

        if (isset($params['date_from'])) {
            $model = $model->where($by, '>=', $params['date_from']);
        }

        if (isset($params['date_to'])) {
            $model = $model->where($by, '<', $params['date_to']);
        }

        if (isset($params['sale_type'])) {
            $model = $model->where('order_details.sale_type', $params['sale_type']);
        }

        return $model;
    }
}
