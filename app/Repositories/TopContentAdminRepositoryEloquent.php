<?php

namespace App\Repositories;

use App\Models\TopContent;
use Illuminate\Support\Facades\DB;

/**
 * Class TopContentAdminRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class TopContentAdminRepositoryEloquent extends BaseRepositoryEloquent implements TopContentAdminRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return TopContent::class;
    }

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
    }

    /**
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function updateWithAdjustmentSort(array $attributes, $id)
    {
        return DB::transaction(function () use ($attributes, $id) {
            $model = $this->model->lockForUpdate()->findOrFail($id);

            $mainVisuals = $model->main_visuals;
            $oldSort = $mainVisuals[$attributes['old_index']];

            $upward = $attributes['new_sort'] > $oldSort['sort'];

            $mainVisuals[$attributes['old_index']]['sort'] = $attributes['new_sort'];

            if (!$upward) {
                foreach ($mainVisuals as $key => &$targetRecord) {
                    if ($key == $attributes['old_index']) {
                        continue;
                    }
                    if ($targetRecord['sort'] >= $attributes['new_sort']) {
                        $targetRecord['sort'] = $targetRecord['sort'] + 1;
                    }
                }
            } else {
                foreach ($mainVisuals as $key => &$targetRecord) {
                    if ($targetRecord['sort'] > $attributes['new_sort']) {
                        $targetRecord['sort'] = $targetRecord['sort'] + 1;
                    }
                }
                foreach ($mainVisuals as $key => &$targetRecord) {
                    if ($key == $attributes['old_index']) {
                        continue;
                    }
                    if ($targetRecord['sort'] <= $attributes['new_sort']) {
                        $targetRecord['sort'] = $targetRecord['sort'] - 1;
                    }
                }
            }

            $mainVisuals = $this->resetSort($mainVisuals);

            $model->main_visuals = $mainVisuals;

            $model->save();

            $this->resetModel();

            return $model;
        }, 3);
    }

    /**
     * レコード削除と他のレコードの優先度を更新内容に合わせて更新する
     *
     * @param int $id
     *
     * @return int
     */
    public function deleteWithAdjustmentSort($id, $sort)
    {
        return DB::transaction(function () use ($id, $sort) {
            $model = $this->find($id);
            $mainVisuals = $model->main_visuals;

            foreach ($mainVisuals as $key => $mainVisual) {
                if ($mainVisual['sort'] === $sort) {
                    unset($mainVisuals[$key]);
                }
            }
            $mainVisuals = $this->resetSort($mainVisuals);

            $model->main_visuals = $mainVisuals;

            $model->save();

            $this->resetModel();

            return $model;
        }, 3);
    }

    /**
     * ソートのリセット処理
     *
     * @param array $targetArray
     *
     * @return void
     */
    private function resetSort(array $targetRecords)
    {
        $sorts = array_column($targetRecords, 'sort');
        array_multisort($sorts, SORT_ASC, $targetRecords);

        foreach ($targetRecords as $key => &$targetRecord) {
            if ($targetRecord['sort'] === $key + 1) {
                continue;
            }
            $targetRecord['sort'] = $key + 1;
        }

        return $targetRecords;
    }
}
