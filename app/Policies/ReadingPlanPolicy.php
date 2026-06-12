<?php

namespace App\Policies;

use App\Models\ReadingPlan;
use App\Models\User;

class ReadingPlanPolicy
{
    public function complete(User $user, ReadingPlan $plan): bool
    {
        return $plan->user_id === $user->id;
    }
}
