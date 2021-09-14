<?php

namespace App\Domain;

use App\Repositories\StoreRepository;
use App\Utils\Cache;

class Store implements StoreInterface
{
    const DATA_EXPIRATION_SEC = 86400;

    /**
     * @var StoreRepository
     */
    private $storeRepository;

    public function __construct(StoreRepository $storeRepository)
    {
        $this->storeRepository = $storeRepository;
    }

    /**
     * ショップIDから店舗情報を取得します。
     * キャッシュします。
     *
     * @param int $shopId
     * @param bool $isCache
     *
     * @return \App\Models\Shop
     */
    public function get($shopId = null, $isCache = false)
    {
        if ($shopId == null) {
            return null;
        }

        $key = sprintf(Cache::KEY_STORE_ID, $shopId);
        if ($isCache && $shop = Cache::get($key)) {
            return $shop;
        }

        $shop = $this->storeRepository->find($shopId);
        Cache::put($key, $shop, self::DATA_EXPIRATION_SEC);

        return $shop;
    }
}
