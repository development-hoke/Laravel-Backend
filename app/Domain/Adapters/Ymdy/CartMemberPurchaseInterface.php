<?php

namespace App\Domain\Adapters\Ymdy;

interface CartMemberPurchaseInterface
{
    /**
     * メンバートークンの設定
     *
     * @param string $token
     *
     * @return static
     */
    public function setMemberToken(string $token);

    /**
     * スタッフトークンの設定
     *
     * @param string $token
     *
     * @return static
     */
    public function setStaffToken(string $token);

    /**
     * 付与ポイントの計算
     *
     * @param \App\Models\Cart $cart
     *
     * @return array
     */
    public function calculatePoint(\App\Models\Cart $cart, array $prices, array $options = []);
}
