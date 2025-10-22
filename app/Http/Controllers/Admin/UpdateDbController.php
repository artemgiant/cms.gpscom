<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\DbService;

class UpdateDbController extends Controller
{
    /**
     * Оновлення бд
     */
    public function index()
    {
        DbService::updateClients();
        DbService::updateTariffs();
        DbService::updateEquipments();
        DbService::updateSimCards();
        DbService::updateInstallations();
        DbService::updateSensors();

        return '/';
    }
}
