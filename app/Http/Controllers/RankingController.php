<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\View\View;

/**
 * 書籍ランキング表示コントローラ.
 *
 * - レビュー数が 1 件以上の書籍を対象に
 *   評価平均順で上位 10 件を表示する
 */
class RankingController extends Controller
{
    /**
     * ランキング一覧
     */
    public function index(): View
    {
        $rankedBooks = Book::withAvg('reviews', 'rating')
            ->withCount('reviews')
            ->whereHas('reviews')
            ->orderByDesc('reviews_avg_rating')
            ->limit(10)
            ->get();

        return view('ranking.index', ['rankedBooks' => $rankedBooks]);
    }
}
