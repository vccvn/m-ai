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
        Schema::create('themes', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('author_id')->unsigned()->nullable()->default(0);
            $table->string('secret_id')->nullable();
            $table->string('name')->nullable()->default('GomeeTheme');
            $table->string('slug')->nullable();
            $table->string('view_type')->nullable()->default('multi-page');
            $table->string('mobile_version')->nullable()->default('responsive');
            $table->string('web_types')->nullable();
            $table->double('version', 5, 2)->nullable()->default(1.0);
            $table->text('description')->nullable();
            $table->string('privacy')->nullable()->default('protected');
            $table->string('zip')->nullable();
            $table->string('image')->nullable();
            $table->integer('available')->default(0);
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
        Schema::dropIfExists('themes');
    }
};
