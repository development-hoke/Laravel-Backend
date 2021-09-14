<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderChangeHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_change_histories', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('order_id')->unsigned()->nullable(false)->index()->comment('受注ID');
            $table->string('log_type', 255)->nullable(false)->comment('関連ログタイプ');
            $table->unsignedBigInteger('log_id')->nullable(false)->comment('関連ログID');
            $table->unsignedBigInteger('staff_id')->nullable(false)->comment('スタッフID');
            $table->unsignedTinyInteger('event_type')->nullable(false)->default(\App\Enums\OrderChangeHistory\EventType::AddItem)->comment('イベントタイプ');
            $table->text('diff_json')->nullable(true)->comment('差分情報');
            $table->text('memo')->nullable(true)->comment('変更理由');
            $table->timestamps();

            $table->index(['log_id', 'log_type']);
            $table->foreign('staff_id')->references('id')->on('staffs')->onUpdate('cascade')->onDelete('restrict');
            $table->foreign('order_id')->references('id')->on('orders')->onUpdate('cascade')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order_change_histories');
    }
}
