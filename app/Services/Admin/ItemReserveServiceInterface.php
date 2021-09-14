<?php

namespace App\Services\Admin;

interface ItemReserveServiceInterface
{
    /**
     * @param array $params
     * @param int $itemId
     *
     * @return \App\Models\ItemReserve
     */
    public function create(array $params, int $itemId);

    /**
     * @param array $params
     * @param int $itemId
     *
     * @return \App\Models\ItemReserve
     */
    public function update(array $params, int $itemId);
}
