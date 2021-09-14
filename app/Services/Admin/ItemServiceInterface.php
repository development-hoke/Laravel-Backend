<?php

namespace App\Services\Admin;

interface ItemServiceInterface
{
    /**
     * @param array $params
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function search(array $params);

    /**
     * Update
     *
     * @param array $request
     * @param int $itemId
     *
     * @return \App\Models\Item
     */
    public function update(array $params, int $itemId);

    /**
     * @param \Illuminate\Http\UploadedFile $image
     * @param int $itemId
     *
     * @return Collection $item
     */
    public function uploadImage(\Illuminate\Http\UploadedFile $image, int $itemId);

    /**
     * @param array $params
     *
     * @return \Closure
     */
    public function getCsvExporter(array $params);

    /**
     * @param array $params
     *
     * @return \Closure
     */
    public function getImageCsvExporter(array $params);

    /**
     * @param array $params
     *
     * @return \Closure
     */
    public function getInfoCsvExporter(array $params);

    /**
     * 受注追加用商品検索
     *
     * @param array $params
     * @param \App\Models\Order $order
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function searchForEditingOrder(array $params, \App\Models\Order $order);
}
