<?php

namespace App\Repositories;

use App\Models\HelpCategoryRelation;
use App\Repositories\Traits\DeleteAndInsertBatchTrait;

/**
 * Class HelpCategoryRelationRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class HelpCategoryRelationRepositoryEloquent extends BaseRepositoryEloquent implements HelpCategoryRelationRepository
{
    use DeleteAndInsertBatchTrait;

    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return HelpCategoryRelation::class;
    }

    /**
     * Boot up the repository
     */
    public function boot()
    {
    }

    /**
     * @param $helpId
     *
     * @return bool|null
     */
    public function deleteHelp($helpId)
    {
        return $this->model->where('help_id', $helpId)->delete();
    }
}
