<?php

namespace App\Http\Controllers\Admin;

use App\Exports\ReportingExportAvance;
use App\Exports\ReportingExportInstalation;
use App\Exports\ReportingExportPerMounth;
use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Equipment;
use App\Models\Installation;
use App\Models\Tariff;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Requests\ReportUpdateRequest;


class ReportController extends Controller
{
    public function index(Request $request)
    {
        if ($request->type == Client::REPORT_PER_MONTH ||
            $request->type == Client::REPORT_ADVANCE ||
            !$request->type) {

            $query = Client::query();
            $query->select('clients.*', 'equipments.date_end');
            $query->leftJoin('equipments', 'equipments.client_id', 'clients.id');
            $query->groupBy('clients.id');

            /* Тип компании */
            if ($request->client_type) {
                $query->where('client_type', $request->client_type);
            } else {
                $query->where('client_type', 'ip');
            }


            $query->orderBy('clients.id', 'asc');
            $clients = $query->get();

            return view('admin.reporting.index', compact('clients'));

        } elseif ($request->type == Client::REPORT_MOUNTING) {
            $query = Installation::query();

            /* Месяц */
            if ($request->mount) {
                $data = explode('.', $request->mount);
                $year = (int)$data[0];
                $mount = (int)$data[1];

                $query->whereMonth('date_create', $mount);
                $query->whereYear('date_create', $year);
            } else {
                $query->whereMonth('date_create', date('m'));
                $query->whereYear('date_create', date('Y'));
            }

            $installation = $query->get();

            return view('admin.reporting.index', compact('installation'));
        }
    }

    public function view(Request $request, $client_id)
    {
        $tariffs = Tariff::all();
        $client = Client::find($client_id);
        $equipments = Equipment::where('client_id', $client_id)->orderBy('date_start', 'desc')->get();
        $freeEquipments = Equipment::where('status', false)->take(50)->get();
        $clients = Client::where('id', '!=', $client->id)->get();

        return view('admin.reporting.index', compact('tariffs', 'client', 'clients', 'equipments', 'freeEquipments'));
    }

    /**
     * Ексорт звітності
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function export(Request $request)
    {
        switch ($request->type) {
            case Client::REPORT_PER_MONTH:
                $excel = Excel::download(new ReportingExportPerMounth(), 'report_per_mount_export' . '_' . time() . '.xlsx');
                break;
            case Client::REPORT_ADVANCE:
                $excel = Excel::download(new ReportingExportAvance(), 'report_advance_export' . '_' . time() . '.xlsx');
                break;
            case Client::REPORT_MOUNTING:
                $excel = Excel::download(new ReportingExportInstalation(), 'report_installation_export' . '_' . time() . '.xlsx');
                break;
            default:
                $excel = Excel::download(new ReportingExportPerMounth(), 'report_per_mount_export' . '_' . time() . '.xlsx');
        }

        return $excel;
    }

    public function store(ReportUpdateRequest $request)
    {
        $client = new Client();
        $client->client_id = $request->client_id;
        $client->client_type = $request->client_type;
        $client->name = $request->name;
        $client->account = $request->account;
        $client->contract_number = $request->contract_number;
        $client->contract_date = date('Y-m-d H:i:s', strtotime($request->contract_date));
        $client->save();
        NotificationService::addNotification(User::ADMIN, null, $client->id, null, 'Отчет добавлен ' . $client->name);

        return response()->json([
            'status' => true,
            'message' => 'Отчет удачно добавлен',
        ]);
    }

    public function show(Request $request)
    {
        $client = Client::find($request->id);
        $price = $client->getPerMountPrice();
        $start = $client->getEquipments();
        $end = $client->getReportingEquipment();
        if (!$client) {
            return response()->json([
                'error' => 'Client not found',
            ]);
        }
        $client->contract_date = date('Y-m-d', strtotime($client->contract_date));
        $client = $client->getAttributes();
        $client['price'] = $price;
        $client['start'] = $start;
        $client['end'] = $end;

        return response()->json([
            'client' => $client
        ]);
    }

    public function update(ReportUpdateRequest $request)
    {
        $client = Client::find($request->id);
        $client->update([
            'client_id' => $request->client_id,
            'client_type' => $request->client_type,
            'name' => $request->name,
            'account' => $request->account,
            'contract_number' => $request->contract_number,
            'contract_date' => date('Y-m-d H:i:s', strtotime($request->contract_date)),

        ]);
        // $client->price = $request->price;
        // $client->start = $request->start;
        // $client->end = $request->end;


        return response()->json([
            'status' => true,
            'message' => 'Отчет успешно отредактирован',
        ]);
    }

    public function clientRegister(Request $request, $client_id)
    {
        $data['client'] = Client::find($client_id);
        $data['equipments'] = Equipment::where('client_id', $client_id)->orderBy('date_start', 'desc')->get();
        $data['fixedEquipmentPrice'] = $data['client']->getFixedEquipmentPrice();

        return view('admin.client.register', $data);
    }

}
