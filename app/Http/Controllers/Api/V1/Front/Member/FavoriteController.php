<?php

namespace App\Http\Controllers\Api\V1\Front\Member;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Front\Member\Favorite\IndexRequest;
use App\Http\Resources\Front\Item as ItemResource;
use App\Services\Front\ItemServiceInterface;

class FavoriteController extends Controller
{
    /**
     * @var ItemServiceInterface
     */
    private $service;

    /**
     * Create a new controller instance
     *
     * @return void
     */
    public function __construct(ItemServiceInterface $service)
    {
        $this->service = $service;
    }

    /**
     * @param App\Http\Requests\Api\V1\Front\Member\Favorite\IndexRequest $request
     * @param $memberId
     *
     * @return array
     */
    public function index(IndexRequest $request, $memberId)
    {
        $params = $request->validated();
        $params[\App\Criteria\Item\FrontMypageFavoriteCriteria::KEY] = true;
        $items = $this->service->search($params);

        return ItemResource::collection($items);
    }
}
