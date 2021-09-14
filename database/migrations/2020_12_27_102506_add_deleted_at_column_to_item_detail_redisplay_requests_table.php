<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * @SuppressWarnings(PHPMD.LongClassName)
 */
class AddDeletedAtColumnToItemDetailRedisplayRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('item_detail_redisplay_requests', function (Blueprint $table) {
            $table->datetime('deleted_at')->nullable(true);
            $table->dropUnique('item_detail_redisplay_requests_email_item_detail_id_unique');
            $table->index('email');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('item_detail_redisplay_requests', function (Blueprint $table) {
            $table->dropColumn('deleted_at');
            $table->dropIndex('item_detail_redisplay_requests_email_index');
            $table->unique(['email', 'item_detail_id']);
        });
    }
}
