<?php

namespace Tests\Feature\Books;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Book;
use App\Models\Review;

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
        $response->assertSee('詳細テスト本');
        $response->assertSee('最良');
        $response->assertSee('普通');
        $response->assertSee('4'); // 平均評価
    }
}
