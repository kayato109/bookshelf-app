<?php

namespace App\Http\Controllers;

use App\Models\ReadingPlan;
use App\Enums\ReadingPlanStatus;
use Illuminate\Http\Request;

class ReadingPlanController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        $currentStatus = $request->status;

        // Enum の value 一覧
        $validStatuses = array_map(fn($s) => $s->value, ReadingPlanStatus::cases());

        $readingPlans = ReadingPlan::with('book')
            ->where('user_id', $user->id)
            ->when(in_array($currentStatus, $validStatuses, true), function ($query) use ($currentStatus) {
                $query->where('status', $currentStatus);
            })
            ->orderBy('target_date')
            ->get();

        return view('reading-plans.index', compact('readingPlans', 'currentStatus'));
    }
}
