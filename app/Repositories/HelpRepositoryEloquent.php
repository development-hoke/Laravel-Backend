<?php

namespace App\Repositories;

use App\Models\Help;
use Prettus\Repository\Criteria\RequestCriteria;

/**
 * Class HelpRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class HelpRepositoryEloquent extends BaseRepositoryEloquent implements HelpRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Help::class;
    }

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
}
