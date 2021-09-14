<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderCreditsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_credits', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('ID');
            $table->bigInteger('order_id')->unsigned()->nullable(false)->index()->comment('受注ID');
            $table->string('authorization_number', 6)->nullable(false)->comment('承認番号');
            $table->string('transaction_number', 20)->nullable(false)->comment('取引番号');
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
        Schema::dropIfExists('order_credits');
    }
}
