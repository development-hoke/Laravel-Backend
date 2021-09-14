<?php

return [
    'use_mock' => [
        'ymdy' => (int) env('YMDY_HTTP_COMMUNICATION_USE_MOCK', false),
        'ymdy_keiei' => (int) env('YMDY_KEIEI_HTTP_COMMUNICATION_USE_MOCK', false),
        'shohin' => (int) env('SHOHIN_HTTP_COMMUNICATION_USE_MOCK', false),
        'send_grid' => (int) env('SEND_GRID_HTTP_COMMUNICATION_USE_MOCK', false),
        'f_regi' => (int) env('F_REGI_HTTP_COMMUNICATION_USE_MOCK', false),
        'np' => (int) env('NP_HTTP_COMMUNICATION_USE_MOCK', false),
        'staff_start' => (int) env('STAFF_START_HTTP_COMMUNICATION_USE_MOCK', false),
        'amazon_pay' => (int) env('AMAZON_PAY_HTTP_COMMUNICATION_USE_MOCK', false),
    ],

    'save_log' => [
        'ymdy_member_system_api' => (int) env('YMDY_MEMBER_SYSTEM_API', false),
    ],

    /*
     * SendGrid (システムメール送信全般で使用するWebAPI)
     */
    'send_grid' => [
        'api_key' => env('SENDGRID_API_KEY'),
        'from' => [
            'address' => env('MAIL_FROM_ADDRESS', 'hello@example.com'),
            'name' => env('MAIL_FROM_NAME', 'Example'),
        ],
    ],

    /*
     * F-REGI
     */
    'f_regi' => [
        'shop_id' => '20563',
        'api_key' => 'efc92ffcb0dd007b',
        'host' => env('F_REGI_BASE_URL'),
        'endpoint' => [
            // オーソリ処理
            'auth' => ['get', 'authm.cgi'],
            // 売り上げ処理
            'sale' => ['get', 'sale.cgi'],
            // オーソリキャンセル
            'auth_cancel' => ['get', 'authcancel.cgi'],
            // 承認金額変更
            'auth_change' => ['get', 'authchange.cgi'],
            // 売上取消
            'sale_cancel' => ['get', 'salecancel.cgi'],
            // 売上金額変更
            'sale_change' => ['get', 'salechange.cgi'],
            // 顧客情報取得
            'fetch_customer_info' => ['get', 'customerinfo.cgi'],
            // 顧客ID削除
            'leave_customer' => ['get', 'leavecustomer.cgi'],
        ],
    ],

    /*
     * NP後払い
     */
    'np' => [
        'shop_code' => env('NP_SHOPCODE'),
        'sp_code' => env('NP_SPCODE'),
        'terminal_id' => env('NP_TERMINAL_ID'),
        'site_name' => env('NP_SITE_NAME'),
        'site_url' => env('NP_SITE_URL'),
        'host' => env('NP_BASE_URL'),
        'endpoint' => [
            // 取引登録
            'transactions' => ['post', 'transactions'],
            // 取引変更
            'update_transaction' => ['patch', 'transactions/update'],
            // 出荷報告
            'shipments' => ['post', 'shipments'],
            // 一部返品取引再登録
            'transaction_reregister' => ['post', 'transactions/reregister'],
            // 取引キャンセル
            'transaction_cancel' => ['patch', 'transactions/cancel'],
        ],
    ],

    /*
     * スタッフスタート
     */
    'staff_start' => [
        'host' => env('STAFF_START_HOST'),
        'merchant_id' => env('STAFF_START_MERCHANT_ID'),
        'pagination' => [
            'default_per_page' => 30,
        ],
        'endpoint' => [
            // コーディネート一覧
            'index_coordinates' => ['get', '/v1/coordinate'],
            // コーディネート詳細
            'show_coordinate_detail' => ['get', '/v1/coordinate/detail'],
        ],
    ],

    /*
     * 基幹認証
     * 管理画面のログイン情報や、どのスタッフがどの画面を操作出来るかなどの情報を取得するシステムです。
     */
    'ymdy_admin_auth' => [
        'headers' => [
            'System-Token' => env('YMDY_ADMIN_AUTH_SYSTEM_TOKEN'),
        ],
        'staff_token_expiration' => 86400,
        'host' => env('YMDY_ADMIN_AUTH_HOST'),
        'prefix' => 'api/v1',
        'endpoint' => [
            // システム一覧
            'index_model_systems' => ['get', 'model/systems'],
            // システムカテゴリ一覧
            'index_model_system_categories' => ['get', 'model/system_categories'],
            // 所属一覧
            'index_model_belongings' => ['get', 'model/belongings'],
            // パスワード認証
            'auth_password' => ['post', 'auth/password'],
            // トークンリフレッシュ
            'auth_token_refresh' => ['post', 'auth/token_refresh'],
            // トークン認証
            'auth_token' => ['post', 'auth/token'],
        ],
    ],

    /*
     * 経営基幹
     */
    'ymdy_keiei' => [
        'headers' => [
            'System-Token' => env('YMDY_KEIEI_SYSTEM_TOKEN'),
        ],
        'staff_token_expiration' => 86400,
        'host' => env('YMDY_KEIEI_HOST'),
        'prefix' => 'api/v1',
        'endpoint' => [
            // 色マスタ一覧取得
            'fetch_colors' => ['get', 'colors'],
            // 大事業部マスタ一覧取得
            'fetch_division_groups' => ['get', 'division_groups'],
            // 事業部マスタ一覧取得
            'fetch_divisions' => ['get', 'divisions'],
            // 部門マスタ一覧取得
            'fetch_section_groups' => ['get', 'section_groups'],
            // 部門マスタ一覧取得
            'fetch_sections' => ['get', 'sections'],
            // 店舗マスタ一覧取得
            'fetch_stores' => ['get', 'shops'],
            // 季節グループマスタ一覧取得
            'fetch_season_groups' => ['get', 'season_groups'],
            // 季節マスタ一覧取得
            'fetch_seasons' => ['get', 'seasons'],
            // サイズマスタ一覧取得
            'fetch_sizes' => ['get', 'sizes'],
            // 取引先マスタ一覧取得
            'fetch_counter_parties' => ['get', 'counter_parties'],
        ],
    ],

    /*
     * 会員ポイントシステム
     */
    'ymdy_member' => [
        'headers' => [
            'System-Token' => env('YMDY_MEMBER_SYSTEM_TOKEN'),
        ],
        'member_token_expiration' => 7776000, // 90日間
        // 代理ログイン時のトークン期限 (1時間)
        'agent_member_token_expiration' => 3600,
        'host' => env('YMDY_MEMBER_HOST'),
        'prefix' => 'api/v1',
        'endpoint' => [
            // パスワード認証
            'auth_password' => ['post', 'member/auth/password'],
            // 会員仮登録
            'store_temp' => ['post', 'member/store/temp'],
            // 会員Amazon登録
            'store_amazon' => ['post', 'member/store/amazon'],
            // 会員本登録・更新
            'update_member' => ['put', 'member/:member_id'],
            // 会員amazonアカウント紐付け
            'link_amazon' => ['post', 'member/:member_id/link/amazon'],
            // トークンリフレッシュ
            'token_refresh' => ['post', 'member/token/refresh'],
            // トークン破棄
            'token_expire' => ['delete', 'member/token'],
            // メールアドレス変更
            'change_email' => ['post', 'member/:member_id/change_mail/send'],
            // パスワード変更
            'change_password' => ['put', 'member/:member_id/password'],
            // 会員パスワード再設定依頼
            'reset_password' => ['post', 'member/reset_password/send'],
            // 会員パスワード再設定
            'reset_password_decision' => ['post', 'member/:member_id/reset_password'],
            // 会員検索
            'index_member' => ['get', 'member'],
            // 会員詳細
            'show_member' => ['get', 'member/:member_id'],
            // 会員発行可能クーポン一覧取得
            'index_member_coupon' => ['get', 'member/:member_id/coupon'],
            // 会員クーポン発行
            'issue_member_coupon' => ['post', 'member/:member_id/coupon/:coupon_id'],
            // 会員利用可能クーポン一覧取得
            'index_member_available_coupon' => ['get', 'member/:member_id/available_coupon'],
            // 会員利用可能クーポン検索
            'search_member_available_coupon' => ['post', 'member/:member_id/available_coupon/search'],
            // 会員クーポン利用可能判定
            'check_member_available_coupon' => ['post', 'member/:member_id/available_coupon/check'],
            // 会員クーポン利用
            'use_member_available_coupon' => ['post', 'member/:member_id/available_coupon/:coupon_id'],
            // ポイントの付与
            'add_point_to_member' => ['post', 'point'],
            // クーポン詳細取得
            'show_coupon' => ['get', 'coupon/:coupon_id'],
            // 会員配送先住所一覧
            'index_shipping_address' => ['get', 'member/:member_id/shipping_address'],
            // 会員配送先住所登録
            'store_shipping_address' => ['post', 'member/:member_id/shipping_address'],
            // 会員配送先住所詳細
            'show_shipping_address' => ['get', 'member/shipping_address/:shipping_address_id'],
            // 会員配送先住所更新
            'update_shipping_address' => ['put', 'member/shipping_address/:shipping_address_id'],
            // 会員配送先住所削除
            'delete_shipping_address' => ['delete', 'member/shipping_address/:shipping_address_id'],
            // 会員購買登録
            'store_purchase' => ['post', 'member/:member_id/purchase'],
            // 会員購買登録
            'update_purchase' => ['put', 'member/:member_id/purchase/:purchase_id'],
            // 配送先住所
            'shipping_address' => [
                'index' => ['get', 'member/:member_id/shipping_address'],
                'store' => ['post', 'member/:member_id/shipping_address'],
                'get' => ['get', 'member/shipping_address/:shipping_address_id'],
                'update' => ['put', 'member/shipping_address/:shipping_address_id'],
                'destroy' => ['delete', 'member/shipping_address/:shipping_address_id'],
            ],
            // 購買時ポイント計算
            'purchase_point' => ['post', 'member/:member_id/purchase/point'],
            // 購買キャンセル
            'purchase_cancel' => ['post', 'purchase/:purchase_id/cancel'],
            // 購買完了（配送完了）
            'purchase_finish' => ['get', 'purchase/:purchase_id/finish'],
            // 購買後返品
            'purchase_markdown' => ['post', 'purchase/:purchase_id/markdown'],
            // 会員削除
            'withdraw' => ['patch', 'member/:member_id'],
            // 旧カード会員PINコード認証
            'pin' => ['post', 'old_member/card/auth/pin'],
            // 旧会員メールアドレス認証メール送信
            'check_mail' => ['post', 'old_member/mail_auth/send'],
            // 会員ポイント履歴取得
            'point_history' => ['get', 'member/:member_id/point'],
            // 代理ログイン
            'auth_agent' => ['post', 'member/auth/agent'],
            // 旧会員カスタマーサービス連絡
            'old_member_contact' => ['post', 'old_member/contact'],
            // 旧会員メールアドレス忘れメールアドレス認証メール送信
            'old_member_forget_mail_send' => ['post', 'old_member/mail_auth/forget_mail/send'],
            // 旧カード会員メールアドレス認証メール送信
            'card_mail_auth' => ['post', 'old_member/card/mail_auth/send'],
        ],
    ],

    /*
    * 商品基幹
    */
    'shohin' => [
        'headers' => [
            'System-Token' => env('SHOHIN_SYSTEM_TOKEN'),
        ],
        'host' => env('SHOHIN_HOST'),
        'prefix' => 'api/v1',
        'endpoint' => [
            // 在庫情報取得
            'fetch_stocks' => ['post', 'item/stocks'],
            // 商品マスタ取得
            'fetch_masters' => ['post', 'item/masters'],
            // 販売情報登録
            'purchase' => ['post', 'item/purchase'],
            // 注文キャンセル
            'purchase_cancel' => ['post', 'item/purchase_cancel'],
            // EC情報変更
            'ec_update' => ['post', 'item/ec_update'],
        ],
    ],

    /*
     * Amazon Pay
     */
    'amazon_pay' => [
        'merchant_id' => env('AMAZON_PAY_MERCHANT_ID'),
        'access_key' => env('AMAZON_PAY_ACCESS_KEY'),
        'secret_key' => env('AMAZON_PAY_SECRET_KEY'),
        'client_id' => env('AMAZON_PAY_STORE_ID'),
        'sandbox' => (bool) ((int) env('AMAZON_PAY_SANDBOX')),
        'region' => 'jp',
        'currency_code' => 'JPY',
        'store_name' => 'YAMADAYA onlinestore',
    ],
];
