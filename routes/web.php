<?php

use App\Http\Controllers\BookController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\GenreController;
use App\Http\Controllers\RankingController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\ReviewLikeController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ReadingPlanController;
use App\Http\Controllers\NotificationController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
| Web アプリケーションの画面ルートを定義
|--------------------------------------------------------------------------
*/

// 書籍一覧
Route::redirect('/', '/books');

// 書籍一覧
Route::get('/books', [BookController::class, 'index'])->name('books.index');

// ランキング（公開）
Route::get('/ranking', [RankingController::class, 'index'])->name('ranking.index');

// 認証必須ルート
Route::middleware('auth')->group(function () {

    /**
     * 書籍
     */
    Route::get('/books/create', [BookController::class, 'create'])->name('books.create');
    Route::post('/books', [BookController::class, 'store'])->name('books.store');
    Route::get('/books/{book}/edit', [BookController::class, 'edit'])->name('books.edit');
    Route::put('/books/{book}', [BookController::class, 'update'])->name('books.update');
    Route::delete('/books/{book}', [BookController::class, 'destroy'])->name('books.destroy');
    Route::get('/books/isbn/{isbn}', [BookController::class, 'searchIsbn'])->name('books.searchIsbn');

    /**
     * レビュー
     */
    Route::post('/books/{book}/reviews', [ReviewController::class, 'store'])->name('reviews.store');
    Route::get('/reviews/{review}/edit', [ReviewController::class, 'edit'])->name('reviews.edit');
    Route::put('/reviews/{review}', [ReviewController::class, 'update'])->name('reviews.update');
    Route::delete('/reviews/{review}', [ReviewController::class, 'destroy'])->name('reviews.destroy');

    // レビューいいね（トグル）
    Route::post('/reviews/{review}/like', [ReviewLikeController::class, 'toggle'])->name('reviews.like');

    /**
     * お気に入り
     */
    Route::get('/favorites', [FavoriteController::class, 'index'])->name('favorites.index');
    Route::post('/books/{book}/favorites', [FavoriteController::class, 'toggle'])->name('favorites.toggle');

    /**
     * ジャンル
     */
    Route::get('/genres', [GenreController::class, 'index'])->name('genres.index');
    Route::get('/genres/create', [GenreController::class, 'create'])->name('genres.create');
    Route::post('/genres', [GenreController::class, 'store'])->name('genres.store');
    Route::get('/genres/{genre}', [GenreController::class, 'show'])->name('genres.show');
    Route::get('/genres/{genre}/edit', [GenreController::class, 'edit'])->name('genres.edit');
    Route::put('/genres/{genre}', [GenreController::class, 'update'])->name('genres.update');
    Route::delete('/genres/{genre}', [GenreController::class, 'destroy'])->name('genres.destroy');

    /**
     * マイ読書レポート
     */
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');

    /**
     * 読書計画
     */
    Route::get('/reading-plans', [ReadingPlanController::class, 'index'])->name('reading-plans.index');
    Route::post('/reading-plans/{readingPlan}/complete', [ReadingPlanController::class, 'complete'])->name('reading-plans.complete');
    Route::get('/reading-plans/{readingPlan}/edit', [ReadingPlanController::class, 'edit'])->name('reading-plans.edit');
    Route::delete('/reading-plans/{readingPlan}', [ReadingPlanController::class, 'destroy'])->name('reading-plans.destroy');
    Route::get('/reading-plans/create', [ReadingPlanController::class, 'create'])->name('reading-plans.create');
    Route::post('/reading-plans', [ReadingPlanController::class, 'store'])->name('reading-plans.store');
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');

    /**
     * 通知機能
     */
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');

});

// 書籍詳細（公開）・・・認証ルートの後
Route::get('/books/{book}', [BookController::class, 'show'])->name('books.show');

// 存在しない URL → 書籍一覧へ
Route::fallback(fn() => redirect('/books'));
