<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropColumnsFromItemReservesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('item_reserves', function (Blueprint $table) {
            $table->dropColumn('normal_sale_after_period');
            $table->dropColumn('point_rate');
            $table->string('expected_arrival_date')->nullable(false)->comment('入荷予定日')->change();
            $table->boolean('is_free_delivery')->nullable(false)->comment('送料無料設定')->after('reserve_price');
            $table->boolean('is_enable')->nullable(false)->comment('予約を受け付ける')->after('item_id');
        });
        Schema::table('items', function (Blueprint $table) {
            $table->dropColumn('reserve_status');
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
            $table->boolean('normal_sale_after_period')->nullable(false)->default(false)->comment('期間終了後の通常販売')->after('period_to');
            $table->decimal('point_rate')->nullable(false)->comment('ポイント付与率')->after('normal_sale_after_period');
            $table->dropColumn('expected_arrival_date');
            $table->dropColumn('is_free_delivery');
            $table->dropColumn('is_enable');
        });
        Schema::table('item_reserves', function (Blueprint $table) {
            $table->datetime('expected_arrival_date')->nullable(false)->comment('入荷予定日')->after('out_of_stock_threshold');
        });
        Schema::table('items', function (Blueprint $table) {
            $table->unsignedTinyInteger('reserve_status')->nullable()->default(\App\Enums\Item\ReserveStatus::Normal)->comment('予約販売ステータス')->after('status');
        });
    }
}
