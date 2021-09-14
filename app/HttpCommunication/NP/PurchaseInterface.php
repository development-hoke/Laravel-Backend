<?php

namespace App\HttpCommunication\NP;

interface PurchaseInterface
{
    const ENDPOINT_TRANSACTIONS = 'transactions';
    const ENDPOINT_UPDATE_TRANSACTION = 'update_transaction';
    const ENDPOINT_SHIPMENTS = 'shipments';
    const ENDPOINT_TRANSACTION_REREGISTER = 'transaction_reregister';
    const ENDPOINT_TRANSACTION_CANCEL = 'transaction_cancel';

    /**
     * 注文登録
     *
     * @param array $params
     * @param array|null $options
     *
     * @return \App\HttpCommunication\Response\ResponseInterface
     */
    public function transactions(array $params, ?array $options = []);

    /**
     * 注文変更
     *
     * @param string $npTransactionId
     * @param array $params
     * @param array|null $options
     *
     * @return \App\HttpCommunication\Response\ResponseInterface
     */
    public function updateTransaction(string $npTransactionId, array $params, ?array $options = []);

    /**
     * 一部返品再登録
     *
     * @param string $npTransactionId
     * @param array $params
     * @param array|null $options
     *
     * @return \App\HttpCommunication\Response\ResponseInterface
     */
    public function reregister(string $npTransactionId, array $params, ?array $options = []);

    /**
     * 出荷報告
     *
     * @param array $params
     * @param array|null $options
     *
     * @return \App\HttpCommunication\Response\ResponseInterface
     */
    public function shipments(array $params, ?array $options = []);

    /**
     * 取引キャンセル
     *
     * @param string $npTransactionId
     * @param array|null $options
     *
     * @return \App\HttpCommunication\Response\ResponseInterface
     */
    public function cancel(string $npTransactionId, ?array $options = []);
}
