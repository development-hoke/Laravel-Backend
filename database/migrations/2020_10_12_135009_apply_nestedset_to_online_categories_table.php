<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Kalnoy\Nestedset\NestedSet;

class ApplyNestedsetToOnlineCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('online_categories', function (Blueprint $table) {
            $table->unsignedInteger(NestedSet::LFT)->default(0)->after('name');
            $table->unsignedInteger(NestedSet::RGT)->default(0)->after(NestedSet::LFT);
            $table->index(NestedSet::getDefaultColumns());
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('online_categories', function (Blueprint $table) {
            $table->dropIndex(NestedSet::getDefaultColumns());
            $table->dropColumn(NestedSet::LFT);
            $table->dropColumn(NestedSet::RGT);
        });
    }
}
