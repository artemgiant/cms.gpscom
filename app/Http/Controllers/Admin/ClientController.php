<?php

namespace App\Http\Controllers\Admin;

use App\Exports\ClientEquipmentExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\ClientEquipmentStoreRequest;
use App\Http\Requests\ClietStoreRequest;
use App\Http\Requests\ClietUpdateRequest;
use App\Models\Client;
use App\Models\ClientTariff;
use App\Models\Equipment;
use App\Models\Installation;
use App\Models\Sensor;
use App\Models\SimCard;
use App\Models\Tariff;
use App\Models\User;
use App\Models\SimCardHistory;
use App\Services\ApiService;
use App\Services\ClientService;
use App\Services\NotificationService;
use App\Services\SettingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Auth;

class ClientController extends Controller
{
    public function index()
    {
        $clients = Client::orderBy('id', 'desc')->paginate();
        return view('admin.client.index', compact('clients'));
    }

    public function store(ClietStoreRequest $request)
    {
        $client = new Client();
        $client->client_id = $request->client_id;
        $client->client_type = $request->client_type;
        $client->name = $request->name;
        $client->account = $request->account;
        $client->contract_number = $request->contract_number;
        $client->contract_date = date('Y-m-d H:i:s', strtotime($request->contract_date));
        $client->person = $request->person;
        $client->phone = SettingService::convertPhone($request->phone);
        $client->manager = $request->manager;
        $client->accountant_phone = $request->accountant_phone;
        $client->email = $request->email;
        $client->save();
        NotificationService::addNotification(User::ADMIN, null, $client->id, null, 'Клиент добавлен ' . $client->name);

        return response()->json([
            'status' => true,
            'message' => 'Клиент удачно добавлен',
        ]);
    }

    public function show(Request $request)
    {
        $client = Client::find($request->id);

        if (!$client) {
            return response()->json([
                'error' => 'Client not found',
            ]);
        }
        $client->contract_date = date('Y-m-d', strtotime($client->contract_date));
        $client = $client->getAttributes();

        return response()->json([
            'client' => $client,
        ]);
    }

    public function search(Request $request)
    {
        $q = $request->search_value;

        if (isset($q)) {

            $clients = Client::where('name', 'like', "%$q%")
                ->orWhere('account', 'like', "%$q%")
                ->orWhere('contract_number', 'like', "%$q%")
                ->orWhere('person', 'like', "%$q%")
                ->orWhere('phone', 'like', "%$q%")
                ->orderBy('id', 'desc')
                ->take(10)
                ->get();

        } else {
            $clients = Client::orderBy('id', 'desc')->paginate();
        }

        $html = view('admin.client.modals.table_item', [
            'clients' => $clients,
        ])->render();

        return response()->json(['html' => $html]);
    }

    public function update(ClietUpdateRequest $request)
    {
        $client = Client::find($request->id);
        $client->client_id = $request->client_id;
        $client->client_type = $request->client_type;
        $client->name = $request->name;
        $client->account = $request->account;
        $client->contract_number = $request->contract_number;
        $client->contract_date = date('Y-m-d H:i:s', strtotime($request->contract_date));
        $client->person = $request->person;
        $client->phone = SettingService::convertPhone($request->phone);
        $client->manager = $request->manager;
        $client->accountant_phone = $request->accountant_phone;
        $client->email = $request->email;
        $client->save();

        return response()->json([
            'status' => true,
            'message' => 'Клиент успешно отредактирован',
        ]);
    }

    public function destroy(Request $request)
    {
        $client = Client::findOrFail($request->id);
        if ($client) {
            $client->delete();
        }

        return redirect()->back()->with('success', 'Клиент успешно удалён');
    }

    public function view(Request $request, $client_id)
    {
        $tariffs = Tariff::all();
        $client = Client::find($client_id);
        $freeEquipments = Equipment::where('status', false)->take(50)->get();
        $clients = Client::where('id', '!=', $client->id)->get();
        $equipments = Equipment::with('operatorData')
            ->where('client_id', $client_id)
            ->orderBy('date_start', 'desc')
            ->get();

        return view('admin.client.view', compact('tariffs', 'client', 'clients', 'equipments', 'freeEquipments'));
    }

