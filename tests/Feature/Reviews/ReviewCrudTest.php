<?php

namespace Tests\Feature\Reviews;

use App\Models\Book;
use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReviewCrudTest extends TestCase
{
    use RefreshDatabase;

    /* ---------------------------------------------------------
        レビュー投稿（POST /books/{book}/reviews）
    --------------------------------------------------------- */
    public function test_認証ユーザーはレビューを投稿できる()
    {
        $user = User::factory()->create();
        $book = Book::factory()->create();

        $response = $this->actingAs($user)->post(route('reviews.store', $book), [
            'rating' => 5,
            'comment' => '最高の本でした！',
        ]);

        $response->assertRedirect(route('books.show', $book));

        $this->assertDatabaseHas('reviews', [
            'book_id' => $book->id,
            'user_id' => $user->id,
            'rating' => 5,
            'comment' => '最高の本でした！',
        ]);
    }

    public function test_未認証ユーザーはレビュー投稿できずログインへリダイレクト()
    {
        $book = Book::factory()->create();

        $response = $this->post(route('reviews.store', $book), [
            'rating' => 5,
            'comment' => 'テスト',
        ]);

        $response->assertRedirect(route('login'));
    }

    /* ---------------------------------------------------------
        レビュー編集画面（GET /reviews/{review}/edit）
    --------------------------------------------------------- */
    public function test_作成者はレビュー編集画面を表示できる()
    {
        $user = User::factory()->create();
        $review = Review::factory()->for($user)->create();

        $response = $this->actingAs($user)->get(route('reviews.edit', $review));

        $response->assertStatus(200)
            ->assertSeeText('レビューの編集');
    }

    public function test_作成者以外はレビュー編集画面にアクセスできず403()
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        $review = Review::factory()->for($user)->create();

        $response = $this->actingAs($other)->get(route('reviews.edit', $review));

        $response->assertStatus(403);
    }

    /* ---------------------------------------------------------
        レビュー更新（PUT /reviews/{review}）
    --------------------------------------------------------- */
    public function test_作成者はレビューを更新できる()
    {
        $user = User::factory()->create();
        $review = Review::factory()->for($user)->create();

        $response = $this->actingAs($user)->put(route('reviews.update', $review), [
            'rating' => 4,
            'comment' => '更新後のコメント',
        ]);

        $response->assertRedirect(route('books.show', $review->book_id));

        $this->assertDatabaseHas('reviews', [
            'id' => $review->id,
            'rating' => 4,
            'comment' => '更新後のコメント',
        ]);
    }

    public function test_作成者以外はレビューを更新できず403()
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        $review = Review::factory()->for($user)->create();

        $response = $this->actingAs($other)->put(route('reviews.update', $review), [
            'rating' => 3,
            'comment' => '不正更新',
        ]);

        $response->assertStatus(403);
    }

    /* ---------------------------------------------------------
        レビュー削除（DELETE /reviews/{review}）
    --------------------------------------------------------- */
    public function test_作成者はレビューを削除できる()
    {
        $user = User::factory()->create();
        $review = Review::factory()->for($user)->create();

        $response = $this->actingAs($user)->delete(route('reviews.destroy', $review));

        $response->assertRedirect(route('books.show', $review->book_id));

        $this->assertDatabaseMissing('reviews', [
            'id' => $review->id,
        ]);
    }

    public function test_作成者以外はレビューを削除できず403()
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        $review = Review::factory()->for($user)->create();

        $response = $this->actingAs($other)->delete(route('reviews.destroy', $review));

        $response->assertStatus(403);
    }
}
