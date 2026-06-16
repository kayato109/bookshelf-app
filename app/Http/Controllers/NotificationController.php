<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\View\View;

class NotificationController extends Controller
{
    public function index(Request $request): View
    {
        $notifications = DatabaseNotification::where('notifiable_id', $request->user()->id)
            ->where('notifiable_type', get_class($request->user()))
            ->orderBy('created_at', 'desc')
            ->get();

        return view('notifications.index', [
            'notifications' => $notifications,
        ]);
    }

    public function markAsRead(Request $request, string $id): RedirectResponse
    {
        // 存在しない場合は 404
        $notification = DatabaseNotification::findOrFail($id);

        // 認可（所有者チェック）
        $this->authorize('update', $notification);

        // 未読なら read_at を更新
        if ($notification->read_at === null) {
            $notification->update([
                'read_at' => now(),
            ]);
        }

        return redirect()
            ->route('notifications.index')
            ->with('success', '通知を既読にしました');
    }
}
