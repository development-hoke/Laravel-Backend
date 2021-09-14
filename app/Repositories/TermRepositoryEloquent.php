<?php

namespace App\Repositories;

use App\Models\Term;

/**
 * Class TermRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class TermRepositoryEloquent extends BaseRepositoryEloquent implements TermRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Term::class;
    }

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
    }
}
