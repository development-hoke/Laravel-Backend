<?php

namespace App\Repositories;

use App\Models\EventItem;
use App\Repositories\Traits\ConditionalUpdateMethodTrait;
use App\Repositories\Traits\CopyTrait;

/**
 * Class EventItemRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class EventItemRepositoryEloquent extends BaseRepositoryEloquent implements EventItemRepository
{
    use ConditionalUpdateMethodTrait;
    use CopyTrait;

    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return EventItem::class;
    }

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
    }
}
