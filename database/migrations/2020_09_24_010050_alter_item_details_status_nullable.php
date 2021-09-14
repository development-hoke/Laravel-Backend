<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterItemDetailsStatusNullable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('item_details', function (Blueprint $table) {
            $table->boolean('status')->nullable(false)->default(\App\Enums\Common\Status::Unpublished)->comment('公開ステータス')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('item_details', function (Blueprint $table) {
            $table->boolean('status')->nullable()->default(\App\Enums\Common\Status::Unpublished)->comment('公開ステータス')->change();
        });
    }
}
