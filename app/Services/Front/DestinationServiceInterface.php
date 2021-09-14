<?php

namespace App\Services\Front;

interface DestinationServiceInterface
{
    /**
     * 会員配送先住所一覧
     *
     * @param int $memberId
     * @param array $params
     *
     * @return array
     */
    public function indexDestinations(int $memberId, array $params);

    /**
     * 会員配送先住所登録
     *
     * @param int $memberId
     * @param array $params
     *
     * @return array
     */
    public function storeDestination(int $memberId, array $params);

    /**
     * 会員配送先住所詳細
     *
     * @param int $destinationId
     *
     * @return array
     */
    public function showDestination(int $destinationId);

    /**
     * 会員配送先住所更新
     *
     * @param int $destinationId
     * @param array $params
     *
     * @return array
     */
    public function updateDestination(int $destinationId, array $params);

    /**
     * 会員配送先住所削除
     *
     * @param int $destinationId
     *
     * @return array
     */
    public function deleteDestination(int $destinationId);
}
