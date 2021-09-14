<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Api\V1\Admin\Controller as ApiAdminController;
use App\Http\Requests\Api\V1\Admin\Auth\AgentLoginRequest;
use App\Http\Requests\Api\V1\Admin\Auth\LoginRequest;
use App\Http\Resources\Staff as StaffResource;
use App\Services\Admin\AuthServiceInterface;
use Illuminate\Auth\AuthenticationException;
use Tymon\JWTAuth\Exceptions\JWTException;

class AuthController extends ApiAdminController
{
    /**
     * @var AuthServiceInterface
     */
    private $service;

    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct(AuthServiceInterface $service)
    {
        // 会員を特定する必要があるので、middlewareでは実行しない
        $this->middleware('admin_log')->except('agentLogin');
        $this->service = $service;
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(LoginRequest $request)
    {
        $params = $request->validated();

        $data = $this->service->attempt($params);

        $staff = $this->service->saveAuthorizedStaff($data);

        // 認証サーバーとリフレッシュを同期させるため、有効期限を明示的に指定する。
        $auth = auth('admin_api')->setTTL(config('jwt.admin_ttl'));

        if (!$token = $auth->login($staff)) {
            throw new AuthenticationException();
        }

        $this->service->writeAdminLog($request, $staff);

        return $this->respondWithToken($token);
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        $staff = auth('admin_api')->user();

        return new StaffResource($staff);
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth('admin_api')->logout();

        return response()->json(form_response_array([
            'message' => 'Successfully logged out',
        ]));
    }

    /**
     * トークンのリフレッシュをする
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        try {
            $auth = auth('admin_api');

            // (1) JWTトークンのリフレッシュ
            $token = $auth->refresh();
            $auth->setToken($token);

            $staff = $auth->user();

            if (!$staff) {
                throw new AuthenticationException(error_format('error.unauthenticated'));
            }

            // (2) 認証基幹のトークンリフレッシュ
            $this->service->refreshAuthToken($staff);

            return $this->respondWithToken($token);
        } catch (JWTException $e) {
            throw new AuthenticationException(error_format('error.unauthenticated'));
        }
    }

    /**
     * Get the token array structure.
     *
     * @param string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json(form_response_array([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('admin_api')->factory()->getTTL() * 60,
        ]));
    }

    /**
     * @param AgentLoginRequest $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function agentLogin(AgentLoginRequest $request)
    {
        try {
            $params = $request->validated();

            $staff = auth('admin_api')->user();

            $user = $this->service->agentLogin($params, $staff);

            // フロントエンド側のauthでJWT認証する
            if (!$token = auth('api')->login($user)) {
                throw new AuthenticationException();
            }

            $this->service->writeAdminLog($request, $staff, [
                'action_text' => "(会員ID: {$user->code})",
            ]);

            return response()->json([
                'member_token' => $token,
                'token_type' => 'bearer',
                'expires_in' => auth('api')->factory()->getTTL() * 60,
            ]);
        } catch (JWTException $e) {
            throw new AuthenticationException(error_format('error.unauthenticated'));
        }
    }
}
