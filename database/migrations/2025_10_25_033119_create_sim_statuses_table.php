<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSimStatusesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sim_statuses', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Активна, Неактивна, Заблокована оператором, Немає зв'язку
            $table->string('code')->unique(); // active, inactive, blocked, no_signal
            $table->string('color')->default('#000000'); // Колір для badge
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true); // Чи використовується цей статус
            $table->boolean('is_working')->default(false); // Чи це робочий стан (true для активних)
            $table->integer('sort_order')->default(0);
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
        Schema::dropIfExists('sim_statuses');
    }
}
