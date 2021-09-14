<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCartsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('carts', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('ID');
            $table->string('token', 64)->nullable(false)->unique()->comment('トークン');
            $table->bigInteger('member_id')->unsigned()->nullable(true)->index()->comment('users.id');
            $table->json('items')->nullable(false)->comment('カート内の商品情報');
            $table->json('use_coupon_ids')->nullable(true)->comment('使用クーポンID');
            $table->softDeletes('deleted_at', 0);
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
        Schema::dropIfExists('carts');
    }
}
