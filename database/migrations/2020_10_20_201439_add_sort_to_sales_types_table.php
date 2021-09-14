<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSortToSalesTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sales_types', function (Blueprint $table) {
            $table->bigInteger('sort')->unsigned()->nullable(false)->default(0)->index()->comment('順序')->after('text_color');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sales_types', function (Blueprint $table) {
            $table->dropColumn('sort');
        });
    }
}
