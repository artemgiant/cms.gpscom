<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNotificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->comment('Id менеджера');
            $table->bigInteger('equipment_id')->nullable()->comment('Id обєкта');
            $table->bigInteger('client_id')->nullable()->comment('Id клієнта');
            $table->bigInteger('sim_card_id')->nullable()->comment('Id сам карти');
            $table->text('message')->comment('Комент');
            $table->boolean('status')->default(0);
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
        Schema::dropIfExists('notifications');
    }
}
