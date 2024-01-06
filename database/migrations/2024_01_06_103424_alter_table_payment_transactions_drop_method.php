<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('payment_transactions', function (Blueprint $table) {
            $table->dropColumn('method');
            $table->string('success_redirect_url')->nullable();
            $table->string('cancel_redirect_url')->nullable();
            $table->string('error_redirect_url')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('payment_transactions', function (Blueprint $table) {
            $table->string('method')->nullable();
            $table->dropColumn('success_redirect_url');
            $table->dropColumn('cancel_redirect_url');
            $table->dropColumn('error_redirect_url');

        });
    }
};
