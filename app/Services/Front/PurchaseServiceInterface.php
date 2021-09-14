<?php

namespace App\Services\Front;

interface PurchaseServiceInterface
{
    /**
     * 注文処理
     *
     * @param Cart $cart
     * @param array $params
     *
     * @return array
     *
     * @throws FatalException
     */
    public function order(\App\Models\Cart $cart, array $params);

    /**
     * カード情報取得
     *
     * @return \App\Models\MemberCreditCard|null
     */
    public function fetchCreditCardInfo();

    /**
     * カード情報削除
     *
     * @param int $id
     *
     * @return bool
     */
    public function deleteCreditCardInfo(int $id);
}
