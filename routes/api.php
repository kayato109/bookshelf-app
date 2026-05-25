<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\BookController;

Route::prefix('v1')
    ->name('api.v1.')
    ->group(function () {
        Route::apiResource('books', BookController::class);
    });
