<?php

namespace App\Exceptions;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    public function render($request, Throwable $e)
    {
        // API リクエストかどうか判定
        $isApi = $request->is('api/*');

        if ($e instanceof ModelNotFoundException && $isApi) {
            return response()->json([
                'error' => '書籍が見つかりませんでした。',
            ], 404);
        }

        if ($e instanceof AuthenticationException && $isApi) {
            return response()->json(['error' => '認証されていません。'], 401);
        }

        if ($e instanceof AuthorizationException && $isApi) {
            return response()->json(['error' => 'この操作は許可されていません。'], 403);
        }

        return parent::render($request, $e);
    }
}
