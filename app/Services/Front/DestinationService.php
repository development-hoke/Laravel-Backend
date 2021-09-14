<?php

namespace App\Services\Front;

use App\HttpCommunication\Ymdy\DestinationInterface;
use App\Services\Service;
use Illuminate\Http\Response;

class DestinationService extends Service implements DestinationServiceInterface
{
    /**
     * @var DestinationInterface
     */
    private $httpCommunication;

    public function __construct(DestinationInterface $httpCommunication)
    {
        $this->httpCommunication = $httpCommunication;
    }

    /**
     * 会員配送先住所一覧
     *
     * @param int $memberId
     * @param array $params
     *
     * @return array
     */
    public function indexDestinations(int $memberId, array $params)
    {
        $response = $this->httpCommunication->indexDestinations($memberId, $params);

        if ($response->getStatusCode() === Response::HTTP_UNAUTHORIZED) {
            throw new AuthenticationException();
        }

        $data = $response->getBody();

        return $data['shipping_addresses'];
    }

    /**
     * 会員配送先住所登録
     *
     * @param int $memberId
     * @param array $params
     *
     * @return array
     */
    public function storeDestination(int $memberId, array $params)
    {
        $response = $this->httpCommunication->storeDestination($memberId, $params);

        if ($response->getStatusCode() === Response::HTTP_UNAUTHORIZED) {
            throw new AuthenticationException();
        }

        $data = $response->getBody();

        return $data['shipping_address'];
    }

    /**
     * 会員配送先住所詳細
     *
     * @param int $destinationId
     *
     * @return array
     */
    public function showDestination(int $destinationId)
    {
        $response = $this->httpCommunication->showDestination($destinationId);

        if ($response->getStatusCode() === Response::HTTP_UNAUTHORIZED) {
            throw new AuthenticationException();
        }

        return $response->getBody();
    }

    /**
     * 会員配送先住所更新
     *
     * @param int $destinationId
     * @param array $params
     *
     * @return array
     */
    public function updateDestination(int $destinationId, array $params)
    {
        $response = $this->httpCommunication->updateDestination($destinationId, $params);

        if ($response->getStatusCode() === Response::HTTP_UNAUTHORIZED) {
            throw new AuthenticationException();
        }

        $data = $response->getBody();

        return $data['shipping_address'];
    }

    /**
     * 会員配送先住所削除
     *
     * @param int $destinationId
     *
     * @return array
     */
    public function deleteDestination(int $destinationId)
    {
        $response = $this->httpCommunication->deleteDestination($destinationId);

        if ($response->getStatusCode() === Response::HTTP_UNAUTHORIZED) {
            throw new AuthenticationException();
        }

        return true;
    }
}
