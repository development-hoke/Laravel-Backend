<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTokenUpdatedAtToStaffsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('staffs', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->nullable(false)->unique()->comment('ID')->change();
            $table->string('code', 100)->nullable(false)->unique()->comment('スタッフコード')->after('id');
            $table->dateTime('token_limit', 0)->nullable(true)->comment('トークン期限')->after('role');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('staffs', function (Blueprint $table) {
            $table->dropUnique('staffs_id_unique');
            // MySQLエラーがでるので除外する
            // $table->unsignedBigInteger('id')->nullable(false)->comment('ID')->autoIncrement()->change();
            $table->dropColumn('code');
            $table->dropColumn('token_limit');
        });
    }
}
