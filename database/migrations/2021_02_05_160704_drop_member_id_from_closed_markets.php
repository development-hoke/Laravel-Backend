<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropMemberIdFromClosedMarkets extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('closed_markets', function (Blueprint $table) {
            $table->dropColumn('member_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('closed_markets', function (Blueprint $table) {
            $table->bigInteger('member_id')->unsigned()->nullable(false)->index()->comment('users.id')->after('id');
        });
    }
}
