<?php

namespace App\Domain;

interface ItemImageInterface
{
    /**
     * 画像データの入れ替え
     *
     * @param array $params
     * @param \App\Models\Item $item
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function deleteAndInsertItemImages(array $params, \App\Models\Item $item);

    /**
     * @param array $itemImgaes
     * @param \App\Models\Item $item
     * @param string $previewKey
     *
     * @return array
     */
    public function putNewPreviewFiles(array $itemImgaes, \App\Models\Item $item, string $previewKey);

    /**
     * @param \App\Models\Item $item
     * @param mixed $content
     * @param array $params
     *
     * @return \App\Models\ItemImage
     */
    public function create(\App\Models\Item $item, $content, array $params);
}
