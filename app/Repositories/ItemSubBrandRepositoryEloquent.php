<?php

namespace App\Repositories;

use App\Models\ItemSubBrand;
use App\Repositories\Traits\DeleteAndInsertBatchTrait;
use Prettus\Repository\Criteria\RequestCriteria;

/**
 * Class ItemSubBrandRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class ItemSubBrandRepositoryEloquent extends BaseRepositoryEloquent implements ItemSubBrandRepository
{
    use DeleteAndInsertBatchTrait;

    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return ItemSubBrand::class;
    }

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
}
