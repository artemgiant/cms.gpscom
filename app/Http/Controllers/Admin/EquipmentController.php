<?php

namespace App\Http\Controllers\Admin;

use App\Exports\EquipmentExport;
use App\Exports\UsersExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\EquipmentStoreRequest;
use App\Http\Requests\EquipmentUpdateRequest;
use App\Models\Equipment;
use App\Models\Operator;
use App\Models\SimCard;
use App\Models\Tariff;
use App\Models\SimCardHistory;
use App\Services\ApiService;
use App\Services\EquipmentService;
use App\Services\NotificationService;
use App\Services\SettingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class EquipmentController extends Controller
{
    public function index(Request $request)
    {
        $tariffs = Tariff::all();
        $query = Equipment::query();

        $q = $request->q;

        if (!empty($q)) {
            $query->join('clients', 'clients.id', '=', 'equipments.client_id')
                ->where(function ($subQuery) use ($q) {
                    $subQuery->where('equipments.object', 'like', "%$q%")
                        ->orWhere('equipments.imei', 'like', "%$q%")
                        ->orWhere('equipments.phone', 'like', "%$q%")
                        ->orWhere('clients.account', 'like', "%$q%")
                        ->orWhere('clients.name', 'like', "%$q%");
                })
                ->orderBy('equipments.id', 'desc')
                ->limit(10);
        }

        $equipments = $query->orderBy('equipments.id', 'desc')->paginate();
        return view('admin.equipment.index', compact('tariffs', 'equipments'));
    }

    public function store(EquipmentStoreRequest $request)
    {
        $equipment = new Equipment();
        $equipment->fill([
            'object' => $request->object,
            'device' => $request->device,
            'imei' => $request->imei,
            'phone' => SettingService::convertPhone($request->phone),
            'phone2' => SettingService::convertPhone($request->phone2),
            'date_start' => null,
            'date_end' => null,
        ]);

        $simCard = SimCard::where('phone', $equipment->phone)->first();
        $equipment->tariff_id = $this->determineTariff($simCard);

        if (!empty($request->tariff_id)) {
            $equipment->tariff_id = $request->tariff_id;
        }

        $this->clearPhoneNumbers($equipment->phone, $equipment->phone2);

        if ($equipment->save()) {
            if ($simCard) {
                $simCard->update([
                    'status' => true,
                    'equipment_id' => $equipment->id,
                ]);
            }
        }

        NotificationService::addNotification(Auth::id(), $equipment->id, null, null, 'Объект создан ' . $equipment->object);

        return response()->json([
            'status' => true,
            'message' => 'Устройство успешно сохранено',
        ]);
    }

    private function determineTariff($simCard)
    {
        if (!$simCard) {
            return Tariff::where('title', 'like', "%Без сим%")->first()->id;
        }

        $operator = strtolower($simCard->operator);

        if ($operator === 'm2m') {
            $operatorData = Operator::with('tariff')->where('slug', 'm2m')->first();

            if ($operatorData) {
                return $operatorData->tariff->id;
            } else {
                return Tariff::where('title', 'like', "%Без сим%")->first()->id;
            }
        }

        if ($operator === 'beeline') {
            $operatorData = Operator::with('tariff')->where('slug', 'beeline')->first();

            if ($operatorData) {
                return $operatorData->tariff->id;
            } else {
                return Tariff::where('title', 'like', "%Без сим%")->first()->id;
            }
        }

        if ($operator === 'kcell') {
            $operatorData = Operator::with('tariff')->where('slug', 'kcell')->first();

            if ($operatorData) {
                return $operatorData->tariff->id;
            } else {
                return Tariff::where('title', 'like', "%Без сим%")->first()->id;
            }
        }

        return Tariff::where('title', 'like', "%Без сим%")->first()->id;
    }

    private function clearPhoneNumbers($phone, $phone2)
    {
        $equipments = Equipment::where('phone', $phone)
            ->orWhere('phone', $phone2)
            ->get();

        foreach ($equipments as $e) {
            $e->update(['phone' => null]);
        }

        $equipments = Equipment::where('phone2', $phone)
            ->orWhere('phone2', $phone2)
            ->get();

        foreach ($equipments as $e) {
            $e->update(['phone2' => null]);
        }
    }


    public function show(Request $request)
    {
        $equipment = Equipment::find($request->id);

        if (!$equipment) {
            return response()->json([
                'error' => 'Equipment not found',
            ]);
        }

        $equipment = $equipment->getAttributes();

        return response()->json([
            'equipment' => $equipment,
        ]);
    }

    public function search(Request $request)
    {
        $q = $request->search_value;

        if (isset($q)) {

            $equipments = Equipment::where('object', 'like', "%$q%")
                ->orWhere('imei', 'like', "%$q%")
                ->orWhere('phone', 'like', "%$q%")
                ->orderBy('id', 'desc')
                ->take(10)
                ->get();

        } else {
            $equipments = Equipment::orderBy('id', 'desc')->paginate();
        }

        $html = view('admin.equipment.modals.table_item', [
            'equipments' => $equipments,
        ])->render();

        return response()->json(['html' => $html]);
    }

    /**
     * Список девайсів (устройств)
     * @return mixed
     */
    public function getDevices()
    {
        $devices = Equipment::groupBy('device')
            ->get()
            ->pluck('device')
            ->toArray();

        return response()->json([
            'devices' => $devices
        ]);
    }

    public function update(EquipmentUpdateRequest $request)
    {
        $equipment = Equipment::find($request->id);
        if ($equipment) {
            $equipment->object = $request->object;
            $equipment->device = $request->device;
            $equipment->imei = $request->imei;
            $equipment->phone = SettingService::convertPhone($request->phone);
            $equipment->phone2 = SettingService::convertPhone($request->phone2);
            $equipment->tariff_id = $request->tariff_id;

            if ($request->date_start) {
                $equipment->date_start = $request->date_start;
            }
            if ($request->date_end != $equipment->date_end) {
                $equipment->custom_update = 1;
            }
            if ($request->date_end) {
                $equipment->date_end = $request->date_end;
            }
        }
        $equipments = Equipment::where('id', '!=', $request->id)->where('phone', $equipment->phone)->orWhere('phone', $request->phone2)->get();
        if (!empty($equipments)) {
            foreach ($equipments as $key => $e) {
                $e->phone = null;
                $e->save();
            }
        }
        $equipments = Equipment::where('id', '!=', $request->id)->where('phone2', $equipment->phone)->orWhere('phone2', $request->phone2)->get();
        if (!empty($equipments)) {
            foreach ($equipments as $key => $e) {
                $e->phone2 = null;
                $e->save();
            }
        }
        if ($equipment->update()) {
            $simCard = SimCard::where('phone', (int)$request->phone)->first();

            if ($simCard) {
                $simCard->status = true;
                $simCard->equipment_id = $equipment->id;
                $simCard->update();

                if ($simCard) {
                    if (!SimCardHistory::where([['sim_card_id', $simCard->id], ['equipment_id', $simCard->equipment_id]])->orderBy('id', 'DESC')->first() || (SimCardHistory::where([['sim_card_id', $simCard->id], ['equipment_id', $simCard->equipment_id]])->orderBy('id', 'DESC')->first() && SimCardHistory::where([['sim_card_id', $simCard->id], ['equipment_id', $simCard->equipment_id]])->orderBy('id', 'DESC')->first()->operation != "Подключение")) {
                        $sim_card_history = new SimCardHistory();
                        $sim_card_history->create([
                            'sim_card_id' => $simCard ? $simCard->id : null,
                            'equipment_id' => $simCard->equipment_id,
                            'operation_date_time' => date('Y-m-d H:i:s'),
                            'user_id' => Auth::id(),
                            'operation' => 'Подключение',

                        ]);
                    }
                } else {
                    if (!SimCardHistory::whereNull('sim_card_id')->andWhere([['equipment_id', $simCard->equipment_id]])->orderBy('id', 'DESC')->first() || (SimCardHistory::whereNull('sim_card_id')->andWhere([['equipment_id', $simCard->equipment_id]])->orderBy('id', 'DESC')->first() && SimCardHistory::whereNull('sim_card_id')->andWhere([['equipment_id', $simCard->equipment_id]])->orderBy('id', 'DESC')->first()->operation != "Подключение")) {
                        $sim_card_history = new SimCardHistory();
                        $sim_card_history->create([
                            'sim_card_id' => $simCard ? $simCard->id : null,
                            'equipment_id' => $simCard->equipment_id,
                            'operation_date_time' => date('Y-m-d H:i:s'),
                            'user_id' => Auth::id(),
                            'operation' => 'Подключение',
                        ]);
                    }
                }
            }
        }


        return response()->json([
            'status' => true,
            'message' => 'Устройство успешно отредактировано',
        ]);
    }

    public function destroy(Request $request)
    {
        $equipment = Equipment::findOrFail($request->id);
        if ($equipment) {
            NotificationService::addNotification(Auth::id(), $equipment->id, null, null, 'Объект удалён ' . $equipment->object);
            if ($equipment->delete()) {
                $simCard = SimCard::where('phone', (int)$request->phone)->first();
                if ($simCard) {
                    $simCard->status = false;
                    $simCard->equipment_id = null;
                    $simCard->update();
                    if ($simCard) {
                        if (!SimCardHistory::where([['sim_card_id', $simCard->id], ['equipment_id', $simCard->equipment_id]])->orderBy('id', 'DESC')->first() || (SimCardHistory::where([['sim_card_id', $simCard->id], ['equipment_id', $simCard->equipment_id]])->orderBy('id', 'DESC')->first() && SimCardHistory::where([['sim_card_id', $simCard->id], ['equipment_id', $simCard->equipment_id]])->orderBy('id', 'DESC')->first()->operation != "Устройство удалён")) {
                            $sim_card_history = new SimCardHistory();
                            $sim_card_history->create([
                                'sim_card_id' => $simCard ? $simCard->id : null,
                                'equipment_id' => $simCard->equipment_id,
                                'operation_date_time' => date('Y-m-d H:i:s'),
                                'user_id' => Auth::id(),
                                'operation' => 'Устройство удалён',

                            ]);
                        }
                    } else {
                        if (!SimCardHistory::whereNull('sim_card_id')->andWhere([['equipment_id', $simCard->equipment_id]])->orderBy('id', 'DESC')->first() || (SimCardHistory::whereNull('sim_card_id')->andWhere([['equipment_id', $simCard->equipment_id]])->orderBy('id', 'DESC')->first() && SimCardHistory::whereNull('sim_card_id')->andWhere([['equipment_id', $simCard->equipment_id]])->orderBy('id', 'DESC')->first()->operation != "Устройство удалён")) {
                            $sim_card_history = new SimCardHistory();
                            $sim_card_history->create([
                                'sim_card_id' => $simCard ? $simCard->id : null,
                                'equipment_id' => $simCard->equipment_id,
                                'operation_date_time' => date('Y-m-d H:i:s'),
                                'user_id' => Auth::id(),
                                'operation' => 'Устройство удалён',
                            ]);
                        }
                    }
                }
            }
        }

        return redirect()->back()->with('success', 'Устройство успешно удалён');
    }

    public function searchFreeEquipments(Request $request)
    {
        $q = $request->search_value;

        if (isset($q)) {

            $freeEquipments = Equipment::where('status', false)
                ->where('object', 'like', "%$q%")
                ->orWhere('imei', 'like', "%$q%")
                ->orWhere('phone', 'like', "%$q%")
                ->orderBy('id', 'desc')
                ->take(10)
                ->get();

        } else {
            $freeEquipments = Equipment::where('status', false)->take(50)->get();
        }

        $html = view('admin.equipment.modals.table_free_item', [
            'freeEquipments' => $freeEquipments,
        ])->render();

        return response()->json(['html' => $html]);
    }

    public function exportEquipments()
    {
        return Excel::download(new EquipmentExport(), 'equipment_export' . '_' . time() . '.xlsx');
    }

    public function checkEquipment(Request $request)
    {
        $data = $request->all();
        $data['item'] = [];
        if (isset($data['imei'])){
            $data['item'] = EquipmentService::getEquipmentByImei($data['imei']);
        }

        return view('admin.equipment.check_wialon', $data);
    }
}
