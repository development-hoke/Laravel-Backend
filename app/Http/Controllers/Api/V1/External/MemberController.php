<?php

namespace App\Http\Controllers\Api\V1\External;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Front\PasswordResetRequest;
use App\HttpCommunication\Exceptions\AuthHttpException;
use App\Services\Front\MemberServiceInterface;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class MemberController extends Controller
{
    /**
     * @var MemberServiceInterface
     */
    private $service;

    /**
     * Create a new controller instance
     *
     * @return void
     */
    public function __construct(MemberServiceInterface $service)
    {
        $this->service = $service;
    }

    /**
     * パスワードリマインダー
     *
     * @param Request $request
     *
     * @return array
     */
    public function resetPassword(PasswordResetRequest $request)
    {
        try {
            $data = $this->service->sendPasswordResetRequest([
                'success_url' => config('app.front_url') . '/password/reset' . '?member_id={%member_id%}&member_token={%member_token%}',
                'error_url' => config('app.front_url') . '/register/error' . '?error_code={%error_code%}',
            ] + $request->validated());
        } catch (AuthHttpException $e) {
            throw new AuthenticationException(error_format('error.wrong_email_birthday'));
        }

        return response()->json($data);
    }
}
