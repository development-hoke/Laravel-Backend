<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropYamadayaUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign('orders_user_id_foreign');
            $table->dropIndex('orders_user_id_index');
            $table->index('user_id', 'orders_member_id_index');
            $table->renameColumn('user_id', 'member_id');
        });
        Schema::table('event_users', function (Blueprint $table) {
            $table->dropForeign('event_users_user_id_foreign');
            $table->dropIndex('event_users_user_id_index');
            $table->index('user_id', 'event_users_member_id_index');
            $table->renameColumn('user_id', 'member_id');
        });
        Schema::table('item_favorites', function (Blueprint $table) {
            $table->dropForeign('item_favorites_user_id_foreign');
            $table->dropIndex('item_favorites_user_id_index');
            $table->index('user_id', 'item_favorites_member_id_index');
            $table->renameColumn('user_id', 'member_id');
        });
        Schema::table('item_detail_requests', function (Blueprint $table) {
            $table->dropForeign('item_detail_requests_user_id_foreign');
            $table->dropIndex('item_detail_requests_user_id_index');
            $table->index('user_id', 'item_detail_requests_member_id_index');
            $table->renameColumn('user_id', 'member_id');
        });
        Schema::table('closed_markets', function (Blueprint $table) {
            $table->dropForeign('closed_markets_user_id_foreign');
            $table->dropIndex('closed_markets_user_id_index');
            $table->index('user_id', 'closed_markets_member_id_index');
            $table->renameColumn('user_id', 'member_id');
        });

        Schema::dropIfExists('users');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('ID');
            $table->string('token', 64)->nullable(false)->unique()->comment('トークン');
            $table->softDeletes('deleted_at', 0);
            $table->timestamps();
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->renameColumn('member_id', 'user_id');
            $table->dropIndex('orders_member_id_index');
            $table->index('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('restrict');
        });
        Schema::table('event_users', function (Blueprint $table) {
            $table->renameColumn('member_id', 'user_id');
            $table->dropIndex('event_users_member_id_index');
            $table->index('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('restrict');
        });
        Schema::table('item_favorites', function (Blueprint $table) {
            $table->renameColumn('member_id', 'user_id');
            $table->dropIndex('item_favorites_member_id_index');
            $table->index('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('restrict');
        });
        Schema::table('item_detail_requests', function (Blueprint $table) {
            $table->renameColumn('member_id', 'user_id');
            $table->dropIndex('item_detail_requests_member_id_index');
            $table->index('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('restrict');
        });
        Schema::table('closed_markets', function (Blueprint $table) {
            $table->renameColumn('member_id', 'user_id');
            $table->dropIndex('closed_markets_member_id_index');
            $table->index('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('restrict');
        });
    }
}
