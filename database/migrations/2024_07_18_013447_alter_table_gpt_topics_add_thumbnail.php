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
        Schema::table('gpt_topics', function (Blueprint $table) {
            $table->string('thumbnail')->nullable()->after('description')->comment('Ảnh thumbnail');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('gpt_topics', function (Blueprint $table) {
            $table->dropColumn('thumbnail');
        });
    }
};
