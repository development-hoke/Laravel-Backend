<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_details', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('ID');
            $table->bigInteger('order_id')->unsigned()->nullable(false)->index()->comment('');
            $table->bigInteger('item_id')->unsigned()->nullable(false)->index()->comment('');
            $table->unsignedInteger('retail_price')->nullable(false)->comment('上代');
            $table->unsignedInteger('amount')->nullable(false)->default(1)->comment('購入数');
            $table->decimal('discount_rate')->nullable(true)->comment('値引率');
            $table->softDeletes('deleted_at', 0);
            $table->timestamps();

            $table->foreign('order_id')->references('id')->on('orders')->onUpdate('cascade')->onDelete('restrict');
            $table->foreign('item_id')->references('id')->on('items')->onUpdate('cascade')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order_details');
    }
}
