<?php

namespace Tests\Feature\ReadingPlans;

use App\Models\Book;
use App\Models\ReadingPlan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReadingPlanStoreTest extends TestCase
{
    use RefreshDatabase;

    public function test_同一ユーザー同一書籍は重複登録できず422が返る()
    {
        $user = User::factory()->create();
        $book = Book::factory()->create();

        ReadingPlan::factory()
            ->for($user)
            ->for($book)
            ->create();

        $response = $this->actingAs($user)
            ->postJson(route('reading-plans.store'), [
                'book_id' => $book->id,
                'target_date' => now()->addDay()->toDateString(),
            ]);

        $response->assertStatus(422);
    }

    public function test_過去日は422が返る()
    {
        $user = User::factory()->create();
        $book = Book::factory()->create();

        $response = $this->actingAs($user)
            ->postJson(route('reading-plans.store'), [
                'book_id' => $book->id,
                'target_date' => now()->subDay()->toDateString(),
            ]);

        $response->assertStatus(422);
    }

    public function test_今日以降なら登録成功しリダイレクトされる()
    {
        $user = User::factory()->create();
        $book = Book::factory()->create();

        $response = $this->actingAs($user)
            ->post(route('reading-plans.store'), [
                'book_id' => $book->id,
                'target_date' => now()->toDateString(),
            ]);

        $response->assertRedirect(route('reading-plans.index'));

        $this->assertDatabaseHas('reading_plans', [
            'user_id' => $user->id,
            'book_id' => $book->id,
        ]);
    }
}
