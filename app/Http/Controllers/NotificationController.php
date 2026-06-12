<?php

namespace App\Http\Controllers;

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
}
