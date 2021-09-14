<?php

namespace App\Domain;

use App\Models\Order as OrderModel;

interface OrderPortionInterface
{
    /**
     * ポイント按分計算
     *
     * @param \App\Models\Order $order
     * @param bool|null $loadRelation
     *
     * @return \Illuminate\Support\Collection
     */
    public function portionPoints(OrderModel $order, ?bool $loadRelation = true);

    /**
     * クーポン按分計算
     *
     * @param \App\Models\Order $order
     *
     * @return array
     */
    public function portionCoupons(OrderModel $order);
}
