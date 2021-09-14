<?php

namespace App\Domain\Utils;

use Carbon\Carbon;

class Cart
{
    /**
     * 最大有効時間(分)
     */
    private const VALID_MAX_TIME = 90;

    /**
     * 最小有効時間(分)
     */
    private const VALID_MIN_TIME = 0;

    /**
     * 在庫確保時間計算
     *
     * @param string $postedAt
     *
     * @return int
     */
    public static function calculateValidTime(string $postedAt = null)
    {
        if (!$postedAt) {
            return self::VALID_MIN_TIME;
        }
        $now = Carbon::now();
        $target = new Carbon($postedAt);
        $validTime = self::VALID_MAX_TIME - (int) ceil($now->diffInMinutes($target));
        if ($validTime <= self::VALID_MIN_TIME) {
            return self::VALID_MIN_TIME;
        }

        return $validTime;
    }

    /**
     * 在庫確保期限を過ぎているかどうか
     *
     * @param \App\Models\CartItem
     *
     * @return bool
     */
    public static function hasNoTime(\App\Models\CartItem $cart)
    {
        return self::calculateValidTime($cart->posted_at) === self::VALID_MIN_TIME;
    }

    /**
     * 有効なカート商品のposted_atの下限値を計算する
     *
     * @return void
     */
    public static function computeAliveItemPostedAtBound()
    {
        return Carbon::now()->subMinutes(self::VALID_MAX_TIME);
    }
}
