<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveIconIdFromEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropForeign('events_icon_id_foreign');
            $table->dropIndex('events_icon_id_index');
            $table->dropColumn('icon_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('events', function (Blueprint $table) {
            $table->bigInteger('icon_id')->unsigned()->nullable(false)->index()->comment('icons.id');
            $table->foreign('icon_id')->references('id')->on('icons')->onUpdate('cascade')->onDelete('restrict');
        });
    }
}
