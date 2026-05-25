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

    public function test_bookは複数のgenreを持てる()
    {
        $book = Book::factory()->create();
        $genres = Genre::factory()->count(2)->create();

        $book->genres()->attach($genres);

        $this->assertCount(2, $book->genres);
    }

    public function test_bookは複数のreviewを持てる()
    {
        $book = Book::factory()->create();
        Review::factory()->count(3)->create(['book_id' => $book->id]);

        $this->assertCount(3, $book->reviews);
    }

    public function test_bookの平均評価が正しく計算される()
    {
        $book = Book::factory()->create();

        Review::factory()->create(['book_id' => $book->id, 'rating' => 5]);
        Review::factory()->create(['book_id' => $book->id, 'rating' => 3]);

        $book = Book::withAvg('reviews', 'rating')->find($book->id);

        $this->assertEquals(4, $book->reviews_avg_rating);
    }
}
