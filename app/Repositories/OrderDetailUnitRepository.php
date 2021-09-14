<?php

namespace App\Repositories;

use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Interface OrderDetailUnitRepository.
 *
 * @package namespace App\Repositories;
 */
interface OrderDetailUnitRepository extends RepositoryInterface
{
    /**
     * 取り消し可能な注文データを取得する。
     * JANが新しいものから取得する。（注文時とは逆の順序）
     *
     * @param int $orderDetailId
     * @param int $cancelAmount
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function findCancelableUnits($orderDetailId, $cancelAmount);
}
