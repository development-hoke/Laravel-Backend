<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderNpsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_nps', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('ID');
            $table->bigInteger('order_id')->unsigned()->nullable(false)->index()->comment('受注ID');
            $table->string('shop_transaction_id', 80)->nullable(false)->comment('加盟店取引ID');
            $table->string('np_transaction_id', 22)->nullable(false)->comment('NP取引ID');
            $table->string('authori_result', 4)->nullable(false)->comment('与信結果');
            $table->string('authori_required_date', 50)->nullable(true)->comment('結果確定日時');
            $table->string('authori_ng', 10)->nullable(true)->comment('与信NG事由');
            $table->string('authori_hold', 200)->nullable(true)->comment('与信保留事由');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order_nps');
    }
}
