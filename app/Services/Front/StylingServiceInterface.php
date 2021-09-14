<?php

namespace App\Services\Front;

interface StylingServiceInterface
{
    /**
     * @param array $params
     *
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function search(array $params);
}
