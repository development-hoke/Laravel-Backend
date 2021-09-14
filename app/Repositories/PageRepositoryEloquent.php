<?php

namespace App\Repositories;

use App\Models\Page;
use App\Repositories\Traits\CopyTrait;

/**
 * Class EventRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class PageRepositoryEloquent extends BaseRepositoryEloquent implements PageRepository
{
    use CopyTrait;

    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Page::class;
    }

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
    }
}
