<?php

namespace App\Repositories;

use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Interface CartItemRepository.
 *
 * @package namespace App\Repositories;
 */
interface CartItemRepository extends RepositoryInterface
{
    /**
     * whereIn句で対象を指定して削除する
     *
     * @param array $where
     *
     * @return int
     */
    public function deleteWhereIn($field, array $values);

    /**
     * @param int $id
     *
     * @return \App\Models\CartItem
     */
    public function restore($id);
}
