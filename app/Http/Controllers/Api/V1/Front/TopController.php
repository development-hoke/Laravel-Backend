<?php

namespace App\Http\Controllers\Api\V1\Front;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Front\Top\FetchByStoreBrandRequest;
use App\Http\Resources\TopContent as TopResource;
use App\Services\Front\TopContentServiceInterface;

class TopController extends Controller
{
    /**
     * @var TopServiceInterface
     */
    private $topService;

    public function __construct(TopContentServiceInterface $topService)
    {
        $this->topService = $topService;
    }

    /**
     * @param FetchByStoreBrandRequest $request
     *
     * @return TopResource
     */
    public function fetchByStoreBrand(FetchByStoreBrandRequest $request)
    {
        $params = $request->validated();

        $topContent = $this->topService->fetchOneByStoreBrand($params);

        return new TopResource($topContent);
    }
}
