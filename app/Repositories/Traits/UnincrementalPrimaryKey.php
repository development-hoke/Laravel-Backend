<?php

namespace App\Repositories\Traits;

trait UnincrementalPrimaryKey
{
    /**
     * @param array $attributes
     * @param mixed $key
     * @param string $keyName
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function createWithKey(array $attributes, $key, $keyName = 'id')
    {
        $model = $this->model->newInstance($attributes);

        $model->{$keyName} = $key;

        $model->save();

        $this->resetModel();

        return $model;
    }
}
