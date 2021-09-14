<?php

namespace App\Services\Admin;

interface TopContentServiceInterface
{
    /**
     * ストアブランドからtop_contentを取得
     *
     * @param int|null $storeBrand
     *
     * @return TopContent
     */
    public function fetchOneByStoreBrand(int $storeBrand = null);

    /**
     * Update
     *
     * @param array $request
     * @param int $id
     *
     * @return \App\Models\Item
     */
    public function addMainVisual(array $params, int $id);

    /**
     * メインビジュアルを更新し、他のレコードの優先度を更新内容に合わせて更新する
     *
     * @param array $attributes
     * @param int $id
     *
     * @return TopContent
     */
    public function updateMainVisual(array $params, $id);

    /**
     * メインビジュアルのステータス更新
     *
     * @param int $id
     * @param int $itemId
     * @param array $attributes
     * @param array $except
     *
     * @return TopContent
     */
    public function updateStatusMainVisual(int $id, int $itemId, array $attributes, array $except = []);

    /**
     * メインビジュアル削除
     *
     * @param array $params
     * @param int $id
     *
     * @return void
     */
    public function deleteMainVisual(int $id, int $sort);

    /**
     * 新着商品を追加する
     *
     * @param array $params
     * @param int $id
     *
     * @return \App\Models\TopContent
     */
    public function addNewItems(array $params, int $id);

    /**
     * 新着商品からデータを削除
     *
     * @param int $id
     * @param int $itemId
     *
     * @return TopContent
     */
    public function deleteNewItem(int $id, int $itemId);

    /**
     * 新着商品の更新
     *
     * @param int $id
     * @param int $itemId
     * @param array $attributes
     * @param array $except
     *
     * @return TopContent
     */
    public function updateNewItem(int $id, int $itemId, array $attributes, array $except = []);

    /**
     * おすすめ商品を追加する
     *
     * @param array $params
     * @param int $id
     *
     * @return \App\Models\TopContent
     */
    public function addPickups(array $params, int $id);

    /**
     * おすすめ商品からデータを削除
     *
     * @param int $id
     * @param int $itemId
     *
     * @return TopContent
     */
    public function deletePickup(int $id, int $itemId);

    /**
     * おすすめ商品の更新
     *
     * @param int $id
     * @param int $itemId
     * @param array $attributes
     * @param array $except
     *
     * @return TopContent
     */
    public function updatePickup(int $id, int $itemId, array $attributes, array $except = []);

    /**
     * 特集の背景色の更新
     *
     * @param int $id
     * @param array $attributes
     *
     * @return TopContent
     */
    public function updateBackgroundColor(int $id, array $attributes);

    /**
     * 特集の更新
     *
     * @param int $id
     * @param array $attributes
     *
     * @return TopContent
     */
    public function updateFeatures(int $id, array $attributes);

    /**
     * 特集のソート更新
     *
     * @param int $id
     * @param int $planId
     * @param array $attributes
     * @param array $except
     *
     * @return TopContent
     */
    public function updateSortFeatures(int $id, int $planId, array $attributes, array $except = []);

    /**
     * NEWSの更新
     *
     * @param int $id
     * @param array $attributes
     *
     * @return TopContent
     */
    public function updateNews(int $id, array $attributes);

    /**
     * NEWSのソート更新
     *
     * @param int $id
     * @param int $planId
     * @param array $attributes
     * @param array $except
     *
     * @return TopContent
     */
    public function updateSortNews(int $id, int $planId, array $attributes, array $except = []);

    /**
     * 公開が終了した特集を削除する
     *
     * @return void
     */
    public function deleteExpiredFeatures();

    /**
     * 公開が終了したNEWSを削除する
     *
     * @return void
     */
    public function deleteExpiredNews();
}
