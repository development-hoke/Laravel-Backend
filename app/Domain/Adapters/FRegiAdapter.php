<?php

namespace App\Domain\Adapters;

use App\HttpCommunication\FRegi\PurchaseInterface as FRegiHttpCommunication;

class FRegiAdapter implements FRegiAdapterInterface
{
    /**
     * @var FRegiHttpCommunication
     */
    private $fRegiHttpCommunication;

    /**
     * @param FRegiHttpCommunication $fRegiHttpCommunication
     */
    public function __construct(FRegiHttpCommunication $fRegiHttpCommunication)
    {
        $this->fRegiHttpCommunication = $fRegiHttpCommunication;
    }

    /**
     * オーソリ
     *
     * @param array $params
     *
     * @return \App\Entities\FRegi\AuthResult
     */
    public function auth(array $params)
    {
        $response = $this->fRegiHttpCommunication->auth($params);

        $body = $response->getBody();

        return new \App\Entities\FRegi\AuthResult([
            'authorization_number' => $body[1],
            'transaction_number' => $body[2],
        ]);
    }

    /**
     * 売上処理
     *
     * @param string $authorizationNumber
     * @param string $transactionNumber
     *
     * @return \App\Entities\FRegi\SaleResult
     */
    public function sale(string $authorizationNumber, string $transactionNumber)
    {
        $response = $this->fRegiHttpCommunication->sale($authorizationNumber, $transactionNumber);

        $body = $response->getBody();

        return new \App\Entities\FRegi\SaleResult([
            'transaction_number' => $body[1],
        ]);
    }

    /**
     * 承認金額変更
     *
     * @param string $transactionNumber
     * @param array $params
     *
     * @return \App\Entities\FRegi\AuthResult
     */
    public function authChange(string $transactionNumber, array $params)
    {
        $response = $this->fRegiHttpCommunication->authChange($transactionNumber, $params);

        $body = $response->getBody();

        return new \App\Entities\FRegi\AuthResult([
            'transaction_number' => $body[1],
            'authorization_number' => $body[2],
        ]);
    }

    /**
     * 売上金額変更
     *
     * @param string $transactionNumber
     * @param array $params
     *
     * @return \App\Entities\FRegi\SaleResult
     */
    public function saleChange(string $transactionNumber, array $params)
    {
        $response = $this->fRegiHttpCommunication->saleChange($transactionNumber, $params);

        $body = $response->getBody();

        return new \App\Entities\FRegi\SaleResult([
            'transaction_number' => $body[1],
        ]);
    }

    /**
     * オーソリキャンセル
     *
     * @param string $transactionNumber
     *
     * @return bool
     */
    public function authCancel(string $transactionNumber)
    {
        $this->fRegiHttpCommunication->authCancel($transactionNumber);

        return true;
    }

    /**
     * 売上取消
     *
     * @param string $authorizationNumber
     * @param string $transactionNumber
     *
     * @return \App\Entities\FRegi\SaleResult
     */
    public function saleCancel(string $transactionNumber)
    {
        $response = $this->fRegiHttpCommunication->saleCancel($transactionNumber);

        $body = $response->getBody();

        return new \App\Entities\FRegi\SaleResult([
            'transaction_number' => $body[1],
        ]);
    }

    /**
     * 顧客情報取得
     *
     * @param int $memberCreditCardId
     *
     * @return \App\Entities\FRegi\CustomerInfo
     */
    public function fetchCustomerInfo(int $memberCreditCardId)
    {
        $response = $this->fRegiHttpCommunication->fetchCustomerInfo($memberCreditCardId);

        $body = $response->getBody();

        return new \App\Entities\FRegi\CustomerInfo([
            'card_number' => $body[1],
            'expiry_month' => $body[2],
            'expiry_year' => $body[3],
        ]);
    }

    /**
     * 顧客ID削除
     *
     * @param int $memberCreditCardId
     *
     * @return bool
     */
    public function leaveCustomer(int $memberCreditCardId)
    {
        $this->fRegiHttpCommunication->leaveCustomer($memberCreditCardId);

        return true;
    }
}
