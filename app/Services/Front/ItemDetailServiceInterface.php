<?php

namespace App\Services\Front;

use App\Exceptions\InvalidInputException;
use App\Models\ItemDetail;

interface ItemDetailServiceInterface
{
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
    public function findBySKU(string $productNumber, int $colorId, int $sizeId): ItemDetail;
}
