<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdminLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('admin_logs', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('ID');
            $table->bigInteger('staff_id')->unsigned()->nullable(false)->index()->comment('staffs.id');
            $table->string('page', 255)->nullable(false)->unique()->comment('表示ページ');
            $table->unsignedTinyInteger('type')->nullable()->default(\App\Enums\AdminLog\Type::Read)->comment('ログタイプ');
            $table->string('ip', 15)->nullable(false)->comment('変更理由');
            $table->text('memo')->nullable(true)->comment('memo');
            $table->softDeletes('deleted_at', 0);
            $table->timestamps();

            $table->foreign('staff_id')->references('id')->on('staffs')->onUpdate('cascade')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('admin_logs');
    }
}
