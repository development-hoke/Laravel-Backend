<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ChangeOrderNpsTable2 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('order_nps', function (Blueprint $table) {
            $table->string('authori_result', 4)->nullable(false)->comment('与信結果')->after('np_transaction_id');
            $table->text('authori_hold')->nullable(true)->comment('与信保留事由コード(複数件のコードをJSONで保存)')->after('authori_required_date');
        });
        Schema::create('order_np_logs', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('ID');
            $table->dateTime('logged_at')->nullable(false)->useCurrent()->comment('ログ作成日時');
            $table->unsignedBigInteger('order_np_id')->nullable(false)->comment('order_nps.id');
            $table->bigInteger('order_id')->unsigned()->nullable(false)->comment('受注ID');
            $table->string('shop_transaction_id', 80)->nullable(false)->comment('加盟店取引ID');
            $table->string('np_transaction_id', 22)->nullable(false)->comment('NP取引ID');
            $table->string('authori_result', 4)->nullable(false)->comment('与信結果');
            $table->dateTime('authori_required_date')->nullable(true)->comment('結果確定日時');
            $table->text('authori_hold')->nullable(true)->comment('与信保留事由コード(複数件のコードをJSONで保存)');
            $table->timestamps();
            $table->softDeletes('deleted_at', 0);

            $table->index('order_np_id');
        });

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

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // DB::unprepared('DROP TRIGGER after_insert_order_nps;');
        // DB::unprepared('DROP TRIGGER after_update_order_nps;');

        Schema::table('order_nps', function (Blueprint $table) {
            $table->dropColumn('authori_result');
            $table->dropColumn('authori_hold');
        });
        Schema::dropIfExists('order_np_logs');
    }
}
