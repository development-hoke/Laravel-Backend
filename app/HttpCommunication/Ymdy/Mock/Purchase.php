<?php

namespace App\HttpCommunication\Ymdy\Mock;

use App\HttpCommunication\Response\Mock\Response;
use App\HttpCommunication\Ymdy\HttpCommunicationService;
use App\HttpCommunication\Ymdy\PurchaseInterface;

class Purchase extends HttpCommunicationService implements PurchaseInterface
{
    /**
     * リクエスト時に渡したデータを保存する。
     * テストで使用。
     *
     * @var array
     */
    public $lastRequestedParams = [];

    /**
     * メンバートークンの設定
     *
     * @param string $token
     *
     * @return static
     */
    public function setMemberTokenHeader(string $token)
    {
        return $this;
    }

    /**
     * スタッフトークンの設定
     *
     * @param string $token
     *
     * @return static
     */
    public function setStaffToken(string $token)
    {
        return $this;
    }

    /**
     * configを取得するためのキー
     *
     * @return string
     */
    protected function getConfigKey(): string
    {
        return 'ymdy_purchase';
    }

    /**
     * 購買時ポイント計算
     *
     * @param int $memberId
     * @param array $body
     *
     * @return ResponseInterface
     */
    public function calculatePoint(int $memberId, array $body)
    {
        $this->lastRequestedParams = func_get_args();

        return new Response([
            'base_grant_point' => 100,
            'special_grant_point' => 1000,
            'effective_point' => 500,
        ]);
    }

    /**
     * 購買キャンセル
     *
     * @param string $code
     *
     * @return Response
     */
    public function cancel(string $code)
    {
        return new Response();
    }

    /**
     * 購買完了（配送完了）
     *
     * @param string $code
     *
     * @return Response
     */
    public function finishPurchase(string $code)
    {
        return new Response();
    }

    /**
     * 購買後返品
     *
     * @param string $code
     * @param array $params
     *
     * @return ResponseInterface
     */
    public function markdown(string $code, array $params)
    {
        return new Response();
    }
}
