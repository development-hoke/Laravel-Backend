<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropOrderRevisionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('order_revisions');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::create('order_revisions', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('ID');
            $table->bigInteger('original_order_id')->unsigned()->nullable(false)->index()->comment('初回受注注文ID');
            $table->bigInteger('latest_order_id')->unsigned()->nullable(false)->index()->comment('最新の注文ID');
            $table->text('memo')->nullable(false)->comment('変更理由');
            $table->softDeletes('deleted_at', 0);
            $table->timestamps();

            $table->foreign('original_order_id')->references('id')->on('orders')->onUpdate('cascade')->onDelete('restrict');
            $table->foreign('latest_order_id')->references('id')->on('orders')->onUpdate('cascade')->onDelete('restrict');
        });
    }
}
