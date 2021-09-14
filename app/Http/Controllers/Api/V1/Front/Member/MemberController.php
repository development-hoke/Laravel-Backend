<?php

namespace App\Http\Controllers\Api\V1\Front\Member;

use App\Exceptions\FatalException;
use App\Exceptions\InvalidInputException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Front\Auth\ChangePasswordRequest;
use App\Http\Requests\Api\V1\Front\ChangeEmailRequest;
use App\Http\Requests\Api\V1\Front\Member\UpdateMailDmRequest;
use App\Http\Requests\Api\V1\Front\Member\UpdateRequest;
use App\Http\Requests\Api\V1\Front\PasswordResetDecisionRequest;
use App\Http\Requests\Api\V1\Front\PasswordResetRequest;
use App\Http\Resources\User as UserResource;
use App\HttpCommunication\Exceptions\AuthHttpException;
use App\HttpCommunication\Exceptions\HttpException;
use App\HttpCommunication\Exceptions\UnprocessableEntityHttpException;
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
     * 会員新規登録(仮登録)
     *
     * @param Request $request
     *
     * @return array
     */
    public function storeTemp(Request $request)
    {
        try {
            $data = $this->service->storeTemp([
                'email' => $request->email,
                'success_url' => config('app.front_url') . '/register/members/activate' . '?member_id={%member_id%}&member_token={%member_token%}',
                'error_url' => config('app.front_url') . '/register/error' . '?error_code={%error_code%}',
            ]);
        } catch (HttpException $e) {
            if ($e->getCode() === Response::HTTP_CONFLICT) {
                throw new InvalidInputException(error_format('error.email_already_in_use'));
            }

            throw new AuthenticationException(error_format('error.password_unauthenticated', [], ['id' => 'メールアドレス']));
        }

        return response()->json([
            'id' => $data['member']['id'],
            'card_id' => $data['member']['email'],
        ]);
    }

    /**
     * 会員新規登録(本登録)
     *
     * @param Request $request
     * @param $memberId
     *
     * @return array
     */
    public function store(Request $request, $memberId)
    {
        try {
            $data = $this->service->update($memberId, $request->except(['member_token']), $request->get('member_token'));
        } catch (UnprocessableEntityHttpException $e) {
            $body = $e->getResponse()->getBody();

            if (!isset($body['error']['errors'][0]['fields'])) {
                throw new FatalException(__('error.failed_to_extract_http_error'));
            }

            throw new InvalidInputException($body['error']['errors'][0]['fields'], null, $e);
        }

        return response()->json(new UserResource($data));
    }

    /**
     * 会員情報取得
     *
     * @param $memberId
     *
     * @return array
     */
    public function get($memberId)
    {
        $data = $this->service->get($memberId, request()->get('member_token'));

        return response()->json(new UserResource($data));
    }

    /**
     * 会員情報変更
     *
     * @param UpdateRequest $request
     * @param int $memberId
     *
     * @return array
     */
    public function update(UpdateRequest $request, int $memberId)
    {
        $data = $this->service->update($memberId, $request->validated());

        return response()->json(new UserResource($data));
    }

    /**
     * メルマガとDM変更
     *
     * @param UpdateMailDmRequest $request
     * @param int $memberId
     *
     * @return array
     */
    public function updateMailDm(UpdateMailDmRequest $request, int $memberId)
    {
        $data = $this->service->update($memberId, $request->validated());

        return response()->json(new UserResource($data));
    }

    /**
     * メールアドレス変更
     *
     * @param ChangeEmailRequest $request
     * @param $memberId
     *
     * @return array
     */
    public function changeEmail(ChangeEmailRequest $request, $memberId)
    {
        try {
            $params = $request->validated();

            $response = $this->service->changeEmail($memberId, [
                'email' => $params['email'],
                'success_url' => config('app.front_url') . '/mypage/change/email/complete',
                'error_url' => config('app.front_url') . '/mypage/change/email/send',
            ]);
        } catch (HttpException $e) {
            if ($e->getCode() === Response::HTTP_CONFLICT) {
                throw new InvalidInputException(error_format('error.email_already_in_use'));
            }

            throw $e;
        }

        return [
            'id' => $memberId,
            'email' => $response['member']['temp_email'],
        ];
    }

    /**
     * メールアドレス変更(確定)
     *
     * @param Request $request
     * @param $memberId
     *
     * @return array
     */
    public function changeEmailDecision(Request $request, $memberId)
    {
        return [
            'id' => $memberId,
            'lname' => '田中',
            'fname' => '太郎',
            'lkana' => 'たなか',
            'fkana' => 'たろう',
            'birthday' => '1990-09-01',
            'gender' => 1,
            'zip' => '1030004',
            'pref_id' => 13,
            'city' => '中央区',
            'town' => '東日本橋',
            'address' => '1-6-9',
            'building' => 'グリーンパーク東日本橋２ ２０１',
            'tel' => '09000111111',
            'email' => 'test@example.com',
            'mail_dm' => true,
            'post_dm' => true,
            'card_id' => '123456789',
        ];
    }

    /**
     * パスワード変更
     *
     * @param ChangePasswordRequest $request
     * @param $memberId
     *
     * @return array
     */
    public function changePassword(ChangePasswordRequest $request, $memberId)
    {
        try {
            $data = $this->service->changePassword($memberId, $request->all());
        } catch (AuthHttpException $e) {
            throw new AuthenticationException(error_format('error.wrong_password'));
        }

        return response()->json(new UserResource($data));
    }

    /**
     * パスワード変更(確定)
     *
     * @param Request $request
     * @param $memberId
     *
     * @return array
     */
    public function changePasswordDecision(Request $request, $memberId)
    {
        return [
            'id' => $memberId,
            'lname' => '田中',
            'fname' => '太郎',
            'lkana' => 'たなか',
            'fkana' => 'たろう',
            'birthday' => '1990-09-01',
            'gender' => 1,
            'zip' => '1030004',
            'pref_id' => 13,
            'city' => '中央区',
            'town' => '東日本橋',
            'address' => '1-6-9',
            'building' => 'グリーンパーク東日本橋２ ２０１',
            'tel' => '09000111111',
            'email' => 'test@example.com',
            'mail_dm' => true,
            'post_dm' => true,
            'card_id' => '123456789',
        ];
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
                'error_url' => config('app.front_url') . '/password/error' . '?error_code={%error_code%}',
            ] + $request->validated());
        } catch (HttpException $e) {
            throw new InvalidInputException(error_format('error.wrong_email_birthday'));
        }

        return response()->json($data);
    }

    /**
     * パスワードリマインダー(確定)
     *
     * @param Request $request
     *
     * @return array
     */
    public function resetPasswordDecision(PasswordResetDecisionRequest $request)
    {
        $data = $this->service->resetPasswordDecision(
            $request->member_id,
            $request->except('member_id')
        );

        return response()->json($data);
    }
}
