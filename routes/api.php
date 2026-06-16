<?php

use App\Http\Controllers\Api\V1\BookController;
use Illuminate\Support\Facades\Route;

// 読み込み系（GET）は公開
Route::get('/v1/books', [BookController::class, 'index']);
Route::get('/v1/books/{book}', [BookController::class, 'show']);

// 書き込み系（POST / PUT / DELETE）は Sanctum 認証
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/v1/books', [BookController::class, 'store']);
    Route::put('/v1/books/{book}', [BookController::class, 'update']);
    Route::delete('/v1/books/{book}', [BookController::class, 'destroy']);
});
