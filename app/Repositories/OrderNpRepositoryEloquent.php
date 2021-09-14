<?php

namespace App\Repositories;

use App\Models\OrderNp;
use App\Repositories\Traits\QueryBuilderMethodTrait;

/**
 * Class OrderNpRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class OrderNpRepositoryEloquent extends BaseRepositoryEloquent implements OrderNpRepository
{
    use QueryBuilderMethodTrait;

    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return OrderNp::class;
    }

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
    }
}
