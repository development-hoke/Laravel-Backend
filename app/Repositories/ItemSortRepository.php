<?php

namespace App\Repositories;

use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Interface ItemSortRepository.
 *
 * @package namespace App\Repositories;
 */
interface ItemSortRepository extends RepositoryInterface
{
    /**
     * データを更新し、他のレコードの優先度を更新内容に合わせて更新する
     *
     * @param array $attributes
     * @param int $id
     *
     * @return ItemSort
     */
    public function updateWithAdjustmentSort(array $attributes, $id);

    /**
     * 新規レード作成しソートを自動で割り当てる
     *
     * @param array $attributes
     *
     * @return ItemSort
     */
    public function createBatchAndAssignSort(array $attributes);

    /**
     * レコード削除と他のレコードの優先度を更新内容に合わせて更新する
     *
     * @param int $id
     *
     * @return int
     */
    public function deleteWithAdjustmentSort($id);
}
