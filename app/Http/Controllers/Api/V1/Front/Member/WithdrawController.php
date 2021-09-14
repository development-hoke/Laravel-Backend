<?php

namespace App\Http\Controllers\Api\V1\Front\Member;

use App\Exceptions\InvalidInputException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Front\Member\WithdrawRequest;
use App\Services\Front\MemberServiceInterface;
use App\Services\Front\OrderServiceInterface;

class WithdrawController extends Controller
{
    /**
     * @var MemberServiceInterface
     */
    private $service;

    /**
     * @var OrderServiceInterface
     */
    private $orderService;

    /**
     * Create a new controller instance
     *
     * @return void
     */
    public function __construct(
        MemberServiceInterface $service,
        OrderServiceInterface $orderService
    ) {
        $this->service = $service;
        $this->orderService = $orderService;
    }

    /**
     * 退会
     *
     * @param WithdrawRequest $request
     * @param $memberId
     *
     * @return array
     */
    public function withdraw(WithdrawRequest $request, $memberId)
    {
        if (!$this->orderService->canWithdraw($memberId)) {
            throw new InvalidInputException(error_format('error.cannot_withdraw'));
        }

        $result = $this->service->withdraw($memberId, [
            'reason' => intval($request->get('reason')),
        ]);

        return response()->json(['status' => $result]);
    }
}
