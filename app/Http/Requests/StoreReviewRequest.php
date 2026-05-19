<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreReviewRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules()
    {
        return [
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|max:2000',
        ];
    }

    public function messages()
    {
        return [
            'rating.required' => '評価を選択してください',
            'comment.required' => 'コメントを入力してください',
            'comment.max' => 'コメントは2000文字以内で入力してください',
        ];
    }
}
