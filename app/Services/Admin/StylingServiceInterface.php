<?php

namespace App\Services\Admin;

interface StylingServiceInterface
{
    /**
     * @param array $params
     *
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function search(array $params);
}
