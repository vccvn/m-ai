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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->unsigned()->nullable()->default(0);
            $table->string('name')->nullable();
            $table->string('email')->nullable();
            $table->string('phone_number')->nullable();
            $table->text('address')->nullable();
            $table->bigInteger('region_id')->unsigned()->nullable()->default(0);
            $table->bigInteger('district_id')->unsigned()->nullable()->default(0);
            $table->bigInteger('ward_id')->unsigned()->nullable()->default(0);
            $table->decimal('balance', 14, 2)->default(0);
            $table->string('remember_token')->nullable();
            $table->integer('trashed_status')->default(0);
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
        Schema::dropIfExists('customers');
    }
};
