<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyAmazonPayLoggedAt extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('amazon_pay_order_logs', function (Blueprint $table) {
            $table->dropForeign('amazon_pay_order_logs_amazon_pay_order_id_foreign');
            $table->index('amazon_pay_order_id');
        });
        Schema::table('amazon_pay_authorization_logs', function (Blueprint $table) {
            $table->dropForeign('amazon_pay_authorization_logs_foreign1');
            $table->index('amazon_pay_authorization_id');
        });
        Schema::table('amazon_pay_capture_logs', function (Blueprint $table) {
            $table->dropForeign('amazon_pay_capture_logs_amazon_pay_capture_id_foreign');
            $table->index('amazon_pay_capture_id');
        });
        Schema::table('amazon_pay_refund_logs', function (Blueprint $table) {
            $table->dropForeign('amazon_pay_refund_logs_amazon_pay_refund_id_foreign');
            $table->index('amazon_pay_refund_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('amazon_pay_order_logs', function (Blueprint $table) {
            $table->foreign('amazon_pay_order_id')->references('id')->on('amazon_pay_orders')->onUpdate('cascade')->onDelete('restrict');
            $table->dropIndex('amazon_pay_order_logs_amazon_pay_order_id_index');
        });
        Schema::table('amazon_pay_authorization_logs', function (Blueprint $table) {
            $table->foreign('amazon_pay_authorization_id', 'amazon_pay_authorization_logs_foreign1')->references('id')->on('amazon_pay_authorizations')->onUpdate('cascade')->onDelete('restrict');
            $table->dropIndex('amazon_pay_authorization_logs_amazon_pay_authorization_id_index');
        });
        Schema::table('amazon_pay_capture_logs', function (Blueprint $table) {
            $table->foreign('amazon_pay_capture_id')->references('id')->on('amazon_pay_captures')->onUpdate('cascade')->onDelete('restrict');
            $table->dropIndex('amazon_pay_capture_logs_amazon_pay_capture_id_index');
        });
        Schema::table('amazon_pay_refund_logs', function (Blueprint $table) {
            $table->foreign('amazon_pay_refund_id')->references('id')->on('amazon_pay_refunds')->onUpdate('cascade')->onDelete('restrict');
            $table->dropIndex('amazon_pay_refund_logs_amazon_pay_refund_id_index');
        });
    }
}
