<?php

namespace App\Http\Controllers\Api\V1\Front\Member;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Front\Point\IndexRequest;
use App\Http\Resources\PointHistory as PointHistoryResource;
use App\Services\Front\MemberServiceInterface;

class PointController extends Controller
{
    const PER_PAGE = 10;

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
        $member = $this->service->get($memberId);

        $page = $request->get('page') ?? 1;
        list($histories) = $this->service->getPointHistory($memberId, [
            'page' => $page,
            'per_page' => $request->get('per_page') ?? static::PER_PAGE,
            'order' => $request->get('order') ?? 'created_at|desc',
        ]);

        return response()->json([
            'amount' => $member['member']['member_stat']['except_invalid_point'],
            'limit_date' => $member['member']['member_stat']['except_invalid_date'],
            'histories' => PointHistoryResource::collection($histories),
            'next_page' => "/api/v1/member/${memberId}/point?page=" . ($page + 1),
        ]);
    }
}
