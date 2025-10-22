<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\SimCardImportRequest;
use App\Http\Requests\SimCardStoreRequest;
use App\Http\Requests\SimCardUpdateRequest;
use App\Imports\SimCardImport;
use App\Models\Notification;
use App\Models\Operator;
use App\Models\SimCard;
use App\Models\User;
use App\Models\SimCardHistory;
use App\Models\Equipment;
use App\Models\Tariff;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class SimCardController extends Controller
{
    public function index(Request $request)
    {
        $query = SimCard::query();
        $query->with('operatorData');
        $q = $request->q;

        if (isset($q)) {
            $query->where('phone', 'like', "%$q%")
                ->orWhere('operator', 'like', "%$q%")
                ->orderBy('id', 'desc')
                ->take(10);
        }

        $simCards = $query->orderBy('id', 'desc')->paginate();
        $operators = Operator::all();

        return view('admin.sim_card.index', compact('simCards', 'operators'));
    }

    public function store(SimCardStoreRequest $request)
    {
        $data = $request->all();
        $simCard = new SimCard();
        $simCard->phone = $simCard->changePhone($request->phone);

        $operator = Operator::find($data['operator']);
        if ($operator) {
            $simCard->operator = $operator->slug;
            $simCard->operator_id = $operator->id;
        }

        if ($simCard->save()) {
            NotificationService::addNotification(
                User::ADMIN,
                null,
                null,
                $simCard->id,
                'Пользователь ' . auth()->user()->name . ' ' . auth()->user()->surname . ' добавил новую Sim-карту ' . $simCard->phone
            );
        }

        $equipment = Equipment::where('phone', 'like', "%$simCard->phone%")->first();

        if (!empty($equipment)) {
            $equipment->tariff_id = $this->determineTariff($simCard->operator);
            $equipment->update();
        }

        return redirect()->back()->with('success', 'Sim-карта успешно добавлена');
    }

    public function update(SimCardUpdateRequest $request)
    {
        $data = $request->all();
        $simCard = SimCard::find($request->id);
        if (!$simCard) {
            return redirect()->back();
        }
        $simCard->phone = (int)$request->phone;

        if ($data['operator']) {
            $operator = Operator::find($data['operator']);
            $tariff = Tariff::where('operator_id', $operator->id)->first();
            if ($operator) {
                $simCard->operator = $operator->slug;
                $simCard->operator_id = $operator->id;
            }

            if ($operator->id || $tariff->id) {
                Equipment::where('phone', $simCard->phone)
                    ->update([
                        'operator' => $operator->slug,
                        'operator_id' => $operator->id,
                        'tariff_id' => $tariff->id,
                    ]);
            }
        }


        if ($simCard->update()) {
            NotificationService::addNotification(
                Auth::id(),
                null,
                null,
                $simCard->id,
                'Пользователь ' . auth()->user()->name . ' ' . auth()->user()->surname . ' обновил Sim-карту ' . $simCard->phone
            );
        }

        $equipment = Equipment::where('phone', 'like', "%$simCard->phone%")->first();

        if ($equipment) {
            $equipment->tariff_id = $this->determineTariff($simCard->operator);
            $equipment->update();
        }

        return redirect()->back()->with('success', 'Sim-карта успешно обновлена');
    }

    private function determineTariff($operator)
    {
        $operator = strtolower($operator);

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


    public function show(Request $request)
    {
        $simCard = SimCard::find($request->id);

        if (!$simCard) {
            return response()->json([
                'error' => 'Sim Card not found',
            ]);
        }

        $simCard = $simCard->getAttributes();

        return response()->json([
            'simCard' => $simCard,
        ]);
    }


    public function view(Request $request, $sim_card)
    {
        $card = SimCard::find($sim_card);
        $equipment = Equipment::where('phone', $card->phone)->orWhere('phone2', $card->phone)->first();
        if (!empty($equipment)) {
            $histories = SimCardHistory::where('sim_card_id', $sim_card)->orWhere('equipment_id', $equipment->id)->orderBy('id', 'desc')->paginate();

        } else {
            $histories = SimCardHistory::where('sim_card_id', $sim_card)->orderBy('id', 'desc')->paginate();
        }
        return view('admin.sim_card.view', compact('card', 'histories'));
    }

    public function search(Request $request)
    {
        $q = $request->search_value;

        if (isset($q)) {

            $simCards = SimCard::where('phone', 'like', "%$q%")
                ->orWhere('operator', 'like', "%$q%")
                ->orderBy('id', 'desc')
                ->take(10)
                ->get();

        } else {
            $simCards = SimCard::orderBy('id', 'desc')->paginate();
        }

        $html = view('admin.sim_card.modals.table_item', [
            'simCards' => $simCards,
        ])->render();

        return response()->json(['html' => $html]);
    }

    public function destroy(Request $request)
    {
        $simCard = SimCard::findOrFail($request->id);

        if ($simCard) {
            $equipment = Equipment::where('phone', $simCard->phone)->orWhere('phone2', $simCard->phone)->first();
            if (!empty($equipment)) {
                $histories = SimCardHistory::where('sim_card_id', $simCard->id)->orWhere('equipment_id', $equipment->id)->get();
                foreach ($histories as $history) {
                    $history->delete();
                }
            }
            if ($simCard->delete()) {
                NotificationService::addNotification(Auth::id(), null, null, $simCard->id, 'Пользователь ' . auth()->user()->name . ' ' . auth()->user()->surname . ' удалил Sim-карту ' . $simCard->phone);
            }
        }

        return redirect()->back()->with('success', 'Sim-карта успешно удалён');
    }

    /**
     * Імпорт Sim-Card
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function import(SimCardImportRequest $request)
    {
        $operator = $request->operator;
        $file = $request->sim_cards_file;

        Excel::import(new SimCardImport($operator), $file);

        return redirect()->back()->with('success', 'Sim-карта успешно импортированные');
    }
}
