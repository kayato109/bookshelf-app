<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * ジャンル登録用リクエスト.
 *
 * - name: 必須・50文字以内・ユニーク
 */
class StoreGenreRequest extends FormRequest
{
    /**
     * 認可（ジャンル作成はコントローラ側で制御）
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
            'name' => ['required', 'string', 'max:50', 'unique:genres,name'],
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
            'name.required' => 'ジャンル名を入力してください',
            'name.max' => 'ジャンル名は 50 文字以内で入力してください',
            'name.unique' => 'そのジャンル名は既に使用されています',
        ];
    }
}
