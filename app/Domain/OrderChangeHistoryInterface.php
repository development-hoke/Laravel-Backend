<?php

namespace App\Domain;

interface OrderChangeHistoryInterface
{
    /**
     * @param \App\Models\OrderDetail $orderDetail
     * @param \App\Models\OrderDetailUnit $unit
     * @param int $amount
     * @param int $price
     * @param int $staffId
     *
     * @return \App\Models\OrderChangeHistory
     */
    public function createAddingUnitHistory(
        \App\Models\OrderDetail $orderDetail,
        \App\Models\OrderDetailUnit $unit,
        int $amount,
        int $price,
        int $staffId
    );

    /**
     * バンドル販売再計算のときに生じた差額を履歴として保存する
     *
     * @param \App\Models\OrderDetail $orderDetail
     * @param \App\Models\OrderDiscount $orderDiscount
     * @param int $originalAppliedPrice
     * @param int $amount
     * @param int $staffId
     *
     * @return \App\Models\OrderChangeHistory
     */
    public function createRecalculateBundleSaleHistory(
        \App\Models\OrderDetail $orderDetail,
        \App\Models\OrderDiscount $orderDiscount,
        int $originalAppliedPrice,
        int $amount,
        int $staffId
    );

    /**
     * 商品キャンセル
     *
     * @param \App\Models\Order $order
     * @param array $diffInfo
     * @param int $staffId
     *
     * @return \App\Models\OrderChangeHistory
     */
    public function createRemoveItemHistory(
        \App\Models\Order $order,
        array $diffInfo,
        int $staffId
    );

    /**
     * 商品返品
     *
     * @param \App\Models\Order $order
     * @param array $diffInfo
     * @param int $staffId
     *
     * @return \App\Models\OrderChangeHistory
     */
    public function createReturnItemHistory(
        \App\Models\Order $order,
        array $diffInfo,
        int $staffId
    );
}
