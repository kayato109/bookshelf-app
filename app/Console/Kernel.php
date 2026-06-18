<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

/**
 * アプリケーションのコンソールカーネル.
 *
 * - スケジュール設定
 * - コマンド登録
 */
class Kernel extends ConsoleKernel
{
    /**
     * アプリケーションのスケジュール定義
     */
    protected function schedule(Schedule $schedule): void
    {
        // JST 0:00 に実行
        $schedule->command('batch:daily-reading-plan')
            ->dailyAt('00:00');
    }

    /**
     * アプリケーションのコマンド登録
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
