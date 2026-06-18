<?php

namespace Tests\Feature\ReadingPlans;

use App\Models\ReadingPlan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReadingPlanEditTest extends TestCase
{
    use RefreshDatabase;

    public function test_所有者は編集画面を表示できる()
    {
        $user = User::factory()->create();

        $plan = ReadingPlan::factory()
            ->for($user)
            ->create();

        $response = $this->actingAs($user)
            ->get(route('reading-plans.edit', $plan));

        $response->assertStatus(200)
            ->assertSee('読書計画編集');
    }

    public function test_他ユーザーは編集画面にアクセスできず403()
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();

        $plan = ReadingPlan::factory()
            ->for($owner)
            ->create();

        $response = $this->actingAs($other)
            ->get(route('reading-plans.edit', $plan));

        $response->assertStatus(403);
    }
}
