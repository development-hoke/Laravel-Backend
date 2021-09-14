<?php

namespace App\Repositories;

use App\Models\NpRejectedTransaction;
use Prettus\Repository\Eloquent\BaseRepository;

/**
 * Class NpRejectedTransactionRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class NpRejectedTransactionRepositoryEloquent extends BaseRepository implements NpRejectedTransactionRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return NpRejectedTransaction::class;
    }

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
    }
}
