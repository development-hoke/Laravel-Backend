<?php

namespace App\Repositories;

use App\Models\AdminLog;

/**
 * Class AdminLogRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class AdminLogRepositoryEloquent extends BaseRepositoryEloquent implements AdminLogRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return AdminLog::class;
    }

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
    }
}
