<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserDeleteRequest;
use App\Http\Requests\UserStoreRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Models\Client;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function store(UserStoreRequest $request)
    {
        $user = new User();
        $user->name = $request->name;
        $user->role_id = $request->role_id;
        $user->surname = $request->surname;
        $user->phone = $request->phone;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->save();

        return response()->json([
            'status' => true,
            'message' => 'Пользователь удачно добавлен',
        ]);
    }

    public function show(Request $request)
    {
        $user = User::find($request->id);

        if (!$user) {
            return response()->json([
                'error' => 'User not found',
            ]);
        }
        $user->created_at = date('Y-m-d', strtotime($user->created_at));
        $user = $user->getAttributes();

        return response()->json([
            'user' => $user,
        ]);
    }

    public function update(UserUpdateRequest $request)
    {
        $user = User::find($request->id);

        if ($user) {
            $user->name = $request->name;
            $user->role_id = $request->role_id;
            $user->surname = $request->surname;
            $user->phone = $request->phone;
            $user->email = $request->email;

            if ($request->password) {
                $user->password = Hash::make($request->password);
            }
            $user->save();
        }

        return response()->json([
            'status' => true,
            'message' => 'Пользователь успешно отредактирован',
        ]);
    }

    public function destroy(UserDeleteRequest $request)
    {
        $data = $request->all();
        $user = User::findOrFail($data['user_id']);
        if ($user) {
            Client::where('manager', $data['user_id'])->update([
                'manager' => $data['new_user_id'],
            ]);
            $user->delete();
        }

        return redirect()->back()->with('success', 'Пользователь успешно удалён');
    }
}
