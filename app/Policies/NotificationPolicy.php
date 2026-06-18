<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Notifications\DatabaseNotification;

/**
 * 通知に関する認可ポリシー.
 *
 * - update: 自分宛ての通知のみ既読にできる
 */
class NotificationPolicy
{
    /**
     * 通知を更新（既読化）できるか
     */
    public function update(User $user, DatabaseNotification $notification): bool
    {
        return $notification->notifiable_id === $user->id
            && $notification->notifiable_type === get_class($user);
    }
}
