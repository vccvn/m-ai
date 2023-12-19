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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->unsigned()->nullable()->default(0);
            $table->bigInteger('customer_id')->unsigned()->nullable()->default(0);
            $table->bigInteger('promo_id')->unsigned()->nullable();
            $table->string('secret_id')->nullable();
            $table->string('type')->nullable()->default('cart');
            $table->string('code')->nullable();
            $table->boolean('ship_to_different_address')->default(false);
            $table->bigInteger('payment_method_id')->default(0);
            $table->decimal('shipping_fee', 10, 2)->default(0);
            $table->decimal('tax', 14, 2)->default(0);
            $table->string('coupon')->nullable();
            $table->integer('promo_type')->unsigned()->nullable()->default(0);
            $table->decimal('sub_total', 14, 2)->default(0);
            $table->decimal('promo_total', 14, 2)->default(0);
            $table->decimal('total_money', 14, 2)->default(0);
            $table->text('note')->nullable();
            $table->integer('status')->default(0);
            $table->integer('payment_status')->default(0);
            $table->integer('delivery_status')->default(0);

            $table->integer('trashed_status')->default(0);
            $table->dateTime('completed_at')->nullable();
            $table->timestamp('ordered_at')->nullable();
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
        Schema::dropIfExists('orders');
    }
};
