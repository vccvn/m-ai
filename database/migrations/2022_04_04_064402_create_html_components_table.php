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
        Schema::create('html_components', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('component_id')->unsigned()->nullable()->default(0);
            $table->bigInteger('area_id')->nullable()->default(0);
            $table->bigInteger('parent_id')->nullable()->default(0);
            $table->integer('priority')->unsigned()->default(0);
            $table->json('data')->nullable();
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
        Schema::dropIfExists('html_components');
    }
};
