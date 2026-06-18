<?php

namespace Tests\Feature\Books;

use App\Models\Book;
use App\Models\Favorite;
use App\Models\Genre;
use App\Models\Review;
use App\Models\ReviewLike;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookCrudTest extends TestCase
{
    use RefreshDatabase;

    /* ---------------------------------------------------------
        書籍登録（POST /books）
    --------------------------------------------------------- */
    public function test_認証ユーザーは書籍を登録できる()
    {
        $user = User::factory()->create();
        $genre = Genre::factory()->create();

        $response = $this->actingAs($user)->post(route('books.store'), [
            'title' => '新しい本',
            'author' => '著者名',
            'isbn' => '1234567890123',
            'published_date' => '2024-01-01',
            'description' => '説明文',
            'image_url' => 'https://example.com',
            'genres' => [$genre->id],
        ]);

        $response->assertRedirect(route('books.index'));

        $this->assertDatabaseHas('books', [
            'title' => '新しい本',
            'author' => '著者名',
            'isbn' => '1234567890123',
        ]);

        $book = Book::first();

        $this->assertDatabaseHas('book_genre', [
            'book_id' => $book->id,
            'genre_id' => $genre->id,
        ]);
    }

    public function test_未認証ユーザーは書籍を登録できずログインへリダイレクト()
    {
        $response = $this->post(route('books.store'), []);
        $response->assertRedirect(route('login'));
    }

    /* ---------------------------------------------------------
        書籍更新（PUT /books/{book}）
    --------------------------------------------------------- */
    public function test_作成者は書籍を更新できる()
    {
        $user = User::factory()->create();
        $book = Book::factory()->create(['user_id' => $user->id]);
        $genre = Genre::factory()->create();

        $response = $this->actingAs($user)->put(route('books.update', $book), [
            'title' => '更新後のタイトル',
            'author' => '更新後の著者',
            'isbn' => '1234567890123',
            'published_date' => '2024-01-01',
            'description' => '更新後の説明',
            'image_url' => 'https://example.com',
            'genres' => [$genre->id],
        ]);

        $response->assertRedirect(route('books.show', $book));

        $this->assertDatabaseHas('books', [
            'id' => $book->id,
            'title' => '更新後のタイトル',
        ]);

        $this->assertDatabaseHas('book_genre', [
            'book_id' => $book->id,
            'genre_id' => $genre->id,
        ]);
    }

    public function test_作成者以外は書籍を更新できず403()
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        $book = Book::factory()->create(['user_id' => $user->id]);
        $genre = Genre::factory()->create();

        $response = $this->actingAs($other)->put(route('books.update', $book), [
            'title' => '更新後のタイトル',
            'author' => '更新後の著者',
            'isbn' => '1234567890123',
            'published_date' => '2024-01-01',
            'description' => '説明',
            'image_url' => 'https://example.com',
            'genres' => [$genre->id],
        ]);

        $response->assertStatus(403);
    }

    /* ---------------------------------------------------------
        書籍削除（DELETE /books/{book}）
    --------------------------------------------------------- */
    public function test_作成者は書籍を削除でき関連データも削除される()
    {
        $user = User::factory()->create();
        $book = Book::factory()->create(['user_id' => $user->id]);

        $review = Review::factory()->for($book)->create();
        Favorite::factory()->for($book)->create();
        ReviewLike::factory()->for($review)->create();

        $response = $this->actingAs($user)->delete(route('books.destroy', $book));

        $response->assertRedirect(route('books.index'));

        $this->assertDatabaseMissing('books', ['id' => $book->id]);
        $this->assertDatabaseMissing('reviews', ['book_id' => $book->id]);
        $this->assertDatabaseMissing('favorites', ['book_id' => $book->id]);
        $this->assertDatabaseMissing('review_likes', ['review_id' => $review->id]);
        $this->assertDatabaseMissing('book_genre', ['book_id' => $book->id]);
    }

    public function test_作成者以外は書籍を削除できず403()
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        $book = Book::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($other)->delete(route('books.destroy', $book));

        $response->assertStatus(403);
    }
}
