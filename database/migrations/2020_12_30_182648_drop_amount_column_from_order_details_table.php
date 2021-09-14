<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropAmountColumnFromOrderDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('order_details', function (Blueprint $table) {
            $table->dropColumn('amount');
        });
        Schema::table('order_detail_logs', function (Blueprint $table) {
            $table->dropColumn('amount');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('order_details', function (Blueprint $table) {
            $table->unsignedInteger('amount')->nullable(false)->default(1)->comment('購入数')->after('retail_price');
        });
        Schema::table('order_detail_logs', function (Blueprint $table) {
            $table->unsignedInteger('amount')->nullable(false)->default(1)->comment('購入数')->after('retail_price');
        });
    }
}
