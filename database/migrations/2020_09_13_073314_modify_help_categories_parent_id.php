<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyHelpCategoriesParentId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('help_categories', function (Blueprint $table) {
            $table->bigInteger('parent_id')->unsigned()->nullable(true)->comment('親ID')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('help_categories', function (Blueprint $table) {
            $table->bigInteger('parent_id')->unsigned()->nullable(false)->comment('親ID')->change();
        });
    }
}