    /**
     * Создать и поключить обьєкт
     * @param ClientEquipmentStoreRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function clientEquipmentStore(ClientEquipmentStoreRequest $request)
    {
        $equipment = new Equipment();
        $equipment->object = $request->object;
        $equipment->device = $request->device;
        $equipment->imei = $request->imei;
        $equipment->phone = SettingService::convertPhone($request->phone);
        $equipment->phone2 = SettingService::convertPhone($request->phone2);
        $equipment->date_start = date('Y-m-d H:i:s', strtotime($request->date_start));
        $equipment->tariff_id = $request->tariff_id;
        $equipment->client_id = $request->client_id;
        $equipment->status = Equipment::ACTIVE;

        if ($equipment->save()) {
            $simCard = SimCard::where('phone', (int)$request->phone)->first();

            if ($simCard) {
                $simCard->status = true;
                $simCard->equipment_id = $equipment->id;
                $simCard->update();
            }
        }

        return response()->json([
            'status' => true,
            'message' => 'Объект успешно добавлен и подключен',
        ]);
    }

    /**
     * Добавить обьєкт
     * @param ClientEquipmentStoreRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function clientFreeEquipmentStore(Request $request)
    {
        $client_id = $request->client_id;
        $equipments = $request->equipments;

        if ($equipments) {
            foreach ($equipments as $id) {
                $equipment = Equipment::find($id);
                if ($equipment) {
                    $equipment->client_id = $client_id;
                    $equipment->status = Equipment::ACTIVE;
                    $equipment->date_start = date('Y-m-d H:i:s');
                    $equipment->date_end = null;
                    $equipment->update();
                }
            }
        }

        return redirect()->back()->with('success', 'Объект успешно добавлен и подключен');
    }

    /**
     * Пошук обладнання клієнта
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function clientEquipmentSearch(Request $request)
    {
        $q = $request->search_value;
        $client_id = $request->client_id;

        if (isset($q)) {

            $equipments = Equipment::where('client_id', $client_id)
                ->where(function ($builder) use ($q) {
                    if (isset($q)) {
                        $builder->where('object', 'like', "%$q%")
                            ->orWhere('imei', 'like', "%$q%")
                            ->orWhere('phone', 'like', "%$q%");
                    }
                })->orderBy('id', 'desc')
                ->take(10)
                ->get();

        } else {
            $equipments = Equipment::where('client_id', $client_id)->get();
        }

        $html = view('admin.client.modals.equipment.table_item', [
            'equipments' => $equipments,
        ])->render();

        return response()->json(['html' => $html]);
    }

    public function clientEquipmentsExport(Request $request)
    {
        $date = $request->date;
        $client_id = $request->client_id;

        return Excel::download(new ClientEquipmentExport($date, $client_id), 'client_equipment_export' . '_' . time() . '.xlsx');
    }

    /**
     * Підлючити обладнання
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function startEquipment(Request $request)
    {
        $equipment_id = $request->equipment_id;
        $equipment = Equipment::find($equipment_id);

        if ($equipment) {
            $date_start = date('d.m.Y');

            $equipment->date_start = date('Y-m-d H:i:s', strtotime($date_start));
            $equipment->date_end = null;
            $equipment->status = Equipment::ACTIVE;
            $equipment->update();
            $sim_card_id = SimCard::where('equipment_id', $equipment->id)->first();
            if ($sim_card_id) {
                if (!SimCardHistory::where([['sim_card_id', $sim_card_id->id], ['equipment_id', $equipment->id]])->orderBy('id', 'DESC')->first() || (SimCardHistory::where([['sim_card_id', $sim_card_id->id], ['equipment_id', $equipment->id]])->orderBy('id', 'DESC')->first() && SimCardHistory::where([['sim_card_id', $sim_card_id->id], ['equipment_id', $equipment->id]])->orderBy('id', 'DESC')->first()->operation != "Подключение")) {
                    $sim_card_history = new SimCardHistory();
                    $sim_card_history->create([
                        'sim_card_id' => $sim_card_id ? $sim_card_id->id : null,
                        'equipment_id' => $equipment->id,
                        'operation_date_time' => date('Y-m-d H:i:s'),
                        'user_id' => Auth::id(),
                        'operation' => 'Подключение',

                    ]);
                }
            } else {
                if (!SimCardHistory::whereNull('sim_card_id')->andWhere([['equipment_id', $equipment->id]])->orderBy('id', 'DESC')->first() || (SimCardHistory::whereNull('sim_card_id')->andWhere([['equipment_id', $equipment->id]])->orderBy('id', 'DESC')->first() && SimCardHistory::whereNull('sim_card_id')->andWhere([['equipment_id', $equipment->id]])->orderBy('id', 'DESC')->first()->operation != "Подключение")) {
                    $sim_card_history = new SimCardHistory();
                    $sim_card_history->create([
                        'sim_card_id' => $sim_card_id ? $sim_card_id->id : null,
                        'equipment_id' => $equipment->id,
                        'operation_date_time' => date('Y-m-d H:i:s'),
                        'user_id' => Auth::id(),
                        'operation' => 'Подключение',
                    ]);
                }
            }
        }
        NotificationService::addNotification(Auth::id(), $equipment->id, null, null, 'Объект подключен ' . $equipment->object);

        return response()->json([
            'status' => true,
            'date_start' => $date_start,
            'message' => 'Объект успешно подключен',
        ]);
    }

    /**
     * Відключити обладнання
     * @param Request $request
     */
    public function endEquipment(Request $request)
    {
        $equipment_id = $request->equipment_id;
        $equipment = Equipment::find($equipment_id);

        if ($equipment) {
            $data_end = date('d.m.Y');

            $equipment->status = Equipment::DELETE;
            $equipment->date_end = date('Y-m-d H:i:s', strtotime($data_end));
            $equipment->custom_update = 1;
            $equipment->update();
            NotificationService::addNotification(Auth::id(), $equipment->id, null, null, 'Объект отключен ' . $equipment->object);

            $sim_card_id = SimCard::where('equipment_id', $equipment->id)->first();
            if ($sim_card_id) {
                if (!SimCardHistory::where([['sim_card_id', $sim_card_id->id], ['equipment_id', $equipment->id]])->orderBy('id', 'DESC')->first() || (SimCardHistory::where([['sim_card_id', $sim_card_id->id], ['equipment_id', $equipment->id]])->orderBy('id', 'DESC')->first() && SimCardHistory::where([['sim_card_id', $sim_card_id->id], ['equipment_id', $equipment->id]])->orderBy('id', 'DESC')->first()->operation != "Отключение")) {
                    $sim_card_history = new SimCardHistory();
                    $sim_card_history->create([
                        'sim_card_id' => $sim_card_id ? $sim_card_id->id : null,
                        'equipment_id' => $equipment->id,
                        'operation_date_time' => date('Y-m-d H:i:s'),
                        'user_id' => Auth::id(),
                        'operation' => 'Отключение',

                    ]);
                }
            } else {
                if (!SimCardHistory::whereNull('sim_card_id')->andWhere([['equipment_id', $equipment->id]])->orderBy('id', 'DESC')->first() || (SimCardHistory::whereNull('sim_card_id')->andWhere([['equipment_id', $equipment->id]])->orderBy('id', 'DESC')->first() && SimCardHistory::whereNull('sim_card_id')->andWhere([['equipment_id', $equipment->id]])->orderBy('id', 'DESC')->first()->operation != "Отключение")) {
                    $sim_card_history = new SimCardHistory();
                    $sim_card_history->create([
                        'sim_card_id' => $sim_card_id ? $sim_card_id->id : null,
                        'equipment_id' => $equipment->id,
                        'operation_date_time' => date('Y-m-d H:i:s'),
                        'user_id' => Auth::id(),
                        'operation' => 'Отключение',
                    ]);
                }
            }
        }

        return response()->json([
            'status' => true,
            'date_end' => $data_end,
            'message' => 'Объект успешно отключено',
        ]);
    }

