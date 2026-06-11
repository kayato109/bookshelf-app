<?php

namespace App\Http\Controllers;

use App\Http\Requests\IndexBookRequest;
use App\Http\Requests\StoreBookRequest;
use App\Http\Requests\UpdateBookRequest;
use App\Models\Book;
use App\Models\Genre;
use Illuminate\Support\Facades\Http;


class BookController extends Controller
{
    public function index(IndexBookRequest $request)
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

            // genre が存在する場合のみフィルタ適用
            if (\App\Models\Genre::where('id', $genreId)->exists()) {
                $query->whereHas('genres', function ($q) use ($genreId) {
                    $q->where('genres.id', $genreId);
                });
            }
        }

        // sort
        $sort = $request->sort;
        if ($sort === null || $sort === '') {
            $sort = 'newest';
        }

        switch ($sort) {
            case 'oldest':
                $query->orderBy('created_at', 'asc');
                break;

            case 'title':
                $query->orderBy('title', 'asc');
                break;

            case 'rating':
                // 評価がない書籍は最後に
                $query->orderBy('reviews_avg_rating', 'desc')
                    ->orderBy('created_at', 'desc');
                break;

            default:
                // newest
                $query->orderBy('created_at', 'desc');
                break;
        }

        // ページネーション（検索条件保持）
        $books = $query->paginate(10)->appends($request->query());

        $genres = \App\Models\Genre::all();

        return view('books.index', compact('books', 'genres'));
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

    public function searchIsbn(string $isbn)
    {
        // ISBN バリデーション
        if (!preg_match('/^[0-9]{13}$/', $isbn)) {
            return response()->json([
                'error' => 'ISBNは13桁で入力してください。',
            ], 422);
        }

        // Google Books API 呼び出し
        try {
            $response = Http::timeout(5)->get(
                'https://www.googleapis.com/books/v1/volumes',
                [
                    'q' => 'isbn:' . $isbn,
                    'key' => config('services.google_books.key'),
                ]
            );
        } catch (\Exception $e) {
            return response()->json([
                'error' => '外部サービスに接続できませんでした。',
            ], 503);
        }

        // ★ 429（レート制限）専用処理
        if ($response->status() === 429) {
            return response()->json([
                'error' => 'Google Books API の利用制限に達しました。しばらく時間をおいて再度お試しください。',
            ], 429);
        }

        // API が失敗した場合
        if ($response->failed()) {
            return response()->json([
                'error' => 'Google Books API の呼び出しに失敗しました。',
            ], 503);
        }

        $items = $response->json('items');

        if (!$items || count($items) === 0) {
            return response()->json([
                'error' => '書籍情報が見つかりませんでした。',
            ], 404);
        }

        $info = $items[0]['volumeInfo'] ?? [];

        // authors（配列）を安全に取得
        $author = '';
        if (isset($info['authors']) && is_array($info['authors']) && count($info['authors']) > 0) {
            $author = $info['authors'][0];
        }

        // imageLinks の安全取得
        $imageUrl = $info['imageLinks']['thumbnail'] ?? null;

        // publishedDate の安全取得
        $publishedDate = $info['publishedDate'] ?? null;

        return response()->json([
            'title' => $info['title'] ?? '',
            'author' => $author,
            'description' => $info['description'] ?? '',
            'published_date' => $publishedDate,
            'image_url' => $imageUrl,
            'isbn' => $isbn,
        ]);
    }
}
