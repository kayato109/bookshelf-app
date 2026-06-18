<?php

namespace App\Http\Controllers;

use App\Models\Review;
use Illuminate\Http\RedirectResponse;

/**
 * レビューの「いいね」機能を扱うコントローラ.
 *
 * - いいねのトグル（追加 / 削除）
 */
class ReviewLikeController extends Controller
{
    /**
     * いいねの追加 / 削除（トグル）
     */
    public function toggle(Review $review): RedirectResponse
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
