<?php

namespace Tests\Feature\Favorites;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Book;
use App\Models\Favorite;
use App\Models\User;

class FavoriteTest extends TestCase
{
    use RefreshDatabase;

    /* ---------------------------------------------------------
        お気に入り一覧（GET /favorites）
    --------------------------------------------------------- */
    public function test_認証ユーザーはお気に入り一覧を表示できる()
    {
        $user = User::factory()->create();
        $book = Book::factory()->create();

        Favorite::factory()->create([
            'user_id' => $user->id,
            'book_id' => $book->id,
        ]);

        $response = $this->actingAs($user)->get('/favorites');

        $response->assertStatus(200);
        $response->assertSee($book->title);
    }

    public function test_未認証ユーザーはお気に入り一覧にアクセスできずログインへリダイレクト()
    {
        $response = $this->get('/favorites');
        $response->assertRedirect('/login');
    }

    /* ---------------------------------------------------------
        お気に入りトグル（POST /books/{book}/favorites）
    --------------------------------------------------------- */
    public function test_認証ユーザーはお気に入りをトグルできる()
    {
        $user = User::factory()->create();
        $book = Book::factory()->create();

        // 1回目 → 追加
        $response = $this
            ->actingAs($user)
            ->from("/books/{$book->id}")
            ->post("/books/{$book->id}/favorites");
        $response->assertRedirect("/books/{$book->id}");

        $this->assertDatabaseHas('favorites', [
            'user_id' => $user->id,
            'book_id' => $book->id,
        ]);

        // 2回目 → 解除
        $response = $this
            ->actingAs($user)
            ->from("/books/{$book->id}")
            ->post("/books/{$book->id}/favorites");
        $response->assertRedirect("/books/{$book->id}");

        $this->assertDatabaseMissing('favorites', [
            'user_id' => $user->id,
            'book_id' => $book->id,
        ]);
    }

    public function test_未認証ユーザーはトグルできずログインへリダイレクト()
    {
        $book = Book::factory()->create();

        $response = $this->post("/books/{$book->id}/favorites");

        $response->assertRedirect('/login');
    }
}
