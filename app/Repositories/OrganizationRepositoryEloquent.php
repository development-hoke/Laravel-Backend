<?php

namespace App\Repositories;

use App\Models\Organization;
use Prettus\Repository\Eloquent\BaseRepository;

/**
 * Class OrganizationRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class OrganizationRepositoryEloquent extends BaseRepository implements OrganizationRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Organization::class;
    }

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
    }
}
