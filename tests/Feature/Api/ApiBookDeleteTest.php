<?php

namespace Tests\Feature\Api;

use App\Models\Book;
use App\Models\Favorite;
use App\Models\Genre;
use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ApiBookDeleteTest extends TestCase
{
    use RefreshDatabase;

    /* ---------------------------------------------------------
        未認証 → 401
    --------------------------------------------------------- */
    public function test_未認証ユーザーは書籍削除できず401が返る()
    {
        $book = Book::factory()->create();

        $response = $this->deleteJson("/api/v1/books/{$book->id}");

        $response->assertStatus(401);
    }

    /* ---------------------------------------------------------
        認証済み所有者 → 204
    --------------------------------------------------------- */
    public function test_書籍削除_apiで削除され204が返る()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        // 所有者の本
        $book = Book::factory()->create(['user_id' => $user->id]);

        $genre = Genre::factory()->create();
        $book->genres()->attach($genre);

        Review::factory()->create(['book_id' => $book->id]);
        Favorite::factory()->create(['book_id' => $book->id]);

        $response = $this->deleteJson("/api/v1/books/{$book->id}");

        $response->assertNoContent(); // 204

        $this->assertDatabaseMissing('books', ['id' => $book->id]);
        $this->assertDatabaseMissing('reviews', ['book_id' => $book->id]);
        $this->assertDatabaseMissing('favorites', ['book_id' => $book->id]);
        $this->assertDatabaseMissing('book_genre', ['book_id' => $book->id]);
    }

    /* ---------------------------------------------------------
        認証済みでも所有者以外 → 403
    --------------------------------------------------------- */
    public function test_認証済みでも所有者以外は書籍削除できず403が返る()
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();

        $book = Book::factory()->create(['user_id' => $owner->id]);

        Sanctum::actingAs($other);

        $response = $this->deleteJson("/api/v1/books/{$book->id}");

        $response->assertStatus(403);
    }

    /* ---------------------------------------------------------
        存在しないID → 404
    --------------------------------------------------------- */
    public function test_書籍削除_api_存在しない_idで404が返る()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->deleteJson('/api/v1/books/999999');

        $response->assertStatus(404)
            ->assertJson([
                'error' => '書籍が見つかりませんでした。',
            ]);
    }
}
