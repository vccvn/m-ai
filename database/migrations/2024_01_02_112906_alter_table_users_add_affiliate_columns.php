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
        Schema::table('users', function (Blueprint $table) {
            $table->string('ref_code')->nullable()->after('type');
            $table->string('affiliate_code')->nullable()->after('type');
            $table->timestamp('expired_at')->nullable()->after('status');
            $table->bigInteger('agent_id')->unsigned()->nullable()->default(0)->after('id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('ref_code');
            $table->dropColumn('affiliate_code');
            $table->dropColumn('expired_at');
            $table->dropColumn('agent_id');
        });
    }
};
