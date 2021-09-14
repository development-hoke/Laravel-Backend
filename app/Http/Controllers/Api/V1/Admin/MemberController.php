<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Api\V1\Admin\Controller as ApiAdminController;
use App\Http\Requests\Api\V1\Admin\Member\IndexAvailableCouponsRequest;
use App\Services\Admin\MemberServiceInterface as MemberService;
use Illuminate\Http\Resources\Json\JsonResource;

class MemberController extends ApiAdminController
{
    /**
     * @var MemberService
     */
    private $memberService;

    /**
     * @param MemberService $memberService
     */
    public function __construct(MemberService $memberService)
    {
        $this->memberService = $memberService;
    }

    /**
     * @param string $memberId
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function indexAvailableCoupons(IndexAvailableCouponsRequest $request, $memberId)
    {
        $coupons = $this->memberService->fetchAvailableMemberCoupons($memberId, $request->validated());

        return JsonResource::collection($coupons);
    }
}
