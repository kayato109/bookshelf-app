<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * 書籍一覧の検索・フィルタリング用リクエスト.
 *
 * - keyword : タイトル / 著者の部分一致検索
 * - genre   : ジャンルIDによる絞り込み
 * - sort    : 並び替え
 */
class IndexBookRequest extends FormRequest
{
    /**
     * 全ユーザーが利用可能（一覧は公開）
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
            'genre' => ['nullable', 'integer'],
            'sort' => ['nullable', 'string'],
        ];
    }
}
