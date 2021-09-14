<?php

namespace App\Domain;

/**
 * 支払い方法別の処理をまとめるクラス
 */
interface PaymentInterface
{
    /**
     * 注文キャンセルを実行する
     *
     * @param \App\Models\Order $order
     *
     * @return bool
     */
    public function cancelOrder(\App\Models\Order $order);

    /**
     * 売上確定前の請求金額変更
     *
     * @param \App\Models\Order $order
     *
     * @return mixed
     */
    public function updateBillingAmount(\App\Models\Order $order);

    /**
     * 売上確定処理
     *
     * @param \App\Models\Order $order
     *
     * @return mixed
     */
    public function sale(\App\Models\Order $order);

    /**
     * 一部返金
     *
     * @param \App\Models\Order $order
     * @param int $amount
     *
     * @return mixed
     */
    public function refundPartially(\App\Models\Order $order, int $amount);

    /**
     * 返金処理
     *
     * @param \App\Models\Order $order
     *
     * @return mixed
     */
    public function refund(\App\Models\Order $order);

    /**
     * 配送先住所の更新
     *
     * @param \App\Models\Order $order
     *
     * @return mixed
     */
    public function updateShippingAddress(\App\Models\Order $order);
}
