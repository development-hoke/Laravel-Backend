<?php

namespace App\Repositories;

use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Interface TopContentAdminRepository.
 *
 * @package namespace App\Repositories;
 */
interface TopContentAdminRepository extends RepositoryInterface
{
    /**
     * データを更新し、他のレコードの優先度を更新内容に合わせて更新する
     *
     * @param array $attributes
     * @param int $id
     *
     * @return TopContent
     */
    public function updateWithAdjustmentSort(array $attributes, $id);

    /**
     * レコード削除と他のレコードの優先度を更新内容に合わせて更新する
     *
     * @param int $id
     *
     * @return int
     */
    public function deleteWithAdjustmentSort(array $attributes, $id);
}
