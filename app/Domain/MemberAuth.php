<?php

namespace App\Domain;

use App\HttpCommunication\Ymdy\Concrete\Member as MemberHttpCommunication;
use App\Repositories\UserRepository;

class MemberAuth implements MemberAuthInterface
{
    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @var MemberHttpCommunication
     */
    private $memberHttpCommunication;

    /**
     * @param UserRepository $userRepository
     */
    public function __construct(
        UserRepository $userRepository,
        MemberHttpCommunication $memberHttpCommunication
    ) {
        $this->userRepository = $userRepository;
        $this->memberHttpCommunication = $memberHttpCommunication;
    }

    /**
     * メンバートークンの設定
     *
     * @param string $token
     *
     * @return static
     */
    public function setMemberToken(string $token)
    {
        $this->memberHttpCommunication->setMemberTokenHeader($token);

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
        $this->memberHttpCommunication->setStaffToken($token);

        return $this;
    }

    /**
     * @param array $data
     * @param string $email
     *
     * @return \App\Models\User
     */
    public function saveMemberTokenAsUser(array $data, string $email)
    {
        $model = $this->userRepository->safeUpdateOrCreate(
            $this->extractFillableAttributes($data),
            (int) $data['member_token']['member_id']
        );

        return $model;
    }

    /**
     * @param array $data
     * @param string $email
     *
     * @return \App\Models\User
     */
    public function saveAgentLoggingIn(array $data, string $email)
    {
        $attributes = $this->extractFillableAttributes($data);

        $user = $this->userRepository->findWhere(['id' => $data['member_token']['member_id']])->first();

        if (empty($user)) {
            $user = $this->userRepository->makeModel();
            $user->id = $data['member_token']['member_id'];
            $user->email = $email;
            $user->code = $attributes['code'];
        }

        $user->token = $attributes['token'];
        $user->token_limit = $attributes['token_limit'];
        $user->save();

        return $user;
    }

    /**
     * @param array $data
     *
     * @return array
     */
    private function extractFillableAttributes(array $data)
    {
        return [
            'token' => $data['member_token']['token'],
            'token_limit' => $data['member_token']['limit'],
            'code' => $data['member_token']['member_id'],
        ];
    }

    /**
     * トークンをリフレッシュして新しいトークンをusersテーブルに保存する
     *
     * @return \App\Models\User
     */
    public function tokenRefresh()
    {
        $response = $this->memberHttpCommunication->tokenRefresh(['effective_seconds' => \App\Domain\Utils\MemberAuthentication::getTokenExpiration()]);

        $data = $response->getBody();

        $memberToken = $data['member_token'] ?? $data['member_toke']; // タイポがあるのでどちらでも対応できるようにしておく。

        $user = $this->userRepository->update([
            'token' => $memberToken['token'],
            'token_limit' => $memberToken['limit'],
        ], $memberToken['member_id']);

        return $user;
    }
}
