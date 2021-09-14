<?php

namespace App\HttpCommunication\StaffStart;

interface StaffStartInterface
{
    const ENDPONT_INDEX_COORDINATES = 'index_coordinates';
    const ENDPONT_SHOW_COORDINATE_DETAIL = 'show_coordinate_detail';

    /**
     * ec側のパラメータ名をスタッフスタートのパラメータ名に変換する
     *
     * @param array $params
     *
     * @return array
     */
    public static function mapParams(array $params);

    /**
     * レスポンスボディの配列から成功・失敗を判定
     *
     * @return bool
     */
    public function isSuccess(array $body);

    /**
     * コーディネート一覧を取得
     *
     * @param array $query
     *
     * @return \App\HttpCommunication\Response\ResponseInterface
     */
    public function fetchCoordinates($query = []);

    /**
     * コーディネート詳細を取得
     *
     * @param int $coordinateId
     *
     * @return \App\HttpCommunication\Response\ResponseInterface
     */
    public function fetchCoordinateDetail($coordinateId);
}
