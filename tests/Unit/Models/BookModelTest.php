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

    public function test_bookは複数のジャンルを持てる()
    {
        $book = Book::factory()->create();
        $genres = Genre::factory()->count(2)->create();

        // 関連付け
        $book->genres()->sync($genres->pluck('id'));

        // リレーションを明示的にロード
        $book->load('genres');

        $this->assertCount(2, $book->genres);
    }

    public function test_bookは複数のレビューを持てる()
    {
        $book = Book::factory()->create();

        Review::factory()->count(3)->for($book)->create();

        $book->load('reviews');

        $this->assertCount(3, $book->reviews);
    }

    public function test_bookの平均評価が正しく計算される()
    {
        $book = Book::factory()->create();

        Review::factory()->for($book)->create(['rating' => 5]);
        Review::factory()->for($book)->create(['rating' => 3]);

        $book = Book::withAvg('reviews', 'rating')->find($book->id);

        $this->assertSame(4.0, (float) $book->reviews_avg_rating);
    }
}
