<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTaxRateIdToOrderDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('order_details', function (Blueprint $table) {
            $table->unsignedTinyInteger('tax_rate_id')->nullable(false)->default(\App\Enums\OrderDetail\TaxRateId::Rate10)->comment('経営基幹の消費税ID')->after('tax');
        });
        Schema::table('order_detail_logs', function (Blueprint $table) {
            $table->unsignedTinyInteger('tax_rate_id')->nullable(false)->default(\App\Enums\OrderDetail\TaxRateId::Rate10)->comment('経営基幹の消費税ID')->after('tax');
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
            $table->dropColumn('tax_rate_id');
        });
        Schema::table('order_detail_logs', function (Blueprint $table) {
            $table->dropColumn('tax_rate_id');
        });
    }
}
