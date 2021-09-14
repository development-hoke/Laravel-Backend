<?php

namespace App\Repositories\Traits;

/**
 * updateに条件を追加する。
 */
trait ConditionalUpdateMethodTrait
{
    /**
     * @param array $attributes
     * @param $id
     * @param array $where
     *
     * @return mixed
     */
    public function updateWithCondition(array $attributes, $id, array $where)
    {
        $this->applyScope();

        $this->applyConditions($where);

        $model = $this->model->findOrFail($id);

        $model->fill($attributes);

        $model->save();

        $this->resetModel();

        return $model;
    }
}
