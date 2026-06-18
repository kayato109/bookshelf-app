<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

/**
 * 書籍登録 API のバリデーションリクエスト.
 *
 * - title / author: 必須
 * - isbn: 13桁・ユニーク
 * - genres: 必須・配列・各要素は genres.id に存在
 */
class StoreBookRequest extends FormRequest
{
    /**
     * 書籍登録は認証ユーザーのみ許可されるため、
     * コントローラ側のミドルウェアで制御する。
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * バリデーションルール
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'author' => ['required', 'string', 'max:255'],
            'isbn' => ['nullable', 'string', 'size:13', 'unique:books,isbn'],
            'published_date' => ['nullable', 'date'],
            'description' => ['nullable', 'string', 'max:2000'],
            'image_url' => ['nullable', 'url'],
            'genres' => ['required', 'array'],
            'genres.*' => ['integer', 'exists:genres,id'],
        ];
    }

    /**
     * 属性名（エラーメッセージ用）
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'title' => 'タイトル',
            'author' => '著者名',
            'isbn' => 'ISBN',
            'published_date' => '出版日',
            'description' => '説明文',
            'image_url' => '画像URL',
            'genres' => 'ジャンル',
            'genres.*' => 'ジャンルID',
        ];
    }
}
