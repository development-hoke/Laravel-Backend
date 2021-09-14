<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddGuestRelatedColumnsToCarts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('carts', function (Blueprint $table) {
            $table->string('guest_token', 32)->nullable(true)->comment('ゲスト認証トークン')->after('order_type');
            $table->dateTime('guest_token_created_at')->nullable(true)->comment('ゲスト認証トークン作成日時')->after('guest_token');
            $table->boolean('guest_verified')->nullable(false)->default(false)->comment('ゲスト購入として認証済み')->after('guest_token_created_at');
            $table->dateTime('guest_verified_at')->nullable(true)->comment('ゲスト購入として認証した日時')->after('guest_verified');
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
            $table->dropColumn('guest_token');
            $table->dropColumn('guest_token_created_at');
            $table->dropColumn('guest_verified');
            $table->dropColumn('guest_verified_at');
        });
    }
}
