<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BookController;
use App\Http\Controllers\RankingController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\GenreController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', [BookController::class, 'index']);

// 書籍一覧（Fortify実装時にとりあえず追加）
Route::get('/books', [BookController::class, 'index'])
    ->name('books.index');

// 書籍登録画面（Fortify実装時にとりあえず追加）
Route::get('/books/create', [BookController::class, 'create'])
    ->middleware('auth')
    ->name('books.create');

// 書籍詳細（Fortify実装時にとりあえず追加）
Route::get('/books/{book}', [BookController::class, 'show'])
    ->name('books.show');

// ランキング（Fortify実装時にとりあえず追加）
Route::get('/ranking', [RankingController::class, 'index'])
    ->name('ranking.index');

// お気に入り一覧（Fortify実装時にとりあえず追加）
Route::get('/favorites', [FavoriteController::class, 'index'])
    ->middleware('auth')
    ->name('favorites.index');

// ジャンル一覧（Fortify実装時にとりあえず追加）
Route::get('/genres', [GenreController::class, 'index'])
    ->middleware('auth')
    ->name('genres.index');

//ルートが見つからない場合リダイレクト(最後に記述のこと)
Route::fallback(function () {
    return redirect('/');
});