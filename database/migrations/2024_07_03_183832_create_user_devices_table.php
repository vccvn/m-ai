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
        Schema::create('user_devices', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->unsigned()->nullable()->default(0);
            $table->text('user_agent')->nullable();
            $table->string('device')->nullable()->default('unknown');
            $table->string('platform')->nullable()->default('unknown');
            $table->string('browser')->nullable()->default('unknown');
            $table->string('ip')->nullable();
            $table->string('session_token')->nullable();
            $table->tinyInteger('approved')->nullable()->default(0);
            $table->timestamp('last_login_at')->nullable();
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
        Schema::dropIfExists('user_devices');
    }
};
