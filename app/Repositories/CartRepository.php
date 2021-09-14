<?php

namespace App\Repositories;

use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Interface CartRepository
 *
 * @package App\Repositories
 */
interface CartRepository extends RepositoryInterface
{
    /**
     * ユニークなトークン発行
     *
     * @return string
     */
    public static function createUniqueToken();

    /**
     * 無効になった商品のカートの移行
     *
     * @param int $id
     *
     * @return \App\Models\Cart
     */
    public function transferCartItems(int $id);
}
