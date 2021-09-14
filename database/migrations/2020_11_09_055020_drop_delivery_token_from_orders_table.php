<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropDeliveryTokenFromOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('delivery_token');
        });
        Schema::table('order_logs', function (Blueprint $table) {
            $table->dropColumn('delivery_token');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('delivery_token', 32)->nullable(false)->comment('配送先トークン')->after('delivery_type');
        });
        Schema::table('order_logs', function (Blueprint $table) {
            $table->string('delivery_token', 32)->nullable(false)->comment('配送先トークン')->after('delivery_type');
        });
    }
}
