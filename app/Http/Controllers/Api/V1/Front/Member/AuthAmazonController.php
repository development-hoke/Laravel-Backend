<?php

namespace App\Http\Controllers\Api\V1\Front\Member;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Front\AuthAmazon\LinkRequest;
use App\Http\Requests\Api\V1\Front\AuthAmazon\LoginRequest;
use App\Http\Requests\Api\V1\Front\AuthAmazon\MeRequest;
use App\Http\Resources\User as UserResource;
use App\Services\Front\AmazonLoginServiceInterface;
use App\Services\Front\AuthServiceInterface;
use Illuminate\Auth\AuthenticationException;

class AuthAmazonController extends Controller
{
    /**
     * @var AuthServiceInterface
     */
    private $authService;

    /**
     * @var AmazonLoginServiceInterface
     */
    private $amazonLoginService;

    /**
     * Create a new controller instance
     *
     * @return void
     */
    public function __construct(
        AuthServiceInterface $authService,
        AmazonLoginServiceInterface $amazonLoginService
    ) {
        $this->middleware('auth:api')->only(['link', 'me']);
        $this->authService = $authService;
        $this->amazonLoginService = $amazonLoginService;
    }

    /**
     * 既存会員にAmazonアカウントを紐付ける
     *
     * @param LinkRequest $request
     *
     * @return UserResource
     */
    public function link(LinkRequest $request)
    {
        $params = $request->validated();

        $user = $this->amazonLoginService->linkAccount($params['access_token']);

        $data = $this->authService->getMemberDetail($user);

        return response()->json(new UserResource($data));
    }

    /**
     * 会員ログイン(amazon)
     *
     * @param LoginRequest $request
     *
     * @return array
     */
    public function auth(LoginRequest $request)
    {
        $params = $request->validated();

        $user = $this->amazonLoginService->auth($params['access_token']);

        if (!$token = auth('api')->login($user)) {
            throw new AuthenticationException();
        }

        return $this->respondWithToken($token);
    }

    /**
     * Amazonアクセストークンを利用してユーザー情報を取得
     *
     * @param MeRequest $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me(MeRequest $request)
    {
        $params = $request->validated();

        $user = $this->amazonLoginService->findUserByAccessToken($params['access_token']);

        $data = $this->authService->getMemberDetail($user);

        return response()->json(new UserResource($data));
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
        return response()->json([
            'member_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60,
        ]);
    }
}
