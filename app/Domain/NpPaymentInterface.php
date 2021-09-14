<?php

namespace App\Domain;

interface NpPaymentInterface
{
    /**
     * @param \App\Models\Order $order
     *
     * @return \App\Models\OrderNp
     */
    public function createTransaction(\App\Models\Order $order);

    /**
     * 取引請求金額変更
     *
     * @param \App\Models\Order $order
     *
     * @return \App\Models\OrderNp
     */
    public function updateTransactionBilledAmount(\App\Models\Order $order);

    /**
     * 配送先の更新
     *
     * @param \App\Models\Order $order
     *
     * @return \App\Models\OrderNp
     */
    public function updateDestination(\App\Models\Order $order);

    /**
     * NP後払い決済情報保存
     *
     * @param \App\Entities\Np\Transaction $failedTransaction
     * @param int|null $status
     *
     * @return \App\Models\OrderNp
     */
    public function importFailedTransactionStatus(\App\Entities\Np\Transaction $failedTransaction, ?int $status = null);

    /**
     * @param int $orderId
     *
     * @return \App\Entities\Np\Shipment
     */
    public function shipment(int $orderId);

    /**
     * 一部返品取引再登録処理
     *
     * @param \App\Models\Order $order
     *
     * @return \App\Models\OrderNp
     */
    public function reregister(\App\Models\Order $order);

    /**
     * order_npをキャンセル済み・再登録失敗のステータスに変更する
     *
     * @param int $orderId
     *
     * @return \App\Models\OrderNp
     */
    public function updateOrderNpToCanceledButFailedReregister(int $orderId);

    /**
     * np_rejected_transactionsの作成
     *
     * @param int $cartId
     * @param int $memberId
     * @param \App\Entities\Np\Transaction $transaction
     * @param array $errorCodes
     *
     * @return \App\Models\NpRejectedTransaction
     */
    public function createNpRejectedTransaction(int $cartId, int $memberId, \App\Entities\Np\Transaction $transaction, array $errorCodes = null);

    /**
     * キャンセル処理
     *
     * @param int $orderId
     *
     * @return bool
     */
    public function cancel(int $orderId);
}
