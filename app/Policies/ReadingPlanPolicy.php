<?php

namespace App\Policies;

use App\Models\ReadingPlan;
use App\Models\User;

class ReadingPlanPolicy
{
    public function complete(User $user, ReadingPlan $readingPlan): bool
    {
        return $readingPlan->user_id === $user->id;
    }

    public function update(User $user, ReadingPlan $readingPlan): bool
    {
        if ($readingPlan->user_id !== $user->id) {
            return false;
        }

        // completed は編集不可
        return $readingPlan->status !== \App\Enums\ReadingPlanStatus::Completed;
    }

    public function delete(User $user, ReadingPlan $readingPlan): bool
    {
        return $readingPlan->user_id === $user->id;
    }
}
