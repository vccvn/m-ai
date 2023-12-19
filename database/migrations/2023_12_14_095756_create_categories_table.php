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
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('dynamic_id')->unsigned()->default(0);
            $table->bigInteger('parent_id')->unsigned()->default(0);
            $table->string('name');
            $table->string('type')->default('post');
            $table->string('slug');
            $table->string('keywords')->nullable();
            $table->text('description')->nullable();
            $table->text('first_content')->nullable();
            $table->text('second_content')->nullable();
            $table->string('featured_image')->nullable();
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
        Schema::dropIfExists('categories');
    }
};
