<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Genre;
use App\Http\Requests\StoreBookRequest;
use App\Http\Requests\UpdateBookRequest;

class BookController extends Controller
{
    public function index()
    {
        $books = Book::with('genres')
            ->withAvg('reviews', 'rating')
            ->paginate(10);

        return view('books.index', ['books' => $books]);
    }

    public function create()
    {
        $genres = Genre::all();

        return view('books.create', [
            'genres' => $genres,
            'bookGenreIds' => [],
        ]);
    }

    public function store(StoreBookRequest $request)
    {
        $book = Book::create(
            $request->validated() + ['user_id' => auth()->id()]
        );

        $book->genres()->sync($request->genres);

        return redirect()
            ->route('books.index')
            ->with('success', '書籍を登録しました');
    }

    public function show(Book $book)
    {
        $book->load(['genres', 'reviews.user']);

        return view('books.show', ['book' => $book]);
    }

    public function edit(Book $book)
    {
        $this->authorize('update', $book);

        $genres = Genre::all();
        $bookGenreIds = $book->genres->pluck('id')->toArray();

        return view('books.edit', [
            'book' => $book,
            'genres' => $genres,
            'bookGenreIds' => $bookGenreIds,
        ]);
    }

    public function update(UpdateBookRequest $request, Book $book)
    {
        $this->authorize('update', $book);

        $book->update($request->validated());
        $book->genres()->sync($request->genres);

        return redirect()
            ->route('books.show', $book)
            ->with('success', '書籍を更新しました');
    }

    public function destroy(Book $book)
    {
        $this->authorize('delete', $book);

        $book->delete();

        return redirect()
            ->route('books.index')
            ->with('success', '書籍を削除しました');
    }
}
