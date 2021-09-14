<?php

namespace App\Services\Admin;

interface OrderServiceInterface
{
    /**
     * 検索
     *
     * @param array $params
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function search(array $params);

    /**
     * 注文情報取得
     *
     * @param int $id
     *
     * @return \App\Models\Order
     */
    public function findOne(int $id);

    /**
     * 更新
     *
     * @param array $attributes
     * @param int $id
     *
     * @return \App\Models\Order
     */
    public function update(array $attributes, int $id);

    /**
     * 受注キャンセル
     *
     * @param int $id
     *
     * @return \App\Models\Order
     */
    public function cancel(int $id);

    /**
     * クーポン追加処理
     *
     * @param array $attributes
     * @param int $id
     *
     * @return \App\Models\Order
     */
    public function addCoupon(array $attributes, int $id);

    /**
     * クーポ削除処理
     *
     * @param array $attributes
     * @param int $id
     *
     * @return \App\Models\Order
     */
    public function removeCoupon(array $attributes, int $id);

    /**
     * 金額の更新をする
     *
     * @param array $attributes
     * @param int $id
     *
     * @return \App\Models\Order
     */
    public function updatePrice(array $attributes, int $id);

    /**
     * 購入者へのメッセージ送信
     *
     * @param int $orderId
     * @param array $params
     *
     * @return \App\Models\OrderMessage
     */
    public function sendOrderMessage(int $orderId, array $params);

    /**
     * 注文返品
     *
     * @param int $orderId
     *
     * @return void
     */
    public function return($orderId);
}
