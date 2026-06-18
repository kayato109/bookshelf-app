<?php

namespace Tests\Feature\ReadingPlans;

use App\Enums\ReadingPlanStatus;
use App\Models\Book;
use App\Models\ReadingPlan;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class DailyReadingPlanBatchTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // 今日を固定
        Carbon::setTestNow('2025-06-15');
    }

    public function test_3日前に通知が作成される()
    {
        $user = User::factory()->create();
        $book = Book::factory()->create();

        $plan = ReadingPlan::factory()
            ->for($user)
            ->for($book)
            ->create([
                'status' => 'pending',
                'target_date' => Carbon::today()->addDays(3)->toDateString(),
            ]);

        Artisan::call('batch:daily-reading-plan');

        $notification = $user->notifications()->latest()->first();
        $this->assertNotNull($notification);

        $data = $notification->data;
        $this->assertSame('three_days_before', $data['timing']);
        $this->assertSame($plan->id, $data['reading_plan_id']);
    }

    public function test_当日に通知が作成される()
    {
        $user = User::factory()->create();
        $book = Book::factory()->create();

        ReadingPlan::factory()
            ->for($user)
            ->for($book)
            ->create([
                'status' => 'pending',
                'target_date' => Carbon::today()->toDateString(),
            ]);

        Artisan::call('batch:daily-reading-plan');

        $notification = $user->notifications()->latest()->first();
        $this->assertNotNull($notification);

        $data = $notification->data;
        $this->assertSame('on_due_date', $data['timing']);
    }

    public function test_3日後に通知が作成される()
    {
        $user = User::factory()->create();
        $book = Book::factory()->create();

        ReadingPlan::factory()
            ->for($user)
            ->for($book)
            ->create([
                'status' => 'overdue',
                'target_date' => Carbon::today()->subDays(3)->toDateString(),
            ]);

        Artisan::call('batch:daily-reading-plan');

        $notification = $user->notifications()->latest()->first();
        $this->assertNotNull($notification);

        $data = $notification->data;
        $this->assertSame('three_days_after', $data['timing']);
    }

    public function test_同じ通知が複数回作成されない()
    {
        $user = User::factory()->create();
        $book = Book::factory()->create();

        ReadingPlan::factory()
            ->for($user)
            ->for($book)
            ->create([
                'status' => 'pending',
                'target_date' => Carbon::today()->addDays(3)->toDateString(),
            ]);

        // 1回目
        Artisan::call('batch:daily-reading-plan');

        // 2回目（重複防止が働くはず）
        Artisan::call('batch:daily-reading-plan');

        $this->assertCount(1, $user->notifications);
    }

    public function test_pendingで期限切れの計画はoverdueに更新される()
    {
        $user = User::factory()->create();
        $book = Book::factory()->create();

        $plan = ReadingPlan::factory()
            ->for($user)
            ->for($book)
            ->create([
                'status' => 'pending',
                'target_date' => Carbon::today()->subDay()->toDateString(),
                'completed_at' => null,
            ]);

        Artisan::call('batch:daily-reading-plan');

        $plan->refresh();
        $this->assertSame(ReadingPlanStatus::Overdue, $plan->status);
    }

    public function test_completedの計画は期限切れ更新の対象外()
    {
        $user = User::factory()->create();
        $book = Book::factory()->create();

        $plan = ReadingPlan::factory()
            ->for($user)
            ->for($book)
            ->create([
                'status' => 'completed',
                'target_date' => Carbon::today()->subDay()->toDateString(),
                'completed_at' => Carbon::yesterday(),
            ]);

        Artisan::call('batch:daily-reading-plan');

        $plan->refresh();
        $this->assertSame(ReadingPlanStatus::Completed, $plan->status);
    }

    public function test_既にoverdueの計画は対象外()
    {
        $user = User::factory()->create();
        $book = Book::factory()->create();

        $plan = ReadingPlan::factory()
            ->for($user)
            ->for($book)
            ->create([
                'status' => 'overdue',
                'target_date' => Carbon::today()->subDay()->toDateString(),
                'completed_at' => null,
            ]);

        Artisan::call('batch:daily-reading-plan');

        $plan->refresh();
        $this->assertSame(ReadingPlanStatus::Overdue, $plan->status);
    }
}
