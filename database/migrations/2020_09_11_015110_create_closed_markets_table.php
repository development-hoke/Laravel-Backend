<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClosedMarketsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('closed_markets', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('ID');
            $table->bigInteger('user_id')->unsigned()->nullable(false)->index()->comment('users.id');
            $table->bigInteger('item_detail_id')->unsigned()->nullable(false)->index()->comment('item_details.id');
            $table->string('slug', 255)->nullable(false)->unique()->comment('スラッグ');
            $table->string('title', 255)->nullable(false)->comment('タイトル');
            $table->string('password', 255)->nullable(false)->comment('パスワード');
            $table->unsignedInteger('num')->nullable(false)->comment('個数');
            $table->datetime('limit_at')->nullable(false)->comment('有効期限');
            $table->softDeletes('deleted_at', 0);
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('restrict');
            $table->foreign('item_detail_id')->references('id')->on('item_details')->onUpdate('cascade')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('closed_markets');
    }
}
