<?php

namespace App\Repositories;

use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Interface ItemDetailRepository.
 *
 * @package namespace App\Repositories;
 */
interface ItemDetailRepository extends RepositoryInterface
{
    /**
     * テーブルをまとめて更新する
     *
     * @param array $params
     *
     * @return array
     */
    public function updateBatch(array $params);
}
