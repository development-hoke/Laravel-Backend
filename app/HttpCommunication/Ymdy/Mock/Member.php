<?php

namespace App\HttpCommunication\Ymdy\Mock;

use App\HttpCommunication\Response\Mock\Response;
use App\HttpCommunication\Ymdy\HttpCommunicationService;
use App\HttpCommunication\Ymdy\MemberInterface;

/**
 * 会員・ポイントシステムとの連携で使用する（モック）
 * 会員ポイントシステム
 *
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
class Member extends HttpCommunicationService implements MemberInterface
{
    /**
     * リクエスト時に渡したデータを保存する。
     * テストで使用。
     *
     * @var array
     */
    public $lastRequestedParams = [];

    /**
     * 手動で設定するダミーレスポンス
     *
     * @var array
     */
    public $dummyResponses = [];

    /**
     * @param string $methodName
     * @param mixed $value
     *
     * @return static
     */
    public function setDummyResponse($methodName, $value)
    {
        $this->dummyResponses[$methodName] = $value;

        return $this;
    }

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
     * 会員一覧・検索
     *
     * @return \App\HttpCommunication\Response\ResponseInterface
     */
    public function indexMember(array $query = [])
    {
        return new Response(require __DIR__.'/fixtures/member_list.php');
    }

    /**
     * 会員詳細
     *
     * @param int $memberId
     * @param string $memberToken
     *
     * @return \App\HttpCommunication\Response\ResponseInterface
     */
    public function showMember(int $memberId, string $memberToken = null)
    {
        // $member = require(__DIR__.'/fixtures/member.php');
        $members = require __DIR__.'/fixtures/member_list.php';

        foreach ($members['members'] as $member) {
            if ((int) $member['id'] === (int) $memberId) {
                break;
            }
        }

        return new Response(['member' => $member]);
    }

    /**
     * クーポン利用
     *
     * @param int $memberId
     * @param int $couponId
     * @param string $memberToken
     *
     * @return \App\HttpCommunication\Response\ResponseInterface
     */
    public function useAvailableCoupon(int $memberId, int $couponId, string $memberToken)
    {
        return new Response();
    }

    /**
     * ポイント付与
     *
     * @param array $body
     *
     * @return \App\HttpCommunication\Response\ResponseInterface
     */
    public function addPointToMember(array $body)
    {
        return new Response();
    }

    /**
     * パスワード認証
     *
     * @param array $params
     *
     * @return \App\HttpCommunication\Response\ResponseInterface
     */
    public function authPassword(array $params)
    {
        return new Response([
            'member_token' => [
                'token' => 'f9c389fe-0a7e-444a-a976-9b2763c37883',
                'member_id' => '200000001',
                'limit' => '2021-02-03 18:30:42',
                'created_at' => '2021-02-03 17:30:42',
                'updated_at' => '2021-02-03 17:30:42',
            ],
        ]);
    }

    /**
     * 会員仮登録
     *
     * @param array $params
     *
     * @return \App\HttpCommunication\Response\ResponseInterface
     */
    public function storeTemp(array $params)
    {
        return new Response();
    }

    /**
     * 会員Amazon登録
     *
     * @param array $params
     *
     * @return \App\HttpCommunication\Response\ResponseInterface
     */
    public function storeAmazon(array $params)
    {
        return new Response();
    }

    /**
     * 会員amazonアカウント紐付け
     *
     * @param array $params
     *
     * @return \App\HttpCommunication\Response\ResponseInterface
     */
    public function linkAmazon(int $memberId, array $params)
    {
        return new Response();
    }

    /**
     * トークンリフレッシュ
     *
     * @param array $params
     *
     * @return \App\HttpCommunication\Response\ResponseInterface
     */
    public function tokenRefresh(array $params)
    {
        return new Response();
    }

    /**
     * トークン破棄
     *
     * @param array $params
     *
     * @return \App\HttpCommunication\Response\ResponseInterface
     */
    public function tokenExpire()
    {
        return new Response();
    }

    /**
     * 会員パスワード再設定依頼
     *
     * @param array $params
     *
     * @return \App\HttpCommunication\Response\ResponseInterface
     */
    public function resetPassword(array $params)
    {
        return new Response();
    }

    /**
     * 会員パスワード再設定
     *
     * @param int $memberId
     * @param array $params
     *
     * @return \App\HttpCommunication\Response\ResponseInterface
     */
    public function resetPasswordDecision(int $memberId, array $params)
    {
        return new Response();
    }

    /**
     * 会員検索
     *
     * @return \App\HttpCommunication\Response\ResponseInterface
     */
    public function fetchMembers()
    {
        return new Response();
    }

    /**
     * 会員詳細
     *
     * @return \App\HttpCommunication\Response\ResponseInterface
     */
    public function fetchMemberDetail(int $memberId)
    {
        return new Response(require __DIR__.'/fixtures/member.php');
    }

    /**
     * 会員更新
     *
     * @param int $memberId
     * @param array $params
     * @param string $memberToken
     *
     * @return \App\HttpCommunication\Response\ResponseInterface
     */
    public function updateMember(int $memberId, array $params, string $memberToken = null)
    {
        return new Response();
    }

    /**
     * メールアドレス変更
     *
     * @param int $memberId
     * @param array $params
     *
     * @return \App\HttpCommunication\Response\ResponseInterface
     */
    public function changeEmail(int $memberId, array $params)
    {
        return new Response();
    }

    /**
     * 会員発行可能クーポン一覧取得
     *
     * @param int $memberId
     * @param array $params
     *
     * @return \App\HttpCommunication\Response\ResponseInterface
     */
    public function getCoupons(int $memberId, array $params)
    {
        return new Response();
    }

    /**
     * 会員クーポン発行
     *
     * @param int $memberId
     * @param array $params
     *
     * @return \App\HttpCommunication\Response\ResponseInterface
     */
    public function issueCoupon(int $memberId, int $couponId, array $params)
    {
        return new Response();
    }

    /**
     * 会員利用可能クーポン一覧取得
     *
     * @param int $memberId
     * @param array $query
     *
     * @return \App\HttpCommunication\Response\ResponseInterface
     */
    public function getAvailableCoupons(int $memberId, ?array $query = [])
    {
        return new Response([
            'member_coupons' => [
                [
                    'id' => 1,
                    'token' => 'string',
                    'member_id' => $memberId,
                    'coupon_id' => 1,
                    'coupon' => [
                        'id' => 1,
                        'member_group_id' => 0,
                        'name' => '年末クーポン',
                        'target_member_type' => 1,
                        'member_data' => [
                            0,
                        ],
                        'target_shop_type' => 1,
                        'shop_data' => [
                            0,
                        ],
                        'issuance_limit' => 0,
                        'usage_number_limit' => 0,
                        'image_path' => 'https://images.ctfassets.net/hrltx12pl8hq/VZW7M82mrxByGHjvze4wu/216d9ff35b6980d850d108a50ae387bf/Carousel_01_FreeTrial.jpg',
                        'start_dt' => '2020-01-01 09:00:00',
                        'end_dt' => '2020-01-01 09:00:00',
                        'free_shipping_flag' => false,
                        'discount_item_flag' => false,
                        'discount_type' => 1,
                        'discount_amount' => 1000,
                        'discount_rate' => 0,
                        'target_item_type' => 1,
                        'item_data' => [
                            0,
                        ],
                        'usage_amount_term_flag' => false,
                        'usage_amount_minimum' => 0,
                        'usage_amount_maximum' => 0,
                        'is_combinable' => false,
                        'description' => '年末クーポンです。オンラインストアでのみ利用できます。',
                        'approval_status' => 1,
                        'crated_at' => '2020-01-01 09:00:00',
                        'updated_at' => '2020-01-01 09:00:00',
                    ],
                    'status' => 1,
                    'expiration' => '2020-01-01',
                    'last_use_date' => '2020-01-01',
                    'created_at' => '2020-01-01 09:00:00',
                    'updated_at' => '2020-01-01 09:00:00',
                ],
            ],
        ]);
    }

    /**
     * 会員利用可能クーポン検索
     *
     * @param int $memberId
     * @param array|null $query
     * @param array|null $params
     *
     * @return \App\HttpCommunication\Response\ResponseInterface
     */
    public function searchAvailableCoupon(int $memberId, ?array $query = [], ?array $body = [])
    {
        $page = $query['page'] ?? 1;

        if ($page > 3) {
            return new Response(['member_coupons' => [], 'total_count' => 10]);
        }

        $memberCoupons = require __DIR__.'/fixtures/member_coupon_list.php';

        $memberCoupons['member_coupons'] = array_map(function ($memberCoupon) use ($page, $memberCoupons) {
            $memberCoupon['coupon']['id'] += ($page - 1) * count($memberCoupons['member_coupons']);

            return $memberCoupon;
        }, $memberCoupons['member_coupons']);

        return new Response($memberCoupons);
    }

    /**
     * クーポン併用可能の判定
     *
     * @param int $memberId
     * @param array $body
     *
     * @return \App\HttpCommunication\Response\ResponseInterface
     */
    public function checkAvailableCoupons(int $memberId, array $body = [])
    {
        return new Response();
    }

    /**
     * 会員クーポン利用
     *
     * @param int $memberId
     * @param array $params
     *
     * @return \App\HttpCommunication\Response\ResponseInterface
     */
    public function useCoupon(int $memberId, int $couponId, array $params)
    {
        return new Response();
    }

    /**
     * クーポン詳細取得
     *
     * @param int $couponId
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function showCoupon($couponId)
    {
        if (isset($this->dummyResponses[__FUNCTION__])) {
            return new Response($this->dummyResponses[__FUNCTION__]);
        }

        $data = require __DIR__.'/fixtures/coupon_list.php';

        foreach ($data['coupons'] as $coupon) {
            if ((int) $coupon['id'] === $couponId) {
                break;
            }
        }

        return new Response(['coupon' => $coupon]);
    }

    /**
     * 会員購買登録
     *
     * @param int $memberId
     * @param array $params
     *
     * @return \App\HttpCommunication\Response\ResponseInterface
     */
    public function storePurchase(int $memberId, array $params)
    {
        $this->lastRequestedParams = func_get_args();

        return new Response(require __DIR__.'/fixtures/purchasing_bill.php');
    }

    /**
     * 会員購買登録
     *
     * @param int $memberId
     * @param string $memberId
     * @param array $params
     *
     * @return \App\HttpCommunication\Response\ResponseInterface
     */
    public function updatePurchase(int $memberId, string $purchaseId, array $params)
    {
        $this->lastRequestedParams = func_get_args();

        return new Response(require __DIR__.'/fixtures/purchasing_bill.php');
    }

    public function changePassword(int $memberId, array $params)
    {
        // TODO: Implement changePassword() method.
    }

    public function withdraw(int $memberId, array $params)
    {
        // TODO: Implement withdraw() method.
    }

    public function pointHistory(int $memberId, array $params)
    {
        $data = require __DIR__.'/fixtures/coupon_list.php';

        return new Response($data);
    }

    /**
     * 代理ログイン
     *
     * @param array $params
     *
     * @return \App\HttpCommunication\Response\ResponseInterface
     */
    public function authAgent(array $params)
    {
        $data = require __DIR__.'/fixtures/auth_agent.php';

        return new Response($data);
    }
}
