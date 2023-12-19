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
        Schema::create('product_label_refs', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('label_id')->unsigned()->default(0);
            $table->string('ref')->nullable()->default('product');
            $table->bigInteger('ref_id')->unsigned();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_label_refs');
    }
};
