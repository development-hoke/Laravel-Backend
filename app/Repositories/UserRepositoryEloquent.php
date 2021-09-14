<?php

namespace App\Repositories;

use App\Models\User;
use Prettus\Repository\Criteria\RequestCriteria;

/**
 * Class UserRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class UserRepositoryEloquent extends BaseRepositoryEloquent implements UserRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return User::class;
    }

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
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
