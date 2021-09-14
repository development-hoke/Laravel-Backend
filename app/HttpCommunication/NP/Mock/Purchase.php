<?php

namespace App\HttpCommunication\NP\Mock;

use App\HttpCommunication\Exceptions\HttpException;
use App\HttpCommunication\NP\HttpCommunicationService;
use App\HttpCommunication\NP\PurchaseInterface;
use App\HttpCommunication\Response\Mock\Response;

/**
 * @SuppressWarnings(PHPMD)
 */
class Purchase extends HttpCommunicationService implements PurchaseInterface
{
    private $shipmentCounter = 0;

    /**
     * configを取得するためのキー
     *
     * @return string
     */
    protected function getConfigKey(): string
    {
        return 'np';
    }

    /**
     * 注文登録
     *
     * @param array $params
     * @param array|null $options
     *
     * @return \App\HttpCommunication\Response\ResponseInterface
     */
    public function transactions(array $params, ?array $options = [])
    {
        return new Response([
            'results' => [
                [
                    'shop_transaction_id' => 'abc1234567890',
                    'np_transaction_id' => '18121200001',
                    'authori_result' => '00',
                    'authori_required_date' => '2018-12-12T12:00:00+09:00',
                ],
            ],
        ]);
    }

    /**
     * 注文変更
     *
     * @param string $npTransactionId
     * @param array $params
     * @param array|null $options
     *
     * @return \App\HttpCommunication\Response\ResponseInterface
     */
    public function updateTransaction(string $npTransactionId, array $params, ?array $options = [])
    {
        return new Response([
            'results' => [
                [
                    'shop_transaction_id' => 'abc1234567890',
                    'np_transaction_id' => '18121200001',
                    'authori_result' => '00',
                    'authori_required_date' => '2018-12-12T12:00:00+09:00',
                ],
            ],
        ]);
        // NG
        // return new Response([
        //     'results' => [
        //         [
        //             'shop_transaction_id' => '20210423-09967-999-00006',
        //             'np_transaction_id' => '18121200001',
        //             'authori_result' => '20',
        //             'authori_ng' => 'NG001',
        //             'authori_required_date' => '2018-12-12T12:00:00+09:00',
        //         ],
        //     ],
        // ]);
    }

    /**
     * 一部返品再登録
     *
     * @param string $npTransactionId
     * @param array $params
     * @param array|null $options
     *
     * @return \App\HttpCommunication\Response\ResponseInterface
     */
    public function reregister(string $npTransactionId, array $params, ?array $options = [])
    {
        // $response = new Response([
        //     'results' => [],
        //     'errors' => [
        //         [
        //             'codes' => ['E0100113'],
        //             'id' => '18121200000',
        //         ]
        //     ],
        // ]);
        // $ex = new \GuzzleHttp\Exception\BadResponseException(
        //     '',
        //     new \GuzzleHttp\Psr7\Request('post', 'http://xxx.com'),
        //     new \GuzzleHttp\Psr7\Response(400)
        // );
        // throw new HttpException($ex, $response);

        return new Response([
            'results' => [
                [
                    'base_np_transaction_id' => '18121200000',
                    'shop_transaction_id' => 'abc1234567890',
                    'np_transaction_id' => '18121200001',
                    'authori_result' => '00',
                    'authori_required_date' => '2018-12-12T12:00:00+09:00',
                ],
            ],
        ]);
    }

    /**
     * 出荷報告
     *
     * @param array $params
     * @param array|null $options
     *
     * @return \App\HttpCommunication\Response\ResponseInterface
     */
    public function shipments(array $params, ?array $options = [])
    {
        // if ($this->shipmentCounter === 0) {
        //     $this->shipmentCounter++;
        //     $response = new Response([
        //         'results' => [],
        //         'errors' => [
        //             [
        //                 'codes' => ['E0100114'],
        //                 'id' => '18121200000',
        //             ]
        //         ],
        //     ]);
        //     $ex = new \GuzzleHttp\Exception\BadResponseException(
        //         '',
        //         new \GuzzleHttp\Psr7\Request('post', 'http://xxx.com'),
        //         new \GuzzleHttp\Psr7\Response(400)
        //     );
        //     throw new HttpException($ex, $response);
        // }

        return new Response([
            'results' => [
                [
                    'np_transaction_id' => '18121200001',
                ],
            ],
        ]);
    }

    /**
     * 取引キャンセル
     *
     * @param string $npTransactionId
     * @param array|null $options
     *
     * @return \App\HttpCommunication\Response\ResponseInterface
     */
    public function cancel(string $npTransactionId, ?array $options = [])
    {
        // $response = new Response([
        //     'results' => [],
        //     'errors' => [
        //         [
        //             'codes' => ['E0100118'],
        //             'id' => '18121200000',
        //         ]
        //     ],
        // ]);
        // $ex = new \GuzzleHttp\Exception\BadResponseException(
        //     '',
        //     new \GuzzleHttp\Psr7\Request('post', 'http://xxx.com'),
        //     new \GuzzleHttp\Psr7\Response(400)
        // );
        // throw new HttpException($ex, $response);

        return new Response([
            'results' => [
                [
                    'np_transaction_id' => $npTransactionId,
                ],
            ],
        ]);
    }
}
