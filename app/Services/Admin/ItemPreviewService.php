<?php

namespace App\Services\Admin;

use App\Domain\ItemPreviewInterface as DomainItemPreviewService;

class ItemPreviewService implements ItemPreviewServiceInterface
{
    /**
     * @var DomainItemPreviewService
     */
    private $domainItemPreviewService;

    public function __construct(DomainItemPreviewService $domainItemPreviewService)
    {
        $this->domainItemPreviewService = $domainItemPreviewService;
    }

    /**
     * プレビューデータの保存
     *
     * @param int $id
     * @param array $params
     *
     * @return array cache info
     */
    public function store(int $id, array $params)
    {
        return $this->domainItemPreviewService->store($id, $params);
    }

    /**
     * @param string $key
     *
     * @return array
     */
    public function fetch(string $key)
    {
        return $this->domainItemPreviewService->fetch($key);
    }
}
