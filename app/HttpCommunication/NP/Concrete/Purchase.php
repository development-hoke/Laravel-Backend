<?php

namespace App\HttpCommunication\NP\Concrete;

use App\HttpCommunication\NP\HttpCommunicationService;
use App\HttpCommunication\NP\PurchaseInterface;

class Purchase extends HttpCommunicationService implements PurchaseInterface
{
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
        $config = $this->config;

        $body = [
            'transactions' => [
                array_merge($params, [
                    'site_name' => $config['site_name'],
                    'site_url' => $config['site_url'],
                ]),
            ],
        ];

        return $this->request(self::ENDPOINT_TRANSACTIONS, [], $body, $options);
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
        $body = [
            'transactions' => [
                array_merge($params, [
                    'np_transaction_id' => $npTransactionId,
                ]),
            ],
        ];

        return $this->request(self::ENDPOINT_UPDATE_TRANSACTION, [], $body, $options);
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
        $body = [
            'transactions' => [
                array_merge([
                    'base_np_transaction_id' => $npTransactionId,
                ], $params),
            ],
        ];

        return $this->request(self::ENDPOINT_TRANSACTION_REREGISTER, [], $body, $options);
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
        $body = [
            'transactions' => [$params],
        ];

        return $this->request(self::ENDPOINT_SHIPMENTS, [], $body, $options);
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
        $body = [
            'transactions' => [
                ['np_transaction_id' => $npTransactionId],
            ],
        ];

        return $this->request(self::ENDPOINT_TRANSACTION_CANCEL, [], $body, $options);
    }
}
