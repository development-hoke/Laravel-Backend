<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateItemDetailRedisplayRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('item_detail_redisplay_requests', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('item_detail_id')->unsigned()->nullable(false)->index()->comment('商品詳細ID');
            $table->bigInteger('member_id')->unsigned()->nullable(true)->index()->comment('会員ID');
            $table->string('user_token', 255)->nullable(true)->index()->comment('ユーザートークン');
            $table->string('user_name', 255)->nullable(false)->comment('氏名');
            $table->string('email', 255)->nullable(false)->comment('入荷時お知らせ先メールアドレス');
            $table->boolean('is_notified')->nullable(false)->default(0)->comment('通知済みフラグ');

            $table->foreign('item_detail_id')->references('id')->on('item_details')->onUpdate('cascade')->onDelete('restrict');
            $table->unique(['email', 'item_detail_id']);
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
        Schema::dropIfExists('item_detail_redisplay_requests');
    }
}