    /**
     * Деактивація обєктів
     * @param Request $request
     */
    public function deactiveEquipment(Request $request)
    {
        $equipment_id = $request->equipment_id;
        $equipment = Equipment::find($equipment_id);
        $data_end = date('d.m.Y');

        if ($equipment) {
            $equipment->status = Equipment::DEACTIVE;
            $equipment->date_end = date('Y-m-d H:i:s', strtotime($data_end));
            $equipment->custom_update = 1;

            if ($equipment->update()) {
            }
            NotificationService::addNotification(Auth::id(), $equipment->id, null, null, 'Объект деактивирован ' . $equipment->object);

            $sim_card_id = SimCard::where('equipment_id', $equipment->id)->first();

            if ($sim_card_id) {
                if (!SimCardHistory::where([['sim_card_id', $sim_card_id->id], ['equipment_id', $equipment->id]])->orderBy('id', 'DESC')->first() || (SimCardHistory::where([['sim_card_id', $sim_card_id->id], ['equipment_id', $equipment->id]])->orderBy('id', 'DESC')->first() && SimCardHistory::where([['sim_card_id', $sim_card_id->id], ['equipment_id', $equipment->id]])->orderBy('id', 'DESC')->first()->operation != "Деактивирование")) {
                    $sim_card_history = new SimCardHistory();
                    $sim_card_history->create([
                        'sim_card_id' => $sim_card_id ? $sim_card_id->id : null,
                        'equipment_id' => $equipment->id,
                        'operation_date_time' => date('Y-m-d H:i:s'),
                        'user_id' => Auth::id(),
                        'operation' => 'Деактивирование'
                    ]);
                }
            } else {
                if (!SimCardHistory::whereNull('sim_card_id')->andWhere([['equipment_id', $equipment->id]])->orderBy('id', 'DESC')->first() || (SimCardHistory::whereNull('sim_card_id')->andWhere([['equipment_id', $equipment->id]])->orderBy('id', 'DESC')->first() && SimCardHistory::whereNull('sim_card_id')->andWhere([['equipment_id', $equipment->id]])->orderBy('id', 'DESC')->first()->operation != "Деактивирование")) {
                    $sim_card_history = new SimCardHistory();
                    $sim_card_history->create([
                        'sim_card_id' => $sim_card_id ? $sim_card_id->id : null,
                        'equipment_id' => $equipment->id,
                        'operation_date_time' => date('Y-m-d H:i:s'),
                        'user_id' => Auth::id(),
                        'operation' => 'Деактивирование'
                    ]);
                }
            }
        }

        return response()->json([
            'status' => true,
            'date_end' => $data_end,
            'message' => 'Объект успешно деактивирован',
        ]);
    }

