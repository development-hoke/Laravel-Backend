<?php

namespace App\Repositories;

use App\Models\Event;
use App\Repositories\Traits\CopyTrait;
use Illuminate\Support\Collection;

/**
 * Class EventRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class EventRepositoryEloquent extends BaseRepositoryEloquent implements EventRepository
{
    use CopyTrait;

    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Event::class;
    }

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
    }

    /**
     * 商品詳細に紐づくイベントを取得
     *
     * @param Collection $events
     *
     * @return Event|null
     */
    public function getByPeriodFrom(Collection $events)
    {
        if ($events->isEmpty()) {
            return null;
        } else {
            return $events->sortByDesc('period_from')->first();
        }
    }
}
