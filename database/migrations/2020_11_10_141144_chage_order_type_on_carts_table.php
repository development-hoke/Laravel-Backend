<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class ChageOrderTypeOnCartsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $default = \App\Enums\Order\OrderType::Normal;
        DB::statement("ALTER TABLE carts MODIFY order_type INT(10) UNSIGNED NOT NULL DEFAULT {$default} COMMENT '注文タイプ' AFTER use_coupon_ids");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $default = \App\Enums\Order\OrderType::Normal;
        DB::statement("ALTER TABLE carts MODIFY order_type INT(10) UNSIGNED NOT NULL DEFAULT {$default} COMMENT '注文タイプ' AFTER updated_at");
    }
}
