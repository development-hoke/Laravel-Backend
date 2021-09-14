<?php

namespace App\Repositories;

use App\Models\Information;
use Carbon\Carbon;

/**
 * Class InformationRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class InformationRepositoryEloquent extends BaseRepositoryEloquent implements InformationRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Information::class;
    }

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
    }

    /**
     * トップに表示するお知らせを抽出
     *
     * @return Model
     */
    public function getTop()
    {
        return Information::where('status', true)
            ->where('publish_at', '<', Carbon::now()->format('Y-m-d H:i:s'))
            ->where('is_store_top', true)
            ->orderBy('priority')
            ->get();
    }

    /**
     * 直近のお知らせを抽出
     *
     * @return Model
     */
    public function getRecent($params)
    {
        $query = Information::where('status', true)
            ->where('publish_at', '<', Carbon::now()->format('Y-m-d H:i:s'));

        if (isset($params['excluded_id'])) {
            $query->whereNotIn('id', [$params['excluded_id']]);
        }

        return $query->orderBy('priority')
            ->get();
    }
}
