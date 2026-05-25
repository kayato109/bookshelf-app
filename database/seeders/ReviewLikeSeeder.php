<?php

namespace Database\Seeders;

use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Seeder;

class ReviewLikeSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::pluck('id');
        $reviews = Review::all();

        if ($users->isEmpty() || $reviews->isEmpty()) {
            return;
        }

        foreach ($reviews as $review) {
            // レビュー投稿者以外のユーザーに限定
            $likeUserIds = $users
                ->reject(fn($id) => $id === $review->user_id)
                ->shuffle()
                ->take(rand(0, 3));

            if ($likeUserIds->isNotEmpty()) {
                $review->likedByUsers()->syncWithoutDetaching($likeUserIds);
            }
        }
    }
}
