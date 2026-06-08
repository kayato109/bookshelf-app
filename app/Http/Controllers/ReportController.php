<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Collection;
use App\Models\Book;
use App\Models\Genre;
use App\Models\Review;

class ReportController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // 1. 基本サマリー
        $totalReviews = Review::where('user_id', $user->id)->count();

        $booksRead = Review::where('user_id', $user->id)
            ->distinct('book_id')
            ->count('book_id');

        $averageRating = Review::where('user_id', $user->id)
            ->avg('rating');
        $averageRating = $averageRating ? round($averageRating, 1) : 0;

        // 2. 評価分布（1〜5）
        $ratingDistribution = collect();
        for ($i = 1; $i <= 5; $i++) {
            $count = Review::where('user_id', $user->id)
                ->where('rating', $i)
                ->count();
            $ratingDistribution->push($count);
        }

        // 3. 高評価書籍TOP5（4星以上）
        $topRatedBooks = Review::with('book')
            ->where('user_id', $user->id)
            ->groupBy('book_id')
            ->selectRaw('book_id, AVG(rating) as avg_rating')
            ->having('avg_rating', '>=', 4)
            ->orderByDesc('avg_rating')
            ->limit(5)
            ->get()
            ->map(function ($row) {
                return [
                    'id' => $row->book->id,
                    'title' => $row->book->title,
                    'author' => $row->book->author,
                    // Blade 側は整数の★表示なので四捨五入
                    'rating' => (int) round($row->avg_rating),
                ];
            })
            ->values()
            ->all();

        // 4. ジャンル別評価傾向TOP5
        $genreRatings = Genre::with([
            'books.reviews' => function ($q) use ($user) {
                $q->where('user_id', $user->id);
            }
        ])
            ->get()
            ->map(function ($genre) {
                $ratings = $genre->books->flatMap->reviews->pluck('rating');

                return [
                    'id' => $genre->id,
                    'name' => $genre->name,
                    'count' => $ratings->count(),
                    'average_rating' => $ratings->count() > 0
                        ? round($ratings->avg(), 1)
                        : 0,
                ];
            })
            ->filter(fn($g) => $g['count'] > 0)
            ->sortByDesc('average_rating')
            ->take(5)
            ->values()
            ->all();

        $stats = [
            'summary' => [
                'total_reviews' => $totalReviews,
                'books_read' => $booksRead,
                'average_rating' => $averageRating,
            ],
            'rating_distribution' => $ratingDistribution,
            'top_rated_books' => $topRatedBooks,
            'genre_ratings' => $genreRatings,
        ];

        return view('reports.index', compact('stats'));
    }
}
