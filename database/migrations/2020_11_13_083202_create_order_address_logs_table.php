<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderAddressLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_address_logs', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('ID');
            $table->unsignedBigInteger('order_address_id')->nullable(false)->comment('受注住所ID');
            $table->unsignedBigInteger('staff_id')->nullable(true)->comment('スタッフID');
            $table->unsignedTinyInteger('event_type')->nullable(false)->comment('イベントタイプ');
            $table->text('log_memo')->nullable(true)->comment('ログメモ（変更理由等を保存）');
            $table->json('diff_json')->nullable(true)->comment('差分');

            $table->unsignedBigInteger('order_id')->nullable(false)->comment('受注ID');
            $table->unsignedTinyInteger('type')->nullable(false)->default(\App\Enums\OrderAddress\Type::Delivery)->comment('タイプ');
            $table->string('fname', 255)->nullable(false)->comment('名');
            $table->string('lname', 255)->nullable(false)->comment('姓');
            $table->string('fkana', 255)->nullable(false)->comment('名カナ');
            $table->string('lkana', 255)->nullable(false)->comment('姓カナ');
            $table->string('tel', 50)->nullable(false)->comment('電話番号');
            $table->unsignedTinyInteger('pref_id')->nullable(false)->comment('都道府県ID');
            $table->string('zip', 8)->nullable(false)->comment('郵便番号');
            $table->string('city', 50)->nullable(false)->comment('市区町村名');
            $table->string('town', 50)->nullable(false)->comment('町名');
            $table->string('address', 100)->nullable(false)->comment('番地号');
            $table->string('building', 100)->nullable(true)->comment('建物名');
            $table->softDeletes('deleted_at', 0);
            $table->timestamps();

            $table->foreign('order_address_id')->references('id')->on('order_addresses')->onUpdate('cascade')->onDelete('restrict');
            $table->foreign('order_id')->references('id')->on('orders')->onUpdate('cascade')->onDelete('restrict');
            $table->foreign('pref_id')->references('id')->on('prefs')->onUpdate('cascade')->onDelete('restrict');
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
        Schema::dropIfExists('order_address_logs');
    }
}
