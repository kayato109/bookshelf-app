<?php

namespace Database\Seeders;

use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Seeder;

class ReviewLikeSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();
        $reviews = Review::all();

        foreach ($reviews as $review) {
            $likeUsers = $users
                ->where('id', '!=', $review->user_id)
                ->random(rand(0, 3))
                ->pluck('id');

            $review->likedBy()->syncWithoutDetaching($likeUsers);
        }
    }
}