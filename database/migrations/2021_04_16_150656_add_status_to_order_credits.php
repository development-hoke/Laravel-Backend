<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddStatusToOrderCredits extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('order_credits', function (Blueprint $table) {
            $table->unsignedTinyInteger('status')->nullable(false)->default(\App\Enums\OrderCredit\Status::Authorized)->comment('ステータス')->after('transaction_number');
            $table->index('authorization_number');
            $table->string('authorization_number', 12)->nullable(false)->comment('承認番号')->change();
            $table->string('transaction_number', 40)->nullable(false)->comment('取引番号')->change();
            $table->index('transaction_number');
        });
        Schema::create('order_credit_logs', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('ID');
            $table->dateTime('logged_at')->nullable(false)->useCurrent()->comment('ログ作成日時');
            $table->bigInteger('order_credit_id')->unsigned()->nullable(false)->comment('受注クレジットID');
            $table->bigInteger('order_id')->unsigned()->nullable(false)->index()->comment('受注ID');
            $table->string('authorization_number', 12)->nullable(false)->comment('承認番号');
            $table->string('transaction_number', 40)->nullable(false)->comment('取引番号');
            $table->unsignedTinyInteger('status')->nullable(false)->default(\App\Enums\OrderCredit\Status::Authorized)->comment('ステータス');
            $table->timestamps();
            $table->softDeletes('deleted_at', 0);

            $table->index('order_credit_id');
        });

        // DB::unprepared('
        //     CREATE TRIGGER after_insert_order_credits
        //     AFTER INSERT ON order_credits
        //     FOR EACH ROW
        //     INSERT INTO order_credit_logs (order_credit_id, order_id, authorization_number, transaction_number, status, created_at, updated_at, deleted_at)
        //     VALUES (NEW.id, NEW.order_id, NEW.authorization_number, NEW.transaction_number, NEW.status, NEW.created_at, NEW.updated_at, NEW.deleted_at)
        // ;');
        // DB::unprepared('
        //     CREATE TRIGGER after_update_order_credits
        //     AFTER UPDATE ON order_credits
        //     FOR EACH ROW
        //     INSERT INTO order_credit_logs (order_credit_id, order_id, authorization_number, transaction_number, status, created_at, updated_at, deleted_at)
        //     VALUES (NEW.id, NEW.order_id, NEW.authorization_number, NEW.transaction_number, NEW.status, NEW.created_at, NEW.updated_at, NEW.deleted_at);
        // ');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('order_credits', function (Blueprint $table) {
            $table->dropColumn('status');
            $table->dropIndex('order_credits_authorization_number_index');
            $table->dropIndex('order_credits_transaction_number_index');
        });
        // DB::unprepared('DROP TRIGGER after_insert_order_credits;');
        // DB::unprepared('DROP TRIGGER after_update_order_credits;');
        Schema::dropIfExists('order_credit_logs');
    }
}
