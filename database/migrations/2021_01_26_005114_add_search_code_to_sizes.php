<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSearchCodeToSizes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sizes', function (Blueprint $table) {
            $table->string('code', 2)->nullable(false)->comment('コード')->after('id');
            $table->string('search_code', 2)->nullable(false)->comment('検索用コード')->after('code');
            $table->dropColumn('body');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sizes', function (Blueprint $table) {
            $table->dropColumn('code');
            $table->dropColumn('search_code');
            $table->text('body')->nullable(false)->comment('サイズ情報')->after('name');
        });
    }
}
