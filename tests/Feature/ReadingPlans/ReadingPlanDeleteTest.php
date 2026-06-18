<?php

namespace Tests\Feature\ReadingPlans;

use App\Models\ReadingPlan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReadingPlanDeleteTest extends TestCase
{
    use RefreshDatabase;

    public function test_所有者は読書計画を削除できる()
    {
        $user = User::factory()->create();

        $plan = ReadingPlan::factory()
            ->for($user)
            ->create();

        $response = $this->actingAs($user)
            ->delete(route('reading-plans.destroy', $plan));

        $response->assertRedirect(route('reading-plans.index'));

        $this->assertDatabaseMissing('reading_plans', [
            'id' => $plan->id,
        ]);
    }

    public function test_他ユーザーは読書計画を削除できず403()
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();

        $plan = ReadingPlan::factory()
            ->for($owner)
            ->create();

        $response = $this->actingAs($other)
            ->delete(route('reading-plans.destroy', $plan));

        $response->assertStatus(403);
    }
}
