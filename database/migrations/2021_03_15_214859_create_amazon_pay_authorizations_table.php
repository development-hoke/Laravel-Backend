<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateAmazonPayAuthorizationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @see https://developer.amazon.com/ja/docs/amazon-pay-api/authorizationdetails.html
     *
     * @return void
     */
    public function up()
    {
        Schema::create('amazon_pay_authorizations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('amazon_pay_order_id')->nullable(false)->comment('amazon_pay_orders.id');
            $table->string('authorization_reference_id', 32)->nullable(false)->comment('AuthorizationReferenceId (EC側で生成する)');
            $table->string('amazon_authorization_id', 120)->nullable(false)->comment('Amazonが生成したこのオーソリトランザクションのID');
            $table->string('status', 30)->nullable(false)->comment('AuthorizationStatus.state');
            $table->string('status_reason_code', 60)->nullable(true)->comment('AuthorizationStatus.ReasonCode');
            $table->boolean('soft_decline')->nullable(false)->default(false)->comment('オーソリの結果がSoft Declineかどうか。最新のstatusがInvalidPaymentMethodのときのみ有効な値。');
            $table->datetime('last_status_updated_at')->nullable(true)->comment('AuthorizationStatus.LastUpdateTimestamp (Amazon側で保存されている値)');
            $table->integer('amount')->nullable(false)->comment('オーソリした金額');
            $table->integer('capturing_amount')->nullable(false)->comment('売上請求予定の金額（一部キャンセルに対応するためオーソリした金額とは別に保存）');
            $table->integer('fee')->nullable(false)->default(0)->comment('このオーソリでAmazonによって請求された手数料');
            $table->datetime('expiration_at')->nullable(false)->comment('オーソリに対して売上請求をリクエストすることができるオーソリの有効期限');
            $table->timestamps();
            $table->softDeletes('deleted_at', 0);

            $table->unique('authorization_reference_id');
            $table->unique('amazon_authorization_id');
            $table->foreign('amazon_pay_order_id')->references('id')->on('amazon_pay_orders')->onUpdate('cascade')->onDelete('restrict');
        });
        Schema::create('amazon_pay_authorization_logs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->datetime('logged_at')->nullable(false)->useCurrent()->comment('ログ作成日時');
            $table->unsignedBigInteger('amazon_pay_authorization_id')->nullable(false)->comment('amazon_pay_authorizations.id');
            $table->unsignedBigInteger('amazon_pay_order_id')->nullable(false)->comment('amazon_pay_orders.id');
            $table->string('authorization_reference_id', 32)->nullable(false)->comment('AuthorizationReferenceId');
            $table->string('amazon_authorization_id', 120)->nullable(false)->comment('Amazonが生成したこのオーソリトランザクションのID');
            $table->string('status', 30)->nullable(false)->comment('AuthorizationStatus.state');
            $table->string('status_reason_code', 60)->nullable(true)->comment('AuthorizationStatus.ReasonCode');
            $table->boolean('soft_decline')->nullable(false)->default(false)->comment('オーソリの結果がSoft Declineかどうか。最新のstatusがInvalidPaymentMethodのときのみ有効な値。');
            $table->datetime('last_status_updated_at')->nullable(true)->comment('AuthorizationStatus.LastUpdateTimestamp (Amazon側で保存されている値)');
            $table->integer('amount')->nullable(false)->comment('オーソリした金額');
            $table->integer('capturing_amount')->nullable(false)->comment('売上請求予定の金額（一部キャンセルに対応するためオーソリした金額とは別に保存）');
            $table->integer('fee')->nullable(false)->default(0)->comment('このオーソリでAmazonによって請求された手数料');
            $table->datetime('expiration_at')->nullable(false)->comment('オーソリに対して売上請求をリクエストすることができるオーソリの有効期限');
            $table->timestamps();
            $table->softDeletes('deleted_at', 0);

            $table->foreign('amazon_pay_authorization_id', 'amazon_pay_authorization_logs_foreign1')->references('id')->on('amazon_pay_authorizations')->onUpdate('cascade')->onDelete('restrict');
        });
        // DB::unprepared('
        //     CREATE TRIGGER after_insert_amazon_pay_authorizations
        //     AFTER INSERT ON amazon_pay_authorizations
        //     FOR EACH ROW
        //     INSERT INTO amazon_pay_authorization_logs (amazon_pay_authorization_id, amazon_pay_order_id, authorization_reference_id, amazon_authorization_id, status, status_reason_code, soft_decline, last_status_updated_at, amount, capturing_amount, fee, expiration_at, created_at, updated_at, deleted_at)
        //     VALUES (NEW.id, NEW.amazon_pay_order_id, NEW.authorization_reference_id, NEW.amazon_authorization_id, NEW.status, NEW.status_reason_code, NEW.soft_decline, NEW.last_status_updated_at, NEW.amount, NEW.capturing_amount, NEW.fee, NEW.expiration_at, NOW(), NEW.updated_at, NEW.deleted_at)
        // ;');
        // DB::unprepared('
        //     CREATE TRIGGER after_update_amazon_pay_authorizations
        //     AFTER UPDATE ON amazon_pay_authorizations
        //     FOR EACH ROW
        //     INSERT INTO amazon_pay_authorization_logs (amazon_pay_authorization_id, amazon_pay_order_id, authorization_reference_id, amazon_authorization_id, status, status_reason_code, soft_decline, last_status_updated_at, amount, capturing_amount, fee, expiration_at, created_at, updated_at, deleted_at)
        //     VALUES (NEW.id, NEW.amazon_pay_order_id, NEW.authorization_reference_id, NEW.amazon_authorization_id, NEW.status, NEW.status_reason_code, NEW.soft_decline, NEW.last_status_updated_at, NEW.amount, NEW.capturing_amount, NEW.fee, NEW.expiration_at, NOW(), NEW.updated_at, NEW.deleted_at);
        // ');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // DB::unprepared('DROP TRIGGER after_insert_amazon_pay_authorizations;');
        // DB::unprepared('DROP TRIGGER after_update_amazon_pay_authorizations;');
        Schema::dropIfExists('amazon_pay_authorization_logs');
        Schema::dropIfExists('amazon_pay_authorizations');
    }
}
