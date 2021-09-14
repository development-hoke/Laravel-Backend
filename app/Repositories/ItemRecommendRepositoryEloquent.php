<?php

namespace App\Repositories;

use App\Models\ItemRecommend;
use App\Repositories\Traits\DeleteAndInsertBatchTrait;
use Prettus\Repository\Criteria\RequestCriteria;

/**
 * Class ItemRecommendRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class ItemRecommendRepositoryEloquent extends BaseRepositoryEloquent implements ItemRecommendRepository
{
    use DeleteAndInsertBatchTrait;

    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return ItemRecommend::class;
    }

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
}
