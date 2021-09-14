<?php

namespace App\Domain\Utils;

use App\Exceptions\FatalException;

class Item
{
    /**
     * @param \App\Models\Item $item
     *
     * @return bool
     */
    public static function shouldDisplaySoldOut(\App\Models\Item $item)
    {
        return $item->is_sold_out || (int) $item->sales_status !== \App\Enums\Item\SalesStatus::InStoreNow;
    }

    /**
     * 自社製品 メーカー品番前半部分
     *
     * @return array
     */
    public static function getOwnProductMakerProductNumberPrefixes()
    {
        return config('constants.cart.maker_product_number.owns');
    }

    /**
     * 予約販売在庫僅少閾値を超えたか
     *
     * @param \App\Models\Item $item
     * @param \App\Models\ItemDetail $itemDetail
     *
     * @return bool
     */
    public static function isAlmostOutOfReservableStock(\App\Models\Item $item, \App\Models\ItemDetail $itemDetail)
    {
        if (!$item->is_reservation) {
            return false;
        }

        return $itemDetail->reservable_stock < $item->appliedReservation->limited_stock_threshold;
    }

    /**
     * カート追加可能か判定
     *
     * @param \App\Models\ItemDetail $itemDetail
     * @param int|null $requestCount
     *
     * @return bool
     */
    public static function canAddToCart(\App\Models\ItemDetail $itemDetail, ?int $requestCount = 1)
    {
        return ($itemDetail->secuarable_ec_stock - $requestCount) >= 0;
    }

    /**
     * カート追加可能か判定
     *
     * @param \App\Models\ItemDetail $itemDetail
     * @param int|null $requestCount
     *
     * @return bool
     */
    public static function canAddToCartClosedMarket(\App\Models\ItemDetail $itemDetail, ?int $requestCount = 1)
    {
        if (empty($itemDetail->appliedClosedMarket)) {
            throw new FatalException(__('error.no_applied_closed_market'));
        }

        return ($itemDetail->appliedClosedMarket->secuarable_stock - $requestCount) >= 0;
    }

    /**
     * 予約可能か判定
     *
     * @param \App\Models\Item $item
     * @param \App\Models\ItemDetail $itemDetail
     * @param int|null $requestCount
     *
     * @return bool
     */
    public static function canReserve(\App\Models\Item $item, \App\Models\ItemDetail $itemDetail, ?int $requestCount = 1)
    {
        return $item->is_reservation
            && ($itemDetail->secuarable_reservable_stock - $requestCount) >= 0;
    }

    /**
     * お取り寄せが可能か判定
     *
     * @param \App\Models\ItemDetail $itemDetail
     *
     * @return bool
     */
    public static function canBackOrder(\App\Models\ItemDetail $itemDetail)
    {
        if (!$itemDetail->item->back_orderble) {
            return;
        }

        $threshold = \App\Domain\Utils\Stock::computeBackOrderbleStockThreshold($itemDetail);

        return $itemDetail->store_stock >= $threshold;
    }

    /**
     * 再入荷リクエストが可能か判定
     *
     * @param \App\Models\ItemDetail $itemDetail
     *
     * @return bool
     */
    public static function canRequestRedisplay(\App\Models\ItemDetail $itemDetail)
    {
        return (bool) ((int) $itemDetail->redisplay_requested);
    }

    /**
     * 適用可能なカートステータスを取得する
     *
     * @param \App\Models\Item $item
     * @param \App\Models\ItemDetail $itemDetail
     *
     * @return int
     */
    public static function getApplicableCartStatus(\App\Models\Item $item, \App\Models\ItemDetail $itemDetail)
    {
        // 闇市
        if ($item->is_closed_market) {
            return static::getApplicableClosedMarketItemCartStatus($itemDetail);
        }

        // 予約販売の場合と処理を分岐する
        if ($item->is_reservation) {
            return static::getApplicableReservationItemCartStatus($item, $itemDetail);
        }

        // ここからは予約販売以外の商品
        return static::getApplicableNormalItemCartStatus($itemDetail);
    }

    /**
     * @param \App\Models\ItemDetail $itemDetail
     *
     * @return int
     */
    public static function getApplicableClosedMarketItemCartStatus(\App\Models\ItemDetail $itemDetail)
    {
        if (static::canAddToCartClosedMarket($itemDetail)) {
            return \App\Enums\Cart\Status::Add;
        }

        return \App\Enums\Cart\Status::SoldOut;
    }

    /**
     * @param \App\Models\Item $item
     * @param \App\Models\ItemDetail $itemDetail
     *
     * @return int
     */
    public static function getApplicableReservationItemCartStatus(\App\Models\Item $item, \App\Models\ItemDetail $itemDetail)
    {
        if (static::canReserve($item, $itemDetail)) {
            return \App\Enums\Cart\Status::Reserve;
        }

        if (static::canRequestRedisplay($itemDetail)) {
            return \App\Enums\Cart\Status::RestockRequest;
        }

        return \App\Enums\Cart\Status::SoldOut;
    }

    /**
     * @param \App\Models\ItemDetail $itemDetail
     *
     * @return int
     */
    public static function getApplicableNormalItemCartStatus(\App\Models\ItemDetail $itemDetail)
    {
        if (static::canAddToCart($itemDetail)) {
            return \App\Enums\Cart\Status::Add;
        }

        if (static::canBackOrder($itemDetail)) {
            return \App\Enums\Cart\Status::Order;
        }

        if (static::canRequestRedisplay($itemDetail)) {
            return \App\Enums\Cart\Status::RestockRequest;
        }

        return \App\Enums\Cart\Status::SoldOut;
    }

    /**
     * @param \App\Models\Item $item
     *
     * @return bool
     */
    public static function isSoldOutItem(\App\Models\Item $item)
    {
        foreach ($item->itemDetails as $itemDetail) {
            if ((int) $itemDetail->applicable_cart_status !== \App\Enums\Cart\Status::SoldOut) {
                return false;
            }
        }

        return true;
    }
}
