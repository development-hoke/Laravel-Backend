<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCartItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cart_items', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('cart_id')->nullable(false)->comment('carts.id');
            $table->unsignedBigInteger('item_detail_id')->nullable(false)->comment('item_details.id');
            $table->unsignedBigInteger('closed_market_id')->nullable(true)->comment('closed_markets.id');
            $table->unsignedInteger('count')->nullable(false)->comment('数量');
            $table->datetime('posted_at')->nullable(false)->comment('カート投入日時');
            $table->timestamps();

            $table->unique(['cart_id', 'item_detail_id']);
            $table->foreign('cart_id')->references('id')->on('carts')->onUpdate('cascade')->onDelete('restrict');
            $table->foreign('item_detail_id')->references('id')->on('item_details')->onUpdate('cascade')->onDelete('restrict');
            $table->foreign('closed_market_id')->references('id')->on('closed_markets')->onUpdate('cascade')->onDelete('restrict');
        });

        Schema::table('carts', function (Blueprint $table) {
            $table->dropColumn('items');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cart_items');

        Schema::table('carts', function (Blueprint $table) {
            $table->json('items')->nullable(false)->comment('カート内の商品情報')->after('member_id');
        });
    }
}
