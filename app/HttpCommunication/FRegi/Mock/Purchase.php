<?php

namespace App\HttpCommunication\FRegi\Mock;

use App\HttpCommunication\Exceptions\FRegiResponseException;
use App\HttpCommunication\FRegi\HttpCommunicationService;
use App\HttpCommunication\FRegi\PurchaseInterface;
use App\HttpCommunication\Response\Mock\FRegiResponse;

/**
 * @SuppressWarnings(PHPMD)
 */
class Purchase extends HttpCommunicationService implements PurchaseInterface
{
    /**
     * @var int
     */
    private $saleIncrementer = 0;

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
        return new FRegiResponse([
            'OK',
            '222222',
            '00000000000000914710',
            '',
        ]);
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
        return new FRegiResponse([
            'OK',
            '00000000000000914710',
            '',
        ]);

        // $response = new FRegiResponse([
        //     'NG',
        //     'S1-1-125(売上処理時の指定された取引は売上処理期限を超過しています)',
        //     '',
        // ]);

        // throw new FRegiResponseException($response);

        // if ($this->saleIncrementer === 0) {
        //     $this->saleIncrementer++;

        //     $response = new FRegiResponse([
        //         'NG',
        //         'S1-1-125(売上処理時の指定された取引は売上処理期限を超過しています)',
        //         '',
        //     ]);

        //     throw new FRegiResponseException($response);
        // } else {
        //     return new FRegiResponse([
        //         'OK',
        //         '00000000000000914710',
        //         '',
        //     ]);
        // }
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
        return new FRegiResponse([
            'OK',
            '',
        ]);
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
        return new FRegiResponse([
            'OK',
            '222222',
            '00000000000000914710',
            '',
        ]);
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
        return new FRegiResponse([
            'OK',
            '00000000000000914710',
        ]);
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
        return new FRegiResponse([
            'OK',
            '00000000000000914710',
            '',
        ]);

        // $response = new FRegiResponse([
        //     'NG',
        //     'S3-1-21(売上金額変更時の金額が大きすぎます)',
        //     '',
        // ]);

        // throw new FRegiResponseException($response);
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
        return new FRegiResponse([
            'OK',
            '1111',
            '02',
            '27',
        ]);
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
        return new FRegiResponse([
            'OK',
        ]);
    }
}
