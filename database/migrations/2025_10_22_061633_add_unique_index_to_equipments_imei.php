<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUniqueIndexToEquipmentsImei extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Проверяем существует ли таблица
        if (!Schema::hasTable('equipments')) {
            return;
        }

        // Проверяем существует ли колонка imei
        if (!Schema::hasColumn('equipments', 'imei')) {
            return;
        }

        // Проверяем не существует ли уже индекс через сырой SQL запрос
        $indexExists = DB::select("
            SELECT COUNT(*) as count 
            FROM information_schema.statistics 
            WHERE table_schema = DATABASE() 
            AND table_name = 'equipments' 
            AND index_name = 'equipments_imei_unique'
        ");

        if ($indexExists[0]->count > 0) {
            // Индекс уже существует
            return;
        }

        // Создаем уникальный индекс
        Schema::table('equipments', function (Blueprint $table) {
            $table->unique('imei');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Проверяем существует ли таблица
        if (!Schema::hasTable('equipments')) {
            return;
        }

        // Проверяем существует ли индекс перед удалением через сырой SQL запрос
        $indexExists = DB::select("
            SELECT COUNT(*) as count 
            FROM information_schema.statistics 
            WHERE table_schema = DATABASE() 
            AND table_name = 'equipments' 
            AND index_name = 'equipments_imei_unique'
        ");

        // Удаляем индекс только если он существует
        if ($indexExists[0]->count > 0) {
            Schema::table('equipments', function (Blueprint $table) {
                $table->dropUnique(['imei']);
            });
        }
    }

}
