<?php

namespace App\Criteria\SalesAggregation;

use App\Utils\Arr;
use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Class AdminSearchCriteria.
 *
 * @package namespace App\Criteria\Order;
 */
class AdminOrderCriteria implements CriteriaInterface
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

        $itemConditions = Arr::reduce($this->itemConditionFileds, function ($conditions, $field) use ($params) {
            if (isset($params[$field])) {
                $conditions[] = ['items.' . $field, $params[$field]];
            }

            return $conditions;
        }, []);

        if (!empty($itemConditions)) {
            $model = $model->whereItem($itemConditions);
        }

        $by = 'orders.' . $repository->resolveAggregationDateField($params['by']);

        $model = $model->whereOrder([
            [$by, 'is not null'],
            [$by, '>=', $params['date_from']],
            [$by, '<', $params['date_to']],
        ]);

        return $model;
    }
}
