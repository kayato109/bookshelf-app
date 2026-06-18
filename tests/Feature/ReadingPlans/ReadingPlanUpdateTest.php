<?php

namespace Tests\Feature\ReadingPlans;

use App\Models\ReadingPlan;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReadingPlanUpdateTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // テスト日付を固定（更新後の日付比較が安定する）
        Carbon::setTestNow('2026-06-18');
    }

    public function test_所有者は読書計画を更新できる()
    {
        $user = User::factory()->create();

        $plan = ReadingPlan::factory()
            ->for($user)
            ->create([
                'target_date' => Carbon::today()->addDay()->toDateString(),
            ]);

        $response = $this->actingAs($user)
            ->put(route('reading-plans.update', $plan), [
                'target_date' => Carbon::today()->addDays(3)->toDateString(),
            ]);

        $response->assertRedirect(route('reading-plans.index'));

        $this->assertDatabaseHas('reading_plans', [
            'id' => $plan->id,
        ]);

        $this->assertEquals(
            Carbon::today()->addDays(3)->toDateString(),
            $plan->fresh()->target_date->toDateString()
        );
    }

    public function test_他ユーザーは更新できず403()
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();

        $plan = ReadingPlan::factory()
            ->for($owner)
            ->create();

        $response = $this->actingAs($other)
            ->put(route('reading-plans.update', $plan), [
                'target_date' => Carbon::today()->addDay()->toDateString(),
            ]);

        $response->assertStatus(403);
    }

    public function test_completedは編集不可で403()
    {
        $user = User::factory()->create();

        $plan = ReadingPlan::factory()
            ->for($user)
            ->create([
                'status' => 'completed',
            ]);

        $response = $this->actingAs($user)
            ->put(route('reading-plans.update', $plan), [
                'target_date' => Carbon::today()->addDay()->toDateString(),
            ]);

        $response->assertStatus(403);
    }

    public function test_過去日は422()
    {
        $user = User::factory()->create();

        $plan = ReadingPlan::factory()
            ->for($user)
            ->create();

        $response = $this->actingAs($user)
            ->put(route('reading-plans.update', $plan), [
                'target_date' => Carbon::yesterday()->toDateString(),
            ]);

        $response->assertInvalid(['target_date']);
    }

    public function test_overdueから未来日に変更するとpendingに戻る()
    {
        $user = User::factory()->create();

        $plan = ReadingPlan::factory()
            ->for($user)
            ->create([
                'status' => 'overdue',
                'target_date' => Carbon::yesterday()->toDateString(),
            ]);

        $response = $this->actingAs($user)
            ->put(route('reading-plans.update', $plan), [
                'target_date' => Carbon::today()->addDay()->toDateString(),
            ]);

        $response->assertRedirect(route('reading-plans.index'));

        $this->assertDatabaseHas('reading_plans', [
            'id' => $plan->id,
            'status' => 'pending',
        ]);
    }
}
