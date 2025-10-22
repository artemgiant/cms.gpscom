<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('client_id')->nullable()->comment('Id клієнта з Wialon');
            $table->string('name')->comment('Наіменування клієнта');
            $table->string('account')->comment('Аккаунт');
            $table->string('contract_number', 30)->nullable()->comment('Номер договора');
            $table->dateTime('contract_date')->nullable()->comment('Дата договора');
            $table->string('person')->comment('Контактне леце');
            $table->string('phone')->comment('Контакти - телефон');
            $table->string('manager')->nullable()->comment('Менеджер');
            $table->boolean('status')->default(1)->comment('Статус');
            $table->enum('client_type', ['ip', 'fl', 'too']);
            $table->string('filling_status')->default(1)->nullable()->comment('Статус заповнення');
            $table->bigInteger('accountant_phone')->comment('Номер бухгалтера');
            $table->string('email')->comment('Email');
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
        Schema::dropIfExists('clients');
    }
}
