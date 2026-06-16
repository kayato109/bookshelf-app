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
        $this->actingAs($user);

        $book = Book::factory()->create();

        ReadingPlan::factory()->create([
            'user_id' => $user->id,
            'book_id' => $book->id,
        ]);

        $response = $this->postJson('/reading-plans', [
            'book_id' => $book->id,
            'target_date' => now()->addDay()->toDateString(),
        ]);

        $response->assertStatus(422);
    }

    public function test_過去日は422が返る()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $book = Book::factory()->create();

        $response = $this->postJson('/reading-plans', [
            'book_id' => $book->id,
            'target_date' => now()->subDay()->toDateString(),
        ]);

        $response->assertStatus(422);
    }

    public function test_今日以降なら登録成功しリダイレクトされる()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $book = Book::factory()->create();

        $response = $this->post('/reading-plans', [
            'book_id' => $book->id,
            'target_date' => now()->toDateString(),
        ]);

        $response->assertRedirect('/reading-plans');

        $this->assertDatabaseHas('reading_plans', [
            'user_id' => $user->id,
            'book_id' => $book->id,
        ]);
    }
}
