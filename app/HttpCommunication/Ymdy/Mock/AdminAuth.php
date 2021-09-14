<?php

namespace App\HttpCommunication\Ymdy\Mock;

use App\HttpCommunication\Response\Mock\Response;
use App\HttpCommunication\Ymdy\AdminAuthInterface;
use App\HttpCommunication\Ymdy\HttpCommunicationService;

/*
 * 基幹認証
 * 管理画面のログイン情報や、どのスタッフがどの画面を操作出来るかなどの情報を取得するシステムです。
 */
class AdminAuth extends HttpCommunicationService implements AdminAuthInterface
{
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
     * @param string $token
     *
     * @return static
     */
    public function setStaffToken(string $token)
    {
        return $this;
    }

    /**
     * システム一覧取得
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function fetchModelSystems()
    {
        return new Response();
    }

    /**
     * システムカテゴリ一覧
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function fetchModelSystemCategories()
    {
        return new Response();
    }

    /**
     * 所属一覧取得
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function fetchModelBelongings()
    {
        return new Response();
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
        return new Response(require __DIR__.'/fixtures/staff.php');
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
        return new Response(require __DIR__.'/fixtures/staff.php');
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
        return new Response();
    }
}
