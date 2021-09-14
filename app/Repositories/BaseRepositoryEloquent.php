<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;

abstract class BaseRepositoryEloquent extends BaseRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    abstract public function model();

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
    }
}
