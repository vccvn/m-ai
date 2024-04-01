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
        Schema::table('gpt_prompts', function (Blueprint $table) {
            $table->string('ai_model')->nullable()->after('topic_id');
            $table->string('ai_service')->nullable()->after('topic_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('gpt_prompts', function (Blueprint $table) {
            $table->dropColumn('service');
            $table->dropColumn('ai_model');
        });
    }
};
