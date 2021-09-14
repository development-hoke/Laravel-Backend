<?php

namespace App\Services\Front;

use App\Models\Cart;

interface PointServiceInterface
{
    /**
     * 購買時ポイント計算APIを利用して還元ポイントの取得
     *
     * @param Cart $cart
     * @param array $prices
     * @param array $options
     *
     * @return array
     */
    public function getPoint(Cart $cart, array $prices, array $options = []);
}
