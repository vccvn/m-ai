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
        Schema::create('promos', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->text('description')->nullable();
            $table->string('scope')->nullable()->default('product');
            $table->integer('type')->unsigned()->nullable()->default(0);
            $table->decimal('down_price', 12, 2)->nullable()->default(0);
            $table->string('code')->nullable();
            $table->integer('quantity_per_user')->unsigned()->nullable()->default(0);
            $table->integer('limited_total')->unsigned()->default(0);
            $table->integer('usage_total')->unsigned()->default(0);
            $table->string('schedule')->nullable()->default(false);
            $table->string('type_schedule')->nullable();
            $table->string('value_schedule')->nullable();
            $table->integer('is_activated')->unsigned()->nullable()->default(0);
            $table->dateTime('started_at')->nullable();
            $table->dateTime('finished_at')->nullable();
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
        Schema::dropIfExists('promos');
    }
};
