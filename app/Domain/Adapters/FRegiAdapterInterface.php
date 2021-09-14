<?php

namespace App\Domain\Adapters;

interface FRegiAdapterInterface
{
    /**
     * オーソリ
     *
     * @param array $params
     *
     * @return \App\Entities\FRegi\AuthResult
     */
    public function auth(array $params);

    /**
     * 売上処理
     *
     * @param string $authorizationNumber
     * @param string $transactionNumber
     *
     * @return \App\Entities\FRegi\SaleResult
     */
    public function sale(string $authorizationNumber, string $transactionNumber);

    /**
     * 承認金額変更
     *
     * @param string $transactionNumber
     * @param array $params
     *
     * @return \App\Entities\FRegi\AuthResult
     */
    public function authChange(string $transactionNumber, array $params);

    /**
     * 売上金額変更
     *
     * @param string $transactionNumber
     * @param array $params
     *
     * @return \App\Entities\FRegi\SaleResult
     */
    public function saleChange(string $transactionNumber, array $params);

    /**
     * オーソリキャンセル
     *
     * @param string $transactionNumber
     *
     * @return bool
     */
    public function authCancel(string $transactionNumber);

    /**
     * 売上取消
     *
     * @param string $authorizationNumber
     * @param string $transactionNumber
     *
     * @return \App\Entities\FRegi\SaleResult
     */
    public function saleCancel(string $transactionNumber);

    /**
     * 顧客情報取得
     *
     * @param int $memberCreditCardId
     *
     * @return \App\Entities\FRegi\CustomerInfo
     */
    public function fetchCustomerInfo(int $memberCreditCardId);

    /**
     * 顧客ID削除
     *
     * @param int $memberCreditCardId
     *
     * @return bool
     */
    public function leaveCustomer(int $memberCreditCardId);
}
