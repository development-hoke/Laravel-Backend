<?php

namespace App\Services\Admin;

use App\Repositories\ItemRepository;
use App\Repositories\ItemReserveRepository;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ItemReserveService extends Service implements ItemReserveServiceInterface
{
    /**
     * @var ItemReserveRepository
     */
    private $itemReserveRepository;

    /**
     * @var ItemRepository
     */
    private $itemRepository;

    /**
     * @param ItemReserveRepository $repository
     */
    public function __construct(ItemReserveRepository $itemReserveRepository, ItemRepository $itemRepository)
    {
        $this->itemReserveRepository = $itemReserveRepository;
        $this->itemRepository = $itemRepository;
    }

    /**
     * @param array $params
     * @param int $itemId
     *
     * @return \App\Models\ItemReserve
     */
    public function create(array $params, int $itemId)
    {
        return DB::transaction(function () use ($params, $itemId) {
            $itemReserve = $this->itemReserveRepository->create(array_merge(
                $params,
                ['item_id' => $itemId]
            ));

            $itemReserve->load('item.itemDetails');

            return $itemReserve;
        }, 3);
    }

    /**
     * @param array $params
     * @param int $itemId
     *
     * @return \App\Models\ItemReserve
     */
    public function update(array $params, int $itemId)
    {
        return DB::transaction(function () use ($params, $itemId) {
            $itemReserve = $this->itemReserveRepository->findWhere(['item_id' => $itemId])->first();

            if (empty($itemReserve)) {
                throw new HttpException(Response::HTTP_NOT_FOUND, error_format('error.model_not_found', compact('itemId')));
            }

            $itemReserve = $this->itemReserveRepository->update($params, $itemReserve->id);

            $itemReserve->load('item.itemDetails');

            return $itemReserve;
        }, 3);
    }
}
