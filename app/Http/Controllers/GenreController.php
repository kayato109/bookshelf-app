<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Genre;
use App\Http\Requests\StoreGenreRequest;
use App\Http\Requests\UpdateGenreRequest;

class GenreController extends Controller
{
    public function index()
    {
        $genres = Genre::withCount('books')->paginate(10);
        return view('genres.index', compact('genres'));
    }

    public function show(Genre $genre)
    {
        $books = $genre->books()->paginate(10);
        return view('genres.show', compact('genre', 'books'));
    }

    public function create()
    {
        return view('genres.create');
    }

    public function store(StoreGenreRequest $request)
    {
        Genre::create([
            'name' => $request->name,
        ]);

        return redirect()->route('genres.index')
            ->with('success', 'ジャンルを登録しました');
    }

    public function edit(Genre $genre)
    {
        return view('genres.edit', compact('genre'));
    }

    public function update(UpdateGenreRequest $request, Genre $genre)
    {
        $genre->update([
            'name' => $request->name,
        ]);

        return redirect()->route('genres.index')
            ->with('success', 'ジャンルを更新しました');
    }

    public function destroy(Genre $genre)
    {
        if ($genre->books()->exists()) {
            return redirect()->route('genres.index')
                ->with('error', 'このジャンルには書籍が紐づいているため削除できません');
        }

        $genre->delete();

        return redirect()->route('genres.index')
            ->with('success', 'ジャンルを削除しました');
    }
}
