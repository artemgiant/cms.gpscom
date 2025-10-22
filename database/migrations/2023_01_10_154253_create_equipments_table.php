<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEquipmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('equipments', function (Blueprint $table) {
            $table->id();
            $table->string('object')->comment('Обєкт');
            $table->string('device')->nullable()->comment('Пристрій');
            $table->bigInteger('imei')->comment('IMEI');
            $table->bigInteger('phone')->comment('Номер пристою');
            $table->bigInteger('phone2')->nullable()->comment('Номер пристою 2');
            $table->dateTime('date_start')->nullable()->comment('Дата підключення');
            $table->dateTime('date_end')->nullable()->comment('Дата підключення');
            $table->bigInteger('tariff_id')->comment('Id тарифу');
            $table->bigInteger('client_id')->nullable()->comment('Id клієнта');
            $table->tinyInteger('status')->default(0)->comment('Статус активності');
            $table->string('operator')->comment('Оператор');
            $table->bigInteger('operator_id')->nullable()->comment('Оператор id');
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
        Schema::dropIfExists('equipments');
    }
}
