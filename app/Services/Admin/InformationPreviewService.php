<?php

namespace App\Services\Admin;

use App\Domain\InformationPreviewInterface as DomainInfoPreviewService;

class InformationPreviewService implements InformationPreviewServiceInterface
{
    /**
     * @var DomainInfoPreviewService
     */
    private $domainInfoPreviewService;

    public function __construct(DomainInfoPreviewService $domainInfoPreviewService)
    {
        $this->domainInfoPreviewService = $domainInfoPreviewService;
    }

    /**
     * プレビューデータの保存
     *
     * @param array $params
     *
     * @return array cache info
     */
    public function store(array $params)
    {
        return $this->domainInfoPreviewService->store($params);
    }

    /**
     * @param string $key
     *
     * @return array
     */
    public function fetch(string $key)
    {
        return $this->domainInfoPreviewService->fetch($key);
    }
}
