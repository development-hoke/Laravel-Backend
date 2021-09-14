<?php

namespace App\Http\Controllers\Api\V1\Front\Member;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Front\Coupon\IndexRequest;
use App\Http\Requests\Api\V1\Front\Coupon\IssueRequest;
use App\Services\Front\MemberServiceInterface;

class CouponController extends Controller
{
    /**
     * @var MemberServiceInterface
     */
    private $service;

    /**
     * Create a new controller instance
     *
     * @return void
     */
    public function __construct(MemberServiceInterface $service)
    {
        $this->service = $service;
    }

    /**
     * @param IndexRequest $request
     * @param $memberId
     *
     * @return array
     */
    public function get(IndexRequest $request, $memberId)
    {
        $validCoupons = $this->service->getCoupons($memberId, []);
        $myCoupons = $this->service->getAvailableCoupons($memberId, []);

        return [
            'valid_coupons' => $validCoupons,
            'my_coupons' => $myCoupons,
        ];
    }

    /**
     * クーポン発行
     *
     * @param IssueRequest $request
     * @param $memberId
     * @param $couponId
     *
     * @return \array[][]
     */
    public function issue(IssueRequest $request, $memberId, $couponId)
    {
        $this->service->issueCoupon($memberId, $couponId, []);
        $validCoupons = $this->service->getCoupons($memberId, []);
        $myCoupons = $this->service->getAvailableCoupons($memberId, []);

        return [
            'valid_coupons' => $validCoupons,
            'my_coupons' => $myCoupons,
        ];
    }
}
