<?php

use App\Http\Controllers\Api\V1\BookController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes (v1)
|--------------------------------------------------------------------------
|
| 読み込み系（GET）は公開し、
| 書き込み系（POST / PUT / DELETE）は Sanctum 認証を要求する。
|
*/

Route::prefix('v1')->group(function () {
    // 公開（GET）
    Route::get('/books', [BookController::class, 'index']);
    Route::get('/books/{book}', [BookController::class, 'show']);

    // 認証が必要（POST / PUT / DELETE）
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/books', [BookController::class, 'store']);
        Route::put('/books/{book}', [BookController::class, 'update']);
        Route::delete('/books/{book}', [BookController::class, 'destroy']);
    });
});
