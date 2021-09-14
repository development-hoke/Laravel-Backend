<?php

namespace App\Repositories;

use App\Models\OrderMessage;

/**
 * Class OrderMessageRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class OrderMessageRepositoryEloquent extends BaseRepositoryEloquent implements OrderMessageRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return OrderMessage::class;
    }

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
    }
}
