<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddColumnsToOrderCredits extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('order_credits', function (Blueprint $table) {
            $table->string('payment_method', 4)->nullable(false)->comment('支払い回数')->after('status');
        });
        Schema::table('order_credit_logs', function (Blueprint $table) {
            $table->string('payment_method', 4)->nullable(false)->comment('支払い回数')->after('status');
        });

        // DB::unprepared('DROP TRIGGER after_insert_order_credits;');
        // DB::unprepared('DROP TRIGGER after_update_order_credits;');

        // DB::unprepared('
        //     CREATE TRIGGER after_insert_order_credits
        //     AFTER INSERT ON order_credits
        //     FOR EACH ROW
        //     INSERT INTO order_credit_logs (order_credit_id, order_id, authorization_number, transaction_number, status, payment_method, created_at, updated_at, deleted_at)
        //     VALUES (NEW.id, NEW.order_id, NEW.authorization_number, NEW.transaction_number, NEW.status, NEW.payment_method, NEW.created_at, NEW.updated_at, NEW.deleted_at)
        // ;');
        // DB::unprepared('
        //     CREATE TRIGGER after_update_order_credits
        //     AFTER UPDATE ON order_credits
        //     FOR EACH ROW
        //     INSERT INTO order_credit_logs (order_credit_id, order_id, authorization_number, transaction_number, status, payment_method, created_at, updated_at, deleted_at)
        //     VALUES (NEW.id, NEW.order_id, NEW.authorization_number, NEW.transaction_number, NEW.status, NEW.payment_method, NEW.created_at, NEW.updated_at, NEW.deleted_at);
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
            $table->dropColumn('payment_method');
        });
        Schema::table('order_credit_logs', function (Blueprint $table) {
            $table->dropColumn('payment_method');
        });

        // DB::unprepared('DROP TRIGGER after_insert_order_credits;');
        // DB::unprepared('DROP TRIGGER after_update_order_credits;');

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
}
