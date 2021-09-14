<?php

namespace App\Repositories;

use App\Domain\Utils\ItemSort as ItemSortUtil;
use App\Exceptions\FatalException;
use App\Models\ItemSort;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Class ItemSortRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class ItemSortRepositoryEloquent extends BaseRepositoryEloquent implements ItemSortRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return ItemSort::class;
    }

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
    }

    /**
     * データを更新し、他のレコードの優先度を更新内容に合わせて更新する
     *
     * @param array $attributes
     * @param int $id
     *
     * @return ItemSort
     */
    public function updateWithAdjustmentSort(array $attributes, $id)
    {
        return DB::transaction(function () use ($attributes, $id) {
            $model = $this->model->lockForUpdate()->findOrFail($id);

            $upward = $attributes['sort'] > $model->sort;

            $model->fill($attributes);
            $model->save();

            $this->resetModel();

            if (!$upward) {
                $model = $this->getStoreBrandConditionAppliedModel($attributes, $this->model);
                $model->where('item_sorts.id', '!=', $id)
                    ->where('item_sorts.sort', '>=', $attributes['sort'])
                    ->increment('item_sorts.sort');
            } else {
                $model = $this->getStoreBrandConditionAppliedModel($attributes, $this->model);
                $model->where('item_sorts.sort', '>', $attributes['sort'])
                    ->increment('item_sorts.sort');

                $this->resetModel();
                $model = $this->getStoreBrandConditionAppliedModel($attributes, $this->model);
                $model->where('item_sorts.id', '!=', $id)
                    ->where('item_sorts.sort', '<=', $attributes['sort'])
                    ->decrement('item_sorts.sort');
            }

            $this->resetModel();

            $this->resetSort($attributes);

            $model = $this->getStoreBrandConditionAppliedModel($attributes, $this->model)->get();

            return $model;
        }, 3);
    }

    /**
     * @param array $attributes
     * @param \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Eloquent\Builder $model
     *
     * @return \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Eloquent\Builder
     */
    private function getStoreBrandConditionAppliedModel(array $attributes, $model = null)
    {
        if (empty($model)) {
            $model = $this->$model;
        }

        if (isset($attributes['store_brand'])) {
            $model = $model->where('item_sorts.store_brand', $attributes['store_brand']);
        } else {
            $model = $model->whereNull('item_sorts.store_brand');
        }

        return $model;
    }

    /**
     * 新規レード作成しソートを自動で割り当てる
     *
     * @param array $attributes
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function createBatchAndAssignSort(array $attributes)
    {
        return DB::transaction(function () use ($attributes) {
            $model = $this->model->lockForUpdate();

            $model = $this->getStoreBrandConditionAppliedModel($attributes, $model);

            $rows = $model->get();

            $existentCount = $rows->count();

            if (!ItemSortUtil::isAcceptableCount($existentCount + count($attributes['item_id']))) {
                throw new FatalException(__('error.invalid_item_sort_count'), $attributes);
            }

            $sort = $existentCount === 0 ? 1 : $rows->max('sort') + 1;

            $results = new Collection();

            foreach ($attributes['item_id'] as $offset => $itemId) {
                $model = $this->model->newInstance([
                    'store_brand' => $attributes['store_brand'],
                    'item_id' => $itemId,
                    'sort' => $sort + $offset,
                ]);

                $model->save();

                $results->push($model);

                $this->resetModel();
            }

            return $results;
        }, 3);
    }

    /**
     * レコード削除と他のレコードの優先度を更新内容に合わせて更新する
     *
     * @param int $id
     *
     * @return int
     */
    public function deleteWithAdjustmentSort($id)
    {
        return DB::transaction(function () use ($id) {
            $model = $this->find($id);
            $deleted = $model->delete();

            $this->resetModel();

            $this->resetSort(['store_brand' => $model->store_brand || null]);

            return $deleted;
        }, 3);
    }

    /**
     * ソートのリセット処理
     *
     * @param array $attributes
     *
     * @return void
     */
    private function resetSort(array $attributes)
    {
        DB::statement('SET @sort=0');
        $model = $this->getStoreBrandConditionAppliedModel($attributes, $this->model);
        $model->orderBy('item_sorts.sort')->update(['item_sorts.sort' => DB::raw('@sort := @sort + 1')]);

        $this->resetModel();
    }
}
