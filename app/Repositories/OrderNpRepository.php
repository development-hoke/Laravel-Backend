<?php

namespace App\Repositories;

use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Interface OrderNpRepository
 *
 * @package App\Repositories
 */
interface OrderNpRepository extends RepositoryInterface
{
    /**
     * 指定した条件で最初の1件を取得する。なければModelNotFoundExceptionを投げる。
     *
     * @param array $where
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function findOrFail(array $where);
}
