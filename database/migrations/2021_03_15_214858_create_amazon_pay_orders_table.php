<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateAmazonPayOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @see https://developer.amazon.com/ja/docs/amazon-pay-api/orderreferencedetails.html
     *
     * @return void
     */
    public function up()
    {
        Schema::create('amazon_pay_orders', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('order_id')->nullable(false)->comment('orders.id');
            $table->string('order_reference_id', 120)->nullable(false)->comment('Amazonボタンウィジェットから返されたOrder ReferenceのID');
            $table->string('status', 30)->nullable(false)->default(\App\Enums\AmazonPay\Status\OrderReference::Draft)->comment('OrderReferenceStatus.state');
            $table->string('status_reason_code', 60)->nullable(true)->comment('OrderReferenceStatus.ReasonCode');
            $table->datetime('last_status_updated_at')->nullable(true)->comment('OrderReferenceStatus.LastUpdateTimestamp (Amazon側で保存されている値)');
            $table->integer('amount')->nullable(false)->comment('注文金額');
            $table->datetime('expiration_at')->nullable(false)->comment('Order Referenceの有効期限の日時');
            $table->timestamps();
            $table->softDeletes('deleted_at', 0);

            $table->unique('order_reference_id');
            $table->unique('order_id');
            $table->foreign('order_id')->references('id')->on('orders')->onUpdate('cascade')->onDelete('restrict');
        });
        Schema::create('amazon_pay_order_logs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->datetime('logged_at')->nullable(false)->useCurrent()->comment('ログ作成日時');
            $table->unsignedBigInteger('amazon_pay_order_id')->nullable(false)->comment('amazon_pay_orders.id');
            $table->unsignedBigInteger('order_id')->nullable(false)->comment('orders.id');
            $table->string('order_reference_id', 120)->nullable(false)->comment('Amazonボタンウィジェットから返されたOrder ReferenceのID');
            $table->string('status', 30)->nullable(false)->default(\App\Enums\AmazonPay\Status\OrderReference::Draft)->comment('OrderReferenceStatus.state');
            $table->string('status_reason_code', 60)->nullable(true)->comment('OrderReferenceStatus.ReasonCode');
            $table->datetime('last_status_updated_at')->nullable(true)->comment('OrderReferenceStatus.LastUpdateTimestamp (Amazon側で保存されている値)');
            $table->integer('amount')->nullable(false)->comment('注文金額');
            $table->datetime('expiration_at')->nullable(false)->comment('Order Referenceの有効期限の日時');
            $table->timestamps();
            $table->softDeletes('deleted_at', 0);

            $table->foreign('amazon_pay_order_id')->references('id')->on('amazon_pay_orders')->onUpdate('cascade')->onDelete('restrict');
        });

        // DB::unprepared('
        //     CREATE TRIGGER after_insert_amazon_pay_orders
        //     AFTER INSERT ON amazon_pay_orders
        //     FOR EACH ROW
        //     INSERT INTO amazon_pay_order_logs (amazon_pay_order_id, order_id, order_reference_id, status, status_reason_code, last_status_updated_at, amount, expiration_at, created_at, updated_at, deleted_at)
        //     VALUES (NEW.id, NEW.order_id, NEW.order_reference_id, NEW.status, NEW.status_reason_code, NEW.last_status_updated_at, NEW.amount, NEW.expiration_at, NEW.created_at, NEW.updated_at, NEW.deleted_at)
        // ;');
        // DB::unprepared('
        //     CREATE TRIGGER after_update_amazon_pay_orders
        //     AFTER UPDATE ON amazon_pay_orders
        //     FOR EACH ROW
        //     INSERT INTO amazon_pay_order_logs (amazon_pay_order_id, order_id, order_reference_id, status, status_reason_code, last_status_updated_at, amount, expiration_at, created_at, updated_at, deleted_at)
        //     VALUES (NEW.id, NEW.order_id, NEW.order_reference_id, NEW.status, NEW.status_reason_code, NEW.last_status_updated_at, NEW.amount, NEW.expiration_at, NEW.created_at, NEW.updated_at, NEW.deleted_at);
        // ');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // DB::unprepared('DROP TRIGGER after_insert_amazon_pay_orders;');
        // DB::unprepared('DROP TRIGGER after_update_amazon_pay_orders;');
        Schema::dropIfExists('amazon_pay_order_logs');
        Schema::dropIfExists('amazon_pay_orders');
    }
}
