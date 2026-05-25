<?php

namespace Tests\Unit\Models;

use App\Models\Book;
use App\Models\Favorite;
use App\Models\Genre;
use App\Models\Review;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CascadeDeleteTest extends TestCase
{
    use RefreshDatabase;

    public function test_書籍削除時に関連データも削除される()
    {
        $book = Book::factory()->create();
        $bookId = $book->id;

        Review::factory()->create(['book_id' => $bookId]);
        Favorite::factory()->create(['book_id' => $bookId]);

        $genres = Genre::factory()->count(2)->create();
        $book->genres()->attach($genres);

        $book->delete();

        $this->assertDatabaseMissing('reviews', ['book_id' => $bookId]);
        $this->assertDatabaseMissing('favorites', ['book_id' => $bookId]);
        $this->assertDatabaseMissing('book_genre', ['book_id' => $bookId]);
    }
}
