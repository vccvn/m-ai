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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('gender')->nullable()->default('male');
            $table->date('birthday')->nullable();
            $table->string('email');//->unique();
            $table->string('username');//->unique();
            $table->string('password');
            $table->string('phone_number')->nullable();
            $table->string('avatar')->nullable();
            $table->string('type')->nullable()->default('user');
            $table->float('balance', 12, 2, true)->nullable()->default(0);
            $table->float('money_in', 12, 2, true)->nullable()->default(0);
            $table->float('money_out', 12, 2, true)->nullable()->default(0);
            $table->text('google2fa_secret')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('fcm_token')->nullable();
            $table->json('account_data')->nullable();
            $table->rememberToken();
            $table->integer('status')->default(1);
            $table->integer('trashed_status')->default(0);
            $table->softDeletes();
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
        Schema::dropIfExists('users');
    }
};
