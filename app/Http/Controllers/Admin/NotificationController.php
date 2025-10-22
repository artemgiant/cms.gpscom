<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = Notification::orderBy('id', 'desc')
            ->groupBy('message')
            ->paginate();

        return view('admin.notification.index', compact('notifications'));
    }

    public function changeStatus(Request $request)
    {
        $notification = Notification::where([
            ['id', $request->id]
        ])->first();

        $notification->status = $request->status == 'closed' ? 0 : 1;
        $notification->update();

        return response()->json('ok');
    }

    public function search(Request $request)
    {
        $q = $request->search_value;

        if (isset($q)) {

            $notifications = Notification::where('message', 'like', "%$q%")
                ->orWhere('created_at', 'like', "%$q%")
                ->orderBy('id', 'desc')
                ->take(10)
                ->get();

        } else {
            $notifications = Notification::orderBy('id', 'desc')
                ->get();
        }

        $html = view('admin.notification.modals.table_item', [
            'notifications' => $notifications,
        ])->render();

        return response()->json(['html' => $html]);
    }

    public function destroy(Request $request)
    {
        $notification = Notification::findOrFail($request->id);
        if ($notification) {
            $notification->delete();
        }

        return redirect()->back()->with('success', 'Сообщение успешно удалено');
    }

    public function destroyNotifications(Request $request)
    {
        Notification::query()->delete();

        return redirect()->back()->with('success', 'Список уведомлений успешно очищен');
    }
}
