<?php

namespace App\Http\Controllers;

use App\Models\ReadingPlan;
use App\Enums\ReadingPlanStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;

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

    public function complete(ReadingPlan $readingPlan)
    {
        // 所有者チェック（403）
        $this->authorize('complete', $readingPlan);

        // すでに完了済みなら何もしない
        if ($readingPlan->status !== \App\Enums\ReadingPlanStatus::Completed) {
            $readingPlan->update([
                'status' => \App\Enums\ReadingPlanStatus::Completed,
                'completed_at' => now(),
            ]);
        }

        return redirect()
            ->route('reading-plans.index')
            ->with('success', '読書計画を読了にしました');
    }

    public function edit(ReadingPlan $readingPlan)
    {
        $this->authorize('update', $readingPlan);

        return view('reading-plans.edit', [
            'readingPlan' => $readingPlan,
        ]);
    }

    public function destroy(ReadingPlan $readingPlan): RedirectResponse
    {
        $this->authorize('delete', $readingPlan);

        DB::transaction(function () use ($readingPlan): void {
            Auth::user()->notifications()
                ->where('data->plan_id', $readingPlan->id)
                ->delete();

            $readingPlan->delete();
        });

        return redirect()
            ->route('reading-plans.index')
            ->with('success', '読書計画を削除しました。');
    }
}
