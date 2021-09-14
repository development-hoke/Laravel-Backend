<?php

namespace App\Services\Front;

use App\Exceptions\InvalidInputException;
use App\Models\ItemDetail;
use App\Repositories\ItemDetailRepository;
use App\Repositories\ItemRepository;
use App\Services\Service;

class ItemDetailService extends Service implements ItemDetailServiceInterface
{
    private $itemRepository;
    private $itemDetailRepository;

    public function __construct(
        ItemRepository $itemRepository,
        ItemDetailRepository $itemDetailRepository
    ) {
        $this->itemRepository = $itemRepository;
        $this->itemDetailRepository = $itemDetailRepository;
    }

    /**
     * SKUを条件として取得
     *
     * @param string $productNumber
     * @param int $colorId
     * @param int $sizeId
     *
     * @return ItemDetail
     *
     * @throws InvalidInputException
     */
    public function findBySKU(string $productNumber, int $colorId, int $sizeId): ItemDetail
    {
        $item = $this->itemRepository->findWhere(['product_number' => $productNumber])->first();
        if (!$item) {
            throw new InvalidInputException(__('error.no_items'));
        }
        $itemDetail = $this->itemDetailRepository
            ->findWhere([
                'item_id' => $item->id,
                'color_id' => $colorId,
                'size_id' => $sizeId,
            ])->first();
        if (!$itemDetail) {
            throw new InvalidInputException(__('error.no_item_details'));
        }

        return $itemDetail;
    }
}
