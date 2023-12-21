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
        Schema::table('gpt_chat_messages', function (Blueprint $table) {
            $table->string('role')->nullable()->after('user')->default('user');
            $table->mediumText('content')->nullable()->after('message');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('gpt_chat_messages', function (Blueprint $table) {
            $table->dropColumn('role');
            $table->dropColumn('content');
        });
    }
};
