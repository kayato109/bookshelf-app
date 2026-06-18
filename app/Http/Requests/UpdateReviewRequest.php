<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * レビュー更新用リクエスト.
 *
 * - rating: 1〜5 の整数
 * - comment: 必須・2000文字以内
 */
class UpdateReviewRequest extends FormRequest
{
    /**
     * 認可（レビュー更新はコントローラ側で制御）
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
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['required', 'string', 'max:2000'],
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
            'rating.required' => '評価を選択してください',

            'comment.required' => 'コメントを入力してください',
            'comment.max' => 'コメントは 2000 文字以内で入力してください',
        ];
    }
}
