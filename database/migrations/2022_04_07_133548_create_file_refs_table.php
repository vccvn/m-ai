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
        Schema::create('file_refs', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('file_id')->unsigned()->default(0);
            $table->bigInteger('ref_id')->unsigned()->default(0);
            $table->string('ref')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('file_refs');
    }
};
