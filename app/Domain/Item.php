<?php

namespace App\Domain;

use Illuminate\Database\Query\JoinClause;

class Item implements ItemInterface
{
    /**
     * @param \App\Models\Item $item
     * @param int $closedMarketId
     *
     * @return \App\Models\Item
     */
    public function loadItemDetailClosedMarketRelations(\App\Models\Item $item, int $closedMarketId)
    {
        $item->load(['itemDetails' => function ($query) use ($closedMarketId) {
            return $query->join('closed_markets', function (JoinClause $join) use ($closedMarketId) {
                return $join
                    ->on('item_details.id', '=', 'closed_markets.item_detail_id')
                    ->where('closed_markets.id', $closedMarketId);
            })
            ->where('status', \App\Enums\Common\Status::Published)
            ->orderBy('sort')
            ->select('item_details.*');
        }]);

        if ($item->itemDetails->isEmpty()) {
            return $item;
        }

        $this->loadEnableClosedMarketsToItemDetails($item->itemDetails, $closedMarketId);

        $itemDetails = $item->itemDetails->filter(function ($itemDetail) {
            return !empty($itemDetail->appliedClosedMarket);
        });

        $item->setRelation('itemDetails', $itemDetails);

        if ($item->itemDetails->isEmpty()) {
            return $item;
        }

        $item->is_closed_market = true;

        return $item;
    }

    /**
     * @param \App\Models\ItemDetail|\Illuminate\Database\Eloquent\Collection $itemDetails
     * @param int $closedMarketId
     *
     * @return \App\Models\ItemDetail|\Illuminate\Database\Eloquent\Collection
     */
    public function loadEnableClosedMarketsToItemDetails($itemDetails, int $closedMarketId)
    {
        $isSingle = $itemDetails instanceof \App\Models\ItemDetail;

        if ($isSingle) {
            $itemDetails = $itemDetails->newCollection([$itemDetails]);
        }

        $itemDetails->load([
            'enableClosedMarkets' => function ($query) use ($closedMarketId) {
                if (is_null($closedMarketId)) {
                    return $query;
                }

                return $query->where('closed_markets.id', $closedMarketId);
            },
            'itemDetailIdentifications',
        ]);

        $enableClosedMarket = $itemDetails->first()->enableClosedMarkets->first();

        if (!empty($enableClosedMarket)) {
            $enableClosedMarket->load('assignedClosedMarketCartItems');

            $itemDetails->each(function ($itemDetail) use ($enableClosedMarket) {
                $itemDetail->setRelation('appliedClosedMarket', $enableClosedMarket);
            });
        }

        return $isSingle ? $itemDetails->first() : $itemDetails;
    }

    /**
     * カート・在庫関連のステータスを代入する
     *
     * @param \App\Models\Item $item
     *
     * @return \App\Models\Item
     */
    public function fillApplicableCartStatus(\App\Models\Item $item)
    {
        foreach ($item->itemDetails as $itemDetail) {
            $itemDetail->applicable_cart_status = \App\Domain\Utils\Item::getApplicableCartStatus($item, $itemDetail);
            $item->is_sold_out = \App\Domain\Utils\Item::isSoldOutItem($item);
            $itemDetail->is_almost_out_of_reservable_stock = \App\Domain\Utils\Item::isAlmostOutOfReservableStock($item, $itemDetail);
        }

        $item->is_sold_out = \App\Domain\Utils\Item::shouldDisplaySoldOut($item);

        return $item;
    }
}
