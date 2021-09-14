<?php

namespace App\Domain\Utils;

class OrderPrice
{
    /**
     * 割引金額
     *
     * @param int $retailPrice
     * @param float $discountRate
     *
     * @return float
     */
    public static function calcDiscountingPriceByScalar($price, $discountRate)
    {
        return ItemPrice::calcDiscountingPriceByScalar($price, $discountRate);
    }

    /**
     * 割引適用後の金額
     *
     * @param int $retailPrice
     * @param float $discountRate
     *
     * @return int
     */
    public static function calcDiscountedPriceByScalar($price, $discountRate)
    {
        return ItemPrice::calcDiscountedPriceByScalar($price, $discountRate);
    }

    /**
     * 適用された金額
     *
     * @param int $retailPrice
     * @param float $discountRate
     *
     * @return int
     */
    public static function calcAppliedDiscountPriceByScalar($price, $discountRate)
    {
        return $price - ItemPrice::calcDiscountedPriceByScalar($price, $discountRate);
    }

    /**
     * 商品展示時（カート投入前）の割引金額 (通常割引, 会員割引, 社員割引, イベントセールのどれか)
     *
     * @param \App\Models\OrderDetail $orderDetail
     *
     * @return int
     */
    public static function computeDisplayedSalePrice(\App\Models\OrderDetail $orderDetail)
    {
        if ($orderDetail->displayed_discount_method === \App\Enums\OrderDiscount\Method::Fixed) {
            return $orderDetail->retail_price - $orderDetail->displayed_discount_price;
        }

        return static::calcDiscountedPriceByScalar($orderDetail->retail_price, $orderDetail->displayed_discount_rate);
    }

    /**
     * バンドル販売値引きの割引金額
     *
     * @param \App\Models\OrderDetail $orderDetail
     *
     * @return int
     */
    public static function computeBundleDiscountPrice($orderDetail)
    {
        if (empty($orderDetail->bundle_discount_rate)) {
            return 0;
        }

        $priceBeforCart = static::computeDisplayedSalePrice($orderDetail);

        return static::calcDiscountingPriceByScalar($priceBeforCart, $orderDetail->bundle_discount_rate);
    }

    /**
     * バンドル販売値引き適用後の金額
     *
     * @param \App\Models\OrderDetail $orderDetail
     *
     * @return int
     */
    public static function computeBundleSalePrice($orderDetail)
    {
        $priceBeforCart = static::computeDisplayedSalePrice($orderDetail);

        return static::calcDiscountedPriceByScalar($priceBeforCart, $orderDetail->bundle_discount_rate);
    }

    /**
     * 受注以前の表示販売価格 (`computeBundleSalePrice` のプロキシ)
     *
     * @param \App\Models\OrderDetail $orderDetail
     *
     * @return int
     */
    public static function computePriceBeforeOrder($orderDetail)
    {
        return static::computeBundleSalePrice($orderDetail);
    }

    /**
     * 支払い手数料を取得
     *
     * @param int $paymentType
     *
     * @return int
     */
    public static function getPaymentFee(int $paymentType)
    {
        $config = config('constants.order.payment_fee');

        return $config[$paymentType] ?? 0;
    }
}
