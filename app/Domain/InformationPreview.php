<?php

namespace App\Domain;

use App\Models\Information;
use App\Repositories\InformationRepository;
use App\Utils\Cache;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class InformationPreview implements InformationPreviewInterface
{
    /**
     * キャッシュの保存時間（24時間）
     */
    const DATA_EXPIRATION_SEC = 86400;

    /**
     * @var InformationRepository
     */
    private $informationRepository;

    /**
     * @param InformationRepository $informationRepository
     */
    public function __construct(
        InformationRepository $informationRepository
    ) {
        $this->informationRepository = $informationRepository;
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
        $previewKey = (string) \Webpatser\Uuid\Uuid::generate(4);

        $item = new Information();

        $item->fill($params);

        Cache::put(sprintf(Cache::KEY_ADMIN_INFORMATION_PREVIW, $previewKey), $item->toArray(), self::DATA_EXPIRATION_SEC);

        return ['key' => $previewKey, 'expires' => self::DATA_EXPIRATION_SEC];
    }

    /**
     * @param string $key
     *
     * @return array
     */
    public function fetch(string $key)
    {
        $data = Cache::get(sprintf(Cache::KEY_ADMIN_INFORMATION_PREVIW, $key));

        if (!$data) {
            throw new NotFoundHttpException();
        }

        return $data;
    }
}
