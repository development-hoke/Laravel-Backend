<?php

namespace App\Domain;

use Illuminate\Database\Eloquent\Collection;

interface OrderInterface
{
    /**
     * @param string $token
     *
     * @return static
     */
    public function setStaffToken(string $token);

    /**
     * @param string $token
     *
     * @return static
     */
    public function setMemberToken(string $token);

    /**
     * 受注詳細を新規作成する
     *
     * @param \App\Models\Order $order
     * @param \App\Models\Item $requestedItem
     * @param int $staffId
     *
     * @return \App\Models\OrderDetail
     */
    public function createOrderDetail(\App\Models\Order $order, \App\Models\ItemDetail $itemDetail, $staffId = null);

    /**
     * 商品詳細ごとに在庫割当。order_detail_unitsを新規作成する（新規注文用）
     *
     * @param \App\Models\OrderDetail $orderDetail
     * @param \App\Models\CartItem $cartItem
     *
     * @return Collection
     */
    public function addUnitsByFront(\App\Models\OrderDetail $orderDetail, \App\Models\CartItem $cartItem);

    /**
     * order_detail_unitsに商品を追加する
     *
     * @param \App\Models\OrderDetail $orderDetail
     * @param array $params ['amount' => int, 'order_type' => int, 'price' => int (optional), 'closed_market_id' => int (optional)]
     * @param array|null $options ['staff_id' => int, 'create_histories' => bool]
     *
     * @return Collection
     */
    public function addUnits(\App\Models\OrderDetail $orderDetail, array $params, ?array $options = []);

    /**
     * order_detail_unitsを削除する
     *
     * @param \App\Models\OrderDetail $orderDetail
     * @param int|null $staffId
     * @param bool $restoreStock
     *
     * @return \App\Models\OrderDetail
     */
    public function removeUnits(\App\Models\OrderDetail $orderDetail, ?int $staffId = null, ?bool $restoreStock = true);

    /**
     * 注文合計金額を更新する
     *
     * @param \App\Models\Order $order
     * @param bool|null $staffId
     * @param bool|null $skipLoadRelation
     *
     * @return \App\Models\Order
     */
    public function updateTotalPrice(\App\Models\Order $order, ?bool $staffId = null, ?bool $skipLoadRelation = false);

    /**
     * @param \App\Models\Order $order
     * @param bool|null $staffId
     *
     * @return \App\Models\Order
     */
    public function updateOrderDetailSaleTypes(\App\Models\Order $order, ?int $staffId = null);

    /**
     * 付与ポイントを最新の情報で会員ポイントシステムに問い合わせ
     *
     * @param \App\Models\Order $order
     * @param int|null $staffId
     *
     * @return \App\Models\Order
     */
    public function updateAddPoint(\App\Models\Order $order, ?int $staffId = null);

    /**
     * キャンセル
     *
     * @param \App\Models\Order $order
     * @param int|null $staffId
     *
     * @return \App\Models\Order
     */
    public function cancel(\App\Models\Order $order, ?int $staffId = null);

    /**
     * 注文全返品
     *
     * @param int $orderId
     * @param int|null $staffId
     *
     * @return \App\Models\Order
     */
    public function return(int $orderId, ?int $staffId = null);

    /**
     * 一部商品返品
     *
     * @param int $orderDetailId
     * @param int|null $staffId
     *
     * @return \App\Models\RefundDetail
     */
    public function returnItem(int $orderDetailId, ?int $staffId = null);
}