    /**
     * Показати дані обєкт клієнта
     * @param Request $request
     */
    public function showEquipment(Request $request)
    {
        $equipment = Equipment::find($request->id);
        $sensors = Sensor::where('equipment_id', $request->id)->orderBy('id', 'desc')->get();
        $installations = Installation::where('equipment_id', $request->id)->orderBy('id', 'desc')->get();

        if (!$equipment) {
            return response()->json([
                'error' => 'Equipment not found',
            ]);
        }

        $equipment = $equipment->getAttributes();

        return response()->json([
            'equipment' => $equipment,
            'sensors' => $sensors,
            'installations' => $installations
        ]);
    }

    /**
     * Перемістити обєкт клієнта
     * @param Request $request
     */
    public function moveEquipment(Request $request)
    {
        $client_id = $request->client_id;
        $equipment_id = $request->equipment_id;

        $equipment = Equipment::find($equipment_id);
        if ($equipment) {
            $equipment->client_id = $client_id;
            $equipment->status = Equipment::ACTIVE;
            $equipment->date_start = date('Y-m-d H:i:s');
            $equipment->update();

            $sim_card_id = SimCard::where('equipment_id', $equipment->id)->first();

            if ($sim_card_id) {
                if (!SimCardHistory::where([['sim_card_id', $sim_card_id->id], ['equipment_id', $equipment->id]])->orderBy('id', 'DESC')->first() || (SimCardHistory::where([['sim_card_id', $sim_card_id->id], ['equipment_id', $equipment->id]])->orderBy('id', 'DESC')->first() && SimCardHistory::where([['sim_card_id', $sim_card_id->id], ['equipment_id', $equipment->id]])->orderBy('id', 'DESC')->first()->operation != "Перемещение")) {
                    $sim_card_history = new SimCardHistory();
                    $sim_card_history->create([
                        'sim_card_id' => $sim_card_id ? $sim_card_id->id : null,
                        'equipment_id' => $equipment->id,
                        'operation_date_time' => date('Y-m-d H:i:s'),
                        'user_id' => Auth::id(),
                        'operation' => 'Перемещение',
                        'replace_to_acc_id' => $client_id

                    ]);
                }
            } else {
                if (!SimCardHistory::whereNull('sim_card_id')->andWhere([['equipment_id', $equipment->id]])->orderBy('id', 'DESC')->first() || (SimCardHistory::whereNull('sim_card_id')->andWhere([['equipment_id', $equipment->id]])->orderBy('id', 'DESC')->first() && SimCardHistory::whereNull('sim_card_id')->andWhere([['equipment_id', $equipment->id]])->orderBy('id', 'DESC')->first()->operation != "Перемещение")) {
                    $sim_card_history = new SimCardHistory();
                    $sim_card_history->create([
                        'sim_card_id' => $sim_card_id ? $sim_card_id->id : null,
                        'equipment_id' => $equipment->id,
                        'operation_date_time' => date('Y-m-d H:i:s'),
                        'user_id' => Auth::id(),
                        'operation' => 'Перемещение',
                        'replace_to_acc_id' => $client_id
                    ]);
                }
            }
        }
        NotificationService::addNotification(Auth::id(), $equipment->id, null, null, 'Объект перемещен ' . $equipment->object);

        return response()->json([
            'status' => true,
            'message' => 'Объект успешно переместищен',
        ]);
    }

