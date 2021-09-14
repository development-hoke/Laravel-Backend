<?php

namespace App\Repositories;

use App\Models\ItemImage;
use App\Repositories\Traits\DeleteAndInsertBatchTrait;
use Illuminate\Support\Facades\DB;
use Prettus\Repository\Criteria\RequestCriteria;

/**
 * Class ItemImageRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class ItemImageRepositoryEloquent extends BaseRepositoryEloquent implements ItemImageRepository
{
    use DeleteAndInsertBatchTrait;

    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return ItemImage::class;
    }

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

    /**
     * @param int $itemId
     * @param int $start
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function resort(int $itemId, int $start = 1)
    {
        $models = $this->findWhere(['item_id' => $itemId]);

        if (!$models->first()) {
            return $models;
        }

        $this->model->whereIn('id', $models->pluck('id'))
            ->where('sort', '>=', $start)
            ->update(['sort' => DB::raw('sort + 1')]);

        $this->resetModel();

        $models = $this->findWhere(['item_id' => $itemId]);

        return $models;
    }
}
