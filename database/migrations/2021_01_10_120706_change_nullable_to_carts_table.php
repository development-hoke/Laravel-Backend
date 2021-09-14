<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeNullableToCartsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('carts', function (Blueprint $table) {
            $table->unsignedInteger('order_type')->nullable(true)->default(null)->comment('注文タイプ')->after('use_coupon_ids')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('carts', function (Blueprint $table) {
            $table->unsignedInteger('order_type')->nullable()->default(\App\Enums\Order\OrderType::Normal)->comment('注文タイプ')->after('use_coupon_ids')->change();
        });
    }
}
