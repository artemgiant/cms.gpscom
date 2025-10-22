<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\SensorStoreRequest;
use App\Http\Requests\SensorUpdateRequest;
use App\Models\Sensor;
use Illuminate\Http\Request;

class SensorController extends Controller
{
    /**
     * Збереження нових сенсорів
     * @param SensorStoreRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(SensorStoreRequest $request)
    {
        $sensor = new Sensor();
        $sensor->name = $request->name;
        $sensor->type = $request->type;
        $sensor->sensor_id = $request->sensor_id;
        $sensor->equipment_id = $request->equipment_id;
        $sensor->save();

        return response()->json([
            'status' => true,
            'sensor' => $sensor,
            'message' => 'Датчик успешно сохранен',
        ]);
    }

    public function show(Request $request)
    {
        $sensor = Sensor::find($request->id);

        if (!$sensor) {
            return response()->json([
                'error' => 'Sensor not found',
            ]);
        }

        $sensor = $sensor->getAttributes();

        return response()->json([
            'sensor' => $sensor,
        ]);
    }

    public function update(SensorUpdateRequest $request)
    {
        $sensor = Sensor::find($request->id);

        if ($sensor) {
            $sensor->name = $request->name;
            $sensor->type = $request->type;
            $sensor->sensor_id = $request->sensor_id;
            $sensor->update();
        }

        return response()->json([
            'status' => true,
            'sensor' => $sensor,
            'message' => 'Датчик успешно отредактирован',
        ]);
    }

    public function delete(Request $request)
    {
        $sensor = Sensor::find($request->id);
        if ($sensor) {
            $sensor->delete();
        }

        return response()->json([
            'status' => true,
            'message' => 'Сенсор успешно удалён',
        ]);
    }
}
