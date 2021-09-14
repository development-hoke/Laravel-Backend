<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIndexToOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->index(['member_id', 'deleted_at'], 'idx_orders_01');
            $table->index(['code', 'deleted_at'], 'idx_orders_02');
            $table->index(['member_id', 'code', 'deleted_at'], 'idx_orders_03');
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
            $table->dropIndex('idx_orders_01');
            $table->dropIndex('idx_orders_02');
            $table->dropIndex('idx_orders_03');
        });
    }
}
