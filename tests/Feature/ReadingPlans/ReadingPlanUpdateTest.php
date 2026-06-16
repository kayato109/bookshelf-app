<?php

namespace Tests\Feature\ReadingPlans;

use App\Models\ReadingPlan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReadingPlanUpdateTest extends TestCase
{
    use RefreshDatabase;

    public function test_所有者は読書計画を更新できる()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $plan = ReadingPlan::factory()->create([
            'user_id' => $user->id,
            'target_date' => now()->addDay()->toDateString(),
        ]);

        $response = $this->put("/reading-plans/{$plan->id}", [
            'target_date' => now()->addDays(3)->toDateString(),
        ]);

        $response->assertRedirect('/reading-plans');

        $this->assertDatabaseHas('reading_plans', [
            'id' => $plan->id,
            'target_date' => now()->addDays(3)->toDateString(),
        ]);
    }

    public function test_他ユーザーは更新できず403()
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();

        $plan = ReadingPlan::factory()->create([
            'user_id' => $owner->id,
        ]);

        $this->actingAs($other);

        $response = $this->put("/reading-plans/{$plan->id}", [
            'target_date' => now()->addDay()->toDateString(),
        ]);

        $response->assertStatus(403);
    }

    public function test_completedは編集不可で403()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $plan = ReadingPlan::factory()->create([
            'user_id' => $user->id,
            'status' => 'completed',
        ]);

        $response = $this->put("/reading-plans/{$plan->id}", [
            'target_date' => now()->addDay()->toDateString(),
        ]);

        $response->assertStatus(403);
    }

    public function test_過去日は422()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $plan = ReadingPlan::factory()->create([
            'user_id' => $user->id,
        ]);

        $response = $this->put("/reading-plans/{$plan->id}", [
            'target_date' => now()->subDay()->toDateString(),
        ]);

        $response->assertInvalid(['target_date']);
    }

    public function test_overdueから未来日に変更するとpendingに戻る()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $plan = ReadingPlan::factory()->create([
            'user_id' => $user->id,
            'status' => 'overdue',
            'target_date' => now()->subDay()->toDateString(),
        ]);

        $response = $this->put("/reading-plans/{$plan->id}", [
            'target_date' => now()->addDay()->toDateString(),
        ]);

        $response->assertRedirect('/reading-plans');

        $this->assertDatabaseHas('reading_plans', [
            'id' => $plan->id,
            'status' => 'pending',
        ]);
    }
}
