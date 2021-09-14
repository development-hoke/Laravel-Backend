<?php

namespace App\Http\Controllers\Api\V1\Front\Member;

use App\Exceptions\FatalException;
use App\Exceptions\InvalidInputException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Front\Auth\LoginRequest;
use App\Http\Resources\User as UserResource;
use App\HttpCommunication\Exceptions\HttpException;
use App\Services\Front\AuthServiceInterface;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Exceptions\JWTException;

class AuthController extends Controller
{
    /**
     * @var AuthServiceInterface
     */
    private $service;

    protected $redirectTo = '/login';

    /**
     * Create a new controller instance
     *
     * @return void
     */
    public function __construct(AuthServiceInterface $service)
    {
        $this->middleware('auth:api')->only(['me']);
        $this->service = $service;
    }

    /**
     * 会員ログイン(パスワード)
     *
     * @param LoginRequest $request
     *
     * @return string[]
     */
    public function authPassword(LoginRequest $request)
    {
        try {
            $data = $this->service->attempt($request->all());
            $user = $this->service->saveAuthorizedUser($data, $request->email);

            if (!$token = auth('api')->login($user, $request->keep_login)) {
                throw new AuthenticationException();
            }
        } catch (HttpException $e) {
            $body = $e->getResponse()->getBody();

            if (!isset($body['error']['message'])) {
                throw new FatalException(__('error.failed_to_extract_http_error'));
            }

            throw new InvalidInputException($body['error']['message'], $body['error']['code'], $e);
        }

        return $this->respondWithToken($token);
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me(Request $request)
    {
        $user = auth('api')->user();

        $data = $this->service->getMemberDetail($user);

        return response()->json(new UserResource($data));
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth('api')->logout();

        return response()->json(form_response_array([
            'message' => 'Successfully logged out',
        ]));
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        try {
            $auth = auth('api');

            $token = $auth->refresh();

            $auth->setToken($token);

            $user = $auth->user();

            if (!$this->service->validateStaff($user)) {
                throw new AuthenticationException(error_format('error.unauthenticated'));
            }

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
        return response()->json([
            'member_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60,
        ]);
    }
}
