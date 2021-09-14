<?php

namespace App\Services\Front;

use App\HttpCommunication\Ymdy\OldMemberInterface;
use App\Repositories\UserRepository;
use App\Services\Service;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Response;

class OldMemberService extends Service implements OldMemberServiceInterface
{
    /**
     * @var UserRepository
     */
    private $repository;

    /**
     * @var OldMemberInterface
     */
    private $httpCommunication;

    public function __construct(
        UserRepository $repository,
        OldMemberInterface $httpCommunication
    ) {
        $this->repository = $repository;
        $this->httpCommunication = $httpCommunication;
    }

    /**
     * @param array $params
     *
     * @return array
     */
    public function pin(array $params)
    {
        $response = $this->httpCommunication->pin($params);

        if ($response->getStatusCode() === Response::HTTP_UNAUTHORIZED) {
            throw new AuthenticationException();
        }

        return $response->getBody();
    }

    /**
     * 旧会員メールアドレス認証メール送信
     *
     * @param array $params
     *
     * @return array
     */
    public function checkMail(array $params)
    {
        $response = $this->httpCommunication->checkMail($params);

        if ($response->getStatusCode() === Response::HTTP_UNAUTHORIZED) {
            throw new AuthenticationException();
        }

        return $response->getBody();
    }

    /**
     * 旧会員カスタマーサービス連絡
     *
     * @param array $params
     *
     * @return array
     */
    public function forgetAll(array $params)
    {
        $response = $this->httpCommunication->contact($params);

        if ($response->getStatusCode() === Response::HTTP_UNAUTHORIZED) {
            throw new AuthenticationException();
        }

        return $response->getBody();
    }

    /**
     * 新会員移行手続きメールアドレス忘れ
     *
     * @param array $params
     *
     * @return array
     */
    public function forgetMail(array $params)
    {
        $response = $this->httpCommunication->forgetMail($params);

        if ($response->getStatusCode() === Response::HTTP_UNAUTHORIZED) {
            throw new AuthenticationException();
        }

        return $response->getBody();
    }
}
