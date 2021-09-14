<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddIndexToCartItemsAndItemDetailIndent extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('carts', function (Blueprint $table) {
            $table->dropIndex('carts_token_deleted_at_index');
            $table->string('token', 64)->nullable(true)->comment('トークン')->change();
            $table->unique('token');
        });
        Schema::table('cart_items', function (Blueprint $table) {
            DB::statement('SET FOREIGN_KEY_CHECKS = 0');

            $table->index('posted_at');

            $table->dropForeign('cart_items_cart_id_foreign');
            $table->dropForeign('cart_items_item_detail_id_foreign');

            $table->dropUnique('cart_items_cart_id_item_detail_id_unique');

            $table->foreign('cart_id')->references('id')->on('carts')->onUpdate('cascade')->onDelete('restrict');
            $table->foreign('item_detail_id')->references('id')->on('item_details')->onUpdate('cascade')->onDelete('restrict');

            DB::statement('SET FOREIGN_KEY_CHECKS = 1');
        });
        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex('orders_code_index');
            $table->unique('code');
        });
        Schema::table('item_detail_identifications', function (Blueprint $table) {
            $table->index('arrival_date');
        });
        Schema::table('order_discounts', function (Blueprint $table) {
            $table->dropIndex('order_discounts_orderable_type_orderable_id_index');
            $table->dropIndex('order_discounts_discountable_type_discountable_id_index');
            $table->index(['orderable_id', 'orderable_type']);
            $table->index(['discountable_id', 'discountable_type']);
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
            $table->index(['token', 'deleted_at']);
            $table->dropUnique('carts_token_unique');
        });
        Schema::table('cart_items', function (Blueprint $table) {
            $table->dropIndex('cart_items_posted_at_index');
            $table->unique(['cart_id', 'item_detail_id']);
        });
        Schema::table('orders', function (Blueprint $table) {
            $table->dropUnique('orders_code_unique');
            $table->index('code');
        });
        Schema::table('item_detail_identifications', function (Blueprint $table) {
            $table->dropIndex('item_detail_identifications_arrival_date_index');
        });
        Schema::table('order_discounts', function (Blueprint $table) {
            $table->dropIndex('order_discounts_orderable_id_orderable_type_index');
            $table->dropIndex('order_discounts_discountable_id_discountable_type_index');
            $table->index(['orderable_type', 'orderable_id']);
            $table->index(['discountable_type', 'discountable_id']);
        });
    }
}
