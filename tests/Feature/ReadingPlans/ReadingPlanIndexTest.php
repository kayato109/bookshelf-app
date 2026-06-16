<?php

namespace Tests\Feature\ReadingPlans;

use App\Models\Book;
use App\Models\ReadingPlan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReadingPlanIndexTest extends TestCase
{
    use RefreshDatabase;

    public function test_未認証ユーザーは読書計画一覧にアクセスできずログインへリダイレクトされる()
    {
        $response = $this->get('/reading-plans');

        $response->assertRedirect('/login');
    }

    public function test_認証済みユーザーは読書計画一覧を表示できる()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $book = Book::factory()->create();

        ReadingPlan::factory()->create([
            'user_id' => $user->id,
            'book_id' => $book->id,
        ]);

        $response = $this->get('/reading-plans');

        $response->assertStatus(200)
            ->assertSee($book->title);
    }

    public function test_状態フィルタでpendingのみ表示される()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        // ← ここを修正（book を2冊作る）
        $book1 = Book::factory()->create();
        $book2 = Book::factory()->create();

        $pending = ReadingPlan::factory()->create([
            'user_id' => $user->id,
            'book_id' => $book1->id,
            'status' => 'pending',
        ]);

        $completed = ReadingPlan::factory()->create([
            'user_id' => $user->id,
            'book_id' => $book2->id,
            'status' => 'completed',
        ]);

        $response = $this->get('/reading-plans?status=pending');

        $response->assertStatus(200)
            ->assertSee($pending->book->title)
            ->assertDontSee($completed->book->title);
    }
}
