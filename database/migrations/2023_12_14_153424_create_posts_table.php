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
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('author_id')->unsigned()->default(0);
            $table->bigInteger('dynamic_id')->unsigned()->default(0);
            $table->bigInteger('parent_id')->unsigned()->default(0);
            $table->bigInteger('category_id')->unsigned()->default(0);
            $table->string('category_map')->nullable();
            $table->string('type')->default('post');
            $table->string('content_type')->default('text');
            $table->string('title');
            $table->string('slug');
            $table->string('keywords')->nullable();
            $table->text('description')->nullable();
            $table->longText('content')->nullable();
            $table->string('featured_image')->nullable();
            $table->integer('views')->default(0);
            $table->string('privacy')->default('published');
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
        Schema::dropIfExists('posts');
    }
};
