<?php

namespace Tests\Feature\Books;

use App\Models\Book;
use App\Models\Review;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookShowTest extends TestCase
{
    use RefreshDatabase;

    public function test_書籍詳細が表示されレビューと平均評価が見える()
    {
        $book = Book::factory()->create(['title' => '詳細テスト本']);
        Review::factory()->create(['book_id' => $book->id, 'rating' => 5, 'comment' => '最良']);
        Review::factory()->create(['book_id' => $book->id, 'rating' => 3, 'comment' => '普通']);

        $response = $this->get("/books/{$book->id}");

        $response->assertStatus(200);
        $response->assertSeeText('詳細テスト本');
        $response->assertSeeText('最良');
        $response->assertSeeText('普通');
        $response->assertSee('4');
    }
}
