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
        Schema::create('payment_transactions', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('merchant_id')->unsigned()->nullable()->default(0);
            $table->bigInteger('user_id')->unsigned()->nullable()->default(0);
            $table->bigInteger('payment_method_id')->unsigned()->nullable()->default(0);
            $table->bigInteger('order_id')->unsigned()->nullable()->default(0);

            $table->string('transaction_id')->nullable();
            $table->string('transaction_code')->nullable();
            $table->string('type')->nullable()->default('buy');
            $table->string('order_code')->nullable();
            $table->string('ref_code')->nullable();
            $table->string('method')->nullable();
            $table->text('note')->nullable();
            $table->text('description')->nullable();
            $table->float('amount', 12, 2, true)->nullable()->default(0);
            $table->float('discount', 12, 2, true)->nullable()->default(0);
            $table->string('currency')->nullable()->default('VND');
            $table->integer('total_item')->unsigned()->nullable()->default(1);
            $table->string('payment_method_name')->nullable();
            $table->json('ref_data')->nullable();
            $table->integer('is_reported')->nullable()->default(0);



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
        Schema::dropIfExists('payment_transactions');
    }
};

