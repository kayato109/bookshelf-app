<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

/**
 * お気に入り機能を扱うコントローラ.
 *
 * - お気に入り一覧
 * - お気に入りのトグル（追加 / 削除）
 */
class FavoriteController extends Controller
{
    /**
     * お気に入り一覧
     */
    public function index(): View
    {
        $books = auth()->user()
            ->favoriteBooks()
            ->with('genres')
            ->paginate(10);

        return view('favorites.index', compact('books'));
    }

    /**
     * お気に入りの追加 / 削除（トグル）
     */
    public function toggle(Book $book): RedirectResponse
    {
        $user = auth()->user();

        // 既にお気に入りなら解除、なければ追加
        $user->favoriteBooks()->toggle($book->id);

        return back();
    }
}
