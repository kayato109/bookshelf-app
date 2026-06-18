<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

/**
 * 読書計画の更新用リクエスト.
 *
 * - target_date: 必須・今日以降
 */
class UpdateReadingPlanRequest extends FormRequest
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
            'target_date.required' => '期日を入力してください',
            'target_date.after_or_equal' => '期日は今日以降の日付を入力してください',
        ];
    }
}
