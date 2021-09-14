<?php

namespace App\Repositories;

use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Interface ItemBulkUploadRepository.
 *
 * @package namespace App\Repositories;
 */
interface ItemBulkUploadRepository extends RepositoryInterface
{
    /**
     * 余分な古いデータを削除する
     *
     * @return void
     */
    public function clearOldRows();
}
