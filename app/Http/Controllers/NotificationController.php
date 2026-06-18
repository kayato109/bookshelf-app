<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\View\View;

/**
 * 通知一覧・既読処理を行うコントローラ.
 */
class NotificationController extends Controller
{
    /**
     * 通知一覧
     */
    public function index(Request $request): View
    {
        $user = $request->user();

        $notifications = DatabaseNotification::where('notifiable_id', $user->id)
            ->where('notifiable_type', get_class($user))
            ->orderBy('created_at', 'desc')
            ->get();

        return view('notifications.index', [
            'notifications' => $notifications,
        ]);
    }

    /**
     * 通知を既読にする
     */
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
