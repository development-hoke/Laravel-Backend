<?php

namespace App\Domain\Utils;

class Stock
{
    /**
     * お取り寄せ注文可能な在庫数のしきい値を取得
     *
     * @param \App\Models\Item $item
     *
     * @return int
     */
    public static function computeBackOrderbleStockThreshold(\App\Models\ItemDetail $itemDetail)
    {
        // 現仕様だと固定値だが、また可変にする可能性があるので、このメソッド通して返却するようにしておく。
        return config('constants.stock.back_orderble_min_stock');
    }

    /**
     * 在庫確保可能ができない注文タイプ
     *
     * @return array
     */
    public static function getNotSecurableOrderTypes()
    {
        return [\App\Enums\Order\OrderType::BackOrder];
    }

    /**
     * 在庫確保が可能な注文タイプか判定
     *
     * @param int $type
     *
     * @return bool
     */
    public static function isSecurableOrderType(int $type)
    {
        return in_array($type, static::getNotSecurableOrderTypes(), true) === false;
    }

    /**
     * EC側で店舗在庫として扱うデータを判定する。
     * EC側店舗在庫 = 店舗在庫 + 本部在庫
     *
     * @param \App\Entities\Ymdy\Shohin\Item $item
     *
     * @return bool
     */
    public static function isStoreStock(\App\Entities\Ymdy\Shohin\Item $item)
    {
        // 店舗在庫
        if ((int) $item->shop_id !== config('constants.store.headquarter_store_id')) {
            if ((int) $item->item_status_id === \App\Enums\TempStock\ItemStatus::StoreStock) {
                return true;
            }
        }

        // 本部在庫
        if ((int) $item->shop_id === config('constants.store.headquarter_store_id')) {
            if ((int) $item->item_status_id === \App\Enums\TempStock\ItemStatus::Stock) {
                return true;
            }
        }

        return false;
    }
}
