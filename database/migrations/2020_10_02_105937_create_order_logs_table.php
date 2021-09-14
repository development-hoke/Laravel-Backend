<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_logs', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('ID');
            $table->bigInteger('order_id')->comment('ID')->unsigned()->nullable(false)->index();
            $table->text('log_memo')->nullable(true)->comment('ログメモ（変更理由等を保存）');
            $table->bigInteger('member_id')->unsigned()->nullable(false)->index()->comment('');
            $table->string('code', 32)->nullable(false)->index()->comment('受注コード');
            $table->datetime('order_date')->nullable(false)->comment('受注日時');
            $table->unsignedTinyInteger('payment_type')->nullable()->default(\App\Enums\Order\PaymentType::Bank)->comment('決済種類');
            $table->unsignedTinyInteger('delivery_type')->nullable()->default(\App\Enums\Order\DeliveryType::Sagawa)->comment('配送種類');
            $table->string('delivery_token', 32)->nullable(false)->unique()->comment('配送先トークン');
            $table->date('delivery_hope_date')->nullable(false)->comment('配送希望日');
            $table->unsignedTinyInteger('delivery_hope_time')->nullable()->default(\App\Enums\Order\DeliveryTime::Am)->comment('配送希望時間帯');
            $table->unsignedInteger('price')->nullable(false)->comment('請求金額');
            $table->decimal('discount_rate')->nullable(true)->comment('値引率');
            $table->json('discount_memo')->nullable(true)->comment('適用された割引の内訳');
            $table->decimal('tax')->nullable(false)->comment('消費税');
            $table->unsignedInteger('fee')->nullable(false)->comment('手数料');
            $table->string('coupon_code', 32)->nullable(true)->comment('使用クーポン');
            $table->unsignedInteger('use_point')->nullable(false)->comment('使用ポイント');
            $table->unsignedInteger('order_type')->nullable()->default(\App\Enums\Order\OrderType::Normal)->comment('注文タイプ');
            $table->boolean('paid')->nullable(false)->default(false)->comment('入金ステータス');
            $table->datetime('paid_date')->nullable(true)->comment('入金日時');
            $table->boolean('inspected')->nullable(false)->default(false)->comment('検品ステータス');
            $table->datetime('inspected_date')->nullable(true)->comment('検品日時');
            $table->boolean('deliveryed')->nullable(false)->default(false)->comment('配送ステータス');
            $table->datetime('deliveryed_date')->nullable(true)->comment('配送日時');
            $table->unsignedInteger('status')->nullable()->default(\App\Enums\Order\Status::Ordered)->comment('注文ステータス');
            $table->unsignedInteger('add_point')->nullable(false)->comment('追加ポイント');
            $table->string('delivery_number', 100)->nullable(true)->comment('伝票番号');
            $table->text('memo1')->nullable(true)->comment('通信欄');
            $table->text('memo2')->nullable(true)->comment('お客様への連絡事項');
            $table->text('shop_memo')->nullable(true)->comment('ショップメモ');
            $table->softDeletes('deleted_at', 0);
            $table->timestamps();

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
        Schema::dropIfExists('order_logs');
    }
}
