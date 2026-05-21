<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\IndexBookRequest;
use App\Http\Resources\Api\V1\BookResource;
use App\Models\Book;
use Illuminate\Http\Request;

class BookController extends Controller
{
    public function index(IndexBookRequest $request)
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
                $query->whereHas('genres', fn($q) => $q->where('genres.id', $genreId));
            })
            ->latest()
            ->paginate($perPage);

        return BookResource::collection($books);
    }

    public function show(Book $book)
    {
        $book->load([
            'genres',
            'reviews.user',
        ]);

        return new BookResource($book);
    }
}
