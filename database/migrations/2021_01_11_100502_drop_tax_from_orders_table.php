<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropTaxFromOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('tax');
        });
        Schema::table('order_logs', function (Blueprint $table) {
            $table->dropColumn('tax');
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
            $table->decimal('tax')->nullable(false)->comment('消費税')->after('price');
        });
        Schema::table('order_logs', function (Blueprint $table) {
            $table->decimal('tax')->nullable(false)->comment('消費税')->after('price');
        });
    }
}
