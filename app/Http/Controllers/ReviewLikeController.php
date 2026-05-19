<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Review;

class ReviewLikeController extends Controller
{
    public function toggle(Review $review)
    {
        $user = auth()->user();

        if ($review->likes()->where('user_id', $user->id)->exists()) {
            $review->likes()->where('user_id', $user->id)->delete();
        } else {
            $review->likes()->create(['user_id' => $user->id]);
        }

        return back();
    }

}
