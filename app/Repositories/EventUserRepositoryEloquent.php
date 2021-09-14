<?php

namespace App\Repositories;

use App\Models\EventUser;
use App\Repositories\Traits\CopyTrait;

/**
 * Class EventUserRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class EventUserRepositoryEloquent extends BaseRepositoryEloquent implements EventUserRepository
{
    use CopyTrait;

    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return EventUser::class;
    }

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
    }
}
