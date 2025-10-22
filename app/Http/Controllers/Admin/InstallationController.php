<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\InstallationStoreRequest;
use App\Http\Requests\InstallationUpdateRequest;
use App\Models\Installation;
use Illuminate\Http\Request;

class InstallationController extends Controller
{
    public function store(InstallationStoreRequest $request)
    {
        $installation = new Installation();
        $installation->type = $request->type;
        $installation->comment = $request->comment;
        $installation->price = $request->price;
        $installation->user_id = $request->client_id;
        $installation->equipment_id = $request->equipment_id;
        $installation->date_create = date('Y-m-d', strtotime($request->date_create));
        $installation->save();

        return response()->json([
            'status' => true,
            'installation' => $installation,
            'message' => 'Данные монтажной работы успешно сохранены',
        ]);
    }

    public function show(Request $request)
    {
        $installation = Installation::find($request->id);

        if (!$installation) {
            return response()->json([
                'error' => 'Installation not found',
            ]);
        }

        $installation = $installation->getAttributes();

        return response()->json([
            'installation' => $installation,
        ]);
    }

    public function update(InstallationUpdateRequest $request)
    {
        $installation = Installation::find($request->installation_id);
        if ($installation) {
            $installation->type = $request->type;
            $installation->comment = $request->comment;
            $installation->price = $request->price;
            $installation->user_id = $request->client_id;
            $installation->equipment_id = $request->equipment_id;
            $installation->date_create = date('Y-m-d', strtotime($request->date_create));
            $installation->save();
        }

        return response()->json([
            'status' => true,
            'installation' => $installation,
            'message' => 'Данные монтажной работы успешно отредактированы',
        ]);
    }

    public function delete(Request $request)
    {
        $installation = Installation::find($request->id);
        if ($installation) {
            $installation->delete();
        }

        return response()->json([
            'status' => true,
            'message' => 'Данные монтажной работы успешно удалены',
        ]);
    }
}
