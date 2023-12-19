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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('category_id')->unsigned()->default(0);
            $table->bigInteger('subscription_package_id')->unsigned()->default(0);
            $table->string('name');
            $table->string('slug')->nullable();
            $table->string('type')->nullable()->default('standard');
            $table->text('description')->nullable();
            $table->longText('detail')->nullable();
            $table->string('keywords')->nullable();
            $table->string('featured_image')->nullable();
            $table->string('sku')->nullable();
            $table->integer('price_status')->default(1);
            $table->decimal('list_price', 12, 2)->unsigned()->nullable()->default(0);
            $table->decimal('sale_price', 12, 2)->unsigned()->nullable()->default(0);
            $table->integer('on_sale')->unsigned()->nullable()->default(0);
            $table->decimal('wholesale_price', 12, 2)->unsigned()->nullable()->default(0);
            $table->integer('package_total')->unsigned()->nullable()->default(1);
            $table->integer('available_in_store')->unsigned()->nullable()->default(0);
            $table->integer('views')->default(0);
            $table->string('privacy')->default('published');
            $table->string('category_map')->nullable();
            $table->integer('status')->default(1);
            $table->integer('trashed_status')->default(0);
            $table->bigInteger('shop_id')->unsigned()->default(0);
            $table->bigInteger('created_by')->unsigned()->default(0);

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('products');
    }
};
