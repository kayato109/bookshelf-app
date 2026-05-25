<?php

namespace Tests\Feature\Api;

use App\Models\Book;
use App\Models\Favorite;
use App\Models\Genre;
use App\Models\Review;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiBookDeleteTest extends TestCase
{
    use RefreshDatabase;

    public function test_書籍削除_ap_iで削除され204が返る()
    {
        $book = Book::factory()->create();

        $genre = Genre::factory()->create();
        $book->genres()->attach($genre);

        Review::factory()->create(['book_id' => $book->id]);
        Favorite::factory()->create(['book_id' => $book->id]);

        $response = $this->deleteJson("/api/v1/books/{$book->id}");

        $response->assertNoContent();

        $this->assertDatabaseMissing('books', ['id' => $book->id]);
        $this->assertDatabaseMissing('reviews', ['book_id' => $book->id]);
        $this->assertDatabaseMissing('favorites', ['book_id' => $book->id]);
        $this->assertDatabaseMissing('book_genre', ['book_id' => $book->id]);
    }

    public function test_書籍削除_ap_i_存在しない_i_dで404が返る()
    {
        $response = $this->deleteJson('/api/v1/books/999999');

        $response->assertStatus(404)
            ->assertJson([
                'error' => '書籍が見つかりませんでした。',
            ]);
    }
}
