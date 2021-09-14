<?php

namespace App\Http\Resources\Front;

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
            'ec_stock_assigned_to_closed_market' => $this->ec_stock_assigned_to_closed_market,
            'sort' => $this->sort,
            'status' => $this->status,
            'status_change_date' => $this->status_change_date,
            'redisplay_requested' => $this->redisplay_requested,
            'last_sales_date' => $this->last_sales_date,
            'reservable_stock' => $this->reservable_stock,
            'dead_inventory_days' => $this->dead_inventory_days,
            'slow_moving_inventory_days' => $this->slow_moving_inventory_days,
            'latest_added_stock' => $this->latest_added_stock,
            'latest_stock_added_at' => $this->latest_stock_added_at,
            'applicable_cart_status' => $this->applicable_cart_status ?? null,
            'is_almost_out_of_reservable_stock' => $this->is_almost_out_of_reservable_stock ?? null,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'color' => new \App\Http\Resources\Color($this->whenLoaded('color')),
            'size' => new \App\Http\Resources\Size($this->whenLoaded('size')),
            'item' => new \App\Http\Resources\Item($this->whenLoaded('item')),
            'order_details' => \App\Http\Resources\OrderDetail::collection($this->whenLoaded('orderDetails')),
            'applied_closed_market' => new ClosedMarket($this->whenLoaded('appliedClosedMarket')),
            'is_almost_ec_stock' => $this->ec_stock <= config('constants.stock.ec_min_stock'),
        ];
    }
}
