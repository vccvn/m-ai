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
        Schema::create('gpt_prompts', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('topic_id')->unsigned()->nullable()->default(0);
            $table->string('name')->nullable();
            $table->string('keywords')->nullable();
            $table->text('description')->nullable();
            $table->string('prompt')->nullable();
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
        Schema::dropIfExists('gpt_prompts');
    }
};
