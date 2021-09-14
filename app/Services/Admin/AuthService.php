<?php

namespace App\Services\Admin;

use App\Domain\AdminLogInterface;
use App\Domain\MemberAuthInterface as MemberAuthService;
use App\Domain\Utils\StaffAuthentication;
use App\HttpCommunication\Ymdy\AdminAuthInterface;
use App\HttpCommunication\Ymdy\MemberInterface as MemberHttpCommunication;
use App\Models\Staff;
use App\Repositories\StaffRepository;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AuthService extends Service implements AuthServiceInterface
{
    /**
     * @var StaffRepository
     */
    private $staffRepository;

    /**
     * @var AdminAuthInterface
     */
    private $authHttpCommunication;

    /**
     * @var MemberHttpCommunication
     */
    private $memberHttpCommunication;

    /**
     * @var AdminLogInterface
     */
    private $aminLog;

    /**
     * @var MemberAuthService
     */
    private $memberAuthService;

    /**
     * @param StaffRepository $staffRepository
     * @param AdminAuthInterface $authHttpCommunication
     */
    public function __construct(
        StaffRepository $staffRepository,
        AdminAuthInterface $authHttpCommunication,
        MemberHttpCommunication $memberHttpCommunication,
        AdminLogInterface $aminLog,
        MemberAuthService $memberAuthService
    ) {
        $this->staffRepository = $staffRepository;
        $this->authHttpCommunication = $authHttpCommunication;
        $this->memberHttpCommunication = $memberHttpCommunication;
        $this->aminLog = $aminLog;
        $this->memberAuthService = $memberAuthService;

        if (auth('admin_api')->check()) {
            $user = auth('admin_api')->user();
            $this->authHttpCommunication->setStaffToken($user->token);
            $this->memberHttpCommunication->setStaffToken($user->token);
        }
    }

    /**
     * @param array $credentials
     *
     * @return array
     */
    public function attempt(array $credentials)
    {
        try {
            $credentials['limit_sec'] = StaffAuthentication::getTokenExpiration();

            $response = $this->authHttpCommunication->authPassword($credentials);

            $data = $response->getBody();

            return $data;
        } catch (\App\HttpCommunication\Exceptions\AuthHttpException $e) {
            $body = $e->getResponseBody();
            report($e);
            throw new AuthenticationException(@$body['error']['message']);
        } catch (\App\HttpCommunication\Exceptions\HttpException $e) {
            $body = $e->getResponseBody();
            report($e);
            throw new AuthenticationException(@$body['error']['message']);
        }
    }

    /**
     * @param array $data
     *
     * @return \App\Models\Staff
     */
    public function saveAuthorizedStaff(array $data)
    {
        $attributes = $this->extractFillableAttributes($data);

        $model = $this->staffRepository->safeUpdateOrCreate($attributes, (int) $data['id']);

        return $model;
    }

    /**
     * 認証基幹でトークンをリフレッシュして、DBを更新する。
     *
     * @param Staff $staff
     *
     * @return Staff
     */
    public function refreshAuthToken(Staff $staff)
    {
        try {
            $this->authHttpCommunication->setStaffToken(auth('admin_api')->user()->token);

            $response = $this->authHttpCommunication->authTokenRefresh([
                'limit_sec' => StaffAuthentication::getTokenExpiration(),
            ]);

            $staff = $this->saveAuthorizedStaff($response->getBody());

            return $staff;
        } catch (\App\HttpCommunication\Exceptions\AuthHttpException $e) {
            $body = $e->getResponseBody();
            report($e);
            throw new AuthenticationException(@$body['error']['message']);
        } catch (\App\HttpCommunication\Exceptions\HttpException $e) {
            $body = $e->getResponseBody();
            report($e);
            throw new AuthenticationException(@$body['error']['message']);
        }
    }

    /**
     * @param array $data
     *
     * @return array
     */
    private function extractFillableAttributes(array $data)
    {
        return [
            'code' => $data['code'],
            'name' => $data['name'],
            'token' => $data['token'],
            'role' => \App\Enums\Staff\Role::Privilege, // FIXME: Roleの仕様が確定後修正
            'token_limit' => $data['token_limit'],
        ];
    }

    /**
     * @param Request $request
     * @param Staff $staff
     * @param array|null $options
     *
     * @return \App\Models\AdminLog|null
     */
    public function writeAdminLog(Request $request, Staff $staff, array $options = [])
    {
        return $this->aminLog->write($request, $staff, $options);
    }

    /**
     * 代理ログイン
     *
     * @param array $params
     * @param Staff $params
     *
     * @return \App\Models\User
     */
    public function agentLogin(array $params, Staff $staff)
    {
        try {
            $credentials = [];
            $credentials['effective_seconds'] = StaffAuthentication::getAgentMemberTokenExpiration();
            $credentials['member_id'] = $params['member_id'];
            $credentials['admin_code'] = $staff->code;

            $response = $this->memberHttpCommunication->fetchMemberDetail($params['member_id'])->getBody();

            $member = $response['member'];

            $response = $this->memberHttpCommunication->authAgent($credentials);

            if ($response->getStatusCode() === Response::HTTP_UNAUTHORIZED) {
                throw new AuthenticationException();
            }

            $user = $this->memberAuthService->saveAgentLoggingIn(
                $response->getBody(),
                $member['email']
            );

            $user->member = $member;

            return $user;
        } catch (\App\HttpCommunication\Exceptions\AuthHttpException $e) {
            report($e);
            throw new AuthenticationException();
        }
    }
}
