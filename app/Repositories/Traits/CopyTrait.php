<?php

namespace App\Repositories\Traits;

use Illuminate\Database\Eloquent\Collection;

/**
 * 既存のレコードから、新しいレコードを作成する
 */
trait CopyTrait
{
    /**
     * 既存のレコードから、新しいレコードを作成する
     *
     * @param $id
     * @param array $where
     * @param array $replaceColumns 新しく置き換えるカラムの値
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function copy($id, array $where = [], array $replaceColumns = [])
    {
        $this->applyScope();

        if (!empty($where)) {
            $this->applyConditions($where);
        }

        $model = $this->model->findOrFail($id);

        $attributes = $this->createCopyAttributes($model, $replaceColumns);

        $this->resetModel();

        $model = $this->model->newInstance($attributes);
        $model->save();

        $this->resetModel();

        return $model;
    }

    /**
     * 条件に一致したレコードから、新しいレコードを作成する
     *
     * @param array $where
     * @param array $replaceColumns 新しく置き換えるカラムの値
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function copyBatch(array $where, array $replaceColumns = [])
    {
        $this->applyScope();

        $this->applyConditions($where);

        $sources = $this->model->get();
        $this->resetModel();

        $newModels = [];

        foreach ($sources as $source) {
            $attributes = $this->createCopyAttributes($source, $replaceColumns);

            $model = $this->model->newInstance($attributes);
            $model->save();
            $this->resetModel();

            $newModels[] = $model;
        }

        return new Collection($newModels);
    }

    /**
     * @param \Illuminate\Database\Eloquent\Model $source
     * @param array $replaceColumns
     *
     * @return array
     */
    protected function createCopyAttributes($source, array $replaceColumns = [])
    {
        foreach ($source->getFillable() as $field) {
            if (isset($replaceColumns[$field])) {
                $attributes[$field] = $replaceColumns[$field];
            } else {
                $attributes[$field] = $source->{$field};
            }
        }

        return $attributes;
    }
}
