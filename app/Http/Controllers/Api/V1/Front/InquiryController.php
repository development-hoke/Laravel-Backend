<?php

namespace App\Http\Controllers\Api\V1\Front;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class InquiryController extends Controller
{
    /**
     * お問い合わせ
     *
     * @param Request $request
     *
     * @return string[]
     */
    public function inquiry(Request $request)
    {
        return [
            'content' => '商品について',
            'body' => 'お問い合わせ内容サンプル',
            'name' => '田中 太朗',
            'name_kana' => 'たなか たろう',
            'email' => 'test@example.com',
            'confirm_email' => 'test@example.com',
            'phone_number' => '09012341234',
        ];
    }
}
