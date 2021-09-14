<?php

namespace App\Http\Controllers\Api\V1\Front;

use App\Http\Controllers\Api\V1\Controller;
use App\Http\Resources\ItemFavorite as ItemFavoriteResource;
use App\Repositories\ItemFavoriteRepository;
use App\Repositories\ItemRepository;
use Illuminate\Http\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ItemFavoriteController extends Controller
{
    /**
     * @var ItemFavoriteRepository
     */
    private $itemFavoriteRepository;

    /**
     * @var ItemRepository
     */
    private $itemRepository;

    /**
     * @param ItemFavoriteRepository $itemRepository
     */
    public function __construct(ItemFavoriteRepository $itemFavoriteRepository, ItemRepository $itemRepository)
    {
        $this->middleware('auth:api');
        $this->itemFavoriteRepository = $itemFavoriteRepository;
        $this->itemRepository = $itemRepository;
    }

    /**
     * @param int $itemId
     *
     * @return \Illuminate\Http\Response|ItemFavoriteResource
     */
    public function store(int $itemId)
    {
        $item = $this->itemRepository->find($itemId);

        if (empty($item)) {
            throw new HttpException(Response::HTTP_NOT_FOUND, error_format('error.model_not_found', ['item_id' => $itemId]));
        }

        $memberId = auth('api')->id();

        $favoirte = $this->itemFavoriteRepository->findWhere([
            'item_id' => $itemId,
            'member_id' => $memberId,
        ])->first();

        if (!empty($favoirte)) {
            return response(new ItemFavoriteResource($favoirte), Response::HTTP_OK);
        }

        $favoirte = $this->itemFavoriteRepository->create([
            'item_id' => $itemId,
            'member_id' => $memberId,
        ]);

        return new ItemFavoriteResource($favoirte);
    }

    /**
     * @param int $itemId
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(int $itemId)
    {
        $item = $this->itemRepository->find($itemId);

        if (empty($item)) {
            throw new HttpException(Response::HTTP_NOT_FOUND, error_format('error.model_not_found', ['item_id' => $itemId]));
        }

        $memberId = auth('api')->id();

        $this->itemFavoriteRepository->deleteWhere([
            'item_id' => $itemId,
            'member_id' => $memberId,
        ]);

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
