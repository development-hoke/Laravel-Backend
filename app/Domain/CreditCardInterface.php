<?php

namespace App\Domain;

interface CreditCardInterface
{
    const ERR_MEMBER_CREDIT_CARD_NOT_FOUND = 1;

    /**
     * クレジット決済 承認処理
     *
     * @param \App\Models\Order $order
     * @param array $params
     *
     * @return \App\Models\OrderCredit
     *
     * @throws \App\Exceptions\InvalidArgumentValueException
     */
    public function auth(\App\Models\Order $order, array $params);

    /**
     * 顧客情報取得
     *
     * @param int $memberId
     *
     * @return \App\Models\MemberCreditCard|null
     */
    public function fetchCustomerInfo(int $memberId);

    /**
     * オーソリキャンセル
     *
     * @param int $orderId
     *
     * @return bool
     */
    public function authCancel(int $orderId);

    /**
     * 承認金額変更
     *
     * @param int $orderId
     * @param int $price
     *
     * @return \App\Models\OrderCredit
     */
    public function changeAuthPrice(int $orderId, int $price);

    /**
     * クレジット決済 売り上げ処理
     *
     * @param int $orderId
     *
     * @return \App\Models\OrderCredit
     */
    public function sale(int $orderId);

    /**
     * 売上取消
     *
     * @param int $orderId
     *
     * @return void
     */
    public function saleCancel(int $orderId);

    /**
     * 売上金額の変更
     *
     * @param int $orderId
     * @param int $price
     *
     * @return \App\Models\OrderCredit
     */
    public function changeSalePrice(int $orderId, int $price);

    /**
     * 顧客情報削除
     *
     * @param int $id
     *
     * @return bool
     */
    public function deleteCustomerInfo(int $id);
}
