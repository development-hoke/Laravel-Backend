<?php

namespace App\Criteria\Page;

use App\Enums\Common\Status;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Class FrontPageCriteria.
 *
 * @package namespace App\Criteria;
 */
class FrontPageCriteria implements CriteriaInterface
{
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
        $model = $model->where('status', Status::Published)
            ->where('publish_from', '<=', Carbon::now()->toDateTimeString())
            ->where(function ($query) {
                $query->where('publish_to', '>=', Carbon::now()->toDateTimeString())
                    ->orWhere('publish_to', null);
            });

        return $model;
    }
}
