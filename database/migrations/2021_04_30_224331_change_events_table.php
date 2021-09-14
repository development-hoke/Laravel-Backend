<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasColumn('events', 'is_delivery_setting')) {
            Schema::table('events', function (Blueprint $table) {
                $table->dropColumn('is_delivery_setting');
            });
        }
        if (Schema::hasColumn('events', 'delivery_condition')) {
            Schema::table('events', function (Blueprint $table) {
                $table->dropColumn('delivery_condition');
            });
        }
        if (Schema::hasColumn('events', 'delivery_price')) {
            Schema::table('events', function (Blueprint $table) {
                $table->dropColumn('delivery_price');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('events', function (Blueprint $table) {
            $table->boolean('is_delivery_setting')->nullable(false)->default(false)->comment('配送料設定');
            $table->unsignedInteger('delivery_condition')->nullable(true)->comment('配送料割引条件');
            $table->unsignedInteger('delivery_price')->nullable(true)->comment('配送料');
        });
    }
}
