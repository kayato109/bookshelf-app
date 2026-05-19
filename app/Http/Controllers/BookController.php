<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Genre;
use App\Http\Requests\StoreBookRequest;
use App\Http\Requests\UpdateBookRequest;
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

    // 書籍登録画面
    public function create()
    {
        $genres = Genre::all();
        return view('books.create', compact('genres'));
    }

    // 書籍登録処理
    public function store(StoreBookRequest $request)
    {
        // 書籍作成
        $book = Book::create([
            'user_id' => auth()->id(),
            'title' => $request->title,
            'author' => $request->author,
            'isbn' => $request->isbn,
            'published_date' => $request->published_date,
            'description' => $request->description,
            'image_url' => $request->image_url,
        ]);

        // ジャンル紐付け
        $book->genres()->sync($request->genres);

        return redirect()->route('books.index')
            ->with('success', '書籍を登録しました');
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

        $genres = Genre::all();
        $selectedGenres = $book->genres->pluck('id')->toArray();

        return view('books.edit', compact('book', 'genres', 'selectedGenres'));
    }

    //書籍更新
    public function update(UpdateBookRequest $request, Book $book)
    {
        $this->authorize('update', $book);

        // 書籍情報更新
        $book->update([
            'title' => $request->title,
            'author' => $request->author,
            'isbn' => $request->isbn,
            'published_date' => $request->published_date,
            'description' => $request->description,
            'image_url' => $request->image_url,
        ]);

        // ジャンル紐付け更新
        $book->genres()->sync($request->genres);

        return redirect()->route('books.show', $book)
            ->with('success', '書籍を更新しました');
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