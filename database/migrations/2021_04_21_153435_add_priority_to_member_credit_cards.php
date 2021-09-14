<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddPriorityToMemberCreditCards extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('member_credit_cards', function (Blueprint $table) {
            $table->unsignedSmallInteger('priority')->nullable(false)->default(\App\Enums\MemberCreditCard\Priority::Default)->comment('優先度')->after('payment_method');
        });
        Schema::table('order_credits', function (Blueprint $table) {
            $table->unsignedBigInteger('member_credit_card_id')->nullable(true)->comment('member_credit_cards.id')->after('order_id');
            $table->foreign('member_credit_card_id')->references('id')->on('member_credit_cards')->onUpdate('cascade')->onDelete('restrict');
        });
        Schema::table('order_credit_logs', function (Blueprint $table) {
            $table->unsignedBigInteger('member_credit_card_id')->nullable(true)->comment('member_credit_cards.id')->after('order_id');
        });

        // DB::unprepared('DROP TRIGGER after_insert_order_credits;');
        // DB::unprepared('DROP TRIGGER after_update_order_credits;');

        // DB::unprepared('
        //     CREATE TRIGGER after_insert_order_credits
        //     AFTER INSERT ON order_credits
        //     FOR EACH ROW
        //     INSERT INTO order_credit_logs (order_credit_id, order_id, member_credit_card_id, authorization_number, transaction_number, status, payment_method, created_at, updated_at, deleted_at)
        //     VALUES (NEW.id, NEW.order_id, NEW.member_credit_card_id, NEW.authorization_number, NEW.transaction_number, NEW.status, NEW.payment_method, NEW.created_at, NEW.updated_at, NEW.deleted_at)
        // ;');
        // DB::unprepared('
        //     CREATE TRIGGER after_update_order_credits
        //     AFTER UPDATE ON order_credits
        //     FOR EACH ROW
        //     INSERT INTO order_credit_logs (order_credit_id, order_id, member_credit_card_id, authorization_number, transaction_number, status, payment_method, created_at, updated_at, deleted_at)
        //     VALUES (NEW.id, NEW.order_id, NEW.member_credit_card_id, NEW.authorization_number, NEW.transaction_number, NEW.status, NEW.payment_method, NEW.created_at, NEW.updated_at, NEW.deleted_at);
        // ');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('member_credit_cards', function (Blueprint $table) {
            $table->dropColumn('priority');
        });
        Schema::table('order_credits', function (Blueprint $table) {
            $table->dropForeign('order_credits_member_credit_card_id_foreign');
            $table->dropColumn('member_credit_card_id');
        });
        Schema::table('order_credit_logs', function (Blueprint $table) {
            $table->dropColumn('member_credit_card_id');
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
}
