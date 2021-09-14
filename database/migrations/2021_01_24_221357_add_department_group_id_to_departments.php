<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDepartmentGroupIdToDepartments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::disableForeignKeyConstraints();
        Schema::table('departments', function (Blueprint $table) {
            $table->bigInteger('department_group_id')->unsigned()->nullable(false)->index()->comment('departments.id')->after('id');
            $table->foreign('department_group_id')->references('id')->on('department_groups')->onUpdate('cascade')->onDelete('restrict');
        });
        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::disableForeignKeyConstraints();
        Schema::table('departments', function (Blueprint $table) {
            $table->dropForeign('departments_department_group_id_foreign');
            $table->dropColumn('department_group_id');
        });
        Schema::enableForeignKeyConstraints();
    }
}
