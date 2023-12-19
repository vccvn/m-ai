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
        Schema::create('permission_modules', function (Blueprint $table) {
            $table->id();
            $table->string('type')->default('default');
            $table->string('name');
            $table->integer('parent_id')->unsigned()->nullable()->default(0);
            $table->string('ref')->nullable();
            $table->string('description')->nullable();
            $table->string('slug')->nullable();
            $table->string('route')->nullable();
            $table->string('prefix')->nullable();
            $table->string('path')->nullable();


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
        Schema::dropIfExists('permission_modules');
    }
};
