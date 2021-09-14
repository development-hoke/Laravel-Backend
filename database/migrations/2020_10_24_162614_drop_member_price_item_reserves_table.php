<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropMemberPriceItemReservesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('item_reserves', function (Blueprint $table) {
            $table->dropColumn('member_price');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('item_reserves', function (Blueprint $table) {
            $table->unsignedInteger('member_price')->nullable(false)->comment('会員価格')->after('normal_sale_after_period');
        });
    }
}
