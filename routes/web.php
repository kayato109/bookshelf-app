<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BookController;
use App\Http\Controllers\RankingController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\GenreController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\ReviewLikeController;

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

//書籍一覧画面(TOP画面)
Route::get('/', [BookController::class, 'index'])->name('books.index');
Route::get('/books', [BookController::class, 'index'])->name('books.index');

//認証必須ルート
Route::middleware('auth')->group(function () {

    //書籍作成
    Route::get('/books/create', [BookController::class, 'create'])->name('books.create');
    Route::post('/books', [BookController::class, 'store'])->name('books.store');

    // 書籍編集
    Route::get('/books/{book}/edit', [BookController::class, 'edit'])->name('books.edit');

    //書籍更新
    Route::put('/books/{book}', [BookController::class, 'update'])->name('books.update');

    // 書籍削除
    Route::delete('/books/{book}', [BookController::class, 'destroy'])->name('books.destroy');

    // レビュー投稿
    Route::post('/books/{book}/reviews', [ReviewController::class, 'store'])->name('reviews.store');

    // レビュー編集
    Route::get('/reviews/{review}/edit', [ReviewController::class, 'edit'])->name('reviews.edit');

    //レビュー更新
    Route::put('/reviews/{review}', [ReviewController::class, 'update'])->name('reviews.update');

    // レビュー削除
    Route::delete('/reviews/{review}', [ReviewController::class, 'destroy'])->name('reviews.destroy');

    // レビューいいね
    Route::post('/reviews/{review}/like', [ReviewLikeController::class, 'toggle'])->name('reviews.like');

    // お気に入り一覧
    Route::get('/favorites', [FavoriteController::class, 'index'])->name('favorites.index');

    // お気に入りトグル
    Route::post('/books/{book}/favorites', [FavoriteController::class, 'toggle'])->name('favorites.toggle');

});

Route::get('/books/{book}', [BookController::class, 'show'])->name('books.show');

// ランキング（Fortify実装時にとりあえず追加）
Route::get('/ranking', [RankingController::class, 'index'])
    ->name('ranking.index');

// ジャンル一覧（Fortify実装時にとりあえず追加）
Route::get('/genres', [GenreController::class, 'index'])
    ->middleware('auth')
    ->name('genres.index');

//ルートが見つからない場合リダイレクト(最後に記述のこと)
Route::fallback(function () {
    return redirect('/');
});