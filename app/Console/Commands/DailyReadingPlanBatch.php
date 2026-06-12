<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ReadingPlan;
use Illuminate\Support\Facades\Notification;
use App\Notifications\ReadingPlanReminderNotification;
use Carbon\Carbon;

class DailyReadingPlanBatch extends Command
{
    protected $signature = 'batch:daily-reading-plan';
    protected $description = 'Daily batch: update statuses and send reminder notifications';

    public function handle(): int
    {
        $today = Carbon::today();

        // -------------------------------
        // 【1】期限切れ → overdue に更新
        // -------------------------------
        ReadingPlan::where('status', 'pending')
            ->whereDate('target_date', '<', $today)
            ->whereNull('completed_at')
            ->update([
                'status' => 'overdue',
                'updated_at' => now(),
            ]);

        // -------------------------------
        // 【2】リマインダー通知
        // -------------------------------

        // ① 3日前
        $threeDaysBefore = ReadingPlan::where('status', 'pending')
            ->whereDate('target_date', $today->copy()->addDays(3))
            ->get();

        foreach ($threeDaysBefore as $plan) {
            $this->sendReminder($plan, 'three_days_before');
        }

        // ② 当日
        $onDueDate = ReadingPlan::where('status', 'pending')
            ->whereDate('target_date', $today)
            ->get();

        foreach ($onDueDate as $plan) {
            $this->sendReminder($plan, 'on_due_date');
        }

        // ③ 3日後（overdue）
        $threeDaysAfter = ReadingPlan::where('status', 'overdue')
            ->whereDate('target_date', $today->copy()->subDays(3))
            ->get();

        foreach ($threeDaysAfter as $plan) {
            $this->sendReminder($plan, 'three_days_after');
        }

        return Command::SUCCESS;
    }

    /**
     * 重複通知を防ぎつつ通知を送る
     */
    private function sendReminder(ReadingPlan $plan, string $timing): void
    {
        // 重複チェック
        $exists = $plan->user
            ->notifications()
            ->where('type', ReadingPlanReminderNotification::class)
            ->where('data->reading_plan_id', $plan->id)
            ->where('data->timing', $timing)
            ->exists();

        if ($exists) {
            return;
        }

        // 通知送信
        Notification::send($plan->user, new ReadingPlanReminderNotification([
            'reading_plan_id' => $plan->id,
            'plan_id' => $plan->id,
            'book_title' => $plan->book->title,
            'title' => '読書計画のリマインダー',
            'body' => $this->buildBody($plan, $timing),
            'timing' => $timing,
        ]));


    }

    private function buildBody(ReadingPlan $plan, string $timing): string
    {
        $title = $plan->book->title;

        return match ($timing) {
            'three_days_before' => "『{$title}』の読書計画の期日が3日後に迫っています。",
            'on_due_date' => "今日は『{$title}』の読書計画の期日です。",
            'three_days_after' => "『{$title}』の読書計画の期日から3日が経過しました。",
            default => "『{$title}』の読書計画に関する通知です。",
        };
    }

}
