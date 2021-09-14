<?php

namespace App\Domain;

/**
 * 商品関連の割引の処理を担当するクラス
 */
interface ItemOrderDiscountInterface
{
    /**
     * 受注に対して割引情報を作成してorderに読み込む。 (新規注文用)
     *
     * @param \App\Models\Order $order
     *
     * @return void
     */
    public function createAndLoadItemOrderDiscounts(\App\Models\Order $order);

    /**
     * 展示時の割引を作成して読み込む
     *
     * @param \App\Models\Order $order
     * @param \App\Models\OrderDetail $orderDetail
     * @param \App\Models\Item $item
     * @param int|null $staffId
     *
     * @return array
     */
    public function createAndLoadDisplayedDiscount(
        \App\Models\Order $order,
        \App\Models\OrderDetail $orderDetail,
        \App\Models\Item $item,
        ?int $staffId = null
    );

    /**
     * バンドル販売タイプのorder_discountsを作成
     *
     * @param \App\Models\OrderDetail $orderDetail
     * @param \App\Models\Item $item
     * @param int|null $staffId
     *
     * @return \App\Models\OrderDiscount
     */
    public function createBundleSaleDiscount(\App\Models\OrderDetail $orderDetail, \App\Models\Item $item, ?int $staffId = null);

    /**
     * @param \App\Models\OrderDetail $orderDetail
     * @param \App\Models\Item $requestedItem
     * @param int|null $staffId
     *
     * @return \App\Models\OrderDiscount
     */
    public function createDisplayedOrderDiscount(\App\Models\OrderDetail $orderDetail, \App\Models\Item $requestedItem, ?int $staffId = null);

    /**
     * applied_priceの更新。数量に影響を受けるため、個別のメソッドを設ける。
     *
     * @param \App\Models\OrderDetail $orderDetail
     *
     * @return \App\Models\OrderDetail
     */
    public function updateDisplayedDiscountAppliedPrice(\App\Models\OrderDetail $orderDetail);

    /**
     * バンドル販売割引の更新
     * 対象商品の個数に影響を受けるため、まとめて更新する。
     *
     * @param \App\Models\Order $order
     * @param array $member
     * @param int $staffId
     *
     * @return \App\Models\Order
     */
    public function updateBundleSaleDiscount(\App\Models\Order $order, array $member, $staffId);
}
