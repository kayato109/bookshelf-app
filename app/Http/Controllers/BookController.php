<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;

class BookController extends Controller
{
    // 書籍一覧
    public function index()
    {
        $books = Book::with(['genres'])
            ->withAvg('reviews', 'rating')
            ->paginate(10);

        return view('books.index', compact('books'));
    }

    // 書籍登録画面(Fortify実装時にとりあえず追加)
    public function create()
    {
        return view('books.create');
    }


    // 書籍詳細
    public function show(Book $book)
    {
        $book->load([
            'genres',
            'reviews.user',
        ]);
        return view('books.show', compact('book'));
    }

    //書籍編集
    public function edit(Book $book)
    {
        $this->authorize('update', $book);

        return view('books.edit', compact('book'));
    }

    //書籍削除
    public function destroy(Book $book)
    {
        $this->authorize('delete', $book);

        $book->reviews()->delete();
        $book->favorites()->delete();
        $book->genres()->detach();

        $book->delete();

        return redirect()->route('books.index')->with('success', '書籍を削除しました');
    }
}