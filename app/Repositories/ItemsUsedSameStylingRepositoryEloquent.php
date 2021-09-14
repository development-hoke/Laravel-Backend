<?php

namespace App\Repositories;

use App\Models\ItemsUsedSameStyling;
use App\Repositories\Traits\DeleteAndInsertBatchTrait;
use Prettus\Repository\Criteria\RequestCriteria;

/**
 * Class ItemsUsedSameStylingRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class ItemsUsedSameStylingRepositoryEloquent extends BaseRepositoryEloquent implements ItemsUsedSameStylingRepository
{
    use DeleteAndInsertBatchTrait;

    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return ItemsUsedSameStyling::class;
    }

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
}
