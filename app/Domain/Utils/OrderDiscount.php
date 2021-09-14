<?php

namespace App\Domain\Utils;

class OrderDiscount
{
    /**
     * @param int $type
     *
     * @return int
     */
    public static function getMethodByCouponDiscountType($type)
    {
        if ((int) $type === \App\Enums\Coupon\DiscountType::Fixed) {
            return \App\Enums\OrderDiscount\Method::Fixed;
        }

        return \App\Enums\OrderDiscount\Method::Percentile;
    }

    /**
     * 商品展示時（カート投入前）の割引種別 (通常割引, 会員割引, 社員割引, イベントセール)
     *
     * @return array
     */
    public static function getDisplayedDiscountTypes()
    {
        return [
            \App\Enums\OrderDiscount\Type::Normal,
            \App\Enums\OrderDiscount\Type::Member,
            \App\Enums\OrderDiscount\Type::Staff,
            \App\Enums\OrderDiscount\Type::EventSale,
            \App\Enums\OrderDiscount\Type::Reservation,
        ];
    }

    /**
     * バンドル販売セール割引種別
     *
     * @return int
     */
    public static function getBundleSaleDiscountType()
    {
        return \App\Enums\OrderDiscount\Type::EventBundle;
    }

    /**
     * 配送料割引種別
     *
     * @return array
     */
    public static function getDeliveryFeeDiscountTypes()
    {
        return [
            \App\Enums\OrderDiscount\Type::DeliveryFee,
            \App\Enums\OrderDiscount\Type::CouponDeliveryFee,
            \App\Enums\OrderDiscount\Type::ReservationDeliveryFee,
        ];
    }

    /**
     * クーポン割引種別
     *
     * @return int
     */
    public static function getCouponItemDiscountType()
    {
        return \App\Enums\OrderDiscount\Type::CouponItem;
    }

    /**
     * 注文前 (カート投入後) に適用される割引種別
     *
     * @return array
     */
    public static function getDiscountTypesAppliedBeforeOrder()
    {
        $discountTypes = static::getDisplayedDiscountTypes();

        $discountTypes[] = static::getBundleSaleDiscountType();

        return $discountTypes;
    }

    /**
     * @param int $type
     *
     * @return int
     */
    public static function convertItemDiscoutToOrderDiscount($type)
    {
        switch ((int) $type) {
            case \App\Enums\Item\DiscountType::Normal:
                return \App\Enums\OrderDiscount\Type::Normal;

            case \App\Enums\Item\DiscountType::Member:
                return \App\Enums\OrderDiscount\Type::Member;

            case \App\Enums\Item\DiscountType::Event:
                return \App\Enums\OrderDiscount\Type::EventSale;

            case \App\Enums\Item\DiscountType::Staff:
                return \App\Enums\OrderDiscount\Type::Staff;

            case \App\Enums\Item\DiscountType::Reservation:
                return \App\Enums\OrderDiscount\Type::Reservation;

            case \App\Enums\Item\DiscountType::None:
            default:
                return null;
        }
    }

    /**
     * 適用優先度を取得
     * NOTE: 現状配送料割引のみ優先度指定がある。
     *
     * @param int $type \App\Enums\OrderDiscount\Type
     *
     * @return int
     */
    public static function getPriorityByType($type)
    {
        $settings = config('constants.order_discount.priority');

        return $settings[$type] ?? $settings['default'];
    }
}
