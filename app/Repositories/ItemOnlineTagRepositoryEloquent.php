<?php

namespace App\Repositories;

use App\Models\ItemOnlineTag;
use App\Repositories\Traits\DeleteAndInsertBatchTrait;
use Prettus\Repository\Criteria\RequestCriteria;

/**
 * Class ItemOnlineTagRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class ItemOnlineTagRepositoryEloquent extends BaseRepositoryEloquent implements ItemOnlineTagRepository
{
    use DeleteAndInsertBatchTrait;

    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return ItemOnlineTag::class;
    }

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
}
