<?php

namespace App\Services\Front;

interface TopContentServiceInterface
{
    /**
     * 該当ストアブランドのtop_contentを取得
     *
     * @param int|null $storeBrand
     *
     * @return TopContent
     */
    public function fetchOneByStoreBrand(array $attributes);

    /**
     * 該当ストアブランドの新着商品を取得
     *
     * @param int|null $storeBrand
     *
     * @return TopContent
     */
    public function fetchNewItemsByStoreBrand(int $storeBrand);
}
