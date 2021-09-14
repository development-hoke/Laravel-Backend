<?php

namespace App\Repositories;

use App\Models\Staff;

/**
 * Class StaffRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class StaffRepositoryEloquent extends BaseRepositoryEloquent implements StaffRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Staff::class;
    }

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
    }

    /**
     * Update or Create an entity in repository
     *
     * @throws ValidatorException
     *
     * @param array $attributes
     * @param int $id
     *
     * @return mixed
     */
    public function safeUpdateOrCreate(array $attributes, int $id)
    {
        $this->applyScope();

        $model = $this->findWhere(['id' => $id])->first();

        if (empty($model)) {
            $model = $this->model->newInstance();
            $model->id = $id;
        }

        $model->fill($attributes);
        $model->save();

        $this->resetModel();

        return $model;
    }
}
