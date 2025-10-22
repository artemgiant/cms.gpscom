<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Operator;
use App\Models\SimCard;
use App\Models\User;
use App\Models\Equipment;

use App\Services\ApiService;
use App\Services\EquipmentService;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class HomeController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        if (Auth::check() && $user->role_id == User::ADMIN) {
            return redirect()->route('clients');
        }
        return view('admin.auth.login');
    }

    public function clearCache()
    {
        Artisan::call('cache:clear');
        Artisan::call('config:cache');
        Artisan::call('route:cache');

        return redirect()->back();
    }

    public function update()
    {
        EquipmentService::setTariffToEmptyEquipment();
        EquipmentService::updateEquipments();

//        EquipmentService::updateEquipments();
//        ApiService::deleteEquipment();
//        ApiService::updateEquipmentsClients();

        // ApiService::deactiveEquipments();
        // ApiService::updateClients();
        // ApiService::getEquipments();
        // ApiService::updateEquipmentsClients();
//        ApiService::getEquipments();

        return "ok";
    }

    public function updateOperatorsId()
    {
        $operators = Operator::all();

        foreach ($operators as $operator) {
            SimCard::where('operator', $operator->slug)
                ->update([
                    'operator_id' => $operator->id,
                ]);

            Equipment::where('operator', $operator->slug)
                ->update([
                    'operator_id' => $operator->id,
                ]);
        }

        return "ok";
    }

    public function updateClients()
    {
        $clients = Client::all();

        foreach ($clients as $client) {
            if ($client->contract_date == '1970-01-01 00:00:00') {
                $client->contract_date = null;
                $client->update();
            }
        }

        return "ok";
    }
}
