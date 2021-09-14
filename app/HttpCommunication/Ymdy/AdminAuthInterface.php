<?php

namespace App\HttpCommunication\Ymdy;

use App\HttpCommunication\HttpCommunicationServiceInterface;

interface AdminAuthInterface extends HttpCommunicationServiceInterface
{
    const ENDPONT_INDEX_MODEL_SYSTEM = 'index_model_systems';
    const ENDPONT_INDEX_MODEL_SYSTEM_CATEGORIES = 'index_model_system_categories';
    const ENDPONT_INDEX_MODEL_BELONGINGS = 'index_model_belongings';
    const ENDPONT_AUTH_PASSWORD = 'auth_password';
    const ENDPONT_AUTH_TOKEN_REFRESH = 'auth_token_refresh';
    const ENDPONT_AUTH_TOKEN = 'auth_token';

    // スタッフトークンのヘッダ
    const HEADER_STAFF_TOKEN = 'Staff-Token';

    /**
     * @param string $token
     *
     * @return static
     */
    public function setStaffToken(string $token);

    /**
     * システム一覧取得
     *
     * @return \App\HttpCommunication\Response\ResponseInterface
     */
    public function fetchModelSystems();

    /**
     * システムカテゴリ一覧
     *
     * @return \App\HttpCommunication\Response\ResponseInterface
     */
    public function fetchModelSystemCategories();

    /**
     * 所属一覧取得
     *
     * @return \App\HttpCommunication\Response\ResponseInterface
     */
    public function fetchModelBelongings();

    /**
     * パスワード認証
     *
     * @param array $params
     *
     * @return \App\HttpCommunication\Response\ResponseInterface
     */
    public function authPassword(array $params);

    /**
     * トークンリフレッシュ
     *
     * @param array $params
     *
     * @return \App\HttpCommunication\Response\ResponseInterface
     */
    public function authTokenRefresh(array $params);

    /**
     * トークン認証
     *
     * @param array $params
     *
     * @return \App\HttpCommunication\Response\ResponseInterface
     */
    public function authToken(array $params);
}
