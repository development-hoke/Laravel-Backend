<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeOrderNpsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('order_nps', function (Blueprint $table) {
            $table->dropColumn('authori_result');
            $table->dropColumn('authori_required_date');
            $table->dropColumn('authori_ng');
            $table->dropColumn('authori_hold');
            $table->softDeletes('deleted_at', 0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('order_nps', function (Blueprint $table) {
            $table->string('authori_result', 4)->nullable(false)->comment('与信結果');
            $table->string('authori_required_date', 50)->nullable(true)->comment('結果確定日時');
            $table->string('authori_ng', 10)->nullable(true)->comment('与信NG事由');
            $table->string('authori_hold', 200)->nullable(true)->comment('与信保留事由');
            $table->dropColumn('deleted_at');
        });
    }
}
