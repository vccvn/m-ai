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
        Schema::create('commission_policies', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->text('description')->nullable();
            $table->string('type')->nullable()->default('agent');
            $table->integer('receive_times')->nullable()->default(0);
            $table->integer('level')->unsigned()->nullable()->default(1);
            $table->float('revenue_target', 12, 2, true)->nullable()->default(0);
            $table->float('commission_level_1', 5, 2, true)->nullable()->default(0);
            $table->float('commission_level_2', 5, 2, true)->nullable()->default(0);
            $table->float('commission_level_3', 5, 2, true)->nullable()->default(0);
            $table->float('commission_level_4', 5, 2, true)->nullable()->default(0);
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
        Schema::dropIfExists('commission_policies');
    }
};
