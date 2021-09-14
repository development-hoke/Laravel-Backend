<?php

namespace App\Services\Front;

use App\Domain\MemberAuthInterface as MemberAuth;
use App\Http\Response;
use App\HttpCommunication\AmazonPay\Exceptions\GetUserInfoInvalidAccessTokenException;
use App\HttpCommunication\AmazonPay\HttpCommunication as AmazonPayHttpCommunication;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\HttpException;

class AmazonLoginService implements AmazonLoginServiceInterface
{
    /**
     * @var AmazonPayHttpCommunication
     */
    private $amazonHttpCommunication;

    /**
     * @var MemberAuth
     */
    private $memberAuth;

    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @param AmazonPayHttpCommunication $amazonHttpCommunication
     * @param MemberAuth $memberAuth
     * @param UserRepository $userRepository
     */
    public function __construct(
        AmazonPayHttpCommunication $amazonHttpCommunication,
        MemberAuth $memberAuth,
        UserRepository $userRepository
    ) {
        $this->amazonHttpCommunication = $amazonHttpCommunication;
        $this->memberAuth = $memberAuth;
        $this->userRepository = $userRepository;

        if (auth('api')->check()) {
            $this->memberAuth->setMemberToken(auth('api')->user()->token);
        }
    }

    /**
     * Amazonアカウントと会員IDを紐付ける
     *
     * @param string $accessToken
     *
     * @return \App\Models\User
     */
    public function linkAccount(string $accessToken)
    {
        try {
            DB::beginTransaction();

            $userProfile = $this->getAmazonUserProfile($accessToken);

            $user = auth('api')->user();

            $user = $this->memberAuth->setMemberToken($user->token)->tokenRefresh();

            $user = $this->userRepository->update([
                'amazon_user_id' => $userProfile->user_id,
            ], $user->id);

            DB::commit();

            return $user;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Amazonのトークンを利用して、会員ポイントシステムのトークンをリフレッシュする。
     *
     * @param string $accessToken
     *
     * @return \App\Models\User
     */
    public function auth(string $accessToken)
    {
        try {
            DB::beginTransaction();

            $userProfile = $this->getAmazonUserProfile($accessToken);

            $user = $this->userRepository->findWhere([
                'amazon_user_id' => $userProfile->user_id,
            ])->first();

            if (empty($user)) {
                throw new HttpException(Response::HTTP_UNAUTHORIZED, __('validation.amazon_login.no_tied_account'));
            }

            $user = $this->memberAuth->setMemberToken($user->token)->tokenRefresh();

            $user = $this->userRepository->update([
                'amazon_user_id' => $userProfile->user_id,
            ], $user->id);

            DB::commit();

            return $user;
        } catch (\Exception $e) {
            DB::rollBack();

            if ($e instanceof \App\HttpCommunication\Exceptions\AuthHttpException) {
                throw new HttpException(Response::HTTP_UNAUTHORIZED, __('validation.amazon_login.refresh_token_expired'), $e);
            }

            throw $e;
        }
    }

    /**
     * アクセストークンを使用してuserを取得
     *
     * @param string $accessToken
     *
     * @return \App\Models\User
     */
    public function findUserByAccessToken(string $accessToken)
    {
        $profile = $this->getAmazonUserProfile($accessToken);

        $user = $this->userRepository->findWhere(['amazon_user_id' => $profile->user_id])->first();

        return $user;
    }

    /**
     * AccessTokenを使ってAmazonのユーザー情報を取得する
     *
     * @param string $accessToken
     *
     * @return \App\Entities\AmazonPay\UserProfile
     */
    private function getAmazonUserProfile(string $accessToken)
    {
        try {
            $response = $this->amazonHttpCommunication->getUserInfo($accessToken);

            $userProfile = new \App\Entities\AmazonPay\UserProfile($response->getBody());

            return $userProfile;
        } catch (GetUserInfoInvalidAccessTokenException $e) {
            throw new HttpException(Response::HTTP_UNAUTHORIZED, __('validation.amazon_login.invalid_token'), $e);
        }
    }
}
