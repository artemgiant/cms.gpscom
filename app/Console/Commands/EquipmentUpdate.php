<?php

namespace App\Console\Commands;

use App\Services\ApiService;
use App\Services\EquipmentService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class EquipmentUpdate extends Command
{

    protected $signature = 'update_equipments';


    protected $description = 'Update equipments';


    public function __construct()
    {
        parent::__construct();
    }


    public function handle()
    {
        $files = glob('/var/www/vhosts/gpscom.kz/.cagefs/tmp/*');
        if ($files) {
            foreach ($files as $file) {
                if (is_file($file)) {
                    unlink($file);
                }
            }
        }
        ApiService::saveAnalitycs();

        Log::info('Update equipments ' . date('Y-m-d H:i:s'));
        EquipmentService::updateEquipments();

        Log::info('Update clients equipments' . date('Y-m-d H:i:s'));
        ApiService::updateEquipmentsClients();

        Log::info('Deactivate equipments ' . date('Y-m-d H:i:s'));
        ApiService::deactiveEquipments();

        Log::info('Update clients data ' . date('Y-m-d H:i:s'));
        ApiService::updateClients();

        Log::info('Delete equipment ' . date('Y-m-d H:i:s'));
        ApiService::deleteEquipment();

        Log::info('Update operator id ' . date('Y-m-d H:i:s'));
        ApiService::updateOperatorId();
        Log::info('-------------------------------------------------------------------');
        return 1;
    }
}
