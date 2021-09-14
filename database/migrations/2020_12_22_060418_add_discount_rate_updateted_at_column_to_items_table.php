<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * @SuppressWarnings(PHPMD.LongClassName)
 */
class AddDiscountRateUpdatetedAtColumnToItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('items', function (Blueprint $table) {
            $table->datetime('discount_rate_updated_at')->nullable(true)->comment('値引率更新日時')->after('discount_rate');
            $table->datetime('member_discount_rate_updated_at')->nullable(true)->comment('値引率更新日時')->after('member_discount_rate');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('items', function (Blueprint $table) {
            $table->dropColumn('discount_rate_updated_at');
            $table->dropColumn('member_discount_rate_updated_at');
        });
    }
}
