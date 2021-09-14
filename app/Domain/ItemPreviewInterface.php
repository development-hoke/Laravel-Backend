<?php

namespace App\Domain;

interface ItemPreviewInterface
{
    /**
     * プレビューデータの保存
     *
     * @param int $id
     * @param array $params
     *
     * @return array cache info
     */
    public function store(int $id, array $params);

    /**
     * @param string $key
     *
     * @return array
     */
    public function fetch(string $key);

    /**
     * @return \Illuminate\Support\Collection
     */
    public function deleteOldItemImageDirectories();
}
