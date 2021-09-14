<?php

namespace App\HttpCommunication\Ymdy;

use App\HttpCommunication\HttpCommunicationServiceInterface;
use App\HttpCommunication\Response\ResponseInterface;

interface PurchaseInterface extends HttpCommunicationServiceInterface
{
    const PURCHASE_POINT = 'purchase_point'; // 購買時ポイント計算
    const PURCHASE_CANCEL = 'purchase_cancel'; // 購買キャンセル
    const PURCHASE_FINISH = 'purchase_finish'; // 購買完了（配送完了）
    const PURCHASE_MARKDOWN = 'purchase_markdown'; // 購買後返品

    const HEADER_MEMBER_TOKEN = 'Member-Token';
    const HEADER_STAFF_TOKEN = 'Staff-Token';

    /**
     * メンバートークンの設定
     *
     * @param string $token
     *
     * @return static
     */
    public function setMemberTokenHeader(string $token);

    /**
     * スタッフトークンの設定
     *
     * @param string $token
     *
     * @return static
     */
    public function setStaffToken(string $token);

    /**
     * 購買時ポイント計算
     *
     * @param int $memberId
     * @param array $body
     *
     * @return ResponseInterface
     */
    public function calculatePoint(int $memberId, array $body);

    /**
     * 購買キャンセル
     *
     * @param string $code
     *
     * @return ResponseInterface
     */
    public function cancel(string $code);

    /**
     * 購買完了（配送完了）
     *
     * @param string $code
     *
     * @return ResponseInterface
     */
    public function finishPurchase(string $code);

    /**
     * 購買後返品
     *
     * @param string $code
     * @param array $params
     *
     * @return ResponseInterface
     */
    public function markdown(string $code, array $params);
}
