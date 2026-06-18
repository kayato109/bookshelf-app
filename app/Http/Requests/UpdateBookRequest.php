<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * 書籍更新用リクエスト.
 *
 * - ISBN は「自分以外で unique」
 * - genres は 1 つ以上必須
 */
class UpdateBookRequest extends FormRequest
{
    /**
     * 書籍更新は認証・権限チェックを
     * コントローラ側の authorize() で行うため true。
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
        $bookId = $this->route('book')->id;

        return [
            'title' => ['required', 'string', 'max:255'],
            'author' => ['required', 'string', 'max:255'],

            'isbn' => [
                'nullable',
                'string',
                'size:13',
                Rule::unique('books', 'isbn')->ignore($bookId),
            ],

            'published_date' => ['nullable', 'date'],
            'description' => ['nullable', 'string', 'max:2000'],
            'image_url' => ['nullable', 'url'],

            'genres' => ['required', 'array', 'min:1'],
            'genres.*' => ['exists:genres,id'],
        ];
    }

    /**
     * カスタムエラーメッセージ
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'title.required' => 'タイトルを入力してください',
            'author.required' => '著者名を入力してください',

            'isbn.size' => 'ISBN は 13 桁で入力してください',
            'isbn.unique' => '入力された ISBN は既に登録されています',

            'published_date.date' => '出版日は有効な日付形式で入力してください',

            'description.max' => '説明は 2000 文字以内で入力してください',

            'image_url.url' => '画像 URL は正しい形式で入力してください',

            'genres.required' => 'ジャンルは 1 つ以上選択してください',
        ];
    }
}
