<?php

namespace App\HttpCommunication\Ymdy\Concrete;

use App\HttpCommunication\Response\ResponseInterface;
use App\HttpCommunication\Ymdy\HttpCommunicationService;
use App\HttpCommunication\Ymdy\PurchaseInterface;

class Purchase extends HttpCommunicationService implements PurchaseInterface
{
    /**
     * トークンが必要なエンドポイントの設定
     *
     * @var array
     */
    protected $needsToken = [
        self::PURCHASE_POINT,
    ];

    /**
     * configを取得するためのキー
     *
     * @return string
     */
    protected function getConfigKey(): string
    {
        return 'ymdy_member';
    }

    /**
     * メンバートークンの設定
     *
     * @param string $token
     *
     * @return static
     */
    public function setMemberTokenHeader(string $token)
    {
        return $this->setTokenHeader(static::HEADER_MEMBER_TOKEN, $token);
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
        return $this->setTokenHeader(static::HEADER_STAFF_TOKEN, $token);
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
        return $this->request(self::PURCHASE_POINT, ['member_id' => $memberId], $body);
    }

    /**
     * @param string $code
     *
     * @return ResponseInterface
     */
    public function cancel(string $code)
    {
        return $this->request(self::PURCHASE_CANCEL, ['purchase_id' => $code]);
    }

    /**
     * 購買完了（配送完了）
     *
     * @param string $code
     *
     * @return ResponseInterface
     */
    public function finishPurchase(string $code)
    {
        return $this->request(self::PURCHASE_FINISH, ['purchase_id' => $code]);
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
        return $this->request(self::PURCHASE_MARKDOWN, ['purchase_id' => $code], $params);
    }
}
