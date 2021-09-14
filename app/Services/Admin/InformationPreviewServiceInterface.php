<?php

namespace App\Services\Admin;

interface InformationPreviewServiceInterface
{
    /**
     * プレビューデータの保存
     *
     * @param array $params
     *
     * @return array cache info
     */
    public function store(array $params);

    /**
     * @param string $key
     *
     * @return array
     */
    public function fetch(string $key);
}
