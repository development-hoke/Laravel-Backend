<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDeletedAtToCartItems extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cart_items', function (Blueprint $table) {
            $table->boolean('invalid')->default(false)->nullable(false)->comment('無効化した商品。再投入可能。')->after('count');
            $table->unsignedTinyInteger('invalid_reason')->nullable(true)->comment('無効化した商品の理由。')->after('invalid');
            $table->softDeletes('deleted_at', 0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cart_items', function (Blueprint $table) {
            $table->dropColumn('invalid');
            $table->dropColumn('invalid_reason');
            $table->dropColumn('deleted_at');
        });
    }
}
