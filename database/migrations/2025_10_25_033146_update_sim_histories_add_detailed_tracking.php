<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateSimHistoriesAddDetailedTracking extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sim_card_histories', function (Blueprint $table) {
            // Foreign keys для статусів
            $table->foreignId('status_id')->after('phone')->constrained('sim_statuses')->onDelete('cascade');
            $table->foreignId('previous_status_id')->nullable()->after('status_id')->constrained('sim_statuses')->onDelete('set null');

            // JSON з детальними змінами
            $table->json('changes_data')->nullable()->comment('Детальна інформація про всі зміни');

            // Джерело зміни
            $table->enum('change_source', [
                'manual',          // Ручна зміна адміністратором
                'api',            // Через API
                'system_check',   // Автоматична перевірка системи
                'modem_report',   // Звіт від модему
                'import',         // Імпорт даних
                'cron',           // Планувальник завдань
                'webhook',        // Webhook від оператора
                'monitoring'      // Система моніторингу
            ])->default('manual');



            // Причина зміни статусу
            $table->string('change_reason')->nullable();

            // Додаткові примітки
            $table->text('notes')->nullable();

            $table->timestamps();
            // Індекси для швидкого пошуку
            $table->index('sim_status_id');
            $table->index('previous_status_id');
            $table->index('change_source');
            $table->index(['phone', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sim_card_histories', function (Blueprint $table) {
            $table->dropForeign(['status_id']);
            $table->dropForeign(['previous_status_id']);

            // Видаляємо індекси
            $table->dropIndex(['sim_card_id', 'operation_date_time']);

            // Видаляємо колонки
            $table->dropColumn([
                'status_id',
                'previous_sim_status_id',
                'changes_data',
                'change_source',
                'change_reason',
                'notes',
            ]);
        });
    }
}
