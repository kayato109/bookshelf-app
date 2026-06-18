<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

/**
 * 書籍一覧 API の検索パラメータ用リクエスト.
 *
 * - keyword: 部分一致検索
 * - genre_id: ジャンル絞り込み
 * - page / per_page: ページネーション
 */
class IndexBookRequest extends FormRequest
{
    /**
     * 認可（一覧取得は全ユーザー許可）
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
            'keyword' => ['nullable', 'string', 'max:255'],
            'genre_id' => ['nullable', 'integer', 'exists:genres,id'],
            'page' => ['nullable', 'integer', 'min:1'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ];
    }

    /**
     * バリデーション前の整形処理
     */
    protected function prepareForValidation(): void
    {
        if ($this->has('keyword')) {
            $this->merge([
                'keyword' => trim($this->keyword),
            ]);
        }
    }
}
