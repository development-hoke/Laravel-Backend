<?php

namespace App\HttpCommunication\Ymdy\Concrete;

use App\HttpCommunication\Ymdy\AdminAuthInterface;
use App\HttpCommunication\Ymdy\HttpCommunicationService;

/*
 * 基幹認証
 * 管理画面のログイン情報や、どのスタッフがどの画面を操作出来るかなどの情報を取得するシステムです。
 */
class AdminAuth extends HttpCommunicationService implements AdminAuthInterface
{
    /**
     * トークンが必要なエンドポイントの設定
     *
     * @var array
     */
    protected $needsToken = [
        self::ENDPONT_AUTH_TOKEN_REFRESH,
        self::ENDPONT_AUTH_TOKEN,
    ];

    /**
     * configを取得するためのキー
     *
     * @return string
     */
    protected function getConfigKey(): string
    {
        return 'ymdy_admin_auth';
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
     * システム一覧取得
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function fetchModelSystems()
    {
        return $this->request(self::ENDPONT_INDEX_MODEL_SYSTEM);
    }

    /**
     * システムカテゴリ一覧
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function fetchModelSystemCategories()
    {
        return $this->request(self::ENDPONT_INDEX_MODEL_SYSTEM_CATEGORIES);
    }

    /**
     * 所属一覧取得
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function fetchModelBelongings()
    {
        return $this->request(self::ENDPONT_INDEX_MODEL_BELONGINGS);
    }

    /**
     * パスワード認証
     *
     * @param array $params
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function authPassword(array $params)
    {
        return $this->request(self::ENDPONT_AUTH_PASSWORD, [], $params);
    }

    /**
     * トークンリフレッシュ
     *
     * @param array $params
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function authTokenRefresh(array $params)
    {
        return $this->request(self::ENDPONT_AUTH_TOKEN_REFRESH, [], $params);
    }

    /**
     * トークン認証
     *
     * @param array $params
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function authToken(array $params)
    {
        return $this->request(self::ENDPONT_AUTH_TOKEN, [], $params);
    }
}
