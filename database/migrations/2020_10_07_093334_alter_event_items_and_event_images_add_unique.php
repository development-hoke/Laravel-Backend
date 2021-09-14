<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterEventItemsAndEventImagesAddUnique extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('event_items', function (Blueprint $table) {
            $table->unique(['event_id', 'item_id']);
        });
        Schema::table('event_users', function (Blueprint $table) {
            $table->unique(['event_id', 'member_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('event_items', function (Blueprint $table) {
            $table->dropUnique('event_items_event_id_item_id_unique');
        });
        Schema::table('event_users', function (Blueprint $table) {
            $table->dropUnique('event_users_event_id_member_id_unique');
        });
    }
}
