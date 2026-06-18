<?php

namespace App\Exceptions;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

/**
 * アプリケーション全体の例外ハンドラ.
 *
 * API リクエスト時は JSON 形式でエラーを返す。
 */
class Handler extends ExceptionHandler
{
    /**
     * フラッシュしないフィールド
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * レポート登録
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    /**
     * 例外を HTTP レスポンスに変換
     */
    public function render($request, Throwable $e): Response
    {
        $isApi = $request->is('api/*');

        if ($e instanceof ModelNotFoundException && $isApi) {
            return response()->json([
                'error' => '書籍が見つかりませんでした。',
            ], 404);
        }

        if ($e instanceof AuthenticationException && $isApi) {
            return response()->json([
                'error' => '認証されていません。',
            ], 401);
        }

        if ($e instanceof AuthorizationException && $isApi) {
            return response()->json([
                'error' => 'この操作は許可されていません。',
            ], 403);
        }

        return parent::render($request, $e);
    }
}
