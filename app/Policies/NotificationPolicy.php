<?php

namespace App\Policies;

use Illuminate\Notifications\DatabaseNotification;
use App\Models\User;

class NotificationPolicy
{
    public function update(User $user, DatabaseNotification $notification): bool
    {
        return $notification->notifiable_id === $user->id
            && $notification->notifiable_type === get_class($user);
    }
}
