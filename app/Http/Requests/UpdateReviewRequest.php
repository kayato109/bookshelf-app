<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateReviewRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|max:2000',
        ];
    }

    public function messages(): array
    {
        return [
            'rating.required' => '評価を選択してください',

            'comment.required' => 'コメントを入力してください',
            'comment.max' => 'コメントは 2000 文字以内で入力してください',
        ];
    }
}
