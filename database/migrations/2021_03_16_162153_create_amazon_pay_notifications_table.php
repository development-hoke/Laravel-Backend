<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAmazonPayNotificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @see https://developer.amazon.com/docs/amazon-pay-onetime/sample-notifications.html
     * @see https://docs.aws.amazon.com/sns/latest/dg/sns-message-and-json-formats.html#http-notification-json
     *
     * @return void
     */
    public function up()
    {
        Schema::create('amazon_pay_notifications', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('message_id', 120)->nullable(false)->comment('MessageId');
            $table->string('notification_reference_id', 120)->nullable(false)->comment('NotificationReferenceId');
            $table->unsignedTinyInteger('status')->nullable(false)->default(\App\Enums\AmazonPay\NotificationStatus::Processing)->comment('処理状態 1:処理中 2:処理済み 3:失敗');
            $table->text('requested_body')->nullable(false)->comment('リクエストボディをすべて保存');
            $table->text('failed_info')->nullable(true)->comment('処理失敗したときの補足情報');
            $table->string('type', 30)->nullable(true)->comment('NotificationType');
            $table->string('amazon_object_id', 120)->nullable(true)->comment('通知されたオブジェクトのid. amazon_authorization_id|amazon_capture_id|amazon_refund_id');
            $table->timestamps();

            $table->unique('message_id');
            $table->index('notification_reference_id');
            $table->index(['amazon_object_id', 'type']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('amazon_pay_notifications');
    }
}
