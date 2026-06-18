<?php

namespace App\Http\Controllers;

use App\Http\Requests\IndexBookRequest;
use App\Http\Requests\StoreBookRequest;
use App\Http\Requests\UpdateBookRequest;
use App\Models\Book;
use App\Models\Genre;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Http;
use Illuminate\View\View;

class BookController extends Controller
{
    /**
     * 書籍一覧
     */
    public function index(IndexBookRequest $request): View
    {
        $query = Book::with(['genres'])
            ->withAvg('reviews', 'rating');

        // keyword（title / author 部分一致）
        if ($request->filled('keyword')) {
            $keyword = $request->keyword;

            $query->where(function ($q) use ($keyword) {
                $q->where('title', 'LIKE', "%{$keyword}%")
                    ->orWhere('author', 'LIKE', "%{$keyword}%");
            });
        }

        // genre（多対多）
        if ($request->filled('genre')) {
            $genreId = $request->genre;

            if (Genre::whereKey($genreId)->exists()) {
                $query->whereHas('genres', fn ($q) => $q->whereKey($genreId));
            }
        }

        // sort
        $sort = $request->input('sort', 'newest');

        $query = match ($sort) {
            'oldest' => $query->orderBy('created_at', 'asc'),
            'title' => $query->orderBy('title', 'asc'),
            'rating' => $query->orderBy('reviews_avg_rating', 'desc')
                ->orderBy('created_at', 'desc'),
            default => $query->orderBy('created_at', 'desc'),
        };

        $books = $query->paginate(10)->appends($request->query());
        $genres = Genre::all();

        return view('books.index', compact('books', 'genres'));
    }

    /**
     * 書籍作成画面
     */
    public function create(): View
    {
        return view('books.create', [
            'genres' => Genre::all(),
            'bookGenreIds' => [],
        ]);
    }

    /**
     * 書籍登録
     */
    public function store(StoreBookRequest $request): RedirectResponse
    {
        $book = Book::create(
            $request->validated() + ['user_id' => auth()->id()]
        );

        $book->genres()->sync($request->genres);

        return redirect()
            ->route('books.index')
            ->with('success', '書籍を登録しました');
    }

    /**
     * 書籍詳細
     */
    public function show(Book $book): View
    {
        $book->load(['genres', 'reviews.user']);

        return view('books.show', compact('book'));
    }

    /**
     * 書籍編集画面
     */
    public function edit(Book $book): View
    {
        $this->authorize('update', $book);

        return view('books.edit', [
            'book' => $book,
            'genres' => Genre::all(),
            'bookGenreIds' => $book->genres->pluck('id')->toArray(),
        ]);
    }

    /**
     * 書籍更新
     */
    public function update(UpdateBookRequest $request, Book $book): RedirectResponse
    {
        $this->authorize('update', $book);

        $book->update($request->validated());
        $book->genres()->sync($request->genres);

        return redirect()
            ->route('books.show', $book)
            ->with('success', '書籍を更新しました');
    }

    /**
     * 書籍削除
     */
    public function destroy(Book $book): RedirectResponse
    {
        $this->authorize('delete', $book);

        $book->delete();

        return redirect()
            ->route('books.index')
            ->with('success', '書籍を削除しました');
    }

    /**
     * ISBN 検索（Google Books API）
     */
    public function searchIsbn(string $isbn): JsonResponse
    {
        // a. ISBN バリデーション
        if (! preg_match('/^[0-9]{13}$/', $isbn)) {
            return response()->json([
                'error' => 'ISBN を13桁で入力してください',
            ], 422);
        }

        try {
            $response = Http::timeout(5)->get(
                'https://www.googleapis.com/books/v1/volumes',
                [
                    'q' => 'isbn:'.$isbn,
                    'key' => config('services.google_books.key'),
                ]
            );
        } catch (ConnectionException) {
            return response()->json([
                'error' => '外部サービスに接続できませんでした',
            ], 504);
        } catch (\Exception) {
            return response()->json([
                'error' => '外部APIエラーが発生しました',
            ], 503);
        }

        // ★ 429（レート制限）
        if ($response->status() === 429) {
            return response()->json([
                'error' => 'Google Books API の利用制限に達しました。しばらく時間をおいて再度お試しください。',
            ], 429);
        }

        if ($response->failed()) {
            return response()->json([
                'error' => '外部APIエラーが発生しました',
            ], 503);
        }

        $items = $response->json('items');

        if (empty($items)) {
            return response()->json([
                'error' => '書籍情報が見つかりませんでした',
            ], 404);
        }

        $info = $items[0]['volumeInfo'] ?? [];

        if (empty($info['title'])) {
            return response()->json([
                'error' => '書籍情報が不完全です',
            ], 502);
        }

        return response()->json([
            'title' => $info['title'],
            'author' => $info['authors'][0] ?? '',
            'description' => $info['description'] ?? '',
            'published_date' => $info['publishedDate'] ?? null,
            'image_url' => $info['imageLinks']['thumbnail'] ?? null,
            'isbn' => $isbn,
        ]);
    }
}
