<?php

namespace Database\Seeders;

use App\Models\ReadingPlan;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

/**
 * 読書計画データを初期投入するシーダー.
 *
 * 特定ユーザー（id=1, id=2）に対して、
 * さまざまな状態（pending / completed / overdue）の読書計画を作成する。
 */
class ReadingPlanSeeder extends Seeder
{
    /**
     * シーディングの実行.
     */
    public function run(): void
    {
        $today = Carbon::today();

        // 山田太郎（id=1）
        $userId1 = 1;

        $scenarios = [
            ['book_id' => 1, 'target_date' => $today->copy()->addDays(5), 'status' => 'pending', 'completed_at' => null],
            ['book_id' => 2, 'target_date' => $today->copy()->subDays(5), 'status' => 'completed', 'completed_at' => $today->copy()->subDays(5)],
            ['book_id' => 3, 'target_date' => $today->copy()->subDays(2), 'status' => 'overdue', 'completed_at' => null],
            ['book_id' => 4, 'target_date' => $today->copy()->addDays(3), 'status' => 'pending', 'completed_at' => null],
            ['book_id' => 5, 'target_date' => $today->copy(), 'status' => 'pending', 'completed_at' => null],
            ['book_id' => 6, 'target_date' => $today->copy()->subDays(3), 'status' => 'overdue', 'completed_at' => null],
            ['book_id' => 7, 'target_date' => $today->copy()->subDay(), 'status' => 'pending', 'completed_at' => null],
        ];

        foreach ($scenarios as $scenario) {
            ReadingPlan::create([
                'user_id' => $userId1,
                ...$scenario,
            ]);
        }

        // 鈴木花子（id=2）
        ReadingPlan::create([
            'user_id' => 2,
            'book_id' => 8,
            'target_date' => $today->copy()->addDays(3),
            'status' => 'pending',
            'completed_at' => null,
        ]);
    }
}
