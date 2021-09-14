<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Api\V1\Admin\Controller as ApiAdminController;
use App\Http\Requests\Api\V1\Admin\ClosedMarket\CreateRequest;
use App\Http\Requests\Api\V1\Admin\ClosedMarket\DeleteRequest;
use App\Http\Requests\Api\V1\Admin\ClosedMarket\IndexRequest;
use App\Http\Requests\Api\V1\Admin\ClosedMarket\UpdateRequest;
use App\Http\Resources\ClosedMarket as ClosedMarketResource;
use App\Repositories\ClosedMarketRepository;
use App\Services\Admin\ClosedMarketsServiceInterface;

class ClosedMarketController extends ApiAdminController
{
    /**
     * @var ClosedMarketRepository
     */
    private $closedMarketRepository;

    /**
     * @var ClosedMarketsServiceInterface
     */
    private $closedMarketsService;

    /**
     * @param ClosedMarketRepository $closedMarketRepository
     * @param ClosedMarketsServiceInterface $closedMarketsService
     */
    public function __construct(
        ClosedMarketRepository $closedMarketRepository,
        ClosedMarketsServiceInterface $closedMarketsService
    ) {
        $this->closedMarketRepository = $closedMarketRepository;
        $this->closedMarketsService = $closedMarketsService;
    }

    /**
     * @param IndexRequest $request
     * @param int $itemId
     *
     * @return ResourceCollection
     */
    public function index(IndexRequest $request, int $itemId)
    {
        $closedMarkets = $this->closedMarketsService->find($itemId);

        return ClosedMarketResource::collection($closedMarkets);
    }

    /**
     * @param CreateRequest $request
     *
     * @return ClosedMarketResource
     */
    public function store(CreateRequest $request, int $itemId)
    {
        $params = $request->validated();

        $closedMarket = $this->closedMarketsService->store($params, $itemId);

        return new ClosedMarketResource($closedMarket);
    }

    /**
     * @param UpdateRequest $request
     * @param int $itemId
     * @param int $id
     *
     * @return ClosedMarketResource
     */
    public function update(UpdateRequest $request, int $itemId, int $id)
    {
        $params = $request->validated();

        $closedMarket = $this->closedMarketsService->update($params, $itemId, $id);

        return new ClosedMarketResource($closedMarket);
    }

    /**
     * @param DeleteRequest $request
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(DeleteRequest $request, int $id)
    {
        $this->closedMarketRepository->delete($id);

        return response(null, 204);
    }
}
