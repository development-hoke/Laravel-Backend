<?php

namespace App\Http\Controllers\Api\V1\Front\OldMember;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Front\OldMember\ForgetAllRequest;
use App\Http\Requests\Api\V1\Front\OldMember\ForgetMailRequest;
use App\Services\Front\OldMemberServiceInterface;

class ForgetController extends Controller
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
     * 新会員移行手続きメールアドレス忘れ
     *
     * @return string[]
     */
    public function forgetMail(ForgetMailRequest $request)
    {
        $data = $this->service->forgetMail($request->all());

        return response()->json([
            'email' => $data['member']['email'],
            'lname' => $data['member']['lname'],
            'fname' => $data['member']['fname'],
            'birthday' => $data['member']['birthday'],
            'tel' => $data['member']['tel'],
        ]);
    }

    /**
     * 新会員移行手続き 登録メールアドレスに届かない場合
     *
     * @return array
     */
    public function forgetSms()
    {
        return [
            'email' => 'test@example.com',
            'tel' => '09000001111',
        ];
    }

    /**
     * 新会員移行手続き 電話番号もメールアドレスもわからない場合
     *
     * @return array
     */
    public function forgetAll(ForgetAllRequest $request)
    {
        $this->service->forgetAll($request->all());

        return response()->json($request->all());
    }
}
