<?php

namespace App\Domain\Utils;

use Carbon\Carbon;

class ItemPrice
{
    /**
     * 社員割引率 自社製品
     *
     * @return float
     */
    public static function getStaffDiscountRateOwnProduct()
    {
        return config('constants.cart.employee_discount.own');
    }

    /**
     * 社員割引率 他社製品
     *
     * @return float
     */
    public static function getStaffDiscountRateOtherProduct()
    {
        return config('constants.cart.employee_discount.other');
    }

    /**
     * 公取委の制御。2週間以上値引き価格で売ったら元値が消える。
     *
     * @return int
     */
    public static function getDisplayOriginalPricePeriod()
    {
        return config('constants.item_price.display_original_price_period_days') * 24 * 60 * 60;
    }

    /**
     * 値引き適用後の金額
     *
     * @param \App\Models\Item|\App\Http\Resources\Item $item
     *
     * @return int
     */
    public static function calcDiscountedPrice($item)
    {
        return static::calcDiscountedPriceByScalar(
            $item->retail_price,
            $item->discount_rate
        );
    }

    /**
     * NOTE: 割引金額
     *
     * @param int $retailPrice
     * @param float $discountRate
     *
     * @return int
     */
    public static function calcDiscountingPriceByScalar($price, $discountRate)
    {
        $discountedPrice = static::calcDiscountedPriceByScalar($price, $discountRate);

        return $price - $discountedPrice;
    }

    /**
     * NOTE: 割引適用後の金額
     *
     * @param int $retailPrice
     * @param float $discountRate
     *
     * @return int
     */
    public static function calcDiscountedPriceByScalar($price, $discountRate)
    {
        return $price - floor($price * $discountRate);
    }

    /**
     * 会員値引き適用後の金額
     *
     * @param \App\Models\Item|\App\Http\Resources\Item $item
     *
     * @return int
     */
    public static function calcMemberDiscountedPrice($item)
    {
        return static::calcDiscountedPriceByScalar(
            $item->retail_price,
            $item->member_discount_rate
        );
    }

    /**
     * 商品展示時の割引価格
     *
     * @param \App\Models\Item $item
     *
     * @return int
     */
    public static function calcDisplayedDiscountPrice(\App\Models\Item $item)
    {
        if ((int) $item->displayed_discount_type === \App\Enums\Item\DiscountType::Reservation) {
            return static::calcReservationDiscountPrice($item);
        }

        return static::calcDiscountingPriceByScalar(
            $item->retail_price,
            $item->displayed_discount_rate
        );
    }

    /**
     * 予約販売により割り引かれた価格
     *
     * @param \App\Models\Item $item
     *
     * @return int
     */
    public static function calcReservationDiscountPrice(\App\Models\Item $item)
    {
        return $item->retail_price - $item->appliedReservation->reserve_price;
    }

    /**
     * discount_rateの最大値を取得
     *
     * @param \App\Models\Item|\App\Http\Resources\Item $item
     *
     * @return float
     */
    public static function computeMaximumDiscountRate($item)
    {
        return (float) $item->price_change_rate;
    }

    /**
     * discount_rateの最大値を取得
     *
     * @param \App\Models\Item|\App\Http\Resources\Item $item
     *
     * @return float
     */
    public static function isApplicableSalesPeriod($item)
    {
        $priceChangeDate = Carbon::parse($item->price_change_period);
        $salesPeriodFrom = Carbon::parse($item->sales_period_from);

        return $salesPeriodFrom->gt($priceChangeDate);
    }

    /**
     * discount_rateが適用可能な値か判定する
     *
     * @param \App\Models\Item|\App\Http\Resources\Item $item
     *
     * @return bool
     */
    public static function isApplicableDiscountRate($item)
    {
        $max = static::computeMaximumDiscountRate($item);

        if ($max == 0) {
            return false;
        }

        return $max <= $item->discount_rate;
    }

    /**
     * member_discount_rateが適用可能な値か判定する
     *
     * @param \App\Models\Item|\App\Http\Resources\Item $item
     *
     * @return bool
     */
    public static function isApplicableMemberDiscountRate($item)
    {
        $max = static::computeMaximumDiscountRate($item);

        if ($max == 0) {
            return false;
        }

        return $max <= $item->member_discount_rate;
    }

