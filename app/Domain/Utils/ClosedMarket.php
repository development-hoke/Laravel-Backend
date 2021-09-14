<?php

namespace App\Domain\Utils;

class ClosedMarket
{
    /**
     * @param \App\Models\Item $item
     * @param \App\Models\ClosedMarket $closedMarket
     *
     * @return string
     */
    public static function computeUrlPath(\App\Models\Item $item, \App\Models\ClosedMarket $closedMarket)
    {
        return sprintf('%s/items/detail/%s/closed-markets/%s/', config('app.front_url'), $item->product_number, $closedMarket->id);
    }
}
