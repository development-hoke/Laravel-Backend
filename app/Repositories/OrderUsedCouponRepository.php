<?php

namespace App\Repositories;

use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Interface OrderUsedCouponRepository.
 *
 * @package namespace App\Repositories;
 */
interface OrderUsedCouponRepository extends RepositoryInterface
{
    /**
     * 関連データを一度削除して、新しいデータを挿入する。
     *
     * @param array $params
     * @param string $relatedKeyField
     * @param int $relatedKey
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function deleteAndInsertBatch(array $params, string $relatedKeyField, int $relatedKey);
}
