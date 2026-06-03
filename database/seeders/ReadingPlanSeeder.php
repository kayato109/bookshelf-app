<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ReadingPlan;
use Carbon\Carbon;

class ReadingPlanSeeder extends Seeder
{
    public function run(): void
    {
        $today = Carbon::today();

        // 山田太郎（id=1）
        $user1 = 1;

        $scenarios = [
            // 1
            ['book_id' => 1, 'target_date' => $today->copy()->addDays(5), 'status' => 'pending', 'completed_at' => null],
            // 2
            ['book_id' => 2, 'target_date' => $today->copy()->subDays(5), 'status' => 'completed', 'completed_at' => $today->copy()->subDays(5)],
            // 3
            ['book_id' => 3, 'target_date' => $today->copy()->subDays(2), 'status' => 'overdue', 'completed_at' => null],
            // 4
            ['book_id' => 4, 'target_date' => $today->copy()->addDays(3), 'status' => 'pending', 'completed_at' => null],
            // 5
            ['book_id' => 5, 'target_date' => $today->copy(), 'status' => 'pending', 'completed_at' => null],
            // 6
            ['book_id' => 6, 'target_date' => $today->copy()->subDays(3), 'status' => 'overdue', 'completed_at' => null],
            // 7
            ['book_id' => 7, 'target_date' => $today->copy()->subDay(), 'status' => 'pending', 'completed_at' => null],
        ];

        foreach ($scenarios as $s) {
            ReadingPlan::create([
                'user_id' => $user1,
                ...$s,
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
