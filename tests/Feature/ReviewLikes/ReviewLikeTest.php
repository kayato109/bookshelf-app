<?php

namespace Tests\Feature\ReviewLikes;

use App\Models\Review;
use App\Models\ReviewLike;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReviewLikeTest extends TestCase
{
    use RefreshDatabase;

    public function test_認証ユーザーはレビューにいいねできる()
    {
        $user = User::factory()->create();
        $review = Review::factory()->create();

        $response = $this->actingAs($user)
            ->from(route('books.show', $review->book_id))
            ->post(route('reviews.like', $review));

        $response->assertRedirect(route('books.show', $review->book_id));

        $this->assertDatabaseHas('review_likes', [
            'user_id' => $user->id,
            'review_id' => $review->id,
        ]);
    }

    public function test_未認証ユーザーはいいねできずログインへリダイレクト()
    {
        $review = Review::factory()->create();

        $response = $this->post(route('reviews.like', $review));

        $response->assertRedirect(route('login'));
    }

    public function test_いいねトグルが正しく動作する()
    {
        $user = User::factory()->create();
        $review = Review::factory()->create();

        // 1回目 → いいね
        $response = $this->actingAs($user)
            ->from(route('books.show', $review->book_id))
            ->post(route('reviews.like', $review));

        $response->assertRedirect(route('books.show', $review->book_id));

        $this->assertDatabaseHas('review_likes', [
            'user_id' => $user->id,
            'review_id' => $review->id,
        ]);

        // 2回目 → 解除
        $response = $this->actingAs($user)
            ->from(route('books.show', $review->book_id))
            ->post(route('reviews.like', $review));

        $response->assertRedirect(route('books.show', $review->book_id));

        $this->assertDatabaseMissing('review_likes', [
            'user_id' => $user->id,
            'review_id' => $review->id,
        ]);
    }

    public function test_同じレビューに複数回いいねできない()
    {
        $user = User::factory()->create();
        $review = Review::factory()->create();

        ReviewLike::factory()->for($user)->for($review)->create();

        // 2回目のいいね（トグルなので解除される）
        $this->actingAs($user)->post(route('reviews.like', $review));

        $this->assertDatabaseMissing('review_likes', [
            'user_id' => $user->id,
            'review_id' => $review->id,
        ]);
    }
}
