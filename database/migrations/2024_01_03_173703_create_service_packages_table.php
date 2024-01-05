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
        Schema::create('service_packages', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('agent_id')->nullable()->default(0);
            $table->string('role')->nullable()->default('system');
            $table->string('name')->nullable();
            $table->text('description')->nullable();
            $table->integer('quantity')->unsigned()->nullable()->default(0);
            $table->float('wholesale_price')->nullable()->default(0);
            $table->float('retail_price')->nullable()->default(0);
            $table->string('currency')->nullable()->default('VND');
            $table->json('metadata')->nullable();
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
        Schema::dropIfExists('service_packages');
    }
};
