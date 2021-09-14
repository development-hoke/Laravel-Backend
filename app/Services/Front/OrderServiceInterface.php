<?php

namespace App\Services\Front;

interface OrderServiceInterface
{
    /**
     * ゲスト購入の請求先とお届け先を保存
     *
     * @param Order $order
     * @param array $params
     * @param array $member
     */
    public function createGuestOrderAddresses(\App\Models\Order $order, array $params, array $member);
}
