<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAnaliticsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('analitics', function (Blueprint $table) {
            $table->id();
            $table->string('month');
            $table->integer('active_counts_ip');
            $table->integer('active_counts_fl');
            $table->integer('active_counts_too');
            $table->string('graphic_total_ip');
            $table->string('graphic_total_fl');
            $table->string('graphic_total_too');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('analitics');
    }
}
