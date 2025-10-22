<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClientsTariffsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('clients_tariffs', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('client_id')->comment('Id клієнта');
            $table->bigInteger('tariff_id')->comment('Id тарифу');
            $table->float('price')->nullable()->comment('Ціна');
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
        Schema::dropIfExists('clients_tariffs');
    }
}
