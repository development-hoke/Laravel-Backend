<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTypeToOrderMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('order_messages', function (Blueprint $table) {
            $table->text('body')->nullable(true)->comment('本文')->change();
            $table->unsignedTinyInteger('type')->nullable()->default(\App\Enums\OrderMessage\Type::Store)->comment('種類')->after('body');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('order_messages', function (Blueprint $table) {
            $table->text('body')->nullable(false)->comment('本文')->change();
            $table->dropColumn('type');
        });
    }
}
