<?php

namespace Tests\Feature\Books;

use App\Models\Book;
use App\Models\Genre;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookIndexTest extends TestCase
{
    use RefreshDatabase;

    public function test_書籍一覧が正常表示される()
    {
        // Arrange
        $genre = Genre::factory()->create(['name' => '宇宙']);
        $book = Book::factory()->create([
            'title' => 'テスト本',
            'author' => 'テスト著者',
        ]);

        $book->genres()->attach($genre->id);

        // Act
        $response = $this->get('/books');

        // Assert
        $response->assertStatus(200);
        $response->assertSeeText('テスト本');
        $response->assertSeeText('テスト著者');
        $response->assertSeeText('宇宙');
    }
}
