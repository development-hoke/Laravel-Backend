<?php

namespace App\Services\Admin;

interface BrandServiceInterface
{
    /**
     * 新着商品の更新
     *
     * @param int $id
     * @param array $params
     *
     * @return Brand
     */
    public function updateSort(int $id, array $params);
}
