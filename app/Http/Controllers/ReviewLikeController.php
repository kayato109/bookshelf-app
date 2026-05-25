<?php

namespace App\Http\Controllers;

use App\Models\Review;

class ReviewLikeController extends Controller
{
    public function toggle(Review $review)
    {
        $userId = auth()->id();

        $liked = $review->likes()
            ->where('user_id', $userId)
            ->exists();

        if ($liked) {
            $review->likes()->where('user_id', $userId)->delete();
        } else {
            $review->likes()->create(['user_id' => $userId]);
        }

        return back();
    }
}
