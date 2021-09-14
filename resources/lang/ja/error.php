<?php

return [
    'unexpected' => 'エラーが発生しました。',
    'undefined_property' => '指定されたプロパティ:nameは未定義です。',
    'not_allowed_to_set' => '指定したプロパティのセッターは使用できません。',
    'model_not_found' => 'データが見つかりませんでした。',
    'resource_not_found' => 'データが見つかりませんでした。',
    'url_not_found' => '指定したURLは存在しません。',
    'coupon_id_not_found' => '指定されたクーポンIDは存在しません。',
    'coupon_is_not_applicable_discount_amount' => '指定されたクーポンの割引価格は請求金額を上回っています。',
    'invalid_coupon_type' => 'discount_typeが正しくありません。',
    'invalid_arguments' => '指定された引数は正しくありません。',
    'invalid_argument_value' => '指定された引数の値は正しくありません。',
    'invalid_item_sort_count' => 'item_sortsの件数が多すぎます',
    'invalid_order_price_change' => '受注金額の変更結果に不整合が発生しています。',
    'required_parameter' => ':nameは必須のパラメータです。',
    'method_not_exists' => ':classに:methodの実装が必要です。',
    'invalid_instance' => 'クエリビルダは:classのインスタンスでなければなりません。',
    'invalid_upload_file' => 'ファイルの形式が正しくありません。',
    'invalid_content_type' => 'ファイル拡張子が対応していません。',
    'invalid_file_path' => 'ファイルパスが正しくありません。',
    'failed_to_upload_file' => 'ファイルのアップロードが出来ませんでした。',
    'failed_to_update_db' => 'DBの更新が出来ませんでした。',
    'min' => ':attributeは:min以上の値を指定してください。',
    'invalid_range' => ':attributeは:minから:maxまでの値しか指定できません。',
    'invalid_value' => ':valueは:attributeに指定できない値です。',
    'not_exists_enouch_stock' => '在庫が確保できませんでした。',
    'failed_to_secure_stock' => '在庫の確保に失敗しました。',
    'not_exists_number_of_units' => '十分な数量の注文がありません。',
    'stock_cannot_be_negative_value' => '在庫数は0未満にはできません。',
    'inconsistent_level_with_descendants' => '指定された階層の値と子孫ノードの階層の値に不整合が起こっています。',
    'inconsistent_level_with_ancestors' => '指定された階層の値と先祖ノードの階層の値に不整合が起こっています。',
    'inconsistent_level_as_root' => 'ルートノードの階層の値が最上位の階層の値ではありません。',
    'inconsistent_root_id_as_root' => 'ルートノードが持つroot_idが自身のIDと一致していません。',
    'inconsistent_root_id' => 'ルートIDが自身の所属するルートノードのIDと一致していません。',
    'exceeded_maximum_descendant_level' => '子孫ノードの階層の値が最下位の値を超えています。',
    'parent_id_must_not_be_same_value_with_id' => '親IDにIDと同じ値を指定できません。',
    'only_secound_levels_can_be_used' => ':tableは2階層までしか作成できません。',
    'password_unauthenticated' => ':idまたはパスワードが間違っています。',
    'unauthenticated' => '認証に失敗しました。',
    'forbidden' => 'ご指定の:nameはご利用になれません。',
    'not_supported_http_method' => '使用されたHTTPメソッドはサポートされていません。',
    'route_title_cannot_be_resolved' => '指定されたルートはロケールファイル（lang/ja/routes.php）が登録されていません。',
    'no_ec_stock' => '在庫が確保できませんでした。',
    'no_items' => '商品がありません。',
    'no_item_details' => '商品詳細がありません。',
    'no_item_detail_identifications' => '商品詳細識別がありません。',
    'no_cart' => 'カート情報がありません。',
    'cart_invalid_item' => 'カート商品が正しくありません。',
    'cart_invalid_brand' => 'カート商品のブランドが正しくありません。',
    'cart_invalid_color' => 'カート商品のカラーが正しくありません。',
    'cart_invalid_size' => 'カート商品のサイズが正しくありません。',
    'over_use_point' => '利用可能なポイントの上限を超えています。',
    'invalid_status' => '商品ステータスが正しくありません。',
    'exists_other_than_add_items' => 'カートの中に通常購入商品が入っています。',
    'exists_other_than_reserve_items' => 'カートの中に予約商品が入っています。',
    'exists_other_than_order_items' => 'カートの中に取り寄せ商品が入っています。',
    'exists_invalid_items' => 'カートの中に不正な商品が入っています。',
    'exists_reserve_item' => 'カートの中にすでに予約商品が入っています。',
    'exists_order_item' => 'カートの中にすでに取り寄せ商品が入っています。',
    'too_large_search_results' => ':nameの検索結果が多すぎます。',
    'invalid_cart' => 'カート情報が正しくありません。',
    'invalid_fregi' => 'クレジット情報の確認に失敗しました。',
    'fail_fregi_auth_for_log' => 'オーソリ処理:NG',
    'fail_fregi_auth_for_front' => 'クレジット決済認証処理に失敗しました。',
    'staff_code_failed_code' => '異常終了コードが返却されました',
    'staff_code_coordinate_id_not_found' => 'コーディネートID :value は見つかりませんでした',
    'class_method_not_defined' => ':classに:methodの実装が必要です。',
    'class_propery_not_defined' => ':classに:properyが必要です。',
    'no_orders' => '注文履歴がありません。',
    'exceed_cancel_time' => 'キャンセル期間を過ぎています。',
    'already_canceled' => 'すでにキャンセルしています。',
    'not_found_item_detail_related_with_order_change_log' => '変更履歴に関連する商品詳細IDが見つかりませんでした。',
    'required_order_detail_contracted_price' => '受注詳細の読み込みとcontracted_priceの設定が必要です。',
    'getter_lazyload' => 'getterを使用する前にリレーションを明示的に読み込んでください。',
    'invalid_discount_type' => 'discount_typeは正しくありません。',
    'no_applicable_discount_coupon' => '使用されたクーポンに適用可能な割引はありませんでした。',
    'http_client' => 'エラーが発生しました。 response: :response',
    'invalid_closed_market' => '闇市設定を取得できませんでした。',
    'no_applied_closed_market' => 'appliedClosedMarketが読み込まれていません。',
    'invalid_closed_market_password' => 'パスワードが間違っています。',
    'failed_to_open_zip' => 'ZIPファイルを開けませんでした。:path',
    'failed_to_extract_zip' => 'ZIPファイルを展開出来ませんでした。[source]:source [destination]:destination',
    'member_not_found' => 'このメールアドレスに関連付けられているアカウントがありません。他のメールアドレスをお試しください。',
    'member_transferred_already_done' => '既に移行済のためログインしてください',
    'email_already_in_use' => 'このメールアドレスはすでに使用しています。',
    'wrong_password' => 'パスワードが間違っているようです。パスワード、パスワード(確認用)の入力を再度してください。',
    'wrong_email_birthday' => 'メールアドレスまたは生年月日が間違っています。',
    'failed_to_purchase_member_system' => "会員ポイントシステムの購入APIでエラーが発生しました。\n:message",
    'failed_to_parse_shohin_response' => '商品基幹のレスポンスが正常に解析できませんでした。',
    'failed_to_extract_http_error' => '外部システム連携時にエラーが発生しましたがエラー内容を取得できませんでした。',
    'disabled_item_details_in_cart' => 'ご利用になれない商品がカート内にあります。カート内を編集してください。',
    'invalid_access_to_custom_attribute' => '[:method] Custom attributeに正しくアクセスできません。',
    'entity_cast_not_available' => 'キャストに指定された:castは利用できません。',
    'entity_not_supported_attribute_converter' => 'attributeConvertersに指定された:nameは利用できません。',
    'entity_undefined' => ':nameは定義されていないentityです。',
    'entity_doesnot_support_collection' => ':nameはcollectionメソッドを使用できません。',
    'amazon_pay_method_does_not_exists' => '[AMAZON_PAY] :methodは存在しないメソッドです。',
    'amazon_pay_maximum_retry' => '[AMAZON_PAY] 再試行回数の上限を超えました。 method: :method status_code: :status_code retries: :retries params: :params',
    'amazon_pay_failed_request' => '[AMAZON_PAY] リクエストに失敗しました。 original_message: :original_message method: :method params: :params',
    'amazon_pay_order_amount_not_set' => '[AMAZON_PAY] OrderReferenceに金額がセットされていません。 order_id: :order_id order_reference_id: :order_reference_id',
    'amazon_pay_unsupported_constraint' => '[AMAZON_PAY] OrderReferenceDetailsにシステムが対応していないConstraintが設定されていました。  order_id: :order_id order_reference_id: :order_reference_id constraint: :constraint',
    'amazon_pay_invalid_token' => '[AMAZON_PAY] 指定されたアクセストークンからユーザー情報を取得できませんでした。 method: :method',
    'amazon_pay_capture_declined' => '[AMAZON_PAY] キャプチャに失敗しました。 reason_code: :reason_code   reason_description: :reason_description',
    'amazon_pay_failed_recieve_ipn' => '[AMAZON_PAY] IPN通知の受け取りに失敗しました。 message_id: :message_id',
    'amazon_pay_insufficient_refunding_capture' => '[AMAZON_PAY] 返金するための十分な売上がありません。',
    'amazon_pay_invalid_ipn' => '[AMAZON_PAY] INPメッセージの受信に失敗しました。',
    'cannot_withdraw' => '未発送の商品が残っています。商品発送後に改めて退会手続きをお願い致します。',
    'failed_guest_purchase_mail_auth' => 'ゲスト購入のメール認証に失敗しました。',
    'fail_np_auth' => '[NP後払い] 承認できませんでした。',
    'np_failed_to_perse_http_error' => '[NP後払い]エラーが発生しましたが、エラーコードを取得できませんでした。',
    'np_not_failed_transaction' => '[NP後払い]トランザクションは成功しています。',
    'np_unsupported_error_code' => '[NP後払い]エラーが発生しましたが、エラーコードに対応していません。',
    'np_payment_unsolved_failed_transaction' => '[NP後払い]取引登録・更新でNGまたは保留になりましたがキャンセルに失敗しました。',
    'member_ymdy_registered_already_done' => '既に新YAMADAYA会員に移行済なので、ログインしてください',
    'member_registered_already_done' => '既に登録済のためログインしてください。',
    'freg_failed_auth' => '[F-REGI]オーソリに失敗しました。',
    'plan_used_in_top_contents' => '企画がトップ画面で利用中なので削除できません。',
    'no_amount_order_detail' => '数量が0件です。',
];
