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
        Schema::create('agent_accounts', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->unsigned()->nullable()->default(0);
            $table->bigInteger('policy_id')->unsigned()->nullable()->default(0);
            $table->integer('month_balance')->unsigned()->nullable()->default(0);
            $table->float('revenue', 12, 2, true)->nullable()->default(0);
            $table->float('commission_level_1', 5, 2, true)->nullable()->default(0);
            $table->float('commission_level_2', 5, 2, true)->nullable()->default(0);
            $table->float('commission_level_3', 5, 2, true)->nullable()->default(0);
            $table->float('commission_level_4', 5, 2, true)->nullable()->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('agent_accounts');
    }
};
