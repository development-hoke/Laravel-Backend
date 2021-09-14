<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterItemColumnsNullable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('items', function (Blueprint $table) {
            $table->text('size_optional_info')->nullable(true)->comment('サイズ追加情報')->change();
            $table->text('material_info')->nullable(true)->comment('素材情報')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('items', function (Blueprint $table) {
            $table->text('size_optional_info')->nullable(false)->comment('サイズ追加情報')->change();
            $table->text('material_info')->nullable(false)->comment('素材情報')->change();
        });
    }
}
