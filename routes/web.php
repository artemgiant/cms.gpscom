<?php

use App\Http\Controllers\Admin\AnalyticController;
use App\Http\Controllers\Admin\ClientController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\EquipmentController;
use App\Http\Controllers\Admin\InstallationController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\OperatorController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\SensorController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\SimCardController;
use App\Http\Controllers\Admin\TariffController;
use App\Http\Controllers\Admin\UpdateDbController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\WebhookController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Auth::routes();
Route::get('login', function () {
    return redirect()->route('index');
})->name('login');


Route::get('/optimize', function () {
    Artisan::call('view:clear');
    Artisan::call('cache:clear');
    Artisan::call('config:clear');
    Artisan::call('optimize');
});
Route::get('/', [HomeController::class, 'index'])->name('index');

Route::group(['middleware' => ['auth']], function () {
    /* Dashboard */
    Route::get('dashboard', [DashboardController::class, 'dashboard'])->name('dashboard');

    /* Клієнти */
    Route::get('clients', [ClientController::class, 'index'])->name('clients');
    Route::post('_client/store', [ClientController::class, 'store'])->name('client.store');
    Route::get('_client/show', [ClientController::class, 'show'])->name('client.show');
    Route::get('_client/search', [ClientController::class, 'search'])->name('client.search');
    Route::post('_client/update', [ClientController::class, 'update'])->name('client.update');
    Route::delete('client', [ClientController::class, 'destroy'])->name('client.destroy');
    Route::get('client-update-wialon/{id}', [ClientController::class, 'updateClientWialon'])->name('client.update_wialon');

    /* Клієнти Карточка*/
    Route::get('client/{id}', [ClientController::class, 'view'])->name('client');
    Route::post('client/equipment/store', [ClientController::class, 'clientEquipmentStore'])->name('client.equipment.store');
    Route::post('client/free-equipment/store', [ClientController::class, 'clientFreeEquipmentStore'])->name('client.free_equipment.store');
    Route::get('_client/equipments/search', [ClientController::class, 'clientEquipmentSearch'])->name('client.equipment.search');
    Route::post('client/equipments/export', [ClientController::class, 'clientEquipmentsExport'])->name('client.equipments.export');

    /* Об'єкт клієнта */
    Route::get('_start_equipment', [ClientController::class, 'startEquipment'])->name('client.start.equipment');
    Route::get('_end_equipment', [ClientController::class, 'endEquipment'])->name('client.end.equipment');
    Route::get('_deactive-equipment', [ClientController::class, 'deactiveEquipment'])->name('client.deactive.equipment');
    Route::get('_show_equipment', [ClientController::class, 'showEquipment'])->name('client.show.equipment');
    Route::post('_move_equipment', [ClientController::class, 'moveEquipment'])->name('client.move.equipment');
    Route::get('_delete_equipment', [ClientController::class, 'deleteEquipment'])->name('client.delete.equipment');

    /* Датчик */
    Route::post('_sensor-store', [SensorController::class, 'store'])->name('client.sensor.store');
    Route::get('_sensor-show', [SensorController::class, 'show'])->name('client.sensor.show');
    Route::post('_sensor-update', [SensorController::class, 'update'])->name('client.sensor.update');
    Route::get('_sensor-delete', [SensorController::class, 'delete'])->name('client.sensor.delete');

    /* Монтажні роботи */
    Route::post('_installation-store', [InstallationController::class, 'store'])->name('client.installation.store');
    Route::get('_installation-show', [InstallationController::class, 'show'])->name('client.installation.show');
    Route::post('_installation-update', [InstallationController::class, 'update'])->name('client.installation.update');
    Route::get('_installation-delete', [InstallationController::class, 'delete'])->name('client.installation.delete');

    /* Тарифи клієета */
    Route::post('client/tariff/store', [ClientController::class, 'clientTariffStore'])->name('client.tariff.store');
    Route::get('_client/tariff/show', [ClientController::class, 'clientTariffShow'])->name('client.tariff.show');
    Route::post('client/tariff/update', [ClientController::class, 'clientTariffUpdate'])->name('client.tariff.update');
    Route::post('_client/tariff/delete', [ClientController::class, 'clientTariffDelete'])->name('client.tariff.delete');

    /* Оператори */
    Route::get('operators', [OperatorController::class, 'index'])->name('operators');
    Route::post('operator/store', [OperatorController::class, 'store'])->name('operator.store');
    Route::get('_operator/show', [OperatorController::class, 'show'])->name('operator.show');
    Route::post('operator/update', [OperatorController::class, 'update'])->name('operator.update');
    Route::delete('operator/destroy', [OperatorController::class, 'destroy'])->name('operator.destroy');


    /* Об'єкти */
    Route::get('equipments', [EquipmentController::class, 'index'])->name('equipments');
    Route::post('_equipment/store', [EquipmentController::class, 'store'])->name('equipment.store');
    Route::get('_equipment/show', [EquipmentController::class, 'show'])->name('equipment.show');
    Route::get('_equipment/search', [EquipmentController::class, 'search'])->name('equipment.search');
    Route::get('/_equipment/get-devices', [EquipmentController::class, 'getDevices'])->name('equipment.get_devices');
    Route::post('_equipment/update', [EquipmentController::class, 'update'])->name('equipment.update');
    Route::delete('equipment', [EquipmentController::class, 'destroy'])->name('equipment.destroy');
    Route::get('_free-equipments/search', [EquipmentController::class, 'searchFreeEquipments'])->name('equipments.free.search');
    Route::get('equipments/export', [EquipmentController::class, 'exportEquipments'])->name('equipments.export');
    Route::match(['get', 'post'], 'equipment/check-wialon', [EquipmentController::class, 'checkEquipment'])->name('equipments.check_wialon');

    /* Sim-Карти */
    Route::get('sim-cards', [SimCardController::class, 'index'])->name('sim_cards');
    Route::post('sim-card/store', [SimCardController::class, 'store'])->name('sim_card.store');
    Route::get('_sim-card/show', [SimCardController::class, 'show'])->name('sim_card.show');
    Route::get('_sim-card/search', [SimCardController::class, 'search'])->name('sim_card.search');
    Route::post('sim-card/update', [SimCardController::class, 'update'])->name('sim_card.update');
    Route::delete('sim-card', [SimCardController::class, 'destroy'])->name('sim_card.destroy');
    Route::post('sim-card/import', [SimCardController::class, 'import'])->name('sim_card.import');
    Route::get('sim-card/{id}', [SimCardController::class, 'view'])->name('sim-card');

    /* Тарифи */
    Route::get('tariffs', [TariffController::class, 'index'])->name('tariffs');
    Route::post('tariff/store', [TariffController::class, 'store'])->name('tariff.store');
    Route::get('_tariff/show', [TariffController::class, 'show'])->name('tariff.show');
    Route::post('tariff/update', [TariffController::class, 'update'])->name('tariff.update');
    Route::delete('tariff', [TariffController::class, 'destroy'])->name('tariff.destroy');

    /* Отчетность */
    Route::get('reporting', [ReportController::class, 'index'])->name('reporting');
    Route::post('_reporting/store', [ReportController::class, 'store'])->name('reporting.store');
    Route::get('reporting-export', [ReportController::class, 'export'])->name('reporting.export');
    Route::post('_reporting/update', [ReportController::class, 'update'])->name('reporting.update');
    Route::delete('reporting', [ReportController::class, 'destroy'])->name('reporting.destroy');
    Route::get('_reporting/show', [ReportController::class, 'show'])->name('reporting.show');
    Route::get('reporting/register/{client_id}', [ReportController::class, 'clientRegister'])->name('reporting.register');


    /* Аналитика */
    Route::get('analytics', [AnalyticController::class, 'index'])->name('analytics');
    Route::get('analytics/get-earnings', [AnalyticController::class, 'getEarnings'])->name('earnings');

    /* Повідомлення */
    Route::get('notifications', [NotificationController::class, 'index'])->name('notifications');
    Route::get('_notification/status', [NotificationController::class, 'changeStatus'])->name('notification.status');
    Route::get('_notification/search', [NotificationController::class, 'search'])->name('notification.search');
    Route::delete('notification', [NotificationController::class, 'destroy'])->name('notification.destroy');
    Route::get('delete-notifications', [NotificationController::class, 'destroyNotifications'])->name('notifications.destroy');

    /* Настройки */
    Route::get('setting', [SettingController::class, 'index'])->name('setting');
    Route::post('setting-update', [SettingController::class, 'updateSetting'])->name('update_setting');
    Route::get('token-redirect', [SettingController::class, 'tokenRedirect'])->name('token_redirect');

    /* Користувачі */
    Route::post('_user/store', [UserController::class, 'store'])->name('user.store');
    Route::get('_user/show', [UserController::class, 'show'])->name('user.show');
    Route::post('_user/update', [UserController::class, 'update'])->name('user.update');
    Route::delete('user', [UserController::class, 'destroy'])->name('user.destroy');


});

/* Оновлення бази */
Route::get('/db', [UpdateDbController::class, 'index']);
Route::get('/clear', [HomeController::class, 'clearCache']);
Route::get('/update', [HomeController::class, 'update']);
Route::get('/update-operators-id', [HomeController::class, 'updateOperatorsId']);
Route::get('/update-clients', [HomeController::class, 'updateClients']);
Route::get('/home', [HomeController::class, 'index'])->name('home');

Route::get('/migrate', function () {
    Artisan::call('migrate');
});
