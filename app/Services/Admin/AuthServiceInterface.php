<?php

namespace App\Services\Admin;

use App\Models\Staff;
use Illuminate\Http\Request;

interface AuthServiceInterface
{
    /**
     * @param array $credentials
     *
     * @return array
     */
    public function attempt(array $credentials);

    /**
     * @param array $data
     *
     * @return \App\Models\Staff
     */
    public function saveAuthorizedStaff(array $data);

    /**
     * 認証基幹でトークンをリフレッシュして、DBを更新する。
     *
     * @param Staff $staff
     *
     * @return Staff
     */
    public function refreshAuthToken(Staff $staff);

    /**
     * @param Request $request
     * @param Staff $staff
     * @param array|null $options
     *
     * @return \App\Models\AdminLog|null
     */
    public function writeAdminLog(Request $request, Staff $staff, array $options = []);

    /**
     * 代理ログイン
     *
     * @param array $params
     * @param Staff $params
     *
     * @return \App\Models\User
     */
    public function agentLogin(array $params, Staff $staff);
}
