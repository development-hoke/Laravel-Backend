<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStaffsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('staffs', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('ID');
            $table->string('name', 255)->nullable(false)->comment('スタッフ名');
            $table->string('token', 255)->nullable(false)->comment('APIトークン');
            $table->unsignedTinyInteger('role')->nullable()->default(\App\Enums\Staff\Role::Outside)->comment('権限');
            $table->softDeletes('deleted_at', 0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('staffs');
    }
}