    /**
     * Видалити обладення в клієнта
     */
    public function deleteEquipment(Request $request)
    {
        $equipment_id = $request->equipment_id;

        $equipment = Equipment::find($equipment_id);
        if ($equipment) {
            $equipment->client_id = null;
            $equipment->status = Equipment::DELETE;

            if ($equipment->update()) {
                $simCard = SimCard::where('phone', (int)$request->phone)->first();

                if ($simCard) {
                    $simCard->status = false;
                    $simCard->equipment_id = null;
                    $simCard->update();
                }
            }
        }
        NotificationService::addNotification(Auth::id(), $equipment->id, null, null, 'Объект удалён ' . $equipment->object);


        return response()->json([
            'status' => true,
            'message' => 'Объект успешно удалён',
        ]);
    }

    /**
     * Отримати тариф
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function clientTariffShow(Request $request)
    {
        $tariff_id = $request->id;
        $client_id = $request->client_id;
        $clients_tariff_id = $request->clients_tariff_id;

        $clientTariff = ClientTariff::find($clients_tariff_id);

        $price = 0;
        if ($clientTariff && $clientTariff->price) {
            $price = $clientTariff->price;
        } else {
            $tariff = Tariff::find($tariff_id);

            $price = $tariff->price;
        }

        return response()->json([
            'price' => $price,
        ]);
    }

    /**
     * Збереження тарифу
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function clientTariffStore(Request $request)
    {
        $clientTariff = new ClientTariff();
        $clientTariff->client_id = $request->client_id;
        $clientTariff->tariff_id = $request->tariff_id;

        if ($request->price > 0) {
            $clientTariff->price = $request->price;
        }

        $clientTariff->save();

        return redirect()->back()->with('success', 'Тариф успешно сохранен');
    }

    /**
     * Оновлення тарифа слієнта
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function clientTariffUpdate(Request $request)
    {
        $clientTariff = ClientTariff::find($request->clients_tariff_id);

        if ($clientTariff) {
            $clientTariff->price = $request->price;
            $clientTariff->update();
        }

        return redirect()->back()->with('success', 'Тариф успешно сохранен');
    }

    /**
     * Видалення тарифу клієнта
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function clientTariffDelete(Request $request)
    {
        $clientTariff = ClientTariff::where('tariff_id', $request->tariff_id)
            ->where('client_id', $request->client_id)
            ->first();

        if ($clientTariff) {
            $clientTariff->delete();
        }

        Session::flash('success', 'Тариф успешно удален');

        return response()->json([
            'status' => true
        ]);
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function updateClientWialon(Request $request, $id)
    {
        $clientUser = Client::find($id);

        try {
            ClientService::updateEquipmentsClientsData($clientUser);
            ClientService::updateEquipmentsClients($clientUser);
            ClientService::updateClient($clientUser);
        } catch (\Exception $e) {
            Log::info('Update client error: ' . $e->getMessage());
        }

        return redirect()->back()->with('success', 'Информация о клиенте и его оборудовании успешно обновлена!');
    }




}
