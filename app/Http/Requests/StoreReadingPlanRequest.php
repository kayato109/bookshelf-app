<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * 読書計画の新規登録用リクエスト.
 *
 * - book_id: 必須・存在チェック・ユーザーごとにユニーク
 * - target_date: 必須・今日以降
 */
class StoreReadingPlanRequest extends FormRequest
{
    /**
     * 認可（ログイン済みユーザーのみ）
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * バリデーションルール
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'book_id' => [
                'required',
                'integer',
                'exists:books,id',
                Rule::unique('reading_plans', 'book_id')
                    ->where('user_id', auth()->id()),
            ],
            'target_date' => [
                'required',
                'date',
                'after_or_equal:today',
            ],
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
            'book_id.required' => '書籍を選択してください',
            'book_id.exists' => '選択した書籍が存在しません',
            'book_id.unique' => '同じ書籍の読書計画は1つしか作成できません',

            'target_date.required' => '期日を入力してください',
            'target_date.after_or_equal' => '期日は今日以降の日付を入力してください',
        ];
    }
}
