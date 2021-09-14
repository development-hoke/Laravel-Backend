<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPaymentMethodToMemberCreditCards extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('member_credit_cards', function (Blueprint $table) {
            $table->string('payment_method', 10)->nullable(false)->comment('支払い方法')->after('member_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('member_credit_cards', function (Blueprint $table) {
            $table->dropColumn('payment_method');
        });
    }
}
