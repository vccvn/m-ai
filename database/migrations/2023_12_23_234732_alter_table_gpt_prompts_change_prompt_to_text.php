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
            $table->text('prompt')->nullable()->change();
            $table->text('prompt_config')->nullable();

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
            $table->string('prompt')->nullable()->change();
            $table->dropColumn('prompt_config');
        });
    }
};
