<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNpRejectedTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('np_rejected_transactions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('member_id')->unsigned()->nullable(false)->comment('会員ID');
            $table->bigInteger('cart_id')->unsigned()->nullable(false)->comment('carts.id');
            $table->string('shop_transaction_id', 80)->nullable(false)->comment('加盟店取引ID');
            $table->string('np_transaction_id', 22)->nullable(false)->comment('NP取引ID');
            $table->string('authori_result', 4)->nullable(false)->comment('与信結果コード');
            $table->dateTime('authori_required_date')->nullable(true)->comment('結果確定日時');
            $table->string('authori_ng', 10)->nullable(true)->comment('与信NG事由コード');
            $table->text('authori_hold')->nullable(true)->comment('与信保留事由コード(複数件のコードをJSONで保存)');
            $table->text('error_codes')->nullable(true)->comment('エラーコード(複数件のコードをJSONで保存)');
            $table->timestamps();

            $table->index('member_id');
            $table->index('cart_id');
            $table->index('np_transaction_id');
        });
        Schema::table('order_nps', function (Blueprint $table) {
            $table->dropIndex('order_nps_order_id_index');
            $table->foreign('order_id')->references('id')->on('orders')->onUpdate('cascade')->onDelete('restrict');
            $table->index('np_transaction_id');
            $table->dateTime('authori_required_date')->nullable(true)->comment('結果確定日時')->after('np_transaction_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('np_rejected_transactions');
        Schema::table('order_nps', function (Blueprint $table) {
            $table->dropForeign('order_nps_order_id_foreign');
            $table->index('order_id');
            $table->dropIndex('order_nps_np_transaction_id_index');
            $table->dropColumn('authori_required_date');
        });
    }
}
