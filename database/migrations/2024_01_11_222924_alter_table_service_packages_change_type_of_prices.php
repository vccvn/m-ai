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
        Schema::table('service_packages', function (Blueprint $table) {

            $table->float('wholesale_price', 12, 2, true)->nullable()->default(0)->change();
            $table->float('retail_price', 12, 2, true)->nullable()->default(0)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('service_packages', function (Blueprint $table) {

            $table->float('wholesale_price', 8, 2)->nullable()->default(0)->change();
            $table->float('retail_price', 8, 2)->nullable()->default(0)->change();
        });
    }
};
