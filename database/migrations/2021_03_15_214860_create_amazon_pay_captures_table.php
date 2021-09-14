<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateAmazonPayCapturesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @see https://developer.amazon.com/ja/docs/amazon-pay-api/capturedetails.html
     *
     * @return void
     */
    public function up()
    {
        Schema::create('amazon_pay_captures', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('amazon_pay_authorization_id')->nullable(false)->comment('amazon_pay_authorizations.id');
            $table->string('capture_reference_id', 32)->nullable(false)->comment('CaptureReferenceId (EC側で生成する)');
            $table->string('amazon_capture_id', 120)->nullable(false)->comment('Amazonが生成したこの売上請求のIDです');
            $table->string('status', 30)->nullable(false)->comment('CaptureStatus.state');
            $table->string('status_reason_code', 60)->nullable(true)->comment('CaptureStatus.ReasonCode');
            $table->datetime('last_status_updated_at')->nullable(true)->comment('CaptureStatus.LastUpdateTimestamp (Amazon側で保存されている値)');
            $table->integer('amount')->nullable(false)->comment('売上請求される金額');
            $table->integer('fee')->nullable(true)->comment('この売上請求でのAmazonの手数料。売上請求が完了した後にのみ取得可能になる。');
            $table->timestamps();
            $table->softDeletes('deleted_at', 0);

            $table->unique('capture_reference_id');
            $table->unique('amazon_capture_id');
            $table->foreign('amazon_pay_authorization_id')->references('id')->on('amazon_pay_authorizations')->onUpdate('cascade')->onDelete('restrict');
        });
        Schema::create('amazon_pay_capture_logs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->datetime('logged_at')->nullable(false)->useCurrent()->comment('ログ作成日時');
            $table->unsignedBigInteger('amazon_pay_capture_id')->nullable(false)->comment('amazon_pay_captures.id');
            $table->unsignedBigInteger('amazon_pay_authorization_id')->nullable(false)->comment('amazon_pay_authorizations.id');
            $table->string('capture_reference_id', 32)->nullable(false)->comment('CaptureReferenceId (EC側で生成する)');
            $table->string('amazon_capture_id', 120)->nullable(false)->comment('Amazonが生成したこの売上請求のIDです');
            $table->string('status', 30)->nullable(false)->comment('CaptureStatus.state');
            $table->string('status_reason_code', 60)->nullable(true)->comment('CaptureStatus.ReasonCode');
            $table->datetime('last_status_updated_at')->nullable(true)->comment('CaptureStatus.LastUpdateTimestamp (Amazon側で保存されている値)');
            $table->integer('amount')->nullable(false)->comment('売上請求される金額');
            $table->integer('fee')->nullable(true)->comment('この売上請求でのAmazonの手数料。売上請求が完了した後にのみ取得可能になる。');
            $table->timestamps();
            $table->softDeletes('deleted_at', 0);

            $table->foreign('amazon_pay_capture_id')->references('id')->on('amazon_pay_captures')->onUpdate('cascade')->onDelete('restrict');
        });
        // DB::unprepared('
        //     CREATE TRIGGER after_insert_amazon_pay_captures
        //     AFTER INSERT ON amazon_pay_captures
        //     FOR EACH ROW
        //     INSERT INTO amazon_pay_capture_logs (amazon_pay_capture_id, amazon_pay_authorization_id, capture_reference_id, amazon_capture_id, status, status_reason_code, last_status_updated_at, amount, fee, created_at, updated_at, deleted_at)
        //     VALUES (NEW.id, NEW.amazon_pay_authorization_id, NEW.capture_reference_id, NEW.amazon_capture_id, NEW.status, NEW.status_reason_code, NEW.last_status_updated_at, NEW.amount, NEW.fee, NEW.created_at, NEW.updated_at, NEW.deleted_at)
        // ;');
        // DB::unprepared('
        //     CREATE TRIGGER after_update_amazon_pay_captures
        //     AFTER UPDATE ON amazon_pay_captures
        //     FOR EACH ROW
        //     INSERT INTO amazon_pay_capture_logs (amazon_pay_capture_id, amazon_pay_authorization_id, capture_reference_id, amazon_capture_id, status, status_reason_code, last_status_updated_at, amount, fee, created_at, updated_at, deleted_at)
        //     VALUES (NEW.id, NEW.amazon_pay_authorization_id, NEW.capture_reference_id, NEW.amazon_capture_id, NEW.status, NEW.status_reason_code, NEW.last_status_updated_at, NEW.amount, NEW.fee, NEW.created_at, NEW.updated_at, NEW.deleted_at);
        // ');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // DB::unprepared('DROP TRIGGER after_insert_amazon_pay_captures;');
        // DB::unprepared('DROP TRIGGER after_update_amazon_pay_captures;');
        Schema::dropIfExists('amazon_pay_capture_logs');
        Schema::dropIfExists('amazon_pay_captures');
    }
}
