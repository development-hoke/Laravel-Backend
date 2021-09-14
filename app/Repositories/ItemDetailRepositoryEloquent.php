<?php

namespace App\Repositories;

use App\Models\ItemDetail;
use App\Repositories\Traits\PaginateWithDistinctTrait;
use App\Repositories\Traits\QueryBuilderMethodTrait;

/**
 * Class ItemDetailRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class ItemDetailRepositoryEloquent extends BaseRepositoryEloquent implements ItemDetailRepository
{
    use PaginateWithDistinctTrait;
    use QueryBuilderMethodTrait;

    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return ItemDetail::class;
    }

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
    }

    /**
     * テーブルをまとめて更新する
     *
     * @param array $params
     *
     * @return array
     */
    public function updateBatch(array $params)
    {
        $models = [];
        $datetime = date('Y-m-d H:i:s');

        foreach ($params as $data) {
            $itemDetail = $this->model->find($data['id']);

            if ((int) $itemDetail->status !== (int) $data['status']) {
                $itemDetail->status_change_date = $datetime;
            }

            $itemDetail->fill($data);
            $itemDetail->save();

            $this->resetModel();

            $models[] = $itemDetail;
        }

        return $models;
    }
}
