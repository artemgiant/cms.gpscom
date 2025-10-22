<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSimCardsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sim_cards', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('phone')->comment('Номер телефону');
            $table->boolean('status')->default(0)->comment('Статус');
            $table->enum('operator', ['beeline', 'kcell','m2m'])->comment('Оператор');
            $table->bigInteger('operator_id')->nullable()->comment('Оператор id');
            $table->bigInteger('equipment_id')->nullable()->comment('Id обєкта');
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
        Schema::dropIfExists('sim_cards');
    }
}
