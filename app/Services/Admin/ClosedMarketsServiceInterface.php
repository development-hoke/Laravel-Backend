<?php

namespace App\Services\Admin;

interface ClosedMarketsServiceInterface
{
    /**
     * @param int $itemId
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function find(int $itemId);

    /**
     * 闇市設定を新規作成
     *
     * @param array $params
     * @param int $itemId
     *
     * @return \App\Models\ClosedMarket
     */
    public function store(array $params, int $itemId);

    /**
     * @param array $params
     * @param int $itemId
     * @param int $id
     *
     * @return \App\Models\ClosedMarket
     */
    public function update(array $params, int $itemId, int $id);
}
