<?php

namespace App\Services\Admin;

use App\Domain\CouponInterface as CouponService;
use App\Domain\MemberInterface as DomainMemberService;

class MemberService extends Service implements MemberServiceInterface
{
    /**
     * @var CouponService
     */
    private $couponService;

    /**
     * @var DomainMemberService
     */
    private $domainMemberService;

    /**
     * @param OrderRepository $orderRepository
     */
    public function __construct(
        CouponService $couponService,
        DomainMemberService $domainMemberService
    ) {
        $this->couponService = $couponService;
        $this->domainMemberService = $domainMemberService;

        if (auth('admin_api')->check()) {
            $staff = auth('admin_api')->user();

            $this->couponService->setStaffToken($staff->token);
            $this->domainMemberService->setStaffToken($staff->token);
        }
    }

    /**
     * 会員検索
     *
     * @param array $params
     * @param array $applyingPertialParams (default: ['tel', 'name', 'email'])
     *
     * @return array
     */
    public function search(array $params, $applyingPertialParams = ['tel', 'name', 'email'])
    {
        return $this->domainMemberService->search($params, $applyingPertialParams);
    }

    /**
     * 会員詳細
     *
     * @param string $memberId
     *
     * @return array
     */
    public function fetchOne($memberId)
    {
        return $this->domainMemberService->fetchOne($memberId);
    }

    /**
     * 利用可能クーポン取得
     *
     * @param string $memberId
     * @param array $query (default: [])
     *
     * @return \App\Entities\Collection
     */
    public function fetchAvailableMemberCoupons($memberId, array $query = [])
    {
        $memberCoupon = $this->couponService->fetchAvailableMemberCoupons($memberId, $query);

        return $memberCoupon;
    }
}
