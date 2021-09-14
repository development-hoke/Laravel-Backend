<?php

namespace App\Repositories;

use App\Models\UrgentNotice;

/**
 * Class UrgentNoticeRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class UrgentNoticeRepositoryEloquent extends BaseRepositoryEloquent implements UrgentNoticeRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return UrgentNotice::class;
    }

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
    }
}
