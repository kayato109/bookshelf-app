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
        $response = $this->get(route('reading-plans.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_認証済みユーザーは読書計画一覧を表示できる()
    {
        $user = User::factory()->create();
        $book = Book::factory()->create();

        ReadingPlan::factory()
            ->for($user)
            ->for($book)
            ->create();

        $response = $this->actingAs($user)
            ->get(route('reading-plans.index'));

        $response->assertStatus(200)
            ->assertSee($book->title);
    }

    public function test_状態フィルタでpendingのみ表示される()
    {
        $user = User::factory()->create();

        // book を2冊作成
        $book1 = Book::factory()->create();
        $book2 = Book::factory()->create();

        $pending = ReadingPlan::factory()
            ->for($user)
            ->for($book1)
            ->create([
                'status' => 'pending',
            ]);

        $completed = ReadingPlan::factory()
            ->for($user)
            ->for($book2)
            ->create([
                'status' => 'completed',
            ]);

        $response = $this->actingAs($user)
            ->get(route('reading-plans.index', ['status' => 'pending']));

        $response->assertStatus(200)
            ->assertSee($pending->book->title)
            ->assertDontSee($completed->book->title);
    }
}
