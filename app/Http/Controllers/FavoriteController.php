<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    // お気に入り一覧
    public function index()
    {
        $books = auth()->user()
            ->favoriteBooks()
            ->with('genres')
            ->paginate(10);

        return view('favorites.index', compact('books'));
    }

    //お気に入りトグル
    public function toggle(Book $book)
    {
        $user = auth()->user();

        if ($user->favoriteBooks()->where('book_id', $book->id)->exists()) {
            $user->favoriteBooks()->detach($book->id);
        } else {
            $user->favoriteBooks()->attach($book->id);
        }

        return back();
    }
}
