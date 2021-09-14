<?php

namespace App\Services\Admin;

interface PlanServiceInterface
{
    /**
     * Store
     *
     * @param array $params
     *
     * @return array
     */
    public function create(array $params);

    /**
     * Update
     *
     * @param array $request
     * @param int $planId
     *
     * @return \App\Models\Plan
     */
    public function update(array $params, int $planId);

    /**
     * Delete
     *
     * @param int $planId
     *
     * @return array
     */
    public function delete(int $planId);

    /**
     * Copy
     *
     * @param int $planId
     *
     * @return array
     */
    public function copy(int $planId);

    /**
     * 一覧商品からデータを削除
     *
     * @param int $id
     * @param int $itemId
     *
     * @return array
     */
    public function deleteItem(int $id, int $itemId);

    /**
     * 商品を追加する
     *
     * @param array $params
     * @param int $planId
     *
     * @return array
     */
    public function addNewItems(array $params, int $planId);

    /**
     * 商品一覧表示設定変更
     *
     * @param array $request
     * @param int $planId
     *
     * @return array
     */
    public function updateItemSetting(array $params, int $planId);

    /**
     * 指定されたストアブランドの企画管理を取得
     *
     * @param int|null $storeBrand
     *
     * @return array
     */
    public function fetchByStoreBrand(int $storeBrand = null);
}
