<?php

namespace App\Repositories\AmazonPay;

use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Interface OrderRepository.
 *
 * @package namespace App\Repositories\AmazonPay;
 */
interface OrderRepository extends RepositoryInterface
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
