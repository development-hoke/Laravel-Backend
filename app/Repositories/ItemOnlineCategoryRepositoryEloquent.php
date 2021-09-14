<?php

namespace App\Repositories;

use App\Models\ItemOnlineCategory;
use App\Repositories\Traits\DeleteAndInsertBatchTrait;
use Prettus\Repository\Criteria\RequestCriteria;

/**
 * Class ItemOnlineCategoryRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class ItemOnlineCategoryRepositoryEloquent extends BaseRepositoryEloquent implements ItemOnlineCategoryRepository
{
    use DeleteAndInsertBatchTrait;

    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return ItemOnlineCategory::class;
    }

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
}
