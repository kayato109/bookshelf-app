<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\StoreReviewRequest;
use App\Models\Book;
use App\Models\Review;

class ReviewController extends Controller
{
    public function store(StoreReviewRequest $request, Book $book)
    {
        $book->reviews()->create([
            'user_id' => auth()->id(),
            'rating' => $request->rating,
            'comment' => $request->comment,
        ]);

        return back()->with('success', 'レビューを投稿しました');
    }

    public function edit(Review $review)
    {
        $this->authorize('update', $review);

        return view('reviews.edit', compact('review'));
    }

    public function destroy(Review $review)
    {
        $this->authorize('delete', $review);

        $review->delete();

        return back()->with('success', 'レビューを削除しました');
    }
}
