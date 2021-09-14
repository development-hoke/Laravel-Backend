<?php

namespace App\Criteria\Information;

use App\Enums\Common\Status;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Class FrontInformationCriteria.
 *
 * @package namespace App\Criteria;
 */
class FrontInformationCriteria implements CriteriaInterface
{
    private $place;

    public function __construct($place)
    {
        $this->place = $place;
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
        $model = $model->where('status', Status::Published)
            ->where('publish_at', '<=', Carbon::now()->toDateTimeString());

        if ($this->place === 'top_content') {
            $model = $model->where('is_store_top', 1);
        }

        $model = $model->orderBy('priority', 'asc')
            ->orderBy('publish_at', 'desc');

        return $model;
    }
}
