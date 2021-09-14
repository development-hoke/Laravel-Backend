<?php

namespace Tests\Unit\Domain\Utils;

use App\Domain\Utils\CouponDiscount;
use App\Http\Resources\Coupon;
use Tests\TestCase;

class CouponDiscountTest extends TestCase
{
    public function dataCalculateTotal()
    {
        $sampleCoupons = [
            [
                'coupon' => [
                    'id' => 11,
                    'discount_amount' => 1000,
                ],
            ],
            [
                'coupon' => [
                    'id' => 12,
                    'discount_amount' => 2000,
                ],
            ],
        ];

        return [
            'クーポン利用なし' => [
                [
                    'use_coupon_ids' => [],
                    'coupons' => $sampleCoupons,
                ],
                0,
            ],
            'クーポン利用 1件' => [
                [
                    'use_coupon_ids' => [11],
                    'coupons' => $sampleCoupons,
                ],
                1000,
            ],
            'クーポン利用 2件' => [
                [
                    'use_coupon_ids' => [11, 12],
                    'coupons' => $sampleCoupons,
                ],
                3000,
            ],
            '利用できないクーポンを指定' => [
                [
                    'use_coupon_ids' => [1],
                    'coupons' => $sampleCoupons,
                ],
                0,
            ],
        ];
    }

    /**
     * @param $param
     * @param $expected
     * @dataProvider dataCalculateTotal
     */
    public function testCalculateTotal($param, $expected)
    {
        $result = CouponDiscount::calculateTotal($param['use_coupon_ids'], Coupon::collection($param['coupons']));
        $this->assertEquals($expected, $result);
    }
}
