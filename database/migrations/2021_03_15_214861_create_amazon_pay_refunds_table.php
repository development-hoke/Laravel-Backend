<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateAmazonPayRefundsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @see https://developer.amazon.com/ja/docs/amazon-pay-api/refund.html
     *
     * @return void
     */
    public function up()
    {
        Schema::create('amazon_pay_refunds', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('amazon_pay_capture_id')->nullable(false)->comment('amazon_pay_captures.id');
            $table->string('refund_reference_id', 32)->nullable(false)->comment('RefundReferenceId (EC側で生成する)');
            $table->string('amazon_refund_id', 120)->nullable(false)->comment('Amazonが生成したこの返金トランザクションのID');
            $table->string('status', 30)->nullable(false)->comment('RefundStatus.state');
            $table->string('status_reason_code', 60)->nullable(true)->comment('RefundStatus.ReasonCode');
            $table->datetime('last_status_updated_at')->nullable(true)->comment('CapturRefundStatuseStatus.LastUpdateTimestamp (Amazon側で保存されている値)');
            $table->integer('amount')->nullable(false)->comment('返金金額');
            $table->integer('fee')->nullable(false)->comment('返金で請求される手数料');
            $table->timestamps();
            $table->softDeletes('deleted_at', 0);

            $table->unique('refund_reference_id');
            $table->unique('amazon_refund_id');
            $table->foreign('amazon_pay_capture_id')->references('id')->on('amazon_pay_captures')->onUpdate('cascade')->onDelete('restrict');
        });
        Schema::create('amazon_pay_refund_logs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->datetime('logged_at')->nullable(false)->useCurrent()->comment('ログ作成日時');
            $table->unsignedBigInteger('amazon_pay_refund_id')->nullable(false)->comment('amazon_pay_refunds.id');
            $table->unsignedBigInteger('amazon_pay_capture_id')->nullable(false)->comment('amazon_pay_captures.id');
            $table->string('refund_reference_id', 32)->nullable(false)->comment('RefundReferenceId (EC側で生成する)');
            $table->string('amazon_refund_id', 120)->nullable(false)->comment('Amazonが生成したこの返金トランザクションのID');
            $table->string('status', 30)->nullable(false)->comment('RefundStatus.state');
            $table->string('status_reason_code', 60)->nullable(true)->comment('RefundStatus.ReasonCode');
            $table->datetime('last_status_updated_at')->nullable(true)->comment('CapturRefundStatuseStatus.LastUpdateTimestamp (Amazon側で保存されている値)');
            $table->integer('amount')->nullable(false)->comment('返金金額');
            $table->integer('fee')->nullable(false)->comment('返金で請求される手数料');
            $table->timestamps();
            $table->softDeletes('deleted_at', 0);

            $table->foreign('amazon_pay_refund_id')->references('id')->on('amazon_pay_refunds')->onUpdate('cascade')->onDelete('restrict');
        });
        // DB::unprepared('
        //     CREATE TRIGGER after_insert_amazon_pay_refunds
        //     AFTER INSERT ON amazon_pay_refunds
        //     FOR EACH ROW
        //     INSERT INTO amazon_pay_refund_logs (amazon_pay_refund_id, amazon_pay_capture_id, refund_reference_id, amazon_refund_id, status, status_reason_code, last_status_updated_at, amount, fee, created_at, updated_at, deleted_at)
        //     VALUES (NEW.id, NEW.amazon_pay_capture_id, NEW.refund_reference_id, NEW.amazon_refund_id, NEW.status, NEW.status_reason_code, NEW.last_status_updated_at, NEW.amount, NEW.fee, NEW.created_at, NEW.updated_at, NEW.deleted_at)
        // ;');
        // DB::unprepared('
        //     CREATE TRIGGER after_update_amazon_pay_refunds
        //     AFTER UPDATE ON amazon_pay_refunds
        //     FOR EACH ROW
        //     INSERT INTO amazon_pay_refund_logs (amazon_pay_refund_id, amazon_pay_capture_id, refund_reference_id, amazon_refund_id, status, status_reason_code, last_status_updated_at, amount, fee, created_at, updated_at, deleted_at)
        //     VALUES (NEW.id, NEW.amazon_pay_capture_id, NEW.refund_reference_id, NEW.amazon_refund_id, NEW.status, NEW.status_reason_code, NEW.last_status_updated_at, NEW.amount, NEW.fee, NEW.created_at, NEW.updated_at, NEW.deleted_at);
        // ;');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // DB::unprepared('DROP TRIGGER after_insert_amazon_pay_refunds;');
        // DB::unprepared('DROP TRIGGER after_update_amazon_pay_refunds;');
        Schema::dropIfExists('amazon_pay_refund_logs');
        Schema::dropIfExists('amazon_pay_refunds');
    }
}
