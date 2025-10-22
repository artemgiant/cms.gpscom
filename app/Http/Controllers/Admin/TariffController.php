<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Operator;
use App\Models\Tariff;
use Illuminate\Http\Request;

class TariffController extends Controller
{
    public function index()
    {
        $data['tariffs'] = Tariff::with('operator')->paginate();
        $data['operators'] = Operator::all();

        return view('admin.tariff.index', $data);
    }

    public function store(Request $request)
    {
        $tariff = new Tariff();
        $tariff->title = $request->title;
        $tariff->price = $request->price;
        $tariff->operator_id = $request->operator_id;
        $tariff->save();

        return redirect()->back()->with('success', 'Тариф успешно создан');
    }

    public function show(Request $request)
    {
        $tariff = Tariff::find($request->id);

        if (!$tariff) {
            return response()->json([
                'error' => 'Tariff not found',
            ]);
        }

        $tariff = $tariff->getAttributes();

        return response()->json([
            'tariff' => $tariff,
        ]);
    }

    public function update(Request $request)
    {
        $tariff = Tariff::find($request->id);

        if ($tariff){
            $tariff->title = $request->title;
            $tariff->price = $request->price;
            $tariff->operator_id = $request->operator_id;
            $tariff->update();

            return redirect()->back()->with('success', 'Тариф успешно обновлен');
        } else {
            return redirect()->back();
        }
    }

    public function destroy(Request $request)
    {
        $tariff = Tariff::findOrFail($request->id);
        if ($tariff) {
            $tariff->delete();
        }

        return redirect()->back()->with('success', 'Тариф успешно удалён');
    }
}
