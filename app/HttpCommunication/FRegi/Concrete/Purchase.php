<?php

namespace App\HttpCommunication\FRegi\Concrete;

use App\HttpCommunication\FRegi\HttpCommunicationService;
use App\HttpCommunication\FRegi\PurchaseInterface;

class Purchase extends HttpCommunicationService implements PurchaseInterface
{
    /**
     * configを取得するためのキー
     *
     * @return string
     */
    protected function getConfigKey(): string
    {
        return 'f_regi';
    }

    /**
     * オーソリ処理
     *
     * @param array $authParam
     *
     * @return \App\HttpCommunication\Response\ResponseInterface
     */
    public function auth(array $authParam)
    {
        $memberCreditCardId = $authParam['member_credit_card_id'] ?? null;

        $params = [
            'SHOPID' => $this->config['shop_id'],
            'PAY' => $authParam['total_price'],
            'ID' => $authParam['order_id'],
            'CUSTOMERID' => $memberCreditCardId,
            'CHARCODE' => 'utf8',
            'MONTHLYMODE' => 1,
            'PAYMODE' => $authParam['payment_method'],
            'MOBILE' => 1,
            'IP' => $authParam['ip'],
        ];

        if (!$authParam['use_saved_card_info']) {
            $params['TOKEN'] = $authParam['token'];
            $params['MONTHLY'] = (int) $authParam['is_save_card_info'];
        } else {
            $params['SKIPSCODE'] = 1;
        }

        return $this->request(self::ENDPOINT_AUTH, [], [], ['query' => $params]);
    }

    /**
     * 売り上げ処理
     *
     * @param string $authorizationNumber
     * @param string $transactionNumber
     *
     * @return \App\HttpCommunication\Response\ResponseInterface
     */
    public function sale(string $authorizationNumber, string $transactionNumber)
    {
        $params = [
            'SHOPID' => $this->config['shop_id'],
            'AUTHCODE' => $authorizationNumber,
            'SEQNO' => $transactionNumber,
        ];

        return $this->request(self::ENDPOINT_SALE, [], [], ['query' => $params]);
    }

    /**
     * オーソリキャンセル
     *
     * @param string $transactionNumber
     *
     * @return \App\HttpCommunication\Response\ResponseInterface
     */
    public function authCancel(string $transactionNumber)
    {
        $params = [
            'SHOPID' => $this->config['shop_id'],
            'CANCELSEQNO' => $transactionNumber,
        ];

        return $this->request(self::ENDPOINT_AUTH_CANCEL, [], [], ['query' => $params]);
    }

    /**
     * 承認金額変更
     *
     * @param string $transactionNumber
     * @param array $params
     *
     * @return \App\HttpCommunication\Response\ResponseInterface
     */
    public function authChange(string $transactionNumber, array $params)
    {
        $params = [
            'SHOPID' => $this->config['shop_id'],
            'SEQNO' => $transactionNumber,
            'PAY' => $params['price'],
        ];

        return $this->request(self::ENDPOINT_AUTH_CHANGE, [], [], ['query' => $params]);
    }

    /**
     * 売上取消
     *
     * @param string $transactionNumber
     *
     * @return \App\HttpCommunication\Response\ResponseInterface
     */
    public function saleCancel(string $transactionNumber)
    {
        $params = [
            'SHOPID' => $this->config['shop_id'],
            'CANCELSEQNO' => $transactionNumber,
        ];

        return $this->request(self::ENDPOINT_SALE_CANCEL, [], [], ['query' => $params]);
    }

    /**
     * 売上金額変更
     *
     * @param string $transactionNumber
     * @param array $params
     *
     * @return \App\HttpCommunication\Response\ResponseInterface
     */
    public function saleChange(string $transactionNumber, array $params)
    {
        $params = [
            'SHOPID' => $this->config['shop_id'],
            'SEQNO' => $transactionNumber,
            'PAY' => $params['price'],
        ];

        return $this->request(self::ENDPOINT_SALE_CHANGE, [], [], ['query' => $params]);
    }

    /**
     * 顧客情報取得
     *
     * @param int $memberCreditCardId
     *
     * @return \App\HttpCommunication\Response\ResponseInterface
     */
    public function fetchCustomerInfo(int $memberCreditCardId)
    {
        $params = [
            'SHOPID' => $this->config['shop_id'],
            'CUSTOMERID' => $memberCreditCardId,
            'GETEXPIRE' => 1,
        ];

        return $this->request(self::ENDPOINT_FETCH_CUSTOMER_INFO, [], [], ['query' => $params]);
    }

    /**
     * 顧客ID削除
     *
     * @param int $memberCreditCardId
     *
     * @return \App\HttpCommunication\Response\ResponseInterface
     */
    public function leaveCustomer(int $memberCreditCardId)
    {
        $params = [
            'SHOPID' => $this->config['shop_id'],
            'CUSTOMERID' => $memberCreditCardId,
        ];

        return $this->request(self::ENDPOINT_LEAVE_CUSTOMER, [], [], ['query' => $params]);
    }
}
