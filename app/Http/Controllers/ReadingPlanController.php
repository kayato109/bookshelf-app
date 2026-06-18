<?php

namespace App\Http\Controllers;

use App\Enums\ReadingPlanStatus;
use App\Http\Requests\StoreReadingPlanRequest;
use App\Http\Requests\UpdateReadingPlanRequest;
use App\Models\Book;
use App\Models\ReadingPlan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

/**
 * 読書計画の CRUD + 完了処理を行うコントローラ.
 */
class ReadingPlanController extends Controller
{
    /**
     * 読書計画一覧
     */
    public function index(Request $request): View
    {
        $user = auth()->user();

        $currentStatus = $request->status;

        // Enum の value 一覧
        $validStatuses = array_map(fn ($s) => $s->value, ReadingPlanStatus::cases());

        $readingPlans = ReadingPlan::with('book')
            ->where('user_id', $user->id)
            ->when(
                in_array($currentStatus, $validStatuses, true),
                fn ($query) => $query->where('status', $currentStatus)
            )
            ->orderBy('target_date')
            ->get();

        return view('reading-plans.index', compact('readingPlans', 'currentStatus'));
    }

    /**
     * 読書計画を完了にする
     */
    public function complete(ReadingPlan $readingPlan): RedirectResponse
    {
        $this->authorize('complete', $readingPlan);

        if ($readingPlan->status !== ReadingPlanStatus::Completed) {
            $readingPlan->update([
                'status' => ReadingPlanStatus::Completed,
                'completed_at' => now(),
            ]);
        }

        return redirect()
            ->route('reading-plans.index')
            ->with('success', '読書計画を読了にしました');
    }

    /**
     * 読書計画編集画面
     */
    public function edit(ReadingPlan $readingPlan): View
    {
        $this->authorize('update', $readingPlan);

        return view('reading-plans.edit', [
            'readingPlan' => $readingPlan,
        ]);
    }

    /**
     * 読書計画削除
     */
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

    /**
     * 読書計画作成画面
     */
    public function create(): View
    {
        $books = Book::orderBy('title')->get();

        return view('reading-plans.create', [
            'books' => $books,
        ]);
    }

    /**
     * 読書計画登録
     */
    public function store(StoreReadingPlanRequest $request): RedirectResponse
    {
        ReadingPlan::create([
            'user_id' => auth()->id(),
            'book_id' => $request->book_id,
            'target_date' => $request->target_date,
            'status' => ReadingPlanStatus::Pending->value,
        ]);

        return redirect()
            ->route('reading-plans.index')
            ->with('success', '読書計画を登録しました');
    }

    /**
     * 読書計画更新
     */
    public function update(UpdateReadingPlanRequest $request, ReadingPlan $readingPlan): RedirectResponse
    {
        $this->authorize('update', $readingPlan);

        $validated = $request->validated();

        // overdue → 未来日に変更されたら pending に戻す
        if (
            $readingPlan->status === ReadingPlanStatus::Overdue &&
            $validated['target_date'] >= now()->toDateString()
        ) {
            $readingPlan->status = ReadingPlanStatus::Pending;
            $readingPlan->completed_at = null;
        }

        $readingPlan->target_date = $validated['target_date'];
        $readingPlan->save();

        return redirect()
            ->route('reading-plans.index')
            ->with('success', '読書計画を更新しました');
    }
}
