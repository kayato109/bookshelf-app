<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * ジャンル更新用リクエスト.
 *
 * - name: 必須・50文字以内・ユニーク（自分以外）
 */
class UpdateGenreRequest extends FormRequest
{
    /**
     * 認可（ジャンル更新はコントローラ側で制御）
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
        $genreId = $this->route('genre')->id;

        return [
            'name' => ['required', 'string', 'max:50', "unique:genres,name,{$genreId}"],
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
