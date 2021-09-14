<?php

namespace App\Domain;

interface ItemInterface
{
    /**
     * @param \App\Models\Item $item
     * @param int $closedMarketId
     *
     * @return \App\Models\Item
     */
    public function loadItemDetailClosedMarketRelations(\App\Models\Item $item, int $closedMarketId);

    /**
     * @param \App\Models\ItemDetail|\Illuminate\Database\Eloquent\Collection $itemDetails
     * @param int $closedMarketId
     *
     * @return \App\Models\ItemDetail|\Illuminate\Database\Eloquent\Collection
     */
    public function loadEnableClosedMarketsToItemDetails($itemDetails, int $closedMarketId);

    /**
     * カート・在庫関連のステータスを代入する
     *
     * @param \App\Models\Item $item
     *
     * @return \App\Models\Item
     */
    public function fillApplicableCartStatus(\App\Models\Item $item);
}
