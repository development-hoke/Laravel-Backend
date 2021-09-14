<?php

namespace App\Models\Collections;

use App\Models\Collections\Collection as BaseCollection;
use App\Models\ItemDetail;
use App\Utils\Arr;
use Illuminate\Support\Facades\Log;

class OrderChangeHistoryCollection extends BaseCollection
{
    /**
     * @param string|array $nested
     *
     * @return void
     */
    public function loadItemDetail($nested = null)
    {
        $ids = $this->pluck('item_detail_id')->filter(function ($id) {
            return !is_null($id);
        });

        $itemDetails = $this->newRelatedInstance(ItemDetail::class)->findMany($ids);

        if (!is_null($nested)) {
            $itemDetails->load($nested);
        }

        $itemDetailDict = Arr::dict($itemDetails, 'id');

        foreach ($this->items as &$item) {
            if (!$item->item_detail_id) {
                continue;
            }

            if (!isset($itemDetailDict[$item->item_detail_id])) {
                Log::warning(__('error.not_found_item_detail_related_with_order_change_log'), [
                    'item_detail_id' => $item->item_detail_id,
                    'id' => $item->id,
                ]);
                continue;
            }

            $item->setRelation('itemDetail', $itemDetailDict[$item->item_detail_id]);
        }

        return $this;
    }

    /**
     * Create a new model instance for a related model.
     *
     * @param string $class
     *
     * @return mixed
     */
    protected function newRelatedInstance($class)
    {
        $item = $this->first();

        if (empty($item)) {
            return new $class();
        }

        return tap(new $class(), function ($instance) use ($item) {
            if (!$instance->getConnectionName()) {
                $instance->setConnection($item->getConnectionName());
            }
        });
    }
}
