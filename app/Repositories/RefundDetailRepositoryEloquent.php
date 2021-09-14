<?php

namespace App\Repositories;

use App\Models\RefundDetail;

/**
 * Class RefundDetailRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class RefundDetailRepositoryEloquent extends BaseRepositoryEloquent implements RefundDetailRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return RefundDetail::class;
    }

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
    }
}