    /**
     * 商品合計金額
     *
     * バンドル販売が適用される場合
     * can_display_original_price = true の場合上代に対して打ち消し線が付けられる。
     * can_display_original_price = false の場合、displayed_sale_priceに対して打ち消し線が付けられる。
     *
     * バンドル販売が適用されない場合
     * can_display_original_price = true の場合上代に対して打ち消し線が付けられる。
     *
     * @param array $items
     *
     * @return int
     */
    public static function calculateTotal(array $items)
    {
        $itemsTotal = 0;
        foreach ($items as $item) {
            if ($item['bundle_sale_price']) {
                // バンドル販売が適用される場合
                $itemsTotal += $item['price_before_order'] * $item['count'];
            } else {
                // バンドル販売が適用されない場合
                if ($item['retail_price'] > $item['displayed_sale_price']) {
                    $itemsTotal += $item['displayed_sale_price'] * $item['count'];
                } else {
                    $itemsTotal += $item['retail_price'] * $item['count'];
                }
            }
        }

        return $itemsTotal;
    }

    /**
     * 社員割引合計
     *
     * @param array $items
     * @param array|null $member
     *
     * @return int
     */
    public static function calculateEmployeeDiscountTotal(array $items, array $member = null)
    {
        $total = 0;
        $isStaff = !empty($member) && \App\Domain\Utils\Member::isStaffAccount($member);
        // 社員の場合
        if ($isStaff) {
            foreach ($items as $item) {
                $total += self::calculateEmployeeDiscount($item['retail_price'], $item['maker_product_number']);
            }
        }

        return $total;
    }

    /**
     * 自社商品の場合は5割引、他社商品の場合は3割引
     *
     * @param int $retailPrice
     * @param string $makerProductNumber
     *
     * @return float
     */
    public static function calculateEmployeeDiscount(int $retailPrice, string $makerProductNumber)
    {
        $rate = self::getEmployeeDiscountRate($makerProductNumber);

        return static::calcDiscountingPriceByScalar($retailPrice, $rate);
    }

    /**
     * 社割適用後の価格
     * (自社商品の場合は5割引、他社商品の場合は3割引)
     *
     * @param \App\Models\Item $item
     *
     * @return int
     */
    public static function calculateEmployeeDiscountedPrice(\App\Models\Item $item)
    {
        $discountPrice = static::calculateEmployeeDiscount($item->retail_price, $item->maker_product_number);

        return $item->retail_price - $discountPrice;
    }

    /**
     * 割引率取得
     *
     * @param string $makerProductNumber
     *
     * @return float
     */
    public static function getEmployeeDiscountRate(string $makerProductNumber)
    {
        return self::isOwnItem($makerProductNumber) ? config('constants.cart.employee_discount.own') : config('constants.cart.employee_discount.other');
    }

    /**
     * @param string $makerProductNumber
     *
     * @return bool
     */
    public static function isOwnItem(string $makerProductNumber)
    {
        foreach (config('constants.cart.maker_product_number.owns') as $number) {
            if (strpos($makerProductNumber, $number) !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * 公取委の制御（2週間以上値引き価格で売ったら元値が消える。 SALEの場合はOK）
     *
     * @param \App\Models\Item $item
     *
     * @return bool
     */
    public static function canDisplayOriginalPrice(\App\Models\Item $item)
    {
        switch ($item->displayed_discount_type) {
            case \App\Enums\Item\DiscountType::Normal:
                return time() - strtotime($item->discount_rate_updated_at) <= static::getDisplayOriginalPricePeriod();
            case \App\Enums\Item\DiscountType::Member:
                return time() - strtotime($item->member_discount_rate_updated_at) <= static::getDisplayOriginalPricePeriod();
            case \App\Enums\Item\DiscountType::Reservation:
                return time() - strtotime($item->appliedReservation->period_from) <= static::getDisplayOriginalPricePeriod();
            default:
                return true;
        }
    }

    /**
     * 定率値引の商品割引タイプ
     *
     * @return array
     */
    public static function getPercentileMethodDisplayedDiscountTypes()
    {
        return [
            \App\Enums\Item\DiscountType::Normal,
            \App\Enums\Item\DiscountType::Member,
            \App\Enums\Item\DiscountType::Event,
            \App\Enums\Item\DiscountType::Staff,
        ];
    }

    /**
     * 定額値引の商品割引タイプ
     *
     * @return array
     */
    public static function getFixedMethodDisplayedDiscountTypes()
    {
        return [
            \App\Enums\Item\DiscountType::Reservation,
        ];
    }

    /**
     * 定率値引か判定する
     *
     * @param int $type
     *
     * @return bool
     */
    public static function isPercentileMethodDisplayedDiscountType(int $type)
    {
        return in_array($type, static::getPercentileMethodDisplayedDiscountTypes(), true);
    }

    /**
     * 定額値引か判定する
     *
     * @param int $type
     *
     * @return bool
     */
    public static function isFixedMethodDisplayedDiscountType(int $type)
    {
        return in_array($type, static::getFixedMethodDisplayedDiscountTypes(), true);
    }
}
