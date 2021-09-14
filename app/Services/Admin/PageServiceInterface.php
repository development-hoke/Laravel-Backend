<?php

namespace App\Services\Admin;

interface PageServiceInterface
{
    /**
     * @param int $pageId
     *
     * @return array
     */
    public function copy(int $pageId);
}
