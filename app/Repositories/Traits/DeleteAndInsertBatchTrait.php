<?php

namespace App\Repositories\Traits;

/**
 * deleteAndInsertBatchの提供
 */
trait DeleteAndInsertBatchTrait
{
    /**
     * 関連データを一度削除して、新しいデータを挿入する。
     *
     * @param array $params
     * @param string $relatedKeyField
     * @param int $relatedKey
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function deleteAndInsertBatch(array $params, string $relatedKeyField, int $relatedKey)
    {
        $this->model->where($relatedKeyField, $relatedKey)->delete();

        $this->resetModel();

        $models = [];

        foreach ($params as $data) {
            $model = $this->model->create(array_merge($data, [
                $relatedKeyField => $relatedKey,
            ]));
            $model->save();
            $this->resetModel();
            $models[] = $model;
        }

        return \Illuminate\Database\Eloquent\Collection::make($models);
    }
}
