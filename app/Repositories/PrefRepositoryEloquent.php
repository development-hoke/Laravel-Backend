<?php

namespace App\Repositories;

use App\Models\Pref;

/**
 * Class PrefRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class PrefRepositoryEloquent extends BaseRepositoryEloquent implements PrefRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Pref::class;
    }

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
    }

    public function getIdAndName(int $id)
    {
        $model = $this->find($id);

        return [
            'id' => $model->id,
            'name' => $model->name,
        ];
    }
}
