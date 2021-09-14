<?php

namespace App\HttpCommunication\StaffStart\Concrete;

use App\HttpCommunication\StaffStart\HttpCommunicationService;
use App\HttpCommunication\StaffStart\StaffStartInterface;

class StaffStart extends HttpCommunicationService implements StaffStartInterface
{
    /**
     * @var array
     */
    private static $paramMap = [
        'product_number' => 'product_code',
        'coordinate_id' => 'cid',
    ];

    /**
     * ec側のパラメータ名をスタッフスタートのパラメータ名に変換する
     *
     * @param array $params
     *
     * @return array
     */
    public static function mapParams(array $params)
    {
        return translate($params, static::$paramMap);
    }

    /**
     * レスポンスボディの配列から成功・失敗を判定
     *
     * @return bool
     */
    public function isSuccess(array $body)
    {
        return (int) $body['code'] === self::CODE_SUCCESS;
    }

    /**
     * コーディネート一覧を取得
     *
     * @param array $query
     *
     * @return \App\HttpCommunication\Response\ResponseInterface
     */
    public function fetchCoordinates($query = [])
    {
        return $this->request(self::ENDPONT_INDEX_COORDINATES, [], [], ['query' => $query]);
    }

    /**
     * コーディネート詳細を取得
     *
     * @param int $coordinateId
     *
     * @return \App\HttpCommunication\Response\ResponseInterface
     */
    public function fetchCoordinateDetail($coordinateId)
    {
        return $this->request(self::ENDPONT_SHOW_COORDINATE_DETAIL, [], [], ['cid' => $coordinateId]);
    }
}
