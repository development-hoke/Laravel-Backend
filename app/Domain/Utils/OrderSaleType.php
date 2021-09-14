<?php

namespace App\Domain\Utils;

use App\Enums\Event\Target;
use App\Enums\Order\SaleType;
use App\Models\Event;

class OrderSaleType
{
    /**
     * ECにおいてのプロパー・セールの判断
     *
     * @param array|null $member
     * @param Event|null $event
     * @param array $useCouponIds
     *
     * @return int
     */
    public static function getSaleTypeForEC(array $member = null, Event $event = null, array $useCouponIds = [])
    {
        // 社割で値引き価格で販売 : プロパー
        if (!empty($member) && Member::isStaffAccount($member)) {
            return SaleType::Employee;
        }
        // 通常
        if (!$event) {
            // 社割を除く値引き(クーポン割引)価格で販売 : セール
            if (count($useCouponIds) > 0) {
                return SaleType::Sale;
            }

            // 上代のまま販売 : プロパー
            return SaleType::Employee;
        }
        // イベント
        if ($event->sale_type === Target::Employee) {
            return SaleType::Employee;
        } else {
            return SaleType::Sale;
        }
    }

    /**
     * 商品に適用された割引タイプから、適用可能なセールタイプの候補を取得する
     *
     *   Note: 下記のメソッドでItemの情報を取得する必要がある
     *
     *   - \App\Domain\ItemPrice::fillPriceBeforeOrder
     *   - \App\Domain\ItemPrice::fillDisplayedSalePrice
     *
     * @param \App\Models\Item $item
     *
     * @return int \App\Enums\Order\SaleType
     */
    public static function getSaleTypeByItem(\App\Models\Item $item)
    {
        if (!empty($item->appliedBundleSale) && (int) $item->appliedBundleSale->event->target === Target::Sale) {
            return SaleType::Sale;
        }

        switch ($item->displayed_discount_type) {
            case \App\Enums\Item\DiscountType::Normal:
            case \App\Enums\Item\DiscountType::Member:
                return SaleType::Sale;

            case \App\Enums\Item\DiscountType::Event:
                return (int) $item->applicableEvent->target === Target::Employee
                    ? SaleType::Employee
                    : SaleType::Sale;

            default:
                return SaleType::Employee;
        }
    }

    /**
     * @param \App\Models\OrderDetail $orderDetail
     *
     * @return int \App\Enums\Order\SaleType
     */
    public static function getSaleTypeByOrderDetail(\App\Models\OrderDetail $orderDetail)
    {
        if (!empty($orderDetail->bundleSaleDiscount) && $orderDetail->bundleSaleDiscount->discountable->sale_type === Target::Sale) {
            return SaleType::Sale;
        }

        if (empty($orderDetail->displayedDiscount)) {
            return SaleType::Employee;
        }

        switch ($orderDetail->displayedDiscount->type) {
            case \App\Enums\OrderDiscount\Type::Normal:
            case \App\Enums\OrderDiscount\Type::Member:
                return SaleType::Sale;

            case \App\Enums\OrderDiscount\Type::EventSale:
                return $orderDetail->displayedDiscount->discountable->sale_type === Target::Sale
                    ? SaleType::Sale
                    : SaleType::Employee;

            default:
                return SaleType::Employee;
        }
    }
}
