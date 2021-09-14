<?php

namespace App\Criteria\Item;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Class AdminSearchForAddingOrderCriteria.
 *
 * @package namespace App\Criteria\Item;
 */
class AdminSearchForAddingOrderCriteria implements CriteriaInterface
{
    private $itemColumns = ['department_id', 'maker_product_number'];

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

        foreach ($this->itemColumns as $column) {
            if (isset($params[$column])) {
                $model = $model->where("items.{$column}", $params[$column]);
            }
        }

        $model = $model
            ->where('status', \App\Enums\Common\Status::Published)
            ->where('sales_status', \App\Enums\Item\SalesStatus::InStoreNow);

        return $model;
    }
}
