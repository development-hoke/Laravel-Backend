<?php

namespace App\Repositories;

use App\Exceptions\FatalException;
use App\Models\ClosedMarket;
use Illuminate\Support\Facades\DB;

/**
 * Class ClosedMarketRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class ClosedMarketRepositoryEloquent extends BaseRepositoryEloquent implements ClosedMarketRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return ClosedMarket::class;
    }

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
    }

    /**
     * @param int $id
     * @param int $count
     *
     * @return void
     */
    public function addStock(int $id, int $count)
    {
        if ($count === 0) {
            return;
        }

        $updated = $this->model->where('id', $id)->update(['stock' => DB::raw("stock + {$count}")]);

        if (!$updated) {
            throw new FatalException(error_format('error.failed_to_update_db', ['method', __METHOD__, 'id' => $id, 'count' => $count]));
        }

        $this->resetModel();
    }
}
