<?php

namespace App\HttpCommunication\StaffStart\Mock;

use App\HttpCommunication\Response\Mock\Response;
use App\HttpCommunication\StaffStart\HttpCommunicationService;
use App\HttpCommunication\StaffStart\StaffStartInterface;

class StaffStart extends HttpCommunicationService implements StaffStartInterface
{
    /**
     * ec側のパラメータ名をスタッフスタートのパラメータ名に変換する
     *
     * @param array $params
     *
     * @return array
     */
    public static function mapParams(array $params)
    {
        return $params;
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
        return new Response(require __DIR__.'/fixtures/coordinates.php');
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
        return new Response(require __DIR__.'/fixtures/coordinate_detail.php');
    }
}
