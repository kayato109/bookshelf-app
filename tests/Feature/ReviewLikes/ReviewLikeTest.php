<?php

namespace Tests\Feature\ReviewLikes;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Book;
use App\Models\Review;
use App\Models\ReviewLike;

class ReviewLikeTest extends TestCase
{
    use RefreshDatabase;

    /* ---------------------------------------------------------
        いいね追加（POST /reviews/{review}/like）
    --------------------------------------------------------- */
    public function test_認証ユーザーはレビューにいいねできる()
    {
        $user = User::factory()->create();
        $review = Review::factory()->create();

        $response = $this->actingAs($user)
            ->from("/books/{$review->book_id}")
            ->post("/reviews/{$review->id}/like");

        $response->assertRedirect("/books/{$review->book_id}");

        $this->assertDatabaseHas('review_likes', [
            'user_id' => $user->id,
            'review_id' => $review->id,
        ]);
    }

    public function test_未認証ユーザーはいいねできずログインへリダイレクト()
    {
        $review = Review::factory()->create();

        $response = $this->post("/reviews/{$review->id}/like");

        $response->assertRedirect('/login');
    }

    /* ---------------------------------------------------------
        トグル（いいね → 解除）
    --------------------------------------------------------- */
    public function test_いいねトグルが正しく動作する()
    {
        $user = User::factory()->create();
        $review = Review::factory()->create();

        // 1回目 → いいね
        $response = $this->actingAs($user)
            ->from("/books/{$review->book_id}")
            ->post("/reviews/{$review->id}/like");

        $response->assertRedirect("/books/{$review->book_id}");

        $this->assertDatabaseHas('review_likes', [
            'user_id' => $user->id,
            'review_id' => $review->id,
        ]);

        // 2回目 → 解除
        $response = $this->actingAs($user)
            ->from("/books/{$review->book_id}")
            ->post("/reviews/{$review->id}/like");

        $response->assertRedirect("/books/{$review->book_id}");

        $this->assertDatabaseMissing('review_likes', [
            'user_id' => $user->id,
            'review_id' => $review->id,
        ]);
    }

    /* ---------------------------------------------------------
        同じユーザーが同じレビューに複数いいねできない
    --------------------------------------------------------- */
    public function test_同じレビューに複数回いいねできない()
    {
        $user = User::factory()->create();
        $review = Review::factory()->create();

        ReviewLike::factory()->create([
            'user_id' => $user->id,
            'review_id' => $review->id,
        ]);

        // 2回目のいいね（トグルなので解除される）
        $this->actingAs($user)
            ->post("/reviews/{$review->id}/like");

        $this->assertDatabaseMissing('review_likes', [
            'user_id' => $user->id,
            'review_id' => $review->id,
        ]);
    }
}
