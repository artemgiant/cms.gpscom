<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Client;
use App\Models\Installation;
use App\Models\Equipment;
use App\Models\ClientTariff;
use App\Models\Tariff;
use App\Models\Analitic;
use Carbon\Carbon;
use App\Services\SettingService;
use DateTime;

class AnalyticController extends Controller
{
    public function index(Request $request)
    {
        $client_types = ["ip", "fl", "too"];
        foreach ($client_types as $key => $client_type) {

            $query = Client::query();
            $query->select('clients.*');
            $query->leftJoin('equipments', 'equipments.client_id', 'clients.id');
            $query->groupBy('clients.id');

            $query->where('clients.client_type', $client_type);
            ${"total_" . $client_type} = ${"total_" . $client_type . "_act"} = ${"total_" . $client_type . "_deact"} = 0;
            $rows = $query->get();
            foreach ($rows as $row) {
                ${"total_" . $client_type} += $row->getEquipments();
                ${"total_" . $client_type . "_act"} += $row->getReportingEquipment();
                ${"total_" . $client_type . "_deact"} += $row->getDeactiveEquipments();
            }
        }

        $data = Analitic::orderBy('id', 'DESC')->limit(12)->get([
            'active_counts_ip',
            'active_counts_fl',
            'active_counts_too',
            'earnings_ip',
            'earnings_fl',
            'earnings_too',
        ]);

        $active_counts_ip = $data->pluck('active_counts_ip')->toArray();
        $active_counts_fl = $data->pluck('active_counts_fl')->toArray();
        $active_counts_too = $data->pluck('active_counts_too')->toArray();

        $graphic_total_ip = $data->pluck('earnings_ip')->toArray();
        $graphic_total_fl = $data->pluck('earnings_fl')->toArray();
        $graphic_total_too = $data->pluck('earnings_too')->toArray();

        $activeTotal = [];
        $graphicTotal = [];
        foreach ($graphic_total_ip as $k => $value) {
            $activeСountsIP = $active_counts_ip[$k];
            $activeCountsFL = $active_counts_fl[$k];
            $activeCountsTOO = $active_counts_too[$k];

            $activeTotal[] = round($activeСountsIP + $activeCountsFL + $activeCountsTOO, 2);

            $graphicTotalIp = $graphic_total_ip[$k];
            $graphicTotalFL = $graphic_total_fl[$k];
            $graphicTotalTOO = $graphic_total_too[$k];

            $graphicTotal[] = round($graphicTotalIp + $graphicTotalFL + $graphicTotalTOO, 2);
        }

        return view('admin.analytic.index', compact('total_ip', 'total_too', 'total_fl', 'total_ip_act', 'total_fl_act', 'total_too_act', 'total_ip_deact', 'total_fl_deact', 'total_too_deact', 'graphic_total_ip', 'graphic_total_fl', 'graphic_total_too', 'active_counts_ip', 'active_counts_fl', 'active_counts_too', 'graphicTotal', 'activeTotal'));
    }

    public function getEarnings()
    {
        $client_types = ["ip", "fl", "too"];
        foreach ($client_types as $key => $client_type) {

            $query = Client::query();
            $query->select('clients.*', 'equipments.date_end');
            $query->leftJoin('equipments', 'equipments.client_id', 'clients.id');
            $query->groupBy('clients.id');
            $query->where('clients.client_type', $client_type);
            $query->orderBy('clients.id', 'asc');
            $rows = $query->get();

            $total = 0;

            foreach ($rows as $row) {
                $price = $row->getPerMountPrice();
                $total += $price;
            }
            ${"earnings_" . $client_type} = round($total / 1000, 2);
        }
        return response()->json(['earnings_ip' => $earnings_ip, 'earnings_fl' => $earnings_fl, 'earnings_too' => $earnings_too]);
    }
}
