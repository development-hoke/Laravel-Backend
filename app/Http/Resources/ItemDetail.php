<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ItemDetail extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'item_id' => $this->item_id,
            'color_id' => $this->color_id,
            'size_id' => $this->size_id,
            'sku_number' => $this->sku_number,
            'store_stock' => $this->store_stock,
            'ec_stock' => $this->ec_stock,
            'all_ec_stock' => $this->all_ec_stock,
            'all_stock' => $this->all_stock,
            'ec_stock_assigned_to_closed_market' => $this->ec_stock_assigned_to_closed_market,
            'sort' => $this->sort,
            'status' => $this->status,
            'status_change_date' => $this->status_change_date,
            'redisplay_requested' => $this->redisplay_requested,
            'reservable_stock' => $this->reservable_stock,
            'last_sales_date' => $this->last_sales_date,
            'item_detail_request_count' => $this->item_detail_request_count,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'back_orderble_stock_threshold' => \App\Domain\Utils\Stock::computeBackOrderbleStockThreshold($this->resource),
            'color' => new Color($this->whenLoaded('color')),
            'size' => new Size($this->whenLoaded('size')),
            'item' => new Item($this->whenLoaded('item')),
            'order_details' => OrderDetail::collection($this->whenLoaded('orderDetails')),
            'item_detail_identifications' => ItemDetailIdentification::collection($this->whenLoaded('itemDetailIdentifications')),
            'enable_closed_markets' => ClosedMarket::collection($this->whenLoaded('enableClosedMarkets')),
            'image_url' => $this->image_url,
        ];
    }
}
