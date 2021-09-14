<?php

use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['namespace' => 'Api'], function () {
    Route::group(['prefix' => 'v1', 'namespace' => 'V1'], function () {
        Route::group(['namespace' => 'Front'], function () {
            Route::get('master', 'MasterController@index');
            Route::get('tops/store_brand', 'TopController@fetchByStoreBrand');
            Route::get('information/top_content', 'InformationController@getTop');
            Route::get('information/recent', 'InformationController@getRecent');
            Route::get('information/{id}', 'InformationController@show')->where('id', '[0-9]+');
            Route::get('information/{place?}', 'InformationController@index');
            Route::get('pages/{slug}', 'PageController@show');
            Route::get('urgent_notice', 'UrgentNoticeController@index');
            Route::post('contact', 'ContactController@send');
            // カート
            Route::get('carts', 'CartController@index');
            Route::post('carts', 'CartController@store');
            Route::put('carts/{cart_id}/items/{id}', 'CartController@updateItem')->where(['cart_id', '[0-9]+', 'cart_item_id' => '[0-9]+']);
            Route::put('carts/{cart_id}/restore_items/{cart_item_id}', 'CartController@restoreItem')->where(['cart_id', '[0-9]+', 'cart_item_id' => '[0-9]+']);
            Route::delete('carts/{cart_id}/remove_items/{cart_item_id}', 'CartController@removeItem')->where(['cart_id' => '[0-9]+', 'cart_item_id' => '[0-9]+']);
            Route::post('carts/coupon', 'CartController@coupon');
            Route::put('carts/{cart_id}/use_point', 'CartController@updateUsePoint')->where(['cart_id', '[0-9]+']);

            // ゲスト購入
            Route::post('guest_purchase/{cart_id}/email_auth', 'GuestPurchaseController@emailAuth');
            Route::post('guest_purchase/{cart_id}/verify', 'GuestPurchaseController@verify');
            Route::post('guest_purchase/{cart_id}/confirm', 'GuestPurchaseController@confirm');
            Route::post('guest_purchase/{cart_id}/order', 'GuestPurchaseController@order');

            // 会員
            Route::group(['namespace' => 'Member'], function () {
                // 認証
                Route::post('member/auth/password', 'AuthController@authPassword');
                // 登録
                Route::get('member/auth/me', 'AuthController@me');
                Route::post('member/auth/logout', 'AuthController@logout');
                Route::post('member/store/temp', 'MemberController@storeTemp');
                Route::post('member/{member_id}', 'MemberController@store');
                Route::get('member/{member_id}', 'MemberController@get');
                Route::put('member/{member_id}', 'MemberController@update');
                Route::post('member/{member_id}/change_email', 'MemberController@changeEmail');
                Route::get('member/{member_id}/change_email', 'MemberController@changeEmailDecision');
                Route::post('member/{member_id}/change_password', 'MemberController@changePassword');
                Route::get('member/{member_id}/change_password', 'MemberController@changePasswordDecision');
                Route::put('member/{member_id}/mail', 'MemberController@updateMailDm');
                Route::post('reset_password', 'MemberController@resetPassword');
                Route::post('reset_password_decision', 'MemberController@resetPasswordDecision');
                // Amazonログイン
                Route::post('member/auth/amazon/link', 'AuthAmazonController@link');
                Route::post('member/auth/amazon', 'AuthAmazonController@auth');
                Route::get('member/auth/amazon', 'AuthAmazonController@me');

                // クーポン
                Route::get('member/{member_id}/coupon', 'CouponController@get');
                Route::post('member/{member_id}/coupon/{coupon_id}', 'CouponController@issue');
                // ポイント
                Route::get('member/{member_id}/point', 'PointController@get');
                // お気に入り
                Route::get('member/{member_id}/favorites', 'FavoriteController@index');
                // 購入
                Route::get('member/{memberId}/purchase', 'PurchaseController@index');
                Route::post('member/{memberId}/order/{orderCode}/cancel', 'PurchaseController@cancel');
                // 退会
                Route::put('member/{member_id}/withdraw', 'WithdrawController@withdraw');
                // お届け先情報
                Route::get('member/{memberId}/destinations', 'DestinationController@index');
                Route::post('member/{memberId}/destinations', 'DestinationController@store');
                Route::get('member/{memberId}/destinations/{destinationId}', 'DestinationController@get');
                Route::put('member/{memberId}/destinations/{destinationId}', 'DestinationController@update');
                Route::delete('member/{memberId}/destinations/{destinationId}', 'DestinationController@destroy');
            });

            // 既存会員引き継ぎ
            Route::group(['namespace' => 'OldMember'], function () {
                Route::post('old_member/auth/pin', 'MemberController@pin');
                Route::post('old_member/mail_auth/check', 'MemberController@checkMail');
                Route::post('old_member/send_email', 'MemberController@sendEmail');
                Route::post('old_member/{member_id}', 'MemberController@store');
                Route::put('old_member', 'MemberController@auth');
                Route::post('old_member/forget/mail', 'ForgetController@forgetMail');
                Route::post('old_member/forget/sms', 'ForgetController@forgetSms');
                Route::post('old_member/forget/all', 'ForgetController@forgetAll');
            });

            // 店舗
            Route::get('stores', 'StoreController@index');
            Route::get('items/{item_id}/stores', 'StoreController@itemStores')->where(['item_id' => '[0-9]+']);

            // コンテンツ
            Route::group(['prefix' => 'contents', 'namespace' => 'Content'], function () {
                Route::get('new_items', 'ContentController@getNewItems');
                Route::get('pickups', 'ContentController@getPickups');
                Route::get('plans', 'PlanController@getPlans');
                Route::get('plans/{slug}', 'PlanController@getPlan');
                Route::get('stylings', 'StylingController@index');
                Route::get('stylings/{id}', 'StylingController@show');
                Route::get('helps', 'HelpController@index');
                Route::get('helps/{id}', 'HelpController@show');
                Route::post('helps/{id}', 'HelpController@rate');
            });

            // 商品
            Route::get('items', 'ItemController@index');
            Route::get('items/{product_number}', 'ItemController@show');
            Route::get('items/{product_number}/closed_markets/{id}', 'ItemController@showClosedMarket')->where(['id' => '[0-9]+']);
            Route::get('items/{product_number}/recommends', 'ItemController@recommends');
            Route::get('items/{product_number}/used_same_stylings', 'ItemController@usedSameStylings');
            Route::get('items/{product_number}/redisplay_requests', 'RedisplayRequestController@index');
            Route::post('closed_markets/{id}/verify', 'ItemController@verifyClosedMarket')->where(['id' => '[0-9]+']);

            // Route::post('items/reserve/arrivaled', 'ItemController@arriveReserved');
            // Route::post('items/back_order/arrivaled', 'ItemController@arriveBackOrdered');

            Route::get('item_details/{item_detail_id}/redisplay_requests', 'RedisplayRequestController@show')->where(['item_detail_id' => '[0-9]+']);
            Route::post('item_detail_redisplay_requests', 'RedisplayRequestController@store');
            Route::delete('item_detail/{item_detail_id}/redisplay_requests', 'RedisplayRequestController@destroy')->where(['item_detail_id' => '[0-9]+']);
            Route::post('item_detail_redisplay_requests/validate_email', 'RedisplayRequestController@validateEmail');

            // お気に入り
            Route::post('favorites/{item_id}', 'ItemFavoriteController@store')->where('item_id', '[0-9]+');
            Route::delete('favorites/{item_id}', 'ItemFavoriteController@destroy')->where('item_id', '[0-9]+');

            // 購入
            Route::post('purchase/change_payment_type', 'PurchaseController@changePaymentType');
            Route::post('purchase/confirm', 'PurchaseController@confirm');
            Route::post('purchase/confirm/amazon_pay', 'PurchaseController@confirmAmazonPayOrder');
            Route::get('purchase/member_credit_cards', 'PurchaseController@showMemberCreditCard');
            Route::delete('purchase/member_credit_cards/{id}', 'PurchaseController@destroyMemberCreditCard')->where(['id' => '[0-9]+']);
            Route::post('purchase', 'PurchaseController@order');
            // お問い合わせ
            Route::post('inquiry', 'InquiryController@inquiry');
        });

        Route::group(['prefix' => 'external', 'namespace' => 'External'], function () {
            // 配送完了
            Route::post('purchase/{purchase_id}/delivered', 'PurchaseController@delivered');
            Route::post('member/reset_password/callback_url', 'MemberController@resetPassword');
            Route::get('master/online_categories', 'MasterController@onlineCategoryPagination');
            Route::get('master/online_tags', 'MasterController@onlineTagPagination');
            Route::get('items/all', 'ItemController@ecData');
            Route::post('items/back_order/arrivaled', 'ItemController@arriveBackOrdered');
            Route::post('items/back_order/not_found', 'ItemController@foundBackOrdered');
            Route::post('items/reserve/arrivaled', 'ItemController@arriveReserved');
            Route::put('items/update/stocks', 'ItemController@updateStocks');
            // Amazon Pay IPN message
            Route::post('amazon-pay/ipn_reciver', 'AmazonPayIpnController@recieve');
        });

        Route::group(['prefix' => 'admin', 'namespace' => 'Admin'], function () {
            Route::post('auth/login', 'AuthController@login')->name('admin.auth.login');
            Route::post('auth/refresh', 'AuthController@refresh')->name('admin.auth.refresh');

            Route::group(['middleware' => 'admin_api'], function () {
                Route::get('auth/me', 'AuthController@me')->name('admin.auth.me');
                Route::post('auth/logout', 'AuthController@logout')->name('admin.auth.logout');
                Route::post('auth/agent_login', 'AuthController@agentLogin')->name('admin.auth.agentLogin');

                Route::get('items', 'ItemController@index')->name('admin.item.index');
                Route::get('items/{id}', 'ItemController@show')->where('id', '[0-9]+')->name('admin.item.show');
                Route::put('items/{id}', 'ItemController@update')->where('id', '[0-9]+')->name('admin.item.update');
                Route::put('items/{id}/status', 'ItemController@updateStatus')->where('id', '[0-9]+')->name('admin.item.updateStatus');
                Route::post('items/{id}/image', 'ItemController@uploadImage')->where('id', '[0-9]+')->name('admin.item.uploadImage');
                Route::get('items/csv', 'ItemController@exportCsv')->name('admin.item.exportCsv');
                Route::get('items/csv_info', 'ItemController@exportInfoCsv')->name('admin.item.exportInfoCsv');
                Route::get('items/csv_image', 'ItemController@exportImageCsv')->name('admin.item.exportImageCsv');
                Route::get('item_preview/{key}', 'ItemController@showPreview')->where('key', '.+')->name('admin.item.showPreview');
                Route::post('items/{id}/preview', 'ItemController@storePreview')->where('id', '[0-9]+')->name('admin.item.storePreview');

                Route::get('items/{item_id}/closed_markets', 'ClosedMarketController@index')->where('item_id', '[0-9]+')->name('admin.closedMarket.index');
                Route::post('items/{item_id}/closed_markets', 'ClosedMarketController@store')->where('item_id', '[0-9]+')->name('admin.closedMarket.store');
                Route::put('items/{item_id}/closed_markets/{id}', 'ClosedMarketController@update')->where(['item_id' => '[0-9]+', 'id' => '[0-9]+'])->name('admin.closedMarket.update');
                Route::delete('closed_markets/{id}', 'ClosedMarketController@destroy')->where('id', '[0-9]+')->name('admin.closedMarket.destroy');

                Route::get('items/{item_id}/reserve', 'ItemReserveController@show')->where(['item_id' => '[0-9]+'])->name('admin.itemReserve.show');
                Route::post('items/{item_id}/reserve', 'ItemReserveController@store')->where(['item_id' => '[0-9]+'])->name('admin.itemReserve.store');
                Route::put('items/{item_id}/reserve', 'ItemReserveController@update')->where(['item_id' => '[0-9]+'])->name('admin.itemReserve.update');

                Route::get('item_details', 'ItemDetailController@index')->name('admin.itemDetail.index');
                Route::get('item_details/identifications', 'ItemDetailController@indexIdentifications')->name('admin.itemDetail.indexIdentifications');
                Route::get('item_details/csv', 'ItemDetailController@exportCsv')->name('admin.itemDetail.exportCsv');
                Route::get('items/{item_id}/item_details', 'ItemDetailController@indexByItemId')->where(['itemId' => '[0-9]+'])->name('admin.itemDetail.indexByItemId');

                Route::get('past_items', 'PastItemController@index')->name('admin.pastItem.index');

                Route::get('colors', 'ColorController@index')->name('admin.color.index');
                Route::put('colors/{id}', 'ColorController@update')->where(['id' => '[0-9]+'])->name('admin.color.update');
                Route::get('colors/csv', 'ColorController@exportCsv')->name('admin.color.exportCsv');

                Route::get('sales_type', 'SalesTypeController@index')->name('admin.salesType.index');
                Route::post('sales_type', 'SalesTypeController@create')->name('admin.salesType.create');
                Route::put('sales_type/{id}', 'SalesTypeController@update')->where(['id' => '[0-9]+'])->name('admin.salesType.update');
                Route::delete('sales_type/{id}', 'SalesTypeController@destroy')->where(['id' => '[0-9]+'])->name('admin.salesType.destroy');
                Route::get('sales_type/csv', 'SalesTypeController@exportCsv')->name('admin.salesType.exportCsv');

                Route::get('events', 'EventController@index')->name('admin.event.index');
                Route::get('events/{id}', 'EventController@show')->where(['id' => '[0-9]+'])->name('admin.event.show');
                Route::post('events', 'EventController@store')->name('admin.event.store');
                Route::post('events/{id}/copy', 'EventController@copy')->where(['id' => '[0-9]+'])->name('admin.event.copy');
                Route::put('events/{id}', 'EventController@update')->where(['id' => '[0-9]+'])->name('admin.event.update');
                Route::delete('events/{id}', 'EventController@delete')->where(['id' => '[0-9]+'])->name('admin.event.delete');

                Route::get('events/{event_id}/items', 'EventItemController@index')->where(['event_id' => '[0-9]+'])->name('admin.eventItem.index');
                Route::post('events/{event_id}/items', 'EventItemController@store')->where(['event_id' => '[0-9]+'])->name('admin.eventItem.store');
                Route::post('events/{event_id}/items/csv', 'EventItemController@storeCsv')->where(['event_id' => '[0-9]+'])->name('admin.eventItem.storeCsv');
                Route::put('events/{event_id}/items/{id}', 'EventItemController@update')->where(['event_id' => '[0-9]+', 'id' => '[0-9]+'])->name('admin.eventItem.update');
                Route::delete('events/{event_id}/items/{id}', 'EventItemController@destroy')->where(['event_id' => '[0-9]+', 'id' => '[0-9]+'])->name('admin.eventItem.destroy');

                Route::get('events/{event_id}/users', 'EventUserController@index')->where(['event_id' => '[0-9]+'])->name('admin.eventUser.index');
                Route::post('events/{event_id}/users/csv', 'EventUserController@storeCsv')->where(['event_id' => '[0-9]+'])->name('admin.eventUser.storeCsv');
                Route::delete('events/{event_id}/users/{id}', 'EventUserController@destroy')->where(['event_id' => '[0-9]+', 'id' => '[0-9]+'])->name('admin.eventUser.destroy');

                // Route::get('icons', 'IconController@index')->name('admin.icon.index');

                Route::get('orders', 'OrderController@index')->name('admin.order.index');
                Route::get('orders/{id}', 'OrderController@show')->where(['id' => '[0-9]+'])->name('admin.order.show');
                Route::put('orders/{id}', 'OrderController@update')->where(['id' => '[0-9]+'])->name('admin.order.update');
                Route::put('orders/{id}/cancel', 'OrderController@cancel')->where(['id' => '[0-9]+'])->name('admin.order.cancel');
                Route::put('orders/{id}/return', 'OrderController@return')->where(['id' => '[0-9]+'])->name('admin.order.return');
                Route::put('orders/{id}/coupon/add', 'OrderController@addCoupon')->where(['id' => '[0-9]+'])->name('admin.order.addCoupon');
                Route::put('orders/{id}/coupon/remove', 'OrderController@removeCoupon')->where(['id' => '[0-9]+'])->name('admin.order.removeCoupon');
                Route::put('orders/{id}/price', 'OrderController@updatePrice')->where(['id' => '[0-9]+'])->name('admin.order.updatePrice');
                Route::get('orders/csv', 'OrderController@exportCsv')->name('admin.order.exportCsv');
                Route::get('orders/detail_csv', 'OrderController@exportDetailCsv')->name('admin.order.exportDetailCsv');
                Route::get('order_messages/{id}', 'OrderController@showMessage')->where(['id' => '[0-9]+'])->name('admin.order.showMessage');
                Route::post('orders/{order_id}/messages', 'OrderController@sendMessage')->where(['order_id' => '[0-9]+'])->name('admin.order.sendMessage');

                Route::get('orders/{order_id}/details', 'OrderDetailController@index')->where(['order_id' => '[0-9]+'])->name('admin.orderDetail.index');
                Route::post('orders/{order_id}/details', 'OrderDetailController@store')->where(['order_id' => '[0-9]+'])->name('admin.orderDetail.store');
                Route::put('orders/{order_id}/details/cancel', 'OrderDetailController@cancel')->where(['order_id' => '[0-9]+'])->name('admin.orderDetail.cancel');
                Route::put('orders/{order_id}/details/return', 'OrderDetailController@return')->where(['order_id' => '[0-9]+'])->name('admin.orderDetail.return');

                Route::get('order_details/{id}', 'OrderDetailController@show')->where(['id' => '[0-9]+'])->name('admin.orderDetail.show');
                Route::get('orders/{order_id}/items', 'OrderDetailController@indexItems')->where(['order_id' => '[0-9]+'])->name('admin.orderDetail.indexItems');

                Route::get('sales_aggrigation/orders', 'SalesAggregationController@aggregateOrders')->name('admin.salesAggregation.aggregateOrders');
                Route::get('sales_aggrigation/items', 'SalesAggregationController@aggregateItems')->name('admin.salesAggregation.aggregateItems');
                Route::get('sales_aggrigation/dailiy', 'SalesAggregationController@aggreateDailyOrder')->name('admin.salesAggregation.aggregateDailyOrder');
                Route::get('sales_aggrigation/monthly', 'SalesAggregationController@aggregateMonthlyOrder')->name('admin.salesAggregation.aggregateMonthlyOrder');
                Route::get('sales_aggrigation/orders/csv', 'SalesAggregationController@exportOrderCsv')->name('admin.salesAggregation.exportOrderCsv');
                Route::get('sales_aggrigation/order_details/csv', 'SalesAggregationController@exportOrderDetailCsv')->name('admin.salesAggregation.exportOrderDetailCsv');
                Route::get('sales_aggrigation/items/csv', 'SalesAggregationController@exportItemCsv')->name('admin.salesAggregation.exportItemCsv');

                Route::get('master/enums', 'MasterController@indexEnums')->name('admin.master.indexEnums');
                Route::get('master/terms', 'MasterController@indexTerms')->name('admin.master.indexTerms');
                Route::get('master/prefs', 'MasterController@indexPrefs')->name('admin.master.indexPrefs');
                Route::get('master/divisions', 'MasterController@indexDivisions')->name('admin.master.indexDivisions');
                Route::get('master/departments', 'MasterController@indexDepartments')->name('admin.master.indexDepartments');
                Route::get('master/actionNames', 'MasterController@indexActionNames')->name('admin.master.indexActionNames');
                Route::get('master/organizations', 'MasterController@indexOrganizations')->name('admin.master.indexOrganizations');

                Route::get('online_categories', 'OnlineCategoryController@index')->name('admin.onlineCategory.index');
                Route::post('online_categories', 'OnlineCategoryController@store')->name('admin.onlineCategory.store');
                Route::put('online_categories/{id}', 'OnlineCategoryController@update')->where(['id' => '[0-9]+'])->name('admin.onlineCategory.update');
                Route::delete('online_categories/{id}', 'OnlineCategoryController@destroy')->where(['id' => '[0-9]+'])->name('admin.onlineCategory.destroy');
                Route::get('online_categories/csv', 'OnlineCategoryController@exportCsv')->name('admin.onlineCategory.exportCsv');

                Route::get('online_tags', 'OnlineTagController@index')->name('admin.onlineTag.index');
                Route::post('online_tags', 'OnlineTagController@store')->name('admin.onlineTag.store');
                Route::put('online_tags/{id}', 'OnlineTagController@update')->where(['id' => '[0-9]+'])->name('admin.onlineTag.update');
                Route::delete('online_tags/{id}', 'OnlineTagController@destroy')->where(['id' => '[0-9]+'])->name('admin.onlineTag.destroy');
                Route::get('online_tags/csv', 'OnlineTagController@exportCsv')->name('admin.onlineTag.exportCsv');

                Route::get('delivery_settings/{id}', 'DeliverySettingController@show')->name('admin.deliverySetting.show');
                Route::put('delivery_settings/{id}', 'DeliverySettingController@update')->where(['id' => '[0-9]+'])->name('admin.deliverySetting.update');

                Route::get('brands', 'BrandController@index')->name('admin.brand.index');
                Route::post('brands', 'BrandController@store')->name('admin.brand.store');
                Route::get('brands/{id}', 'BrandController@show')->where(['id' => '[0-9]+'])->name('admin.brand.show');
                Route::put('brands/{id}', 'BrandController@update')->where(['id' => '[0-9]+'])->name('admin.brand.update');
                Route::delete('brands/{id}', 'BrandController@destroy')->where(['id' => '[0-9]+'])->name('admin.brand.destroy');
                Route::post('brands/{id}/update_sort', 'BrandController@updateSort')->name('admin.brand.updateSort');
                Route::get('brands/csv', 'BrandController@exportCsv')->name('admin.brand.exportCsv');

                Route::get('information', 'InformationController@index')->name('admin.information.index');
                Route::post('information', 'InformationController@store')->name('admin.information.store');
                Route::get('information/{id}', 'InformationController@show')->where(['id' => '[0-9]+'])->name('admin.information.show');
                Route::put('information/{id}', 'InformationController@update')->where(['id' => '[0-9]+'])->name('admin.information.update');
                Route::delete('information/{id}', 'InformationController@destroy')->where(['id' => '[0-9]+'])->name('admin.information.destroy');
                Route::get('information_preview/{key}', 'InformationController@showPreview')->where('key', '.+')->name('admin.information.showPreview');
                Route::post('information/preview', 'InformationController@storePreview')->name('admin.information.storePreview');

                Route::get('urgent_notices', 'UrgentNoticeController@show')->name('admin.urgentNotice.show');
                Route::put('urgent_notices/{id}', 'UrgentNoticeController@update')->where(['id' => '[0-9]+'])->name('admin.urgentNotice.update');

                Route::get('admin_logs', 'AdminLogController@index')->name('admin.adminLog.index');
                Route::get('admin_logs/csv', 'AdminLogController@exportCsv')->name('admin.adminLog.exportCsv');

                Route::get('staffs', 'StaffController@index')->name('admin.staff.index');

                Route::get('item_bulk_uploads', 'ItemBulkUploadController@index')->name('admin.itemBulkUpload.index');
                Route::post('item_bulk_uploads/csv/items', 'ItemBulkUploadController@storeItemCsv')->name('admin.itemBulkUpload.storeItemCsv');
                Route::post('item_bulk_uploads/item_images', 'ItemBulkUploadController@storeItemImages')->name('admin.itemBulkUpload.storeItemImages');
                Route::get('item_bulk_uploads/{id}/csv/errors', 'ItemBulkUploadController@exportErrorCsv')->where(['id' => '[0-9]+'])->name('admin.itemBulkUpload.exportErrorCsv');
                Route::get('item_bulk_uploads/csv/format/item', 'ItemBulkUploadController@exportItemCsvFormat')->name('admin.itemBulkUpload.exportItemCsvFormat');
                Route::get('item_bulk_uploads/csv/format/item_image', 'ItemBulkUploadController@exportItemImageCsvFormat')->name('admin.itemBulkUpload.exportItemImageCsvFormat');

                Route::get('pages', 'PageController@index')->name('admin.page.index');
                Route::post('pages', 'PageController@store')->name('admin.page.store');
                Route::get('pages/{id}', 'PageController@show')->where(['id' => '[0-9]+'])->name('admin.page.show');
                Route::put('pages/{id}', 'PageController@update')->where(['id' => '[0-9]+'])->name('admin.page.update');
                Route::delete('pages/{id}', 'PageController@destroy')->where(['id' => '[0-9]+'])->name('admin.page.destroy');
                Route::post('pages/{id}/copy', 'PageController@copy')->where(['id' => '[0-9]+'])->name('admin.page.copy');
                Route::get('member/{id}/coupons', 'MemberController@indexAvailableCoupons')->name('admin.member.indexAvailableCoupons');

                Route::get('plans', 'PlanController@index')->name('admin.plan.index');
                Route::post('plans', 'PlanController@store')->name('admin.plan.store');
                Route::delete('plans/{id}/item/{item_id}', 'PlanController@deleteItems')->where(['id' => '[0-9]+', 'item_id' => '[0-9]+'])->name('admin.plan.deleteItems');
                Route::put('plans/{id}/item_setting', 'PlanController@updateItemSetting')->where(['id' => '[0-9]+'])->name('admin.plan.updateItemSetting');
                Route::get('plans/{id}', 'PlanController@show')->where(['id' => '[0-9]+'])->name('admin.plan.show');
                Route::put('plans/{id}', 'PlanController@update')->where(['id' => '[0-9]+'])->name('admin.plan.update');
                Route::delete('plans/{id}', 'PlanController@destroy')->where(['id' => '[0-9]+'])->name('admin.plan.destroy');
                Route::post('plans/{id}/copy', 'PlanController@copy')->where(['id' => '[0-9]+'])->name('admin.plan.copy');
                Route::post('plans/{id}/new_items', 'PlanController@addNewItems')->where(['id' => '[0-9]+'])->name('admin.plan.addNewItems');
                Route::get('plans/store_brand/{store_brand?}', 'PlanController@showByStoreBrand')->where('store_brand', '[0-9]+')->name('admin.plan.showByStoreBrand');

                Route::get('helps', 'HelpController@index')->name('admin.help.index');
                Route::post('helps', 'HelpController@store')->name('admin.help.store');
                Route::get('helps/{id}', 'HelpController@show')->where(['id' => '[0-9]+'])->name('admin.help.show');
                Route::put('helps/{id}', 'HelpController@update')->where(['id' => '[0-9]+'])->name('admin.help.update');
                Route::delete('helps/{id}', 'HelpController@destroy')->where(['id' => '[0-9]+'])->name('admin.help.destroy');

                Route::get('help_categories', 'HelpCategoryController@index')->name('admin.helpCategory.index');
                Route::post('help_categories', 'HelpCategoryController@store')->name('admin.helpCategory.store');
                Route::put('help_categories/{id}', 'HelpCategoryController@update')->where(['id' => '[0-9]+'])->name('admin.helpCategory.update');
                Route::delete('help_categories/{id}', 'HelpCategoryController@destroy')->where(['id' => '[0-9]+'])->name('admin.helpCategory.destroy');
                Route::get('help_categories/csv', 'HelpCategoryController@exportCsv')->name('admin.helpCategory.exportCsv');

                Route::get('item_sorts', 'ItemSortController@index')->name('admin.itemSort.index');
                Route::post('item_sorts', 'ItemSortController@store')->name('admin.itemSort.store');
                Route::put('item_sorts/{id}', 'ItemSortController@update')->where(['id' => '[0-9]+'])->name('admin.itemSort.update');
                Route::delete('item_sorts/{id}', 'ItemSortController@destroy')->where(['id' => '[0-9]+'])->name('admin.itemSort.destroy');

                Route::get('top_contents', 'TopContentController@index')->name('admin.topContent.index');
                Route::get('top_contents/store_brand/{store_brand?}', 'TopContentController@showByStoreBrand')->where('store_brand', '[0-9]+')->name('admin.topContent.showByStoreBrand');

                Route::post('top_contents/{id}/main_visuals', 'TopContentController@addMainVisuals')->where(['id' => '[0-9]+'])->name('admin.topContent.addMainVisuals');
                Route::put('top_contents/{id}/main_visuals', 'TopContentController@updateMainVisuals')->where('id', '[0-9]+')->name('admin.topContent.updateMainVisuals');
                Route::delete('top_contents/{id}/main_visuals/{sort}', 'TopContentController@deleteMainVisuals')->where(['id' => '[0-9]+', 'sort' => '[0-9]+'])->name('admin.topContent.deleteMainVisuals');
                Route::put('top_contents/{id}/main_visuals/status/{sort}', 'TopContentController@updateStatusMainVisuals')->where(['id' => '[0-9]+', 'sort' => '[0-9]+'])->name('admin.topContent.updateStatusMainVisuals');

                Route::post('top_contents/{id}/new_items', 'TopContentController@addNewItems')->where(['id' => '[0-9]+'])->name('admin.topContent.addNewItems');
                Route::put('top_contents/{id}/new_items/{item_id}', 'TopContentController@updateNewItems')->where(['id' => '[0-9]+', 'item_id' => '[0-9]+'])->name('admin.topContent.updateNewItems');
                Route::delete('top_contents/{id}/new_items/{item_id}', 'TopContentController@deleteNewItems')->where(['id' => '[0-9]+', 'item_id' => '[0-9]+'])->name('admin.topContent.deleteNewItems');

                Route::post('top_contents/{id}/pickups', 'TopContentController@addPickups')->where(['id' => '[0-9]+'])->name('admin.topContent.addPickups');
                Route::put('top_contents/{id}/pickups/{item_id}', 'TopContentController@updatePickups')->where(['id' => '[0-9]+', 'item_id' => '[0-9]+'])->name('admin.topContent.updatePickups');
                Route::delete('top_contents/{id}/pickups/{item_id}', 'TopContentController@deletePickups')->where(['id' => '[0-9]+', 'item_id' => '[0-9]+'])->name('admin.topContent.deletePickups');

                Route::put('top_contents/{id}/background_color', 'TopContentController@updateBackgroundColor')->where(['id' => '[0-9]+'])->name('admin.topContent.updateBackgroundColor');

                Route::put('top_contents/{id}/features', 'TopContentController@updateFeatures')->where(['id' => '[0-9]+'])->name('admin.topContent.updateFeatures');
                Route::put('top_contents/{id}/features/{plan_id}', 'TopContentController@updateSortFeatures')->where(['id' => '[0-9]+', 'plan_id' => '[0-9]+'])->name('admin.topContent.updateSortFeatures');

                Route::put('top_contents/{id}/news', 'TopContentController@updateNews')->where(['id' => '[0-9]+'])->name('admin.topContent.updateNews');
                Route::put('top_contents/{id}/news/{plan_id}', 'TopContentController@updateSortNews')->where(['id' => '[0-9]+', 'plan_id' => '[0-9]+'])->name('admin.topContent.updateSortNews');

                Route::get('stylings', 'StylingController@index')->name('admin.styling.index');
                Route::post('content_images', 'ContentImageController@store')->name('admin.contentImage.store');
            });
        });
    });
});

Route::fallback(function () {
    throw new NotFoundHttpException(error_format('error.url_not_found'));
});
