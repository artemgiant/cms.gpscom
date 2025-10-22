<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        $setting = Setting::where('name', 'access_token')->first();
        $users = User::all();
        $roles = Role::all();

        return view('admin.setting.index', compact('setting', 'users', 'roles'));
    }

    public function updateSetting(Request $request)
    {
        $status = $request->status == 'on' ? 1 : 0;

        $setting = Setting::where('name', 'access_token')->first();
        if ($setting){
            $setting->value = $request->access_token;
            $setting->status = $status;
            $setting->update();
        }

        return redirect()->back()->with('success', 'Данные сохранены успешно');
    }


    public function tokenRedirect(Request $request)
    {
         if ($request->access_token) {

             Setting::updateOrCreate(
                 ['name' => 'access_token'],
                 ['value' => $request->access_token]
             );

             return redirect()->route('setting')->with('success', 'Данные сохранены успешно');
         } else {
             return redirect()->route('setting')->with('error', 'Логин или пароль неверны');
         }
    }
}
