<?php

namespace App\Services\Front;

use App\Domain\MemberAuthInterface as MemberAuthService;
use App\Domain\Utils\MemberAuthentication;
use App\HttpCommunication\Ymdy\MemberInterface;
use App\Models\User;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Response;

class AuthService extends Service implements AuthServiceInterface
{
    /**
     * @var MemberInterface
     */
    private $httpCommunication;

    /**
     * @var MemberAuthService
     */
    private $memberAuthService;

    /**
     * @param MemberInterface $httpCommunication
     * @param MemberAuthService $memberAuthService
     */
    public function __construct(
        MemberInterface $httpCommunication,
        MemberAuthService $memberAuthService
    ) {
        $this->httpCommunication = $httpCommunication;
        $this->memberAuthService = $memberAuthService;

        if (auth('api')->check()) {
            $this->httpCommunication->setMemberTokenHeader(auth('api')->user()->token);
        }
    }

    /**
     * @param array $credentials
     *
     * @return array
     */
    public function attempt(array $credentials)
    {
        $credentials['effective_seconds'] = MemberAuthentication::getTokenExpiration();

        $response = $this->httpCommunication->authPassword([
            'email' => $credentials['email'],
            'password' => $credentials['password'],
            'effective_seconds' => $credentials['keep_login'] ? 2629800 : MemberAuthentication::getTokenExpiration(),
        ]);

        if ($response->getStatusCode() === Response::HTTP_UNAUTHORIZED) {
            throw new AuthenticationException();
        }

        $data = $response->getBody();

        return $data;
    }

    /**
     * @param array $data
     * @param string $email
     *
     * @return \App\Models\User
     */
    public function saveAuthorizedUser(array $data, string $email)
    {
        return $this->memberAuthService->saveMemberTokenAsUser($data, $email);
    }

    /**
     * @param User $user
     *
     * @return array
     */
    public function getMemberDetail(User $user)
    {
        $response = $this->httpCommunication->fetchMemberDetail($user->id);

        if ($response->getStatusCode() === Response::HTTP_UNAUTHORIZED) {
            throw new AuthenticationException();
        }

        $data = $response->getBody();

        $data['member']['is_amazon_linked'] = !empty($user->amazon_user_id);

        return $data;
    }

    /**
     * @param User $user
     *
     * @return bool
     */
    public function validateUser(User $user)
    {
        // 定期バッチで情報を更新する予定なので、ここではトークンの有効期限のみ検証する。
        // 必要に応じて、ここでもトークンログイン・リフレッシュを入れる。
        return MemberAuthentication::hasRemainingTime($user);
    }
}
