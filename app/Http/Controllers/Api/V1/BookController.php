<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\IndexBookRequest;
use App\Http\Requests\Api\V1\StoreBookRequest;
use App\Http\Requests\Api\V1\UpdateBookRequest;
use App\Http\Resources\Api\V1\BookResource;
use App\Models\Book;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/**
 * API V1: 書籍の一覧・詳細・作成・更新・削除を提供するコントローラ.
 */
class BookController extends Controller
{
    /**
     * 書籍一覧（検索・絞り込み・ページネーション対応）
     */
    public function index(IndexBookRequest $request): AnonymousResourceCollection
    {
        $validated = $request->validated();

        $perPage = $validated['per_page'] ?? 20;

        $books = Book::with(['genres'])
            ->withAvg('reviews', 'rating')
            ->withCount('reviews')
            ->when($validated['keyword'] ?? null, function ($query, $keyword) {
                $query->where(function ($q) use ($keyword) {
                    $q->where('title', 'like', "%{$keyword}%")
                        ->orWhere('author', 'like', "%{$keyword}%");
                });
            })
            ->when($validated['genre_id'] ?? null, function ($query, $genreId) {
                $query->whereHas('genres', fn ($q) => $q->where('genres.id', $genreId));
            })
            ->latest()
            ->paginate($perPage);

        return BookResource::collection($books);
    }

    /**
     * 書籍詳細
     */
    public function show(Book $book): BookResource
    {
        $book->load(['genres', 'reviews.user']);

        return new BookResource($book);
    }

    /**
     * 書籍登録
     */
    public function store(StoreBookRequest $request): JsonResponse
    {
        $this->authorize('create', Book::class);

        $validated = $request->validated();
        $validated['user_id'] = $request->user()->id;

        $book = Book::create($validated);
        $book->genres()->attach($validated['genres']);
        $book->load(['genres', 'reviews.user']);

        return (new BookResource($book))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * 書籍更新
     */
    public function update(UpdateBookRequest $request, Book $book): BookResource
    {
        $this->authorize('update', $book);

        $validated = $request->validated();

        $book->update($validated);
        $book->genres()->sync($validated['genres']);
        $book->load(['genres', 'reviews.user']);

        return new BookResource($book);
    }

    /**
     * 書籍削除
     */
    public function destroy(Book $book): JsonResponse
    {
        $this->authorize('delete', $book);

        $book->delete();

        return response()->json(null, 204);
    }
}
