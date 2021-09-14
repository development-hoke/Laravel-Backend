<?php

namespace App\Criteria\Plan;

use App\Enums\Plan\Status;
use Illuminate\Database\Eloquent\Builder;
use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Class FrontSearchCriteria.
 *
 * @package namespace App\Criteria;
 */
class AdminPlanCriteria implements CriteriaInterface
{
    /**
     * Apply criteria in query repository
     *
     * @param Builder|Model $model
     * @param RepositoryInterface $repository
     *
     * @return mixed
     */

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

    public function apply($model, RepositoryInterface $repository)
    {
        $params = $this->params;

        if (isset($params['status'])) {
            if ($params['status'] == Status::Unpublished) {
                $model = $model->where('status', false);
            } elseif ($params['status'] == Status::WaitingPublish) {
                $model = $model->where('status', true)
                    ->where('period_from', '>', date('Y-m-d H:i:s'));
            } elseif ($params['status'] == Status::FinishPublish) {
                $model = $model->where('status', true)
                    ->where('period_to', '<', date('Y-m-d H:i:s'));
            } else {
                $model = $model->where('status', true)
                    ->where(function ($query) {
                        $query->where(function ($query) {
                            $query->where('period_from', '<=', date('Y-m-d H:i:s'))->where('period_to', '>=', date('Y-m-d H:i:s'));
                        })->orWhere(function ($query) {
                            $query->where('period_from', null)->where('period_to', null);
                        })->orWhere(function ($query) {
                            $query->where('period_from', '<=', date('Y-m-d H:i:s'))->where('period_to', null);
                        });
                    });
            }
        }

        if (isset($params['brand'])) {
            $model = $model->where('store_brand', $params['brand']);
        }

        return $model;
    }
}
