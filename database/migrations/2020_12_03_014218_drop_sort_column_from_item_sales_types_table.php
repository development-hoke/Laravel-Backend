<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropSortColumnFromItemSalesTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('item_sales_types', function (Blueprint $table) {
            $table->dropColumn('sort');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('item_sales_types', function (Blueprint $table) {
            $table->bigInteger('sort')->unsigned()->nullable(false)->default(0)->comment('優先順')->after('sales_type_id');
        });
    }
}
