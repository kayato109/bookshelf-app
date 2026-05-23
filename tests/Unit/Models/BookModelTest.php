<?php

namespace Tests\Unit\Models;

use App\Models\Book;
use App\Models\Genre;
use App\Models\Review;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_genresリレーション()
    {
        $book = Book::factory()->has(Genre::factory()->count(2))->create();

        $this->assertCount(2, $book->genres);
    }

    public function test_reviewsリレーション()
    {
        $book = Book::factory()->has(Review::factory()->count(3))->create();

        $this->assertCount(3, $book->reviews);
    }

    public function test_avg_ratingが正しく計算される()
    {
        $book = Book::factory()->create();

        Review::factory()->create(['book_id' => $book->id, 'rating' => 5]);
        Review::factory()->create(['book_id' => $book->id, 'rating' => 3]);

        $this->assertEquals(4, $book->reviews()->avg('rating'));
    }
}
