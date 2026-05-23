<?php

namespace Tests\Unit\Models;

use App\Models\Book;
use App\Models\Favorite;
use App\Models\Review;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CascadeDeleteTest extends TestCase
{
    use RefreshDatabase;

    public function test_書籍削除時に関連データも削除される()
    {
        $book = Book::factory()->create();

        Review::factory()->create(['book_id' => $book->id]);
        Favorite::factory()->create(['book_id' => $book->id]);

        $book->delete();

        $this->assertDatabaseMissing('reviews', ['book_id' => $book->id]);
        $this->assertDatabaseMissing('favorites', ['book_id' => $book->id]);
        $this->assertDatabaseMissing('book_genre', ['book_id' => $book->id]);
    }
}
