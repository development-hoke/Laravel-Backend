<?php

namespace App\Services\Front;

use App\Http\Resources\AvailableCoupon as AvailableCouponResource;
use App\Http\Resources\Coupon as CouponResource;
use App\HttpCommunication\Ymdy\MemberInterface;
use App\Repositories\UserRepository;
use App\Services\Service;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Response;

class MemberService extends Service implements MemberServiceInterface
{
    /**
     * @var UserRepository
     */
    private $repository;

    /**
     * @var MemberInterface
     */
    private $httpCommunication;

    public function __construct(
        UserRepository $repository,
        MemberInterface $httpCommunication
    ) {
        $this->repository = $repository;
        $this->httpCommunication = $httpCommunication;

        if (auth('api')->check()) {
            $this->httpCommunication->setMemberTokenHeader(auth('api')->user()->token);
        }
    }

    /**
     * @param array $params
     *
     * @return array
     */
    public function storeTemp(array $params)
    {
        $response = $this->httpCommunication->storeTemp($params);

        if ($response->getStatusCode() === Response::HTTP_UNAUTHORIZED) {
            throw new AuthenticationException();
        }

        return $response->getBody();
    }

    /**
     * @param int $memberId
     * @param array $params
     *
     * @return array
     */
    public function updateMemberToken(int $memberId, array $params)
    {
        return $this->repository->update($params, $memberId);
    }

    /**
     * @param int $memberId
     * @param array $params
     * @param string $memberToken
     *
     * @return array
     */
    public function update(int $memberId, array $params, string $memberToken = null)
    {
        $response = $this->httpCommunication->updateMember($memberId, $params, $memberToken);

        if ($response->getStatusCode() === Response::HTTP_UNAUTHORIZED) {
            throw new AuthenticationException();
        }

        return $response->getBody();
    }

    /**
     * @param int $memberId
     * @param string $memberToken
     *
     * @return array
     */
    public function get(int $memberId, string $memberToken = null)
    {
        $response = $this->httpCommunication->showMember($memberId, $memberToken);

        if ($response->getStatusCode() === Response::HTTP_UNAUTHORIZED) {
            throw new AuthenticationException();
        }

        return $response->getBody();
    }

    /**
     * パスワードリセット
     *
     * @param array $params
     *
     * @return array
     */
    public function sendPasswordResetRequest(array $params)
    {
        $response = $this->httpCommunication->resetPassword($params);

        if ($response->getStatusCode() === Response::HTTP_UNAUTHORIZED) {
            throw new AuthenticationException();
        }

        return $response->getBody();
    }

    /**
     * パスワードリセット後の新パスワード設定
     *
     * @param int $memberId
     * @param array $params
     *
     * @return array
     */
    public function resetPasswordDecision(int $memberId, array $params)
    {
        $response = $this->httpCommunication->resetPasswordDecision($memberId, [
            'password' => $params['password'],
            'member_token' => $params['member_token'],
        ]);

        if ($response->getStatusCode() === Response::HTTP_UNAUTHORIZED) {
            throw new AuthenticationException();
        }

        return $response->getBody();
    }

    /**
     * メールアドレス変更
     *
     * @param int $memberId
     * @param array $params
     *
     * @return array
     */
    public function changeEmail(int $memberId, array $params)
    {
        $response = $this->httpCommunication->changeEmail($memberId, $params);

        if ($response->getStatusCode() === Response::HTTP_UNAUTHORIZED) {
            throw new AuthenticationException();
        }

        return $response->getBody();
    }

    /**
     * パスワード変更
     *
     * @param int $memberId
     * @param array $params
     *
     * @return array
     */
    public function changePassword(int $memberId, array $params)
    {
        $response = $this->httpCommunication->changePassword($memberId, $params);

        if ($response->getStatusCode() === Response::HTTP_UNAUTHORIZED) {
            throw new AuthenticationException();
        }

        return $response->getBody();
    }

    /**
     * 会員発行可能クーポン一覧取得
     *
     * @param array $params
     *
     * @return array
     */
    public function getCoupons($memberId, array $params)
    {
        $response = $this->httpCommunication->getCoupons($memberId, $params);

        if ($response->getStatusCode() === Response::HTTP_UNAUTHORIZED) {
            throw new AuthenticationException();
        }

        $data = $response->getBody();

        return CouponResource::collection($data['coupons']);
    }

    /**
     * 会員クーポン発行
     *
     * @param array $params
     *
     * @return array
     */
    public function issueCoupon($memberId, int $couponId, array $params)
    {
        $response = $this->httpCommunication->issueCoupon($memberId, $couponId, $params);

        if ($response->getStatusCode() === Response::HTTP_UNAUTHORIZED) {
            throw new AuthenticationException();
        }

        return new CouponResource($response->getBody());
    }

    /**
     * 会員利用可能クーポン一覧取得
     *
     * @param array $params
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function getAvailableCoupons($memberId, array $params = [])
    {
        if (is_null($memberId)) {
            return AvailableCouponResource::collection([]);
        }
        $response = $this->httpCommunication->getAvailableCoupons($memberId, $params);

        if ($response->getStatusCode() === Response::HTTP_UNAUTHORIZED) {
            throw new AuthenticationException();
        }

        $data = $response->getBody();

        return AvailableCouponResource::collection($data['member_coupons']);
    }

    /**
     * 会員クーポン利用
     *
     * @param array $params
     *
     * @return array
     */
    public function useCoupon($memberId, int $couponId, array $params)
    {
    }

    /**
     * 退会
     *
     * @param int $memberId
     * @param array $params
     *
     * @return bool
     */
    public function withdraw(int $memberId, array $params)
    {
        $response = $this->httpCommunication->withdraw($memberId, $params);

        if ($response->getStatusCode() === Response::HTTP_UNAUTHORIZED) {
            throw new AuthenticationException();
        }

        return true;
    }

    /**
     * 会員ポイント履歴取得
     *
     * @param int $memberId
     * @param array $params
     *
     * @return \App\HttpCommunication\Response\ResponseInterface
     */
    public function getPointHistory(int $memberId, array $params)
    {
        $response = $this->httpCommunication->pointHistory($memberId, $params);

        if ($response->getStatusCode() === Response::HTTP_UNAUTHORIZED) {
            throw new AuthenticationException();
        }

        $data = $response->getBody();

        return [$data['point_logs'], $data['total_count']];
    }
}
