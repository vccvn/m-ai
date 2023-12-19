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
        Schema::create('menus', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable()->default('Menu');
            $table->string('slug')->nullable();
            $table->string('type')->nullable()->default('default');
            $table->bigInteger('ref_id')->nullable()->default(0);
            $table->integer('priority')->unsigned()->default(0);
            $table->boolean('is_main')->nullable()->default(false);
            $table->string('positions')->nullable();
            $table->integer('depth')->unsigned()->nullable()->default(4);
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
        Schema::dropIfExists('menus');
    }
};
