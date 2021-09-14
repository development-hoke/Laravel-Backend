<?php

namespace App\HttpCommunication\FRegi;

interface PurchaseInterface
{
    const ENDPOINT_AUTH = 'auth';
    const ENDPOINT_SALE = 'sale';
    const ENDPOINT_AUTH_CANCEL = 'auth_cancel';
    const ENDPOINT_AUTH_CHANGE = 'auth_change';
    const ENDPOINT_SALE_CANCEL = 'sale_cancel';
    const ENDPOINT_SALE_CHANGE = 'sale_change';
    const ENDPOINT_FETCH_CUSTOMER_INFO = 'fetch_customer_info';
    const ENDPOINT_LEAVE_CUSTOMER = 'leave_customer';

    /**
     * オーソリ処理
     *
     * @param array $authParam
     *
     * @return \App\HttpCommunication\Response\ResponseInterface
     */
    public function auth(array $authParam);

    /**
     * 売り上げ処理
     *
     * @param string $authorizationNumber
     * @param string $transactionNumber
     *
     * @return \App\HttpCommunication\Response\ResponseInterface
     */
    public function sale(string $authorizationNumber, string $transactionNumber);

    /**
     * オーソリキャンセル
     *
     * @param string $transactionNumber
     *
     * @return \App\HttpCommunication\Response\ResponseInterface
     */
    public function authCancel(string $transactionNumber);

    /**
     * 承認金額変更
     *
     * @param string $transactionNumber
     * @param array $params
     *
     * @return \App\HttpCommunication\Response\ResponseInterface
     */
    public function authChange(string $transactionNumber, array $params);

    /**
     * 売上取消
     *
     * @param string $transactionNumber
     *
     * @return \App\HttpCommunication\Response\ResponseInterface
     */
    public function saleCancel(string $transactionNumber);

    /**
     * 売上金額変更
     *
     * @param string $transactionNumber
     * @param array $params
     *
     * @return \App\HttpCommunication\Response\ResponseInterface
     */
    public function saleChange(string $transactionNumber, array $params);

    /**
     * 顧客情報取得
     *
     * @param int $memberCreditCardId
     *
     * @return \App\HttpCommunication\Response\ResponseInterface
     */
    public function fetchCustomerInfo(int $memberCreditCardId);

    /**
     * 顧客ID削除
     *
     * @param int $memberCreditCardId
     *
     * @return \App\HttpCommunication\Response\ResponseInterface
     */
    public function leaveCustomer(int $memberCreditCardId);
}
