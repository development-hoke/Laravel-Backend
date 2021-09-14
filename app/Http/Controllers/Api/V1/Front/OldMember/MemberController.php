<?php

namespace App\Http\Controllers\Api\V1\Front\OldMember;

use App\Exceptions\FatalException;
use App\Exceptions\InvalidInputException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Front\OldMember\CheckMailRequest;
use App\Http\Requests\Api\V1\Front\OldMember\PinRequest;
use App\Http\Requests\Api\V1\Front\OldMember\SendMailRequest;
use App\HttpCommunication\Exceptions\HttpException;
use App\Services\Front\OldMemberServiceInterface;
use Illuminate\Http\Response;

class MemberController extends Controller
{
    /**
     * @var OldMemberServiceInterface
     */
    private $service;

    /**
     * Create a new controller instance
     *
     * @return void
     */
    public function __construct(OldMemberServiceInterface $service)
    {
        $this->service = $service;
    }

    /**
     * 既存会員引き継ぎPINコード認証
     *
     * @param PinRequest $request
     *
     * @return array
     */
    public function pin(PinRequest $request)
    {
        try {
            $data = $this->service->pin([
                'card_id' => $request->card_id,
                'pin' => $request->pin_code,
                'success_url' => config('app.front_url') . '/register/members/activate' . '?member_id={%member_id%}&member_token={%member_token%}',
                'error_url' => config('app.front_url') . '/register/error' . '?error_code={%error_code%}',
            ]);
        } catch (HttpException $e) {
            $body = $e->getResponse()->getBody();

            if (!isset($body['error']['message'])) {
                throw new FatalException(__('error.failed_to_extract_http_error'));
            }

            switch ($e->getCode()) {
                case Response::HTTP_NOT_FOUND:
                    throw new InvalidInputException($body['error']['message'], null, $e);
                case Response::HTTP_CONFLICT:
                    throw (new InvalidInputException(__('error.member_ymdy_registered_already_done'), null, $e))->setStatusCode($e->getCode());
                default:
                    throw $e;
            }
        }

        return response()->json($data);
    }

    /**
     * 既存会員引き継ぎメールアドレスチェック
     *
     * @param CheckMailRequest $request
     *
     * @return array
     */
    public function checkMail(CheckMailRequest $request)
    {
        try {
            $data = $this->service->checkMail([
                'email' => $request->email,
                'card_id' => $request->card_id,
                'pin' => $request->pin,
                'success_url' => config('app.front_url') . '/register/card/activate' . '?member_id={%member_id%}&member_token={%member_token%}',
                'error_url' => config('app.front_url') . '/register/error' . '?error_code={%error_code%}',
            ]);
        } catch (HttpException $e) {
            if ($e->getCode() == Response::HTTP_CONFLICT) {
                throw new InvalidInputException(error_format('error.member_registered_already_done'), null, $e);
            }

            throw $e;
        }

        return response()->json($data);
    }

    /**
     * 既存会員引き継ぎ 不足情報登録
     *
     * @return array
     */
    public function store()
    {
        return [
            'id' => 10,
            'lname' => '田中',
            'fname' => '太郎',
            'lkana' => 'たなか',
            'fkana' => 'たろう',
            'birthday' => '1990-09-01',
            'gender' => 1,
            'zip1' => '103',
            'zip2' => '0004',
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
     * 新会員移行手続き
     *
     * @return string[]
     */
    public function sendEmail(SendMailRequest $request)
    {
        try {
            $this->service->checkMail([
                'email' => $request->email,
                'success_url' => config('app.front_url') . '/transfer/activate' . '?member_id={%member_id%}&member_token={%member_token%}',
                'error_url' => config('app.front_url') . '/register/error' . '?error_code={%error_code%}',
            ]);
        } catch (HttpException $e) {
            switch ($e->getCode()) {
                case Response::HTTP_NOT_FOUND:
                    throw new InvalidInputException(error_format('error.member_not_found'), null, $e);
                case Response::HTTP_CONFLICT:
                    throw (new InvalidInputException(error_format('error.member_transferred_already_done'), null, $e))->setStatusCode($e->getCode());
                default:
                    throw $e;
            }
        }

        return [
            'email' => $request->email,
        ];
    }

    /**
     * 新会員移行手続き認証
     *
     * @return array
     */
    public function auth()
    {
        return [
            'id' => 10,
            'lname' => '田中',
            'fname' => '太郎',
            'lkana' => 'たなか',
            'fkana' => 'たろう',
            'birthday' => '1990-09-01',
            'gender' => 1,
            'zip1' => '103',
            'zip2' => '0004',
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
}
