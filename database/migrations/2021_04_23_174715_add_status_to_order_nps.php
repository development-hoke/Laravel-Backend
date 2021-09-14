<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddStatusToOrderNps extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('order_nps', function (Blueprint $table) {
            $table->string('authori_ng', 10)->nullable(true)->comment('与信NG事由コード')->after('authori_required_date');
            $table->unsignedTinyInteger('status')->nullable(false)->after('authori_hold');
        });
        Schema::table('order_np_logs', function (Blueprint $table) {
            $table->string('authori_ng', 10)->nullable(true)->comment('与信NG事由コード')->after('authori_required_date');
            $table->unsignedTinyInteger('status')->nullable(false)->after('authori_hold');
        });

        // DB::unprepared('DROP TRIGGER after_insert_order_nps;');
        // DB::unprepared('DROP TRIGGER after_update_order_nps;');

        // DB::unprepared('
        //     CREATE TRIGGER after_insert_order_nps
        //     AFTER INSERT ON order_nps
        //     FOR EACH ROW
        //     INSERT INTO order_np_logs (order_np_id, order_id, shop_transaction_id, np_transaction_id, authori_result, authori_required_date, authori_ng, authori_hold, status, created_at, updated_at, deleted_at)
        //     VALUES (NEW.id, NEW.order_id, NEW.shop_transaction_id, NEW.np_transaction_id, NEW.authori_result, NEW.authori_required_date, NEW.authori_ng, NEW.authori_hold, NEW.status, NEW.created_at, NEW.updated_at, NEW.deleted_at)
        // ;');
        // DB::unprepared('
        //     CREATE TRIGGER after_update_order_nps
        //     AFTER UPDATE ON order_nps
        //     FOR EACH ROW
        //     INSERT INTO order_np_logs (order_np_id, order_id, shop_transaction_id, np_transaction_id, authori_result, authori_required_date, authori_ng, authori_hold, status, created_at, updated_at, deleted_at)
        //     VALUES (NEW.id, NEW.order_id, NEW.shop_transaction_id, NEW.np_transaction_id, NEW.authori_result, NEW.authori_required_date, NEW.authori_ng, NEW.authori_hold, NEW.status, NEW.created_at, NEW.updated_at, NEW.deleted_at)
        // ');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('order_nps', function (Blueprint $table) {
            $table->dropColumn('authori_ng');
            $table->dropColumn('status');
        });
        Schema::table('order_np_logs', function (Blueprint $table) {
            $table->dropColumn('authori_ng');
            $table->dropColumn('status');
        });

        // DB::unprepared('DROP TRIGGER after_insert_order_nps;');
        // DB::unprepared('DROP TRIGGER after_update_order_nps;');

        // DB::unprepared('
        //     CREATE TRIGGER after_insert_order_nps
        //     AFTER INSERT ON order_nps
        //     FOR EACH ROW
        //     INSERT INTO order_np_logs (order_np_id, order_id, shop_transaction_id, np_transaction_id, authori_result, authori_required_date, authori_hold, created_at, updated_at, deleted_at)
        //     VALUES (NEW.id, NEW.order_id, NEW.shop_transaction_id, NEW.np_transaction_id, NEW.authori_result, NEW.authori_required_date, NEW.authori_hold, NEW.created_at, NEW.updated_at, NEW.deleted_at)
        // ;');
        // DB::unprepared('
        //     CREATE TRIGGER after_update_order_nps
        //     AFTER UPDATE ON order_nps
        //     FOR EACH ROW
        //     INSERT INTO order_np_logs (order_np_id, order_id, shop_transaction_id, np_transaction_id, authori_result, authori_required_date, authori_hold, created_at, updated_at, deleted_at)
        //     VALUES (NEW.id, NEW.order_id, NEW.shop_transaction_id, NEW.np_transaction_id, NEW.authori_result, NEW.authori_required_date, NEW.authori_hold, NEW.created_at, NEW.updated_at, NEW.deleted_at)
        // ');
    }
}
