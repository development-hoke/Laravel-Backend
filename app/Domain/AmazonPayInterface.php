<?php

namespace App\Domain;

interface AmazonPayInterface
{
    /**
     * @param string $orderReferenceId
     * @param string|null $accessToken
     *
     * @return \App\Entities\AmazonPay\OrderReferenceDetails
     */
    public function findOrderReferenceDetails(string $orderReferenceId, ?string $accessToken = null);

    /**
     * 注文金額を設定して、Constraintsの有無を検証する
     *
     * @param string $orderReferenceId
     * @param int $totalAmount
     * @param string $accessToken
     *
     * @return \App\Entities\AmazonPay\OrderReferenceDetails
     */
    public function orderConfirm(string $orderReferenceId, int $totalAmount, string $accessToken);

    /**
     * 注文処理の実行
     * (1) OrderReferenceDetailsの取得とConstraintsの確認
     * (2) amazon_pay_ordersテーブルにレコードを作成
     * (3) OrderReferenceをOpenにする
     * (4) オーソリの実行
     *
     * @param string $orderReferenceId
     * @param int $orderId
     * @param array $params ['access_token' => string]
     *
     * @return \App\Models\AmazonPayOrder
     */
    public function order(string $orderReferenceId, int $orderId, array $params);

    /**
     * オーソリを実行し、amazon_pay_authorizationsテーブルにレコードを作成する
     *
     * @param string $orderReferenceId
     * @param int $amazonPayOrderId
     * @param int $amount
     *
     * @return \App\Models\AmazonPayAuthorization
     */
    public function authorize(string $orderReferenceId, int $amazonPayOrderId, int $amount);

    /**
     * amazon_pay_ordersテーブルにレコードを作成する
     *
     * @param \App\Entities\AmazonPay\OrderReferenceDetails $orderReferenceDetails
     * @param int $orderId
     *
     * @return \App\Models\AmazonPayOrder
     */
    public function createAmazonPayOrder(\App\Entities\AmazonPay\OrderReferenceDetails $orderReferenceDetails, int $orderId);

    /**
     * amazon_pay_ordersを更新
     *
     * @param \App\Entities\AmazonPay\OrderReferenceDetails $orderReferenceDetails
     * @param int $orderId
     *
     * @return \App\Models\AmazonPayOrder
     */
    public function importAmazonPayOrder(\App\Entities\AmazonPay\OrderReferenceDetails $orderReferenceDetails);

    /**
     * キャプチャを実行する
     *
     * @param int $orderId
     *
     * @return array
     */
    public function capture(int $orderId);

    /**
     * amazon_pay_authorizationsテーブルにレコードを作成する
     *
     * @param \App\Entities\AmazonPay\AuthorizationDetails $authorizationDetails
     * @param int $amazonPayOrderId
     *
     * @return \App\Models\AmazonPayAuthorization
     */
    public function createAuthorization(
        \App\Entities\AmazonPay\AuthorizationDetails $authorizationDetails,
        int $amazonPayOrderId
    );

    /**
     * amazon_pay_authorizationの更新
     *
     * @param \App\Entities\AmazonPay\AuthorizationDetails $authorizationDetails
     * @param bool|null $delete
     *
     * @return \App\Models\AmazonPayAuthorization
     */
    public function importAuthorizationDetails(
        \App\Entities\AmazonPay\AuthorizationDetails $authorizationDetails,
        ?bool $delete = false
    );

    /**
     * amazon_pay_capturesを作成する
     *
     * @param \App\Entities\AmazonPay\CaptureDetails $captureDetails
     * @param \App\Models\AmazonPayAuthorization $authorization
     *
     * @return \App\Models\AmazonPayCapture
     */
    public function createCapture(
        \App\Entities\AmazonPay\CaptureDetails $captureDetails,
        \App\Models\AmazonPayAuthorization $authorization
    );

    /**
     * amazon_pay_capturesを更新する
     *
     * @param \App\Entities\AmazonPay\CaptureDetails $captureDetails
     *
     * @return \App\Models\AmazonPayCapture
     */
    public function importCaptureDetails(\App\Entities\AmazonPay\CaptureDetails $captureDetails);

    /**
     * 注文のキャンセル
     *
     * @param int $orderId
     *
     * @return \App\Models\AmazonPayOrder
     */
    public function cancelOrder(int $orderId);

    /**
     * オーソリした金額を変更する
     *
     * @param int $orderId
     * @param int $newAmount
     *
     * @return \App\Models\AmazonPayOrder
     */
    public function changeAuthorizationAmount(int $orderId, int $newAmount);

    /**
     * 返金処理
     *
     * @param int $orderId
     * @param int $amount
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function refund(int $orderId, int $amount);
}
