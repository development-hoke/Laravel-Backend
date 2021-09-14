<?php

namespace App\Domain;

interface StoreInterface
{
    /**
     * ショップIDから店舗情報を取得します。
     * キャッシュします。
     *
     * @param int $shopId
     * @param bool $isCache
     *
     * @return \App\Models\Shop
     */
    public function get($shopId = null, $isCache = false);
}
